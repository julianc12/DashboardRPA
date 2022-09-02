
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{config('admin.title')}} | {{ trans('admin.login') }}</title>
    @if(!is_null($favicon = Admin::favicon()))
        <link rel="shortcut icon" href="{{$favicon}}">
    @endif
  <!-- Global stylesheets -->
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
  <link href="/login/global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
  <link href="/login/assets/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="/login/assets/core.min.css" rel="stylesheet" type="text/css">
  <link href="/login/assets/components.min.css" rel="stylesheet" type="text/css">
  <link href="/login/assets/colors.min.css" rel="stylesheet" type="text/css">
  <!-- /global stylesheets -->

  <!-- Core JS files -->
  <script src="/login/global_assets/pace.min.js"></script>
  <script src="/login/global_assets/jquery.min.js"></script>
  <script src="/login/global_assets/bootstrap.min.js"></script>
  <script src="/login/global_assets/blockui.min.js"></script>
  <!-- /core JS files -->


  <!-- Theme JS files -->
  <script src="/login/assets/app.js"></script>

  <script src="/login/global_assets/ripple.min.js"></script>
  <!-- /theme JS files -->

</head>

<body class="login-container">

<!-- Main navbar -->
<div class="navbar navbar-inverse bg-indigo">
  <div class="navbar-header">
    <a class="navbar-brand" href="{{ admin_base_path('/') }}"><img src="/assets/images/logo_icon_light.png" alt=""></a>

    <ul class="nav navbar-nav pull-right visible-xs-block">
      <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
    </ul>
  </div>

  <div class="navbar-collapse collapse" id="navbar-mobile">
    <ul class="nav navbar-nav navbar-right">
      <li>
        <a href="#">
          <i class="icon-display4"></i> <span class="visible-xs-inline-block position-right"> Go to website</span>
        </a>
      </li>

      <li>
        <a href="#">
          <i class="icon-user-tie"></i> <span class="visible-xs-inline-block position-right"> Contact admin</span>
        </a>
      </li>

      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown">
          <i class="icon-cog3"></i>
          <span class="visible-xs-inline-block position-right"> Options</span>
        </a>
      </li>
    </ul>
  </div>
</div>
<!-- /main navbar -->


<!-- Page container -->
<div class="page-container">

  <!-- Page content -->
  <div class="page-content">

    <!-- Main content -->
    <div class="content-wrapper">

      <!-- Content area -->
      <div class="content">

        <!-- Simple login form -->
        <form action="{{ admin_base_path('auth/login') }}" method="post">
          <div class="panel panel-body login-form">
            <div class="text-center">
              <div class="icon-object border-slate-300 text-slate-300"><i class="icon-reading"></i></div>
              <h5 class="content-group">Login to your account <small class="display-block">Enter your credentials below</small></h5>
            </div>



            <div class="form-group has-feedback has-feedback-left {!! !$errors->has('username') ?: 'has-error' !!}">
                @if($errors->has('username'))
                    @foreach($errors->get('username') as $message)
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                    @endforeach
                @endif
              <input type="text" class="form-control" placeholder="{{ trans('admin.username') }}" name="username" value="{{ old('username') }}">
              <div class="form-control-feedback">
                <i class="icon-user text-muted"></i>
              </div>
            </div>


            <div class="form-group has-feedback has-feedback-left" {!! !$errors->has('password') ?: 'has-error' !!}>

                @if($errors->has('password'))
                    @foreach($errors->get('password') as $message)
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                    @endforeach
                @endif
              <input type="password" class="form-control" placeholder="{{ trans('admin.password') }}" name="password">
              <div class="form-control-feedback">
                <i class="icon-lock2 text-muted"></i>
              </div>
            </div>

            <div class="form-group">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <button type="submit" class="btn bg-pink-400 btn-block">{{ trans('admin.login') }}<i class="icon-circle-right2 position-right"></i></button>
            </div>

              @if(config('admin.auth.remember'))
                  <div class="checkbox icheck">
                      <label>
                          <input type="checkbox" name="remember" value="1" {{ (!old('username') || old('remember')) ? 'checked' : '' }}>
                          {{ trans('admin.remember_me') }}
                      </label>
                  </div>
              @endif


          </div>
        </form>
        <!-- /simple login form -->


        <!-- Footer -->
        <div class="footer text-muted text-center">
        </div>
        <!-- /footer -->

      </div>
      <!-- /content area -->

    </div>
    <!-- /main content -->

  </div>
  <!-- /page content -->

</div>
<!-- /page container -->

</body>
</html>