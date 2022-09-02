<!-- Main sidebar -->
<div class="sidebar sidebar-main">
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-user">
            <div class="category-content">
                <div class="media">
                    <a href="#" class="media-left"><img src="{{ Admin::user()->avatar }}" class="img-circle img-sm" alt=""></a>
                    <div class="media-body">
                        <span class="media-heading text-semibold">{{ Admin::user()->name }}</span>
                        <!-- Status -->
                        <a href="#"><i class="fa fa-circle text-success"></i> {{ trans('admin.online') }}</a>
                        <!--div class="text-size-mini text-muted">
                            <i class="icon-pin text-size-small"></i> &nbsp;Santa Ana, CA
                        </div-->
                    </div>

                    <div class="media-right media-middle">
                        <ul class="icons-list">
                            <li>
                                <a href="{{ admin_base_path('auth/setting') }}"><i class="icon-cog3"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- /user menu -->

    @if(config('admin.enable_menu_search'))
        <!-- search form (Optional) -->
            <form class="sidebar-form" style="overflow: initial;" onsubmit="return false;">
                <div class="input-group">
                    <input type="text" autocomplete="off" class="form-control autocomplete" placeholder="Search...">
                    <span class="input-group-btn">
							<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
							</button>
						  </span>
                    <ul class="dropdown-menu" role="menu" style="min-width: 210px;max-height: 300px;overflow: auto;">
                        @foreach(Admin::menuLinks() as $link)
                            <li>
                                <a href="{{ admin_base_path($link['uri']) }}"><i class="fa {{ $link['icon'] }}"></i>{{ admin_trans($link['title']) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </form>
            <!-- /.search form -->
    @endif
    <!-- Main navigation -->
        <div class="sidebar-category sidebar-category-visible">
            <div class="category-content no-padding">
                <ul class="navigation navigation-main navigation-accordion">

                    <li class="header"></li>

                    @each('admin::partials.menu', Admin::menu(), 'item')

                </ul>
            </div>
        </div>
        <!-- /main navigation -->

    </div>
</div>
<!-- /main sidebar -->