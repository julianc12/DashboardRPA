<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });</script>


<hr>
<a type="button" title="Ejecutar rpa {{$id}}" onclick="orq({{$id}})"  class="btn btn-warning col-md-12">Ejecutar Orquestacion</a>
<script>
    
function orq(id){
     Swal.fire(
            'Envio correcto',
            'La orquestacion se empezara a ejecutar',
            'success'
            );
     $.post("/admin/orquestacion/call", {idorq:id})
                    .done(function (data) {
                    });
                }
</script>