<?php

namespace App\Admin\Controllers;

use App\Models\Payment;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Collection;

class PaymentController extends Controller
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
            ->header(trans('admin.payments'))
            ->description(trans('admin.payments_desc'))
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
        $grid = new Grid(new Payment);


        $user = Admin::user();
        if(isset($user->merchant_id) && !empty($user->merchant_id)) {
            $grid->model()->where('merchant_id', $user->merchant_id);
        }

        $grid->id('ID');
        $grid->sender_account_no('Sender');
        $grid->receiver_account_no('Receiver');
        $grid->amount('Amount');
        $grid->trx_id('Transaction ID');
        $grid->merchant_id('Merchant');
//        $grid->currency('Currency');
        $grid->transaction_datetime('Transaction Time');
//        $grid->creditOrganizationName('creditOrganizationName');
//        $grid->transactionType('transactionType');
//        $grid->MessageId('MessageId');
//        $grid->TopicArn('TopicArn');
        $grid->created_at('Notified At');
//        $grid->updated_at(trans('admin.updated_at'));

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
        $show = new Show($this->getPaymentDetail($id));

        $show->id('ID');
        $show->sender_account_no('sender_account_no');
        $show->receiver_account_no('receiver_account_no');
        $show->amount('amount');
        $show->trx_id('trx_id');
        $show->merchant_id('merchant_id');
        $show->currency('currency');
        $show->transaction_datetime('transaction_datetime');
        $show->creditOrganizationName('creditOrganizationName');
        $show->transactionType('transactionType');
        $show->MessageId('MessageId');
        $show->TopicArn('TopicArn');
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
        $form = new Form(new Payment);

        $form->display('ID');
        $form->text('sender_account_no', 'sender_account_no');
        $form->text('receiver_account_no', 'receiver_account_no');
        $form->text('amount', 'amount');
        $form->text('trx_id', 'trx_id');
        $form->text('merchant_id', 'merchant_id');
        $form->text('currency', 'currency');
        $form->text('transaction_datetime', 'transaction_datetime');
        $form->text('creditOrganizationName', 'creditOrganizationName');
        $form->text('transactionType', 'transactionType');
        $form->text('MessageId', 'MessageId');
        $form->text('TopicArn', 'TopicArn');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }

    public function getPaymentDetail(string $id): Payment {
        $user = Admin::user();
        if(isset($user->merchant_id) && !empty($user->merchant_id)) {
            return Payment::where('merchant_id', $user->merchant_id)->findOrFail($id);
        } else {
            return Payment::findOrFail($id);
        }
    }
}
