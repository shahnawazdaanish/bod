<?php

namespace App\Admin\Controllers;

use App\Models\Merchant;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Crypt;

class MerchantController extends Controller
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
        $grid = new Grid(new Merchant);

        $grid->id('ID');
        $grid->name('Merchant Name');
        $grid->slug('Short Name');
        $grid->endpoint('Webhook Endpoint');
        $grid->contact('Contact Number');
        $grid->account_no('Merchant Wallet Number');
        $grid->app_key('Application Key');
//        $grid->app_secret('Application Secret');
//        $grid->bkash_username('Merchant Username');
//        $grid->bkash_password('Merchant Password');
        $grid->status('Status');
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));

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
        $show = new Show(Merchant::findOrFail($id));

        $show->id('ID');
        $show->name('Merchant Name');
        $show->slug('Short Name');
        $show->endpoint('Webhook Endpoint');
        $show->contact('Contact Number');
        $show->account_no('Merchant Wallet Number');
        $show->text('app_key', 'Application Key');
        $show->text('app_secret', 'Application Secret');
        $show->text('bkash_username', 'Merchant Username');
        $show->text('bkash_password', 'Merchant Password');
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
        $form = new Form(new Merchant);

        // $form->display('ID');
        $form->text('name', 'Merchant Name');
        $form->text('slug', 'Short Name');
        $form->text('endpoint', 'Webhook Endpoint');
        $form->text('contact', 'Contact Number');
        $form->text('account_no', 'Merchant Wallet Number');
        $form->text('app_key', 'Application Key');
        $form->text('app_secret', 'Application Secret');
        $form->text('bkash_username', 'Merchant Username');
        $form->text('bkash_password', 'Merchant Password');
        $form->select('status', 'Status')->options(['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive']);
        // $form->display(trans('admin.created_at'));
        // $form->display(trans('admin.updated_at'));
        $form->saving(function (Form $form) {
            $form->app_secret = Crypt::encrypt($form->app_secret);
            $form->bkash_password = Crypt::encrypt($form->bkash_password);
        });

        return $form;
    }
}
