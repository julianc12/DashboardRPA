<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });</script>

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
<div class="row">
    <a id="click" onclick="history.go(0)" class="btn btn-info btn-lg">
        <span class="glyphicon glyphicon-refresh"></span> Refresh
    </a>
</div>



<form class="form-horizontal" method="post" action="{{ url('/admin/datos') }}">
    <div class="row">
        <div class="col-25">
            <label for="step">Step</label>
        </div>
        <div class="col-75">
            <input type="text" id="step" name="step" placeholder="Step" >
        </div>
    </div>

    <div class="row">
        <div class="col-25">
            <label for="command">Command</label>
        </div>
        <div class="col-75">
            <input type="text" id="command" name="command" placeholder="Command" >
        </div>
    </div>
    <div class="row">
        <div class="col-25">
            <label for="type">Type</label>
        </div>
        <div class="col-75">
            <select id="type" name="type" >
                @foreach($datos as $da)       
                <option value="{{$da}}">{{ $da}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-25">
            <label for="nextYes">Next yes</label>
        </div>
        <div class="col-75">
            <input type="text" id="nextYes" name="nextYes" placeholder="Next yes" >
        </div>
    </div>

    <div class="row">
        <div class="col-25">
            <label for="nextNo">Next no</label>
        </div>
        <div class="col-75">
            <input type="text" id="nextNo" name="nextNo" placeholder="Next no" >
        </div>
    </div>

    <div class="row">
        <div class="col-25">
            <label for="orden">Orden</label>
        </div>
        <div class="col-75">
            <input type="number" id="orden" name="orden" placeholder="orden" >
        </div>
    </div>

    <hr>

    <div class="form-group table-fields">   
        <div class="entry col-md-25 form-inline">                                  
            <label>Properties</label>
            <input name="properties" id="properties" class="form-control" type="text" style="width:250px" placeholder="Entrada properties">
            <button class="btn btn-success btn-add inline" type="button">
                <span class="glyphicon glyphicon-plus"></span>
            </button>
        </div>
    </div>

    </hr>


    <div class="row">
        <button onClick="enviarData()" type="submit" id="btn-enviar" name="btn-enviar" class="btn btn-primary">Enviar</button>
        <button onClick="enviarDatas()" type="submit" id="btn-enviar" name="btn-enviar" class="btn btn-info">Enviar y seguir creando</button>
        <button type="reset" class="btn btn-danger">Resetear</button>
        <a type="button"  href="/admin/rpa/{{$id}}" class="btn btn-warning">Volver</a>
    </div>
    <br>
    <hr>
    <div class="table-responsive">

        <!--Table-->
        <table class="table" id="data">

            <!--Table head-->
            <thead>
                <tr>
                    <th>Step</th>
                    <th class="th-lg">Command</th>
                    <th class="th-lg">Properties</th>
                    <th class="th-lg">Type</th>
                    <th class="th-lg">Next yes</th>
                    <th class="th-lg">Next no</th>
                    <th class="th-lg">Accion</th>
                </tr>
            </thead>
            <!--Table head-->

            <!--Table body-->
            <tbody>
                @foreach($table as $com)           
                <tr>
                    <th scope="row"> {{ $com->step}}  </th>
                    <td>{{  $com->command  }}</td>
                    <td>{{  $com->properties  }}</td>
                    <td>{{ $com->type }} </td>
                    <td> {{$com->nextYes }} </td>
                    <td> {{ $com->nextNo }} </td>
                    <td> <a href="/admin/rpadelete?id={{ $com->id}}&id_rpa={{$id}}" style="color:#FF0000;" ><span class="glyphicon glyphicon-trash"></span></a></td>
                </tr>
                @endforeach

            </tbody>
            <!--Table body-->

        </table>
        <!--Table-->

    </div>

</div>


</form>



<script type="text/javascript">

    function enviarData() {
        var properties = [];
        var i = 0;
        var command = document.getElementById('command').value;
        var id_rpa = <?php echo $id; ?>;
        var step = document.getElementById('step').value;
        var type = document.getElementById('type').value;
        var nextYes = document.getElementById('nextYes').value;
        var nextNo = document.getElementById('nextNo').value;
        var orden = document.getElementById('orden').value;
        if (command == "") {
            command = "\"\"";
        }
        if (step == "") {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'El campo step no puede estar vacio'
            });
        } else {
            $('input[name^="properties"]').each(function () {
                properties[i] = ($(this).val());
                i++;
            });
            $.post("/admin/datos", {orden: orden, properties: properties, nextNo: nextNo, nextYes: nextYes, type: type, step: step, id_rpa: id_rpa, command: command})
                    .done(function (data) {
                        if (data == 'bien') {
                            Swal.fire(
                                    'Envio correcto',
                                    'Sus datos fueron se insertaron correctamente',
                                    'success'
                                    );
                            location.replace("/admin/rpa/" + id_rpa);

                        } else {
                            Swal.fire({
                                type: 'error',
                                title: 'Oops...',
                                text: 'Al parecer algo salio mal'
                            });
                        }
                    });
        }
    }
</script>  




<script type="text/javascript">

    function enviarDatas() {
        var properties = [];
        var i = 0;
        var command = document.getElementById('command').value;
        var id_rpa = <?php echo $id; ?>;
        var step = document.getElementById('step').value;
        var type = document.getElementById('type').value;
        var nextYes = document.getElementById('nextYes').value;
        var nextNo = document.getElementById('nextNo').value;
        var orden = document.getElementById('orden').value;
        if (command == "") {
            command = "\"\"";
        }
        if (step == "") {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'El campo step no puede estar vacio'
            });
        } else {
            $('input[name^="properties"]').each(function () {
                properties[i] = ($(this).val());
                i++;
            });
            console.log(properties);
            $.post("/admin/datos", {orden: orden, properties: properties, nextNo: nextNo, nextYes: nextYes, type: type, step: step, id_rpa: id_rpa, command: command})
                    .done(function (data) {
                        if (data == 'bien') {
                            Swal.fire(
                                    'Envio correcto',
                                    'Sus datos fueron se insertaron correctamente',
                                    'success'
                                    );


                        } else {
                            Swal.fire({
                                type: 'error',
                                title: 'Oops...',
                                text: 'Al parecer algo salio mal'
                            });
                        }
                    });
        }
    }
</script>  


<script type="text/javascript">

    $(document).ready(function () {
        $(document).on('click', '.btn-add', function (event) {
            event.preventDefault

            var tableFields = $('.table-fields'),
                    currentEntry = $(this).parents('.entry:first'),
                    newEntry = $(currentEntry.clone()).appendTo(tableFields);

            newEntry.find('input').val('');
            tableFields.find('.entry:not(:last) .btn-add')
                    .removeClass('btn-add').addClass('btn-remove')
                    .removeClass('btn-success').addClass('btn-danger')
                    .html('<span class="glyphicon glyphicon-minus"></span>');
        }).on('click', '.btn-remove', function (e) {
            $(this).parents('.entry:first').remove();

            e.preventDefault();
            return false;
        });

    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        if (document.URL.indexOf("#") == -1) {
            url = document.URL + "#";
            location = "#";
            location.reload(true);
        }
    });
</script> 


