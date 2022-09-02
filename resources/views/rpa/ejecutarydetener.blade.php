<script type="text/javascript">
    $.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': "{{ csrf_token() }}"
    }
    });</script>


<hr>
<a type="button" title="Ejecutar rpa {{$id}}" onclick="rpaejecu({{$id}})"  class="btn btn-warning col-md-6">Ejecutar Rpa</a>
<a type="button" title="Detener operacion" onclick="funcion({{$id}})"class="btn btn-danger col-md-6">Detener proceso</a>

<script>
    function funcion(id){
    Swal.fire({
    type: 'warning',
            title: '¿Estas seguro?',
            text: '¿Dessea detener el proceso?',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, detener!'
    }).then((result) => {
    if (result.value) {
    $.post("/admin/detener/rpa", {id:id})
            .done(function (data) {
            });
    }
    })
    }
</script>


<script>
    function rpaejecu(id){
    Swal.fire(
            'Envio correcto',
            'El rpa se empezara a ejecutar',
            'success'
            );
    $.post("/admin/rpa/data", {id:id})
            .done(function (data) {
            });
    }
</script>