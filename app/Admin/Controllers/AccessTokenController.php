<?php

namespace App\Admin\Controllers;

use App\Models\AccessToken;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class AccessTokenController extends Controller
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
//        return $content
//            ->header('Index')
//            ->description('description')
//            ->body($this->grid());
                return view("operacion.404");

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
            ->header('Detail')
            ->description('description')
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
            ->header('Edit')
            ->description('description')
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
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AccessToken);
        $grid->disableCreateButton();
        $grid->user_id('Usuario');
        $grid->client_id('Cliente');
        $grid->name('Aplicacion');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        $grid->expires_at('Expire at');

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
        });
        $grid->disableActions();

        /*$grid->filter(function (Grid\Filter $filter) {

            $filter->like('NOMBRE');
            $filter->like('INTEGRACION');
            $filter->like('DESCRIPCION');

        });*/

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
        $show = new Show(AccessToken::findOrFail($id));

        $show->id('ID');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AccessToken);

        $form->text('NOMBRE','nombre');
        $form->text('DESCRIPCION','descripcion');
        $form->text('INTEGRACION','integracion');
        $form->display('Created at');
        $form->display('Updated at');

        return $form;
    }
}
