<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
        @if(config('admin.show_environment'))
            <strong>Entorno</strong>&nbsp;&nbsp; {!! config('app.env') !!}
        @endif

        &nbsp;&nbsp;&nbsp;&nbsp;

        @if(config('admin.show_version'))
        <strong>Version</strong>&nbsp;&nbsp; {!! env('VERSION') !!}
        @endif

    </div>
    <!-- Default to the left -->
    <strong>Atlas 2019 - Proyecto Apolo <a href="#" target="_blank">laravel-admin</a></strong>
</footer>