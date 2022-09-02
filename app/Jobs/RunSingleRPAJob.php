<?php

namespace App\Jobs;

use App\Models\Rpa;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class RunSingleRPAJob implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    private $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request) {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $rpa = Rpa::findOrFail($this->request['id']);
        $rpa->status = "EJECUTANDOSE...";
        $rpa->update();

        $this->request['sendOutput'] = true;
        $datacarg = 0;
        $rpa_data = Rpa::find($this->request['id']);
        if (!empty($rpa_data)) {
            foreach (DB::table('rpa')->select('id_rpa_type')->where('id', '=', $this->request['id'])->get() as $dat) {
                $categories = $dat->id_rpa_type;
            }
            $valor = $categories;

            foreach (DB::table('rpa')->select('id_operacionmultiplex', 'name', 'sitema_destino')->where('id', '=', $this->request['id'])->get() as $dat) {
                $cat = $dat->name;
                $sysdes = $dat->sitema_destino;
                $mul = $dat->id_operacionmultiplex;
            }
            $data = new \App\Http\Controllers\API\RpaController();
            $this->request['rpaName'] = $cat;
            $this->request['rpa_type'] = $valor;

            foreach (DB::table('rpa_property')->select('name', 'value')->where('id_rpa', '=', $this->request['id'])->get() as $dat) {
                $filepath[$dat->name] = $dat->value;
            }


            if (!empty($sysdes)) {
                $this->request['systemDestination'] = $sysdes;
            } else {
                $this->request['systemDestination'] = "vacio";
            }

            $valorcomun = array();
            if ($mul == 1) {
                if (file_exists($filepath['pathcsv'])) {
                    if (($fichero = fopen($filepath['pathcsv'], "r")) !== FALSE) {
                        while (($datos = fgetcsv($fichero, 1000)) !== FALSE) {
                            $valorcomun[$datacarg] = $datos[0];
                            $datacarg++;
                        }
                    }

                    foreach ($valorcomun as $valorc) {
                        $rpa_data = Rpa::find($this->request['id']);
                        if ($rpa_data->status == 'DETENIDO') {
                            $rpa = Rpa::findOrFail($this->request['id']);
                            $rpa->status = "DISPONIBLE";
                            $rpa->update();
                            break;
                        } else {
                            $parametro = strpos($valorc, ';');
                            if ($parametro == false) {
                                $this->request['parametro'] = $valorc . ';';
                            } else {
                                $this->request['parametro'] = $valorc;
                            }
                            $this->request['mul'] = 'si';
                            $this->request['orq'] = 'no';

                            if ($valor == 1) {
                                $data->transportedFile($this->request);
                            } else {
                                $data->callRpa($this->request);
                            }
                        }
                    }
                } else {
                    $tran = new \App\Models\Transaction();
                    $tran->rpa = $this->request['rpaName'];
                    $tran->sistemadestino = $this->request['systemDestination'];
                    $tran->uuid = uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid();
                    $tran->estado = "RPA_CSV_FAIL";
                    $tran->save();
                }
            } else {
                $this->request['mul'] = 'no';
                $this->request['orq'] = 'no';
                if ($valor == 1) {
                    $data->transportedFile($this->request);
                } else {
                    $data->callRpa($this->request);
                }
            }
            $rpa = Rpa::findOrFail($this->request['id']);
            $rpa->status = "DISPONIBLE";
            $rpa->update();
        }
    }

}
