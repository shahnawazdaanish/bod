<?php

namespace App\Admin\Controllers;

use App\Events\EmailableAdded;
use App\Http\Controllers\Controller;
use App\Models\MerchantSendPaymentLink;
use App\Models\MerchantSendPaymentNotification;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;

class SendPaymentLinkController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header(trans('admin.index'))
            ->description(trans('admin.description'))
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.detail'))
            ->description(trans('admin.description'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.create'))
            ->description(trans('admin.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MerchantSendPaymentLink());

        $user = Admin::user();
        if (isset($user->merchant_id) && !empty($user->merchant_id)) {
            $grid->model()->where('merchant_id', '=', $user->merchant_id);
        }

        $grid->id('ID');
        $grid->reference_id('Reference');
        $grid->customer_email('Customer Email');
        $grid->customer_msisdn('Customer bKash Account');
        $grid->merchant_text('Text to send');
        $grid->payable_amount('Payable Amount');
        $grid->merchant_invoice_no('Merchant Invoice Number');
        $grid->column('user.username', 'Created By');
        $grid->status('Status');
        $grid->created_at(trans('admin.created_at'));
        $grid->filter(function ($filter) {
            $filter->between('created_at', 'Date Filter')->datetime();
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(MerchantSendPaymentLink::findOrFail($id));

        $show->id('ID');
        $show->reference_id('Reference ID');
        $show->customer_email('Customer Email');
        $show->customer_msisdn('Customer bKash Account');
        $show->merchant_text('Text to send');
        $show->payable_amount('Payable amount');
        $show->payable_product_description('Payable Product Description');
        $show->user_id('Created By');
        $show->status('Status');
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MerchantSendPaymentLink());

        $form->email('customer_email', 'bKash Customer Email');
        $form->mobile('customer_msisdn', 'bKash Customer Account Number');
        $form->text('merchant_invoice_no', 'Merchants Invoice Number')
            ->rules('required|min:6|unique:merchant_send_pay_link,merchant_invoice_no');
        $form->text('merchant_text', 'Merchants custom text')
            ->rules('required|min:3');
        $form->number('payable_amount', 'Payable amount')
            ->rules('required');
        $form->textarea('payable_product_description', 'Payable Product Description')
            ->rules('required|max:100');
        $form->switch('allow_custom_amount', 'Allow any amount from customer');
        $form->switch('enable_retry', 'Enable retry if sending fails');
        $form->saving(function (Form $form) {
            $form->model()->reference_id = strtoupper(uniqid('L')) . rand(11, 99);

            $user = Admin::user();
            if ($user) {
                $form->model()->user_id = $user->id;
                if (isset($user->merchant_id) && !empty($user->merchant_id)) {
                    $form->model()->merchant_id = $user->merchant_id;
                } else {
                    $form->model()->merchant_id = 0;
                }
            } else {
                return $this->sendError('Error!', 'Please login to perform this action');
            }
        });

        $form->saved(function (Form $form) {
            $email = $form->model()->customer_email;
            event(new EmailableAdded('payment_link', $email, $form->model()));
        });

        return $form;
    }

    public function sendError($title, $message)
    {
        $error = new MessageBag([
            'title' => $title,
            'message' => $message,
        ]);
        return back()->with(compact('error'));
    }
}
