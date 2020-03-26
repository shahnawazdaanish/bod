<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\MissedPayment\Approve;
use App\Models\MissedPayment;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class MissedPaymentController extends Controller
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
        $grid = new Grid(new MissedPayment);

        $user = Admin::user();
        if(isset($user->merchant_id) && !empty($user->merchant_id)){
            $grid->model()->where('merchant_id','=', $user->merchant_id);
        }

        $grid->id('ID');
        $grid->msisdn('Customer bKash Account');
        $grid->transaction_id('Transaction ID');
//        $grid->merchant_id('merchant_id');
        $grid->status('Status');
        $grid->created_at(trans('admin.created_at'));
//        $grid->updated_at(trans('admin.updated_at'));

        $grid->actions(function ($actions) {
            $actions->add(new Approve);
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
        $show = new Show(MissedPayment::findOrFail($id));

        $show->id('ID');
        $show->msisdn('bKash Customer Account Number');
        $show->transaction_id('bKash Transaction ID');
//        $show->merchant_id('merchant_id');
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
        $form = new Form(new MissedPayment);

//        $form->display('ID');
        $form->text('msisdn', 'bKash Customer Account Number');
        $form->text('transaction_id', 'bKash Transaction ID')
            ->rules('required|min:3|unique:missed_payments,transaction_id');
//        $form->text('merchant_id', 'merchant_id');
//        $form->text('status', 'status');
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));
        $form->saving(function(Form $form){
            $user = Admin::user();
            if(isset($user->merchant_id) && !empty($user->merchant_id)){
                $form->model()->merchant_Id = $user->merchant_id;
            }
        });
        return $form;
    }
}
