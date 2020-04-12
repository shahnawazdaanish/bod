<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\MissedPayment\Approve;
use App\Models\Merchant;
use App\Models\MissedPayment;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Carbon\Carbon;
use Encore\Admin\Actions\Response;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

//        $grid->actions(function ($actions) {
//            $actions->add(new Approve);
//        });

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
        $form->saved(function(Form $form){
            $user = Admin::user();
            $merchant = Merchant::findOrFail($user->merchant_id);
            $this->fetchPayment($form->model(), $merchant);
        });
        return $form;
    }


    public function fetchPayment(Model $model, $merchant)
    {
        try {
            // $model ...
            if($merchant) {
                $user = Admin::user();

//                if ($model->status == 'PENDING') {
                    $bkash = new bKashController($merchant);
                    $resp = $bkash->searchTransaction($model->transaction_id);

                    if (is_array($resp) && isset($resp['trxID'])) {
                        $payment = new Payment();
                        $payment->sender_account_no = isset($resp['customerMsisdn']) ? $resp['customerMsisdn'] : '';
                        $payment->receiver_account_no = isset($resp['organizationShortCode']) ? $resp['organizationShortCode'] : '';
                        $payment->amount = isset($resp['amount']) ? (double)$resp['amount'] : '';
                        $payment->trx_id = isset($resp['trxID']) ? $resp['trxID'] : '';
                        $payment->merchant_id = $merchant->id;
                        $payment->currency = isset($resp['currency']) ? $resp['currency'] : '';
                        $payment->transaction_datetime = isset($resp['completedTime']) ?
                            Carbon::createFromFormat('Y-m-d H:i:s',
                                str_replace('T', ' ', str_replace(":000 GMT+0600", "", $resp['completedTime']))
                            )->setTimezone('Asia/Dhaka')->toDateTimeString() : '';
                        $payment->transactionType = isset($resp['transactionType']) ? $resp['transactionType'] : '';
                        $payment->save();
                        if ($payment) {
                            $model->status = "ADDED";
                            $model->save();
                        }
                    } else {
                        $model->status = "NOTFOUND";
                        $model->save();
                    }
//                } else {
//                    $model->status = "EXISTS";
//                    $model->save();
//                }

                /*$userWithRole = Administrator::whereHas('roles',  function ($query) {
                    $query->whereIn('slug', ['payment-admin']);
                })->where('id', $user->id)->first();

                if($userWithRole) {
                    if (isset($userWithRole->merchant_id) && !empty($userWithRole->merchant_id)) {
                        if ($userWithRole->merchant_id != $merchant->id) {
                            return $this->response()->error("You do not have access to approve this");
                        }
                    }


                } else {
                    return $this->response()->error("You are not allowed for this, ask you admin");
                }*/
            } else {
                Log::info("Merchant info not found");
                $resp = new Response();
                return $resp->swal()->error("Merchant information not found");
            }
        } catch (\Exception $e) {
            Log::error("Exception => [". $e->getLine() . "]:" . $e->getMessage());
            $resp = new Response();
            return $resp->swal()->error("Exception => [". $e->getLine() . "]:" . $e->getMessage());
        }
    }
}
