<?php

namespace App\Http\Controllers\API;

use App\Jobs\RunSingleRPAJob;
use App\Models\CgunoDocumentosConfig;
use App\Models\Rpa;
use App\Models\RpaCommand;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\TransactionPending;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use GuzzleHttp\Client;
use DB;

class RpaController extends Controller {

    public $successStatus = 200;
    public $notFoundStatus = 404;
    public $badRequestStatus = 400;

//Recibe el JSON encargado de la ejecucion del desktop
//Envia la seÃƒÂ±al al controlador del RPA con todos los pasos que se realizan

    public function callRpaById($request) {

        try {
            $this->dispatch(new RunSingleRPAJob(['id'=>$request]));
            $description="RPA Agendado para iniciar";
            $successStatus = "OK";
        } catch (\Exception $e) {

            Log::info($e);
            $description="Error ejecutando RPA ".$e->getMessage();
            $successStatus = "FAIL";
        }
        return response()->json(["message" => $description], 200);
    }

    public function callRpa($request) {
        $tran = new Transaction();
        $tran->rpa = $request['rpaName'];
        $tran->sistemadestino = $request['systemDestination'];
        $tran->uuid = uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid();
        $tran->estado = "STARTING";
        $tran->orquestacion = $request['orq'];
        $tran->multiplex = $request['mul'];
        $tran->save();

        $pending = new TransactionPending();
        $pending->id_transaction = $tran->id;
        $pending->criteria = "''";
        $pending->status = "PENDING";
        $pending->save();

        foreach (DB::table("rpa")->select("id_rpa_type")->where("name", "=", $request['rpaName'])->get() as $dat) {
            $atosnew = $dat->id_rpa_type;
        }
        Log::debug($atosnew);

        foreach (DB::table("rpa_endpoint")->select("endpoint")->where("tipo_rpa", "=", $atosnew)->get() as $dat) {
            $atos = $dat->endpoint;
        }
        Log::debug($atos);
        $host = $atos;
// $host = env("RPA_HOST", "http://10.1.1.64:9000/");
        Log::debug("Intentando peticion a " . $host . "...");
        try {
            $client = new Client(['base_uri' => $host]);

// TODO: 2019-07-26 Configurar dinamicamente el token
            $headers = array("authorization" => env("RPA_TOKEN", "123456"));
            if ($request['rpa_type'] == 3) {
                $tran->endpoint = $host . "execute/desktop/run";
            } else if ($request['rpa_type'] == 2) {
                $tran->endpoint = $host . "execute/browser/run";
            } else if ($request['rpa_type'] == 4) {
                $tran->endpoint = $host . "execute/run";
            }
            $tran->save();
            Log::debug($request['rpaName']);
            try {
                $rpa_data = Rpa::where("name", "=", $request['rpaName'])->first();
//  $rpa_properties = RpaProperty::where("id_rpa", "=", $rpa_data->id)->get();
                if (!empty($rpa_data)) {
                    foreach (DB::table('rpa_property')->select('name', 'value')->where('id_rpa', '=', $rpa_data->id)->get() as $vali) {
                        $rpa_properties[$vali->name] = $vali->value;
                    }
                    foreach (DB::table('rpa')->select('id_operacionmultiplex')->where('id', '=', $rpa_data->id)->get() as $vali) {
                        $valorMultiplex = $vali->id_operacionmultiplex;
                    }

                    $rpa_commands = RpaCommand::where("id_rpa", "=", $rpa_data->id)->get();
                    $rpa_commands_array = array();
                    foreach ($rpa_commands as $cmd) {
                        $registry = array();
                        $registry['id'] = $cmd->step;
                        $cmd->properties = str_replace('null', '', $cmd->properties);
                        if ($valorMultiplex == 1) {
                            $megarray = explode(";", $request['parametro']);
                            $searchcommand = strrpos($cmd->command, '@data_csv:insert_array@');

                            if ($searchcommand == true) {

                                $command = str_replace('@data_csv:insert_array@', '', $cmd->command);
                                $registry['command'] = $megarray[$command - 1];
                            } else {
                                $registry['command'] = str_replace("\"\"", "", $cmd->command);
                            }

                            $search = strrpos($cmd->properties, '@data_csv:insert_array@');
                            if ($search == true) {
                                $array = explode("@data_csv:insert_array@", $cmd->properties);
                                $i = 0;
                                $dat = array();
                                foreach ($array as $da) {
                                    $prueba = $i + 1;
                                    if ($prueba % 2 == 0) {
                                        $vardb = '@data_csv:insert_array@' . $da . '@data_csv:insert_array@';
                                        $pro = str_replace($vardb, $megarray[$da - 1], $cmd->properties);
                                        $registry['properties'] = json_decode($pro);
                                    }
                                    $i++;
                                }
                            } else {
                                $registry['properties'] = json_decode($cmd->properties);
                            }
                        } else {
                            $registry['command'] = str_replace("\"\"", "", $cmd->command);
                            $registry['properties'] = json_decode($cmd->properties);
                        }
                        $registry['type'] = $cmd->type;
                        $registry['nextYes'] = $cmd->nextYes;
                        $registry['nextNo'] = $cmd->nextNo;
                        $rpa_commands_array[] = $registry;
                    }
                    Log::debug($rpa_properties);

                    if (!empty($rpa_properties)) {
                        $body_data = array();
                        $body_data['pid'] = $tran->id;
                        $body_data['idPending'] = $pending->id;
                        $body_data['commands'] = $rpa_commands_array;
                        if ($request['rpa_type'] == 4) {
                            $body_data['ruteResponse'] = "/api/saveData";
                        }
                        $body_data['sendOutput'] = $request['sendOutput'] ? true : false;

                        unset($rpa_properties['pathcsv']);
                        foreach ($rpa_properties as $name => $value) {
                            $body_data[$name] = $value;
                        }
                    } else {
                        $tran->estado = "FAIL_RPA_DATA";
                        $tran->save();
                        Log::error("No se encontraron propiedades del RPA con cnombre '" . $data_for_rpa['rpaName'] . "'");
                        return response()->json(["message" => "Propiedades de RPA no encontradas"], $this->notFoundStatus);
                    }
                } else {
                    $tran->estado = "FAIL_RPA";
                    $tran->save();
                    $this->cancelProcessing($tran->id);
                    Log::error("callRpa(): No se encontrÃƒÂ³ el RPA con nombre '" . $data_for_rpa['rpaName'] . "'");
                    return response()->json(["message" => "RPA no encontrado"], $this->notFoundStatus);
                }
            } catch (\Exception $e) {
                $tran->estado = "FAIL_DATA";
                $tran->save();
                Log::error("No es posible transformar el JSON en objeto: " . $e->getMessage());
                Log::error($e);
                return response()->json(["message" => "JSON Malformede"], $this->notFoundStatus);
            }
            log::debug(json_encode($body_data));
// Envio de peticion de ejecucion al RPA
            if ($request['rpa_type'] == 3) {
                $response = $client->request("POST", "execute/desktop/run", [
                    "headers" => $headers,
                    "body" => json_encode($body_data)
                ]);
            } else if ($request['rpa_type'] == 2) {
                $response = $client->request("POST", "execute/browser/run", [
                    "headers" => $headers,
                    "body" => json_encode($body_data)
                ]);
            } else if ($request['rpa_type'] == 4) {
                $response = $client->request("POST", "execute/run", [
                    "headers" => $headers,
                    "body" => json_encode($body_data)
                ]);
            }
            $jsonRes = $response->getBody()->getContents();
            $description = "Archivo recibido y procesado, respuesta: " . $jsonRes;

            try {
                $responseJson = json_decode($jsonRes);
                if ($responseJson->errorCode === "1") {
                    $tran->estado = "PROCESSING";
                    $tran->save();
                } else {
                    $tran->estado = "FAIL";
                    $tran->save();
                }
            } catch (\Exception $e) {
                Log::error("No es posible procesar la transaccion RPA e: " . $e);
                $tran->estado = "UNKNOWN";
                $tran->save();
            }
        } catch (\Exception $e) {
            $tran->estado = "FAIL_RPA_DOWN";
            $tran->save();
            $description = "No hay conexion con el servidor";
            Log::error("No hay conexion con el servidor e: " . $e->getMessage());
        }

        Log::debug("Terminado callRpa().");
        return response()->json(["message" => $description], $this->successStatus);
    }

    public function saveFlow(Request $request) {
        $data = $request->all();
        echo $request;
        Log::debug("Iniciando para guardar comandos de escritorio");
        $pid = $data['pid'];
        $id_pending = $data['idPending'];
        if ($pid > 0 && $id_pending > 0) {
            $tran = Transaction::find($pid);
            $tran->estado = "FINISHED";
            $tran->save();
            $tran_p = TransactionPending::find($id_pending);
            $tran_p->status = "FINISHED";
            $tran_p->save();

            foreach ($data['commands'] as $cmd) {
                $tran_d = new TransactionDetail();
                $tran_d->id_transaction = $tran->id;
                $tran_d->id_transaction_pending = $tran_p->id;
                $tran_d->step = $cmd['id'];
                if (!empty($cmd['command'])) {
                    $tran_d->command = $cmd['command'];
                }

                $tran_d->type = $cmd['type'];
                $tran_d->properties = json_encode($cmd['properties']);
                $tran_d->nextYes = $cmd['nextYes'];
                $tran_d->nextNo = $cmd['nextNo'];
                if (isset($cmd['output'])) {
                    $tran_d->output = substr($cmd['output'], 0, 200000);
                }
                $tran_d->status = $cmd['status'];
                $tran_d->save();
                try {

                    echo '
                { "ESTADO": "OK" }';
                } catch (Exception $e) {

                    echo '
                { "ESTADO": "FAIL" }';
                }
            }
        } else {
            echo '
        { "ESTADO": "FAIL" }';
        }
    }

    /**
     * POST
     * saveResults
     * Obtiene los resultados de la ejecucion del RPA y los almacena
     * @param Request $request nombre del archivo copiado
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveResults(Request $request) {
        $data = $request->all();
        Log::debug("Inicia saveResults()");
        $pid = $data['pid'];
        $id_pending = $data['idPending'];
        if ($pid > 0 && $id_pending > 0) {
            $tran = Transaction::find($pid);
            $tran->estado = "FINISHED";
            $tran->save();

            $tran_p = TransactionPending::find($id_pending);
            $tran_p->status = "FINISHED";
            $tran_p->save();

            foreach ($data['commands'] as $cmd) {
                $tran_d = new TransactionDetail();
                $tran_d->id_transaction = $tran->id;
                $tran_d->id_transaction_pending = $tran_p->id;
                $tran_d->step = $cmd['id'];
                $tran_d->command = $cmd['command'];
                $tran_d->type = $cmd['type'];
                $tran_d->properties = json_encode($cmd['properties']);
                $tran_d->nextYes = $cmd['nextYes'];
                $tran_d->nextNo = $cmd['nextNo'];
                $tran_d->output = substr($cmd['output'], 0, 200000);
                $tran_d->status = $cmd['status'];
                $tran_d->save();
            }
            Log::debug("saveResults(), removiendo archivo procesado: " . $tran_p->value);
            try {
                unlink($tran_p->value);
            } catch (Exception $e) {
                Log::warning("No es posible eliminar el archivo procesado: " . $tran_p->value);
                Log::warning("Vaciando contenido del archivo: " . $tran_p->value);
                $file = fopen($tran_p->value, "w");
                fwrite($file, "");
                fclose($file);
            }


            $next_tran_p = TransactionPending::where("id_transaction", "=", $tran->id)
                    ->where("status", "=", "PENDING")
                    ->first();


            if (!empty($next_tran_p)) {

                $next_tran = Transaction::find($next_tran_p->id_transaction);
                if (!empty($next_tran)) {
                    Log::debug("saveResults(), realizando siguiente ejecucion con patron CO-TN=" . $next_tran_p->criteria);
                    $data_for_rpa = array();
                    $data_for_rpa['rpaName'] = $next_tran->rpa;
                    $data_for_rpa['sendOutput'] = $data['sendOutput'];
                    return $this->callShell($data_for_rpa, $next_tran, $next_tran_p);
                } else {
                    Log::debug("No se encontrÃƒÂ³ ninguna transacciÃƒÂ³n asociada al pendiente");
                    return response()->json(["message" => "No se encontrÃƒÂ³ ninguna transacciÃƒÂ³n asociada al pendiente"], $this->notFoundStatus);
                }
            } else {
                Log::debug("Removiendo archivo procesado 'NMBATCH-PRE.DAT'");
                try {
                    $root_location = env("FTP_LOCATION", "/home/");
                    unlink($root_location . "/trm/NMBATCH-PRE.DAT");
                } catch (Exception $e) {
                    Log::error("No es posible eliminar el archivo procesado 'NMBATCH-PRE.DAT' ");
                }

                Log::debug("Limpiando archivo 'NMBATCH.DAT'...");
                try {
                    $root_location = env("FTP_LOCATION", "/home/");
                    $file = fopen($root_location . "/trm/NMBATCH.DAT", "w");
                    fwrite($file, "");
                    fclose($file);
                } catch (Exception $e) {
                    Log::error("No es posible eliminar el archivo procesado 'NMBATCH.DAT' ");
                }

                Log::debug("Terminado saveResults()");
                return response()->json(["message" => "OK"], $this->successStatus);
            }
        } else {
            Log::error("Se recibio un ID no valido para la transaccion");
            return response()->json(["message" => "Fail"], $this->notFoundStatus);
        }
    }

    /**
     * Obtiene los pendientes de la transaccion y les asigna estado fallido "FAIL"
     * @param $id_transaction
     */
    private function cancelProcessing($id_transaction) {
        $pending_data = TransactionPending::where("id_transaction", "=", $id_transaction)
                ->where("status", "!=", "FINISHED")
                ->get();
        foreach ($pending_data as $p) {
            $p->status = "FAIL";
            $p->save();
        }
    }

    public function transportedFile(Request $request) {
        if (sizeof($request['files']) > 0) {
            Log::debug("Recibidos archivos: " . print_r($request['files'], 1));
            $first_file = $request['files'][0]; // First element of array
            $first_co = "";
            if ($first_file === "NMBATCH-PRE.DAT") {

                $tran = new Transaction();
                $tran->nombrearchivoorigen = $first_file;
                $tran->nombrearchivodestino = "NMBATCH.DAT";
                $tran->checksum = $request['checksum'];
                $tran->rpa = $request['rpaName'];
                $tran->sistemaorigen = $request['systemSource'];
                $tran->sistemadestino = $request['systemDestination'];
                $tran->uuid = uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid();
                $tran->estado = "STARTING";
                $tran->orquestacion = $request['orq'];
                $tran->multiplex = $request['mul'];
                $tran->save();


                $rpa_data = Rpa::where("name", "=", $request['rpaName'])->first();
                if (empty($rpa_data)) {
                    $tran->estado = "FAIL_RPA";
                    $tran->save();
                    Log::error("transportedFile(): No se encontrÃƒÂ³ el RPA con nombre '" . $rpa_data['rpaName'] . "'");
                    return response()->json(["message" => "RPA no encontrado"], $this->notFoundStatus);
                }

                Log::info("Creando archivos separados...");


                $root_location = env("FTP_LOCATION", "/home/");
                $path_file = $root_location . "/trm/NMBATCH-PRE.DAT";

                //$root_location = env("FTP_LOCATION", "/home/");
                if (!file_exists($path_file)) {
                    $tran->estado = "FAIL_" . $first_file;
                    $tran->save();
                    $description = "Archivo '" . $first_file . "' no se encontro el archivo de procesamiento";
                    Log::error($description);
                } else {
                    $file = fopen($path_file, "r");
                    $line_counter = 0;
                    while (($line = fgets($file)) !== false) {
                        $line_counter++;

                        // En caso de que haya una linea en el archivo que no cumpla con la cantidad de columnas (ENV.NMBATCH_COLUMNS)
                        // se debe marcar la transaccion como fallida
                        $col_expected = env("NMBATCH_COLUMNS", -1);
                        $col_obtained = strlen(trim($line, "\n\r"));
                        if ($col_expected != $col_obtained) {
                            Log::error("LÃƒÂ­nea '$line'");
                            Log::error("LÃƒÂ­nea #$line_counter, no cumple la cantidad de columnas, esperado: $col_expected, obtenido: $col_obtained");
                            $tran->estado = "FAIL_FILE_COLUMNS";
                            $tran->save();

// Si la transaccion falla, se deben remover los archivos archivos generados
                            $pending_created = TransactionPending::where("id_transaction", "=", $tran->id)->get();
                            if (!empty($pending_created)) {
                                foreach ($pending_created as $p) {
                                    Log::info("FAIL_FILE_COLUMNS, removiendo archivo generado: " . $p->value);
                                    try {
                                        unlink($p->value);
                                    } catch (Exception $e) {
                                        Log::warning("No es posible eliminar el archivo generado: " . $p->value);
                                        try {
                                            Log::warning("Vaciando contenido del archivo: " . $p->value);
                                            $file = fopen($p->value, "w");
                                            fwrite($file, "");
                                            fclose($file);
                                        } catch (Exception $e) {
                                            Log::error("No es posible vaciar el contenido del archivo: " . $p->value . " e=" . $e->getMessage());
                                        }
                                    }
                                    $p->status = "FAIL";
                                    $p->save();
                                }
                            }
                            return response()->json(["message" => "El archivo posee una estructura invÃƒÂ¡lida"], $this->badRequestStatus);
                        }

// Centro operaciones columnas 18 19 y 20
                        $co = substr($line, 18, 3);
// Tipo de nomina columna 54
                        $rt = substr($line, 154, 1);
                        Log::debug("Archivo separado 'NMBATCH-$co-$rt.DAT'");

                        if (empty($first_co)) {
// Se usa $first_co para procesar el primer archivo
                            $first_co = "$co-$rt";
                        }

                        $local_tmp = env("LOCAL_TMP", "/home/");
                        $tmp_file = fopen($local_tmp . "NMBATCH-$co-$rt.DAT", "a");
                        fwrite($tmp_file, $line);
                        fclose($tmp_file);

                        $pending = TransactionPending::where("id_transaction", "=", $tran->id)
                                ->where("criteria", "=", "$co-$rt")
                                ->first();

                        if (empty($pending)) {
                            $pending = new TransactionPending();
                            $pending->id_transaction = $tran->id;
                            $pending->criteria = "$co-$rt";
                            $pending->value = $local_tmp . "NMBATCH-$co-$rt.DAT";
                            $pending->status = "PENDING";
                            $pending->save();
                        }
                    }
                    fclose($file);

                    $first_try = TransactionPending::where("id_transaction", "=", $tran->id)
                            ->where("criteria", "=", $first_co)
                            ->first();


                    if (!empty($first_try)) {
                        return $this->callShell($request, $tran, $first_try);
                    } else {
                        Log::error("No se encontrÃƒÂ³ ningÃƒÂºn archivo que procesar");
                        return response()->json(["message" => "No se encontrÃƒÂ³ ningÃƒÂºn archivo que procesar"], $this->notFoundStatus);
                    }
                }
            } else {
                $description = "Archivo '" . $first_file . "' no se reconoce como archivo de procesamiento";
                Log::error($description);
            }
        } else {
            $description = "No se recibieron archivos que procesar";
            Log::error($description);
        }
        Log::debug("Terminado transportedFile().");
        return response()->json(["message" => $description], $this->successStatus);
    }

    private function callShell($data_for_rpa, $transaction, $pending) {
        $file_name = "NMBATCH.DAT";
        Log::debug("Archivo TMP '$pending->value' ...");

        $root_location = env("FTP_LOCATION", "/home/");
        $file_src = fopen($pending->value, "r");

        Log::debug("Vaciando NMBATCH.DAT...");
        Log::debug("Escribiendo en NMBATCH.DAT...");
        $file_dtn = fopen($root_location . "/trm/NMBATCH.DAT", "w");
        while (($line = fgets($file_src)) !== false) {
            fwrite($file_dtn, $line);
        }
        fclose($file_dtn);
        fclose($file_src);

        $pending->status = "RUNNING";
        $pending->save();

        $root_location = env("FTP_LOCATION", "/home/");
// Leer el archivo generado para obtener el Centro de costo y Lapso
        $file = fopen($root_location . "/trm/" . $file_name, "r");
        $first_line = fgets($file);
        fclose($file);

// Centro operaciones columnas 18 19 y 20
        $co = substr($first_line, 18, 3);
// Tipo de nomina
        $rt = substr($first_line, 154, 1);
// Lapso columnas 157 158 159 y 160
        $lapse = substr($first_line, 157, 4);

        $tran = $transaction;

// Lapso debe ser aÃƒÂ±o-mes (YYMM)
        if (strlen(trim($lapse)) == 4) {
            $lapse_yy = intval(substr($lapse, 0, 2));
            $lapse_mm = intval(substr($lapse, 2, 2));
// validar aÃƒÂ±o negativo y mes en rango de 1-12
            if ($lapse_yy < 1 || $lapse_mm < 1 || $lapse_mm > 12) {
                $tran->estado = "FAIL_LAPSE";
                $tran->save();
                $this->cancelProcessing($tran->id);
                Log::error("Estructura de lapso no vÃƒÂ¡lida, se esperaba YYMM, se obtuvo '$lapse'");
                return response()->json(["message" => "Estructura de lapso en el archivo no vÃƒÂ¡lida"], $this->badRequestStatus);
            } else {
                /* DO-NOTHING and continue */
            }
        } else {
            $tran->estado = "FAIL_LAPSE";
            $tran->save();
            $this->cancelProcessing($tran->id);
            Log::error("Estructura de lapso no vÃƒÂ¡lida, se esperaba YYMM, se obtuvo '$lapse'");
            return response()->json(["message" => "Estructura de lapso en el archivo no vÃƒÂ¡lida"], $this->badRequestStatus);
        }

// Teniendo el lapso, hay que consultar el numero de Liquidacion nomina
        $config = CgunoDocumentosConfig::where("center", "=", $co)
                ->where("lapse", "=", $lapse)
                ->orderBy("number", "desc")
                ->first();


        if (empty($config)) {
            $last_document = CgunoDocumentosConfig::where("center", "=", $co)
                    ->orderBy("number", "desc")
                    ->first();

            if (empty($last_document)) {
                $tran->estado = "FAIL_LN";
                $tran->save();
                $this->cancelProcessing($tran->id);
                Log::error("Numero de Liquidacion no configurado en tabla cguno_documentos_config");
                return response()->json(["message" => "Numero de Liquidacion no configurado en tablas"], $this->notFoundStatus);
            }

// Si no existe se crean dos para las dos quincenas
            $config = new CgunoDocumentosConfig();
            $config->center = $co;
            $config->lapse = $lapse;
            $n1 = $last_document->number + 1;
            $config->number = str_pad($n1, 6, "0", STR_PAD_LEFT);
            $config->save();
            $document2 = new CgunoDocumentosConfig();
            $document2->center = $co;
            $document2->lapse = $lapse;
            $n2 = $last_document->number + 2;
            $document2->number = str_pad($n2, 6, "0", STR_PAD_LEFT);
            $document2->save();
        }

// El periodo es para configurar nuevos documentos
        $pe = substr($lapse, 2, 2) * 2;
        $period1 = $pe - 1;
        $period2 = $pe;

        $LN_number = $config->number;

        if (!$LN_number) {
            $tran->estado = "FAIL_LN_CO";
            $tran->save();
            $this->cancelProcessing($tran->id);
            Log::error("Numero de Liquidacion no configurado para CO: " . $co . ", Lapso: " . $lapse);
            return response()->json(["message" => "Numero de Liquidacion no configurado para CO: " . $co . ", Lapso: " . $lapse], $this->notFoundStatus);
        }

        foreach (DB::table("rpa")->select("id_rpa_type")->where("name", "=", $request['rpaName'])->get() as $dat) {
            $atosnew = $dat->id;
            $atosnew = $dat->id_rpa_type;
        }
        Log::debug($atosnew);

        foreach (DB::table("rpa_endpoint")->select("endpoint")->where("tipo_rpa", "=", $atosnew)->get() as $dat) {
            $atos = $dat->endpoint;
        }
        Log::debug($atos);
        $host = $atos;
        Log::debug("Intentando peticion a " . $host . "...");

        try {
            $client = new Client(['base_uri' => $host]);

// TODO: 2019-07-26 Configurar dinamicamente el token
            $headers = array("authorization" => env("RPA_TOKEN", "123456"));
            $tran->endpoint = $host . "execute/run";
            $tran->save();

            try {
                $variables_i = array("@@CO@@", "@@LNNUMBER@@", "@@LAPSE@@", "@@PERIOD1@@", "@@PERIOD2@@", "@@ROSTERTYPE@@");
                $variables_r = array($co, $LN_number, $lapse, $period1, $period2, $rt);

                $rpa_data = Rpa::where("name", "=", $data_for_rpa['rpaName'])->first();
                if (!empty($rpa_data)) {
                    foreach (DB::table('rpa_property')->select('name', 'value')->where('id_rpa', '=', $rpa_data->id)->get() as $vali) {
                        $rpa_properties[$vali->name] = $vali->value;
                    }
                    foreach (DB::table('rpa')->select('id_operacionmultiplex')->where('id', '=', $rpa_data->id)->get() as $vali) {
                        $valorMultiplex = $vali->id_operacionmultiplex;
                    }
                    $rpa_commands = RpaCommand::where("id_rpa", "=", $rpa_data->id)->get();
                    $rpa_commands_array = array();
                    foreach ($rpa_commands as $cmd) {
                        $registry = array();
                        $registry['id'] = $cmd->step;
                        $cmd->properties = str_replace('null', '', $cmd->properties);
                        if ($valorMultiplex === 1) {
                            $megarray = explode(";", $request['parametro']);
                            $searchcommand = strrpos($cmd->properties, '@data_csv:insert_array@');
                            if ($searchcommand == true) {
                                $command = str_replace('@data_csv:insert_array@', '', $cmd->command);
                                $registry['command'] = str_replace($variables_i, $variables_r, $megarray[$command - 1]);
                            } else {
                                $registry['command'] = str_replace($variables_i, $variables_r, $cmd->command);
                            }

                            $search = strrpos($cmd->properties, '@data_csv:insert_array@');
                            if ($search == true) {
                                $array = explode("@data_csv:insert_array@", $cmd->properties);
                                $i = 0;
                                $dat = array();
                                foreach ($array as $da) {
                                    $prueba = $i + 1;
                                    if ($prueba % 2 == 0) {
                                        $vardb = '@data_csv:insert_array@' . $da . '@data_csv:insert_array@';
                                        $pro = str_replace($vardb, $megarray[$da - 1], $cmd->properties);
                                        $registry['properties'] = json_decode($pro);
                                    }
                                    $i++;
                                }
                            } else {
                                $registry['properties'] = json_decode($cmd->properties);
                            }
                        } else {
                            $registry['command'] = str_replace($variables_i, $variables_r, $cmd->command);
                            $registry['properties'] = json_decode($cmd->properties);
                        }
                        //
                        $registry['type'] = $cmd->type;
                        $registry['properties'] = json_decode($cmd->properties);
                        $registry['nextYes'] = $cmd->nextYes;
                        $registry['nextNo'] = $cmd->nextNo;
                        $rpa_commands_array[] = $registry;
                    }


                    if (!empty($rpa_properties)) {
                        $body_data = array();
                        $body_data['pid'] = $tran->id;
                        $body_data['idPending'] = $pending->id;
                        $body_data['commands'] = $rpa_commands_array;
                        $body_data['ruteResponse'] = "/api/saveResults";
                        $body_data['sendOutput'] = $data_for_rpa['sendOutput'] ? true : false;

                        unset($rpa_properties['pathcsv']);
                        foreach ($rpa_properties as $name => $value) {
                            $body_data[$name] = $value;
                        }
                    } else {
                        $tran->estado = "FAIL_RPA_DATA";
                        $tran->save();
                        $this->cancelProcessing($tran->id);
                        Log::error("No se encontraron propiedades del RPA con cnombre '" . $data_for_rpa['rpaName'] . "'");
                        return response()->json(["message" => "Propiedades de RPA no encontradas"], $this->notFoundStatus);
                    }
                } else {
                    $tran->estado = "FAIL_RPA";
                    $tran->save();
                    $this->cancelProcessing($tran->id);
                    Log::error("callShell(): No se encontrÃƒÂ³ el RPA con nombre '" . $data_for_rpa['rpaName'] . "'");
                    return response()->json(["message" => "RPA no encontrado"], $this->notFoundStatus);
                }
            } catch (\Exception $e) {
                $tran->estado = "FAIL_DATA";
                $tran->save();
                $this->cancelProcessing($tran->id);
                Log::error("No es posible transformar el JSON en objeto: " . $e->getMessage());
                Log::error($e);
                return response()->json(["message" => "JSON Malformed"], $this->notFoundStatus);
            }

// Envio de peticion de ejecucion al RPA
            $response = $client->request("POST", "execute/run", [
                "headers" => $headers,
                "body" => json_encode($body_data)
            ]);

            $jsonRes = $response->getBody()->getContents();
            $description = "Archivo recibido y procesado, respuesta: " . $jsonRes;

            try {
                $responseJson = json_decode($jsonRes);
                if ($responseJson->errorCode === "1") {
                    $tran->estado = "PROCESSING";
                    $tran->save();
                } else {
                    $tran->estado = "FAIL";
                    $tran->save();
                    $this->cancelProcessing($tran->id);
                }
            } catch (\Exception $e) {
                Log::error("No es posible procesar la transaccion RPA e: " . $e);
                $tran->estado = "UNKNOWN";
                $tran->save();
                $this->cancelProcessing($tran->id);
            }
        } catch (\Exception $e) {
            $tran->estado = "FAIL_RPA_DOWN";
            $tran->save();
            $this->cancelProcessing($tran->id);
            $description = "No hay conexion con el servidor";
            Log::error("No hay conexion con el servidor e: " . $e->getMessage());
        }

        Log::debug("Terminado callShell().");
        return response()->json(["message" => $description], $this->successStatus);
    }

}
