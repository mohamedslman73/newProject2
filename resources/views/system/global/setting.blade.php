<!DOCTYPE html>
<html lang="en" data-textdirection="ltr" class="loading">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Stack admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, stack admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>EGPAY - System Login</title>
    <link rel="apple-touch-icon" href="{{asset('assets/system/images/ico/apple-icon-120.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/system/images/ico/favicon.ico')}}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/fonts/feather/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/fonts/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/fonts/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/extensions/pace.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/forms/icheck/custom.css')}}">
    <!-- END VENDOR CSS-->
    <!-- BEGIN STACK CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/app.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/colors.css')}}">
    <!-- END STACK CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/core/menu/menu-types/vertical-overlay-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/core/colors/palette-gradient.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/pages/login-register.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/plugins/forms/validation/form-validation.css')}}">

      <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/assets/css/style.css')}}">
    <!-- END Custom CSS-->
  </head>
  <body data-open="click" data-menu="vertical-menu" data-col="1-column" class="vertical-layout vertical-menu 1-column  blank-page blank-page">
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="app-content content container-fluid">
      <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body"><section class="flexbox-container">
    <div class="col-md-4 offset-md-4 col-xs-10 offset-xs-1  box-shadow-2 p-0">
        <div class="card border-grey border-lighten-3 m-0">
            <div class="card-header no-border">
                <div class="card-title text-xs-center">
                    <div class="p-1"><img src="{{asset('assets/system/images/logo/stack-logo-dark.png')}}" alt="branding logo"></div>
                </div>
                <h6 class="card-subtitle line-on-side text-muted text-xs-center font-small-3 pt-2"><span>{{__('Login To EGPAY System')}}</span></h6>
            </div>
            <div class="card-body collapse in">
                <div class="card-block">


                    <form autocomplete="off" class="form-horizontal form-simple" method="post" action="{{ url('system/login') }}" novalidate>
                    {{ csrf_field() }}
                        <fieldset  class="form-group{{ $errors->has('email') ? ' has-danger' : '' }} position-relative has-icon-left mb-0" style="margin-bottom: 7px !important;">
                           <div class="controls">

                            <input value="{{old('email')}}" autocomplete="off" type="email" class="form-control form-control-lg input-lg" id="user-name" placeholder="{{__('Enter Your Email')}}" data-validation-required-message="This field is required" name="email" required>
                            <div class="form-control-position">
                                <i class="ft-user"></i>
                            </div>
                           </div>
                        </fieldset>

                        @if($errors->has('email'))
                            <p class="text-xs-right">
                                <small class="danger text-muted">
                                    @foreach($errors->get('email') as $error)
                                        {{$error}} <br />
                                    @endforeach
                                </small>
                            </p>
                        @endif

                        <fieldset class="form-group {{$errors->has('password') ? ' has-danger' : ''}} position-relative has-icon-left" style="margin-bottom: 7px !important;">
                            <input autocomplete="off" type="password" name="password" class="form-control form-control-lg input-lg" id="user-password" placeholder="{{__('Enter Your Password')}}" required>
                            <div class="form-control-position">
                                <i class="fa fa-key"></i>
                            </div>
                        </fieldset>

                        @if($errors->has('password'))
                            <p class="text-xs-right">
                                <small class="danger text-muted">
                                    @foreach($errors->get('password') as $error)
                                        {{$error}} <br />
                                    @endforeach
                                </small>
                            </p>
                        @endif

                        <fieldset class="form-group row">
                            <div class="col-md-6 col-xs-12 text-xs-center text-md-left">
                                <fieldset>
                                    <input type="checkbox" id="remember-me" name="remember" class="chk-remember">
                                    <label for="remember-me"> Remember Me</label>
                                </fieldset>
                            </div>
                        </fieldset>
                        <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="ft-unlock"></i> Login</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

        </div>
      </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->

    <!-- BEGIN VENDOR JS-->
    <script src="{{asset('assets/system/vendors/js/vendors.min.js')}}" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{asset('assets/system/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/vendors/js/forms/validation/jqBootstrapValidation.js')}}" type="text/javascript"></script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN STACK JS-->
    <script src="{{asset('assets/system/js/core/app-menu.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/system/js/core/app.js')}}" type="text/javascript"></script>
    <!-- END STACK JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <script src="{{asset('assets/system/js/scripts/forms/form-login-register.js')}}" type="text/javascript"></script>
    <!-- END PAGE LEVEL JS-->
  </body>
</html>