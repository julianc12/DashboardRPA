<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });
</script>

<style>
    * {
        box-sizing: border-box;
    }

    input, select, textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        resize: vertical;
    }

    input[type=file] {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        resize: vertical;

    }

    label {
        padding: 12px 12px 12px 0;
        display: inline-block;
    }

    input[type=submit] {
        background-color: #4CAF50;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        float: right;
    }


    input[type=submit]:hover {
        background-color: #45a049;
    }

    .container {
        border-radius: 5px;
        background-color: #f2f2f2;
        padding: 20px;
    }

    .col-25 {
        float: left;
        width: 25%;
        margin-top: 6px;
    }

    .col-75 {
        float: left;
        width: 75%;
        margin-top: 6px;
    }

    /* Clear floats after the columns */
    .row:after {
        content: "";
        display: table;
        clear: both;
    }




    /* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
    @media screen and (max-width: 600px) {
        .col-25, .col-75, input[type=submit] {
            width: 100%;
            margin-top: 0;
        }
    }
</style>
<form class="form-horizontal" method="post" action="{{ url('/admin/datos') }}" autocomplete="off">

    <div class="row">
        <div class="col-25">
            <label for="step">Name</label>
        </div>
        <div class="col-75">
            <input type="text" id="name" name="name" placeholder="Name" >
        </div>
    </div>
    <div class="row" style="display: none;">
        <div class="col-25">
            <label for="sis">Destino</label>
        </div>
        <div class="col-75">
            <input type="text" id="sis" name="sis" placeholder="Sistema destino"  >
        </div>
    </div>

    <div class="row">
        <div class="col-25">
            <label for="sleep">Tiempo de espera</label>
        </div>
        <div class="col-75">
            <input type="number" id="sleep" name="sleep" placeholder="Tiempo de espera">
        </div>
    </div>

    <div class="row">
        <div class="col-25">
            <label for="type">Type</label>
        </div>
        <div class="col-75">
            <select id="type" name="type" >
                <?php echo $type ?>
            </select>
        </div>
    </div>
    <div class="row" id="hosti">
        <div class="col-25">
            <label for="host">Host</label>
        </div>
        <div class="col-75">
            <input type="text" id="host" name="host" placeholder="Host" >
        </div>
    </div>

    <div class="row" id="useri">
        <div class="col-25">
            <label for="user">User</label>
        </div>
        <div class="col-75">
            <input type="text" id="user" name="user" placeholder="User" >
        </div>
    </div>

    <div class="row" id="passwordi">
        <div class="col-25">
            <label for="password">Password</label>
        </div>
        <div class="col-75">
            <input type="password" id="password" name="password" placeholder="Password">
        </div>
    </div>

    <div class="row" id="driveri">
        <div class="col-25">
            <label for="driverlocation">Driver Location</label>
        </div>
        <div class="col-75">
            <input type="text" id="driverlocation" name="driverlocation" placeholder="Driver Location" >
        </div>
    </div>

    <div class="row" id="downloadi">
        <div class="col-25">
            <label for="step">Dowload Location</label>
        </div>
        <div class="col-75">
            <input type="text" id="download" name="download" placeholder="Location download" >
        </div>

    </div>


    <div class="row" id="multiplexi">
        <div class="col-25">
            <label for="driverlocation">Multiplex operaciones</label>
        </div>
        <div class="col-75">
            <input type="checkbox" id="multiplex" >Si lo selecciona este se encargara de ejecutar operaciones repetitivas 
        </div>
    </div>

    <div class="row" id="cargcsv">
        <div class="col-25">
            <label for="step">Ruta del csv</label>
        </div>
        <div class="col-75">
            <input type="text" id="csv" name="csv" placeholder="Cargar csv" >
        </div>
    </div>

	<div class="row" id="calendarizai">
        <div class="col-25">
            <label >Calendarización</label>
        </div>
        <div class="col-75">
            <input type="checkbox" id="calendariza" >Si lo selecciona este se encargara calendarizar la ejecución de tú RPA
        </div>
    </div>

    <div class="row" id="cargarFecha">
        <div class="col-md-2">
            <label for="step">Fecha Inicial</label>
        </div>
        <div class="col-md-4">
            <input type="date" id="FechaInicial" name="FechaInicial" placeholder="Fecha Inicial de la ejecución" >
        </div>
        <div class="col-md-2">
            <label for="step">Fecha Fin</label>
        </div>
        <div class="col-md-4">
            <input type="date" id="FechaFin" name="FechaFin" placeholder="Fecha final de ejecución" >
        </div>
    </div>

    <div class="row" id="cargarHora">
        <div class="col-md-2">
            <label for="step">Hora Inicial</label>
        </div>
        <div class="col-md-4">
            <input type="time" id="HoraInicial" name="HoraInicial" placeholder="Hora minima de la ejecución" >
        </div>
        <div class="col-md-2">
            <label for="step">Hora Fin</label>
        </div>
        <div class="col-md-4">
            <input type="time" id="HoraFin" name="HoraFin" placeholder="Hora máxima de ejecución" >
        </div>
    </div>
   
   	<div class="row" id="Repeticion">
        <div class="col-25">
            <label for="step">Repeticiones</label>
        </div>
        <div class="col-75">
            <input type="number" id="repeticiones" name="repeticiones" placeholder="Repeticiones en el día" >
        </div>
    </div>


    </br>
    </br>
    </br>
    <div class="row">
        <a onClick="enviarData()" type="button" id="btn-enviar" name="btn-enviar" class="btn btn-primary">Enviar</a>
        <a onClick="enviarDatas()" type="button" id="btn-enviar" name="btn-enviar" class="btn btn-info">Enviar y seguir creando</a>
        <button type="reset" id="reset" class="btn btn-danger">Resetear</button>
        <a type="button"  href="javascript:window.history.back();" class="btn btn-warning">Volver</a>

    </div>

</div>

</table>
</form>

<script type="text/javascript">

    function enviarData() {
        var type = document.getElementById('type').value;
        var sis = document.getElementById('sis').value;
        var name = document.getElementById('name').value;
        var host = document.getElementById('host').value;
        var user = document.getElementById('user').value;
        var pass = document.getElementById('password').value;
        var driver = document.getElementById('driverlocation').value;
        var download = document.getElementById('download').value;
        var csv = document.getElementById('csv').value;
        var sleep = document.getElementById('sleep').value;
        var multiplex = document.getElementById('multiplex').checked;
        //nuevo
        var calendariza = document.getElementById('calendariza').checked;
        console.log(sleep);

        if (multiplex) {
            multiplex = 1;

        } else {
            multiplex = 2;
        }
        if (type == 0) {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'El campo type debe estar seleccionado'
            });
        } else if (name == "") {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'El campo name no puede estar vacio'
            });
        } else {
            if (type == 1 || type == 4) {
                if (pass == "") {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'El campo password dno puede estar vacio'
                    });
                } else if (user == "") {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'El campo usuario no puede estar vacio'
                    });
                } else if (host == "") {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'El campo host no puede estar vacio'
                    });
                } else {
                    $.post("/admin/rpcreate", {sleep: sleep, csv: csv, type: type, multiplex: multiplex, sis: sis, name: name, pass: pass, host: host, user: user})
                            .done(function (data) {

                                if (data == 'bien') {
                                    Swal.fire(
                                            'Envio correcto',
                                            'Sus datos fueron insertados correctamente',
                                            'success'
                                            );
                                    window.history.back();
                                } else if (data == 'existe') {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Al parecer el nombre que ingresaste ya se encuentra en uso'
                                    });
                                } else if (data == 'faltaarchivocesv') {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Al parecer no digitaste la ruta del archivo'
                                    });


                                } else {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Algo fallo'
                                    });
                                }
                            });
                }
            } else if (type == 2) {
                if (download == "") {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'El campo download no puede estar vacio'
                    });
                } else if (driver == "") {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'El campo driver no puede estar vacio'
                    });
                } else {
                    $.post("/admin/rpcreate", {sleep: sleep, csv: csv, multiplex: multiplex, type: type, sis: sis, name: name, download: download, driver: driver})
                            .done(function (data) {
                                if (data == 'bien') {
                                    Swal.fire(
                                            'Envio correcto',
                                            'Sus datos fueron insertados correctamente',
                                            'success'
                                            );
                                    window.history.back();
                                } else if (data == 'existe') {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Al parecer el nombre que ingresaste ya se encuentra en uso'
                                    });
                                } else if (data == 'faltaarchivocesv') {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Al parecer el no digitaste la ruta del archivo'
                                    });

                                } else {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Algo fallo'
                                    });
                                }
                            });
                }

            } else if (type == 3) {
                if (driver == "") {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'El campo de localizacion del driver no puede estar vacio'
                    });
                } else {
                    $.post("/admin/rpcreate", {sleep: sleep, csv: csv, multiplex: multiplex, type: type, sis: sis, name: name, driver: driver})
                            .done(function (data) {
                                if (data == 'bien') {
                                    Swal.fire(
                                            'Envio correcto',
                                            'Sus datos fueron insertados correctamente',
                                            'success'
                                            );
                                    window.history.back();
                                } else if (data == 'existe') {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Al parecer el nombre que ingresaste ya se encuentra en uso'
                                    });
                                } else if (data == 'faltaarchivocesv') {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Al parecer el no digitaste la ruta del archivo'
                                    });

                                } else {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Algo fallo'
                                    });
                                }
                            });
                }

            }

        }

    }
</script>  

<script type="text/javascript">

    function enviarDatas() {
        var type = document.getElementById('type').value;
        var sis = document.getElementById('sis').value;
        var name = document.getElementById('name').value;
        var host = document.getElementById('host').value;
        var user = document.getElementById('user').value;
        var pass = document.getElementById('password').value;
        var driver = document.getElementById('driverlocation').value;
        var download = document.getElementById('download').value;
        var csv = document.getElementById('csv').value;
        var sleep = document.getElementById('sleep').value;
        var multiplex = document.getElementById('multiplex').checked;
        //nuevo
        var calendariza = document.getElementById('calendariza').checked;*/
        console.log(sleep);

        if (multiplex) {
            multiplex = 1;

        } else {
            multiplex = 2;
        }
        if (type == 0) {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'El campo type debe estar seleccionado'
            });
        } else if (name == "") {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'El campo name no puede estar vacio'
            });
        } else {
            if (type == 1 || type == 4) {
                if (pass == "") {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'El campo password dno puede estar vacio'
                    });
                } else if (user == "") {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'El campo usuario no puede estar vacio'
                    });
                } else if (host == "") {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'El campo host no puede estar vacio'
                    });
                } else {
                    $.post("/admin/rpcreate", {sleep: sleep, csv: csv, type: type, multiplex: multiplex, sis: sis, name: name, pass: pass, host: host, user: user})
                            .done(function (data) {

                                if (data == 'bien') {
                                    Swal.fire(
                                            'Envio correcto',
                                            'Sus datos fueron insertados correctamente',
                                            'success'
                                            );
                                    document.getElementById("reset").click();
                                } else if (data == 'existe') {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Al parecer el nombre que ingresaste ya se encuentra en uso'
                                    });
                                } else if (data == 'faltaarchivocesv') {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Al parecer no digitaste la ruta del archivo'
                                    });


                                } else {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Algo fallo'
                                    });
                                }
                            });
                }
            } else if (type == 2) {
                if (download == "") {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'El campo download no puede estar vacio'
                    });
                } else if (driver == "") {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'El campo driver no puede estar vacio'
                    });
                } else {
                    $.post("/admin/rpcreate", {sleep: sleep, csv: csv, multiplex: multiplex, type: type, sis: sis, name: name, download: download, driver: driver})
                            .done(function (data) {
                                if (data == 'bien') {
                                    Swal.fire(
                                            'Envio correcto',
                                            'Sus datos fueron insertados correctamente',
                                            'success'
                                            );
                                    document.getElementById("reset").click();
                                } else if (data == 'existe') {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Al parecer el nombre que ingresaste ya se encuentra en uso'
                                    });
                                } else if (data == 'faltaarchivocesv') {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Al parecer el no digitaste la ruta del archivo'
                                    });

                                } else {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Algo fallo'
                                    });
                                }
                            });
                }

            } else if (type == 3) {
                if (driver == "") {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'El campo de localizacion del driver no puede estar vacio'
                    });
                } else {
                    $.post("/admin/rpcreate", {sleep: sleep, csv: csv, multiplex: multiplex, type: type, sis: sis, name: name, driver: driver})
                            .done(function (data) {
                                if (data == 'bien') {
                                    Swal.fire(
                                            'Envio correcto',
                                            'Sus datos fueron insertados correctamente',
                                            'success'
                                            );
                                    document.getElementById("reset").click();
                                } else if (data == 'existe') {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Al parecer el nombre que ingresaste ya se encuentra en uso'
                                    });
                                } else if (data == 'faltaarchivocesv') {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Al parecer el no digitaste la ruta del archivo'
                                    });

                                } else {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...',
                                        text: 'Algo fallo'
                                    });
                                }
                            });
                }

            }

        }

    }
</script>  




<script >
    $(document).ready(function () {
        $("#hosti").css("display", "none");
        $("#useri").css("display", "none");
        $("#downloadi").css("display", "none");
        $("#driveri").css("display", "none");
        $("#passwordi").css("display", "none");
        $("#cargcsv").css("display", "none");
        $("#cargdate").css("display", "none");

        $("#cargarFecha").css("display", "none");
        $("#cargarHora").css("display", "none");
        $("#Repeticion").css("display", "none");


        $("#type").change(function () {
            if ($(this).val() == 1 || $(this).val() == 4)
            {
                $("#hosti").css("display", "block");
                $("#useri").css("display", "block");
                $("#passwordi").css("display", "block");
                $("#downloadi").css("display", "none");
                $("#driveri").css("display", "none");

            } else if ($(this).val() == 2)
            {
                $("#hosti").css("display", "none");
                $("#useri").css("display", "none");
                $("#passwordi").css("display", "none");
                $("#downloadi").css("display", "block");
                $("#driveri").css("display", "block");

            } else if ($(this).val() == 3)
            {
                $("#hosti").css("display", "none");
                $("#useri").css("display", "none");
                $("#passwordi").css("display", "none");
                $("#downloadi").css("display", "none");
                $("#driveri").css("display", "block");

            }




        });
        $("#multiplex").change(function () {
            var condiciones = $(this).is(":checked");
            if (!condiciones) {
                $("#cargcsv").css("display", "none");
            } else {
                $("#cargcsv").css("display", "block");
            }

        });

        $("#calendariza").change(function () {
            var condiciones = $(this).is(":checked");
            if (!condiciones) {
                $("#cargarFecha").css("display", "none");
                $("#cargarHora").css("display", "none");
                $("#Repeticion").css("display", "none");
            } else {
                $("#cargarFecha").css("display", "block");
                $("#cargarHora").css("display", "block");
                $("#Repeticion").css("display", "block");
            }

        });
    });
</script>
