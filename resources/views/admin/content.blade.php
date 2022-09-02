@extends('admin::index', ['header' => $header])

@section('content')
        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">{{ $header ?: trans('admin.title') }}</span><small>{{ $description ?: trans('admin.description') }}</small> </h4>
                    <a class="heading-elements-toggle"><i class="icon-more"></i></a></div>
            </div>

            <div class="breadcrumb-line breadcrumb-line-component"><a class="breadcrumb-elements-toggle"><i class="icon-menu-open"></i></a>
                <!-- breadcrumb start -->
                @if ($breadcrumb)
                    <ol class="breadcrumb" style="margin-right: 30px;">
                        <li><a href="{{ admin_url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                        @foreach($breadcrumb as $item)
                            @if($loop->last)
                                <li class="active">
                                    @if (\Illuminate\Support\Arr::has($item, 'icon'))
                                        <i class="fa fa-{{ $item['icon'] }}"></i>
                                    @endif
                                    {{ $item['text'] }}
                                </li>
                            @else
                                <li>
                                    <a href="{{ admin_url(\Illuminate\Support\Arr::get($item, 'url')) }}">
                                        @if (\Illuminate\Support\Arr::has($item, 'icon'))
                                            <i class="fa fa-{{ $item['icon'] }}"></i>
                                        @endif
                                        {{ $item['text'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                @elseif(config('admin.enable_default_breadcrumb'))
                    <ol class="breadcrumb" style="margin-right: 30px;">
                        <li><a href="{{ admin_url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                        @for($i = 2; $i <= count(Request::segments()); $i++)
                            <li>
                                {{ucfirst(Request::segment($i))}}
                            </li>
                        @endfor
                    </ol>
            @endif

            <!-- breadcrumb end -->


            </div>
        </div>

        <div class="content">

            <!-- Simple panel -->
            <div class="panel panel-flat">
                <!--div class="panel-heading">
                    <h5 class="panel-title">Simple panel<a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div-->

                <div class="panel-body">

                    @include('admin::partials.alerts')
                    @include('admin::partials.exception')
                    @include('admin::partials.toastr')

                    {!! $content !!}
                </div>
            </div>
            <!-- /simple panel -->
        </div>




@endsection