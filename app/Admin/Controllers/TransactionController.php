<?php

namespace App\Admin\Controllers;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use App\Models\TransactionDetail;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use DB;

class TransactionController extends Controller {

    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content) {
        return $content
                        ->header('Index')
                        ->description('description')
                        ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content) {
              if (is_numeric($id)) {
                if(strlen($id) > 9){
                    return view('operacion.404');
                }else{
            return $content
                            ->header('Detail')
                            ->description('description')
                            ->body($this->detail($id));
            }
        } else {
            return view('operacion.404');
        }
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content) {
        return view("operacion.404");
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content) {
//      return $content
//      ->header('Create')
//      ->description('description')
//      ->body($this->form());
        return view("operacion.404");
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        $grid = new Grid(new Transaction);
        $grid->id('ID');
        $grid->updated_at('Actualizado el');
        $grid->rpa("RPA");
        $grid->orquestacion()->display(function ($orquestacion) {
            if ($orquestacion == 'si') {
                return '<span class="label label-success"><span class="glyphicon glyphicon-ok"></span></span>';
            } else {
                return '<span class="label label-danger"><span class="glyphicon glyphicon-remove"></span></span>';
            }
        });

        $grid->multiplex()->display(function ($multiplex) {
            if ($multiplex == 'si') {
                return '<span class="label label-success"><span class="glyphicon glyphicon-ok"></span></span>';
            } else {
                return '<span class="label label-danger"><span class="glyphicon glyphicon-remove"></span></span>';
            }
        });
        $grid->paginate(10);

          
        $grid->header(function () {
              foreach (DB::table('url')->select('link')->where('name', '=' ,'seg_show')->get() as $uri) {
                $uril = $uri->link;
            }        
            $data['link'] = $uril;
            return view('button.data', $data);
        });
        $grid->estado()->display(function ($estado) {
            if ($estado == 'FINISHED') {
                // Consultar transacciones fallidas
                $total_fail = TransactionDetail::where("id_transaction", "=", $this->id)
                        ->where("status", "=", "FAIL")
                        ->get();
                return '<span class="label label-success">FINALIZADO</span>';
            } else if ($estado == 'STARTING') {
                return '<span class="label label-primary ">INICIANDO...</span>';
            } else if ($estado == 'FAIL_RPA_DOWN') {
                return '<span class="label label-danger" title="No hay conexi&oacute;n con el RPA">SIN CONEXIÓN</span>';
            }   else if ($estado == 'PROCESSING'){
                return '<span class="label label-info" title="No hay conexi&oacute;n con el RPA">PROCESSING</span>';
		}
		else {
                return '<span class="label label-danger">' . $estado . '</span>';
            }
        });
        $grid->model()->orderBy('id', 'desc');

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
//            $actions->disableView();
        });

        $grid->disableCreateButton();

        $grid->filter(function (Grid\Filter $filter) {

            $filter->date('created_at');
            $filter->like('nombrearchivoorigen');
            $filter->like('rpa');
            $filter->like('sistemaorigen');
            $filter->like('sistemadestino');
            $filter->like('uuid');
            $filter->like('estado');
            $filter->like('nombrearchivodestino');
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id) {
        $show = new Show(Transaction::findOrFail($id));
        $show->id('ID');
        $show->updated_at('Actualizado el');
        $show->nombrearchivoorigen("Archivo Origen");
        $show->rpa("RPA");
        $show->sistemaorigen("Sistema Origen");
        $show->sistemadestino("Sistema Destino");
        $show->uuid("UUID");
        $show->estado("ESTADO");
        $show->nombrearchivodestino("Archivo Destino");
        $show->panel()
                ->tools(function ($tools) {
                    $tools->disableEdit();
                    $tools->disableDelete();
                });
        $show->transactionDetail('Detalles', function ($line) {
            $line->header(function () {
                 foreach (DB::table('url')->select('link')->where('name', '=' ,'seg_detail')->get() as $uri) {
                $uril = $uri->link;
            }        
            $data['link'] = $uril;
                return view('button.data', $data);
            });
            /* $line->id('ID'); */
            $line->step('step');
            $line->type('tipo');
                 $line->column('properties')->display(function () {
                $valor = str_replace("[", "", $this->properties);
                $valor1 = str_replace("]", "", $valor);
                $valor2 = str_replace("\"", "", $valor1);
                $valor3 = str_replace("\\\\", "\\", $valor2);
                if(strlen($valor3) < 20){
                    return $valor3;
                }else{
                return substr($valor3, 0, 20) . "...";
                }
                 
            });
            
               $line->command('Command')->display(function () {
                if(strlen($this->command) < 20){
                    return $this->command;
                }else{
                return substr($this->command, 1, 20) . "...";
                }
                
            });
            $line->status()->display(function ($status) {
                return $status == 'OK' ? '<span class="label label-success">' . $status . '</span>' : '<span class="label label-danger">' . $status . '</span>';
            });
            $line->disableCreateButton();
            $line->updated_at('Updated at');

            $line->disableCreateButton();


            $line->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableEdit();

//            $actions->disableView();
            });
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        $form = new Form(new Transaction);
        $form->disableSubmit();
        $form->text("checksum");
        $form->text("nombrearchivoorigen");
        $form->text("rpa");
        $form->text("sistemaorigen");
        $form->text("sistemadestino");
        $form->text("endpoint");
        $form->text("uuid");
        $form->text("estado");
        $form->text("nombrearchivodestino");
        $form->text("created_at");
        $form->text("updated_at");
        $form->display('Created at');
        $form->display('Updated at');
        return $form;
    }
    //pro

}
