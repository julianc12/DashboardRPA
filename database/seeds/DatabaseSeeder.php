<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Schema\Blueprint;
use App\Models\TypeRpaCommandController;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Incrementar secuencias
        \DB::select("select nextval('type_commands_id_seq')"); // 159 -> 160
        \DB::select("select nextval('type_commands_id_seq')"); // 161
        \DB::select("select nextval('type_rpa_command_id_seq')"); // 93 -> 94
        \DB::select("select nextval('type_rpa_command_id_seq')"); // 95

        // Aumentar el tamaño de la columna properties
        Schema::table('rpa_command', function (Blueprint $table) {
            $table->string('properties', 5000)->nullable()->change();
        });
      
        $id = DB::table('type_commands')->insertGetId(
            ['tipo' => 'ddos']
        );
        TypeRpaCommandController::create(array('type_rpa' => '2', 'type_command' => $id));
        
        $id = DB::table('type_commands')->insertGetId(
            ['tipo' => 'ddos_report_xlsx']
        );
        TypeRpaCommandController::create(array('type_rpa' => '2', 'type_command' => $id));
        
        $id = DB::table('type_commands')->insertGetId(
            ['tipo' => 'ddos_report_docx']
        );
        TypeRpaCommandController::create(array('type_rpa' => '2', 'type_command' => $id));
        
        $id = DB::table('type_commands')->insertGetId(
            ['tipo' => 'ddos_report_pdf']
        );
        TypeRpaCommandController::create(array('type_rpa' => '2', 'type_command' => $id));
        
        
        $id = DB::table('type_commands')->insertGetId(
            ['tipo' => 'sqlinjection']
        );
        TypeRpaCommandController::create(array('type_rpa' => '2', 'type_command' => $id));
        
        $id = DB::table('type_commands')->insertGetId(
            ['tipo' => 'sqlinjection_report_xlsx']
        );
        TypeRpaCommandController::create(array('type_rpa' => '2', 'type_command' => $id));
        
        $id = DB::table('type_commands')->insertGetId(
            ['tipo' => 'sqlinjection_report_docx']
        );
        TypeRpaCommandController::create(array('type_rpa' => '2', 'type_command' => $id));
        
        $id = DB::table('type_commands')->insertGetId(
            ['tipo' => 'sqlinjection_report_pdf']
        );
        TypeRpaCommandController::create(array('type_rpa' => '2', 'type_command' => $id));
        
        
        $id = DB::table('type_commands')->insertGetId(
            ['tipo' => 'bruteforce']
        );
        TypeRpaCommandController::create(array('type_rpa' => '2', 'type_command' => $id));
        
        $id = DB::table('type_commands')->insertGetId(
            ['tipo' => 'bruteforce_report_xlsx']
        );
        TypeRpaCommandController::create(array('type_rpa' => '2', 'type_command' => $id));
        
        $id = DB::table('type_commands')->insertGetId(
            ['tipo' => 'bruteforce_report_docx']
        );
        TypeRpaCommandController::create(array('type_rpa' => '2', 'type_command' => $id));
        
        $id = DB::table('type_commands')->insertGetId(
            ['tipo' => 'bruteforce_report_pdf']
        );
        TypeRpaCommandController::create(array('type_rpa' => '2', 'type_command' => $id));
    }
}
