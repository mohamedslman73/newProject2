<!DOCTYPE html>
<html lang="en" data-textdirection="ltr" class="loading">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="ajax-post" content="{{route('system.ajax.post')}}">

    @php
        preg_match('/([a-z]*)@/i', request()->route()->getActionName(), $matches);
    @endphp
    <title>{{ucfirst(request()->route()->getActionMethod())}} {{str_replace('Controller','',$matches[1])}} - {{setting('sitename_'.\DataLanguage::get())}} {{__('System')}}</title>


    <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->

    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css'.((app()->getLocale()=='ar')?'-rtl':null).'/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/fonts/feather/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/fonts/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/fonts/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/extensions/pace.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}">
    <!-- END VENDOR CSS-->
    <!-- BEGIN STACK CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css'.((app()->getLocale()=='ar')?'-rtl':null).'/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css'.((app()->getLocale()=='ar')?'-rtl':null).'/app.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css'.((app()->getLocale()=='ar')?'-rtl':null).'/colors.css')}}">
    <!-- END STACK CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css'.((app()->getLocale()=='ar')?'-rtl':null).'/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css'.((app()->getLocale()=='ar')?'-rtl':null).'/core/menu/menu-types/vertical-overlay-menu.css')}}">
    <!-- END Page Level CSS-->

    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/css/plugins/extensions/toastr.css')}}">

    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/assets/css/style.css')}}">
    <!-- END Custom CSS-->

    <style>
        #modal-iframe-url{
            width: 100%;
            border: none;
        }

        .color-red{
            color: red !important;
        }

    </style>

    @yield('header')


</head>

@if(Request::route()->getName() == 'system.system-ticket.index')
    <body data-open="click" data-menu="vertical-menu" data-col="content-left-sidebar" class="vertical-layout vertical-menu content-left-sidebar email-application fixed-navbar menu-expanded pace-done">
    @elseif(Request::route()->getName() == 'system.chat.index')
        <body data-open="click" data-menu="vertical-menu" data-col="content-left-sidebar" class="vertical-layout vertical-menu content-left-sidebar chat-application fixed-navbar menu-expanded pace-done">
        @else
            @if(request('without_navbar') == 'true')
                <body data-open="click" data-menu="vertical-menu" data-col="1-column" class="vertical-layout vertical-menu 1-column menu-expanded fixed-navbar">
                @else
                    <body data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar">
                    @endif
                    @endif
                    <!-- navbar-fixed-top-->
                    <nav class="header-navbar navbar navbar-with-menu navbar-fixed-top navbar-light navbar-border">
                        <div class="navbar-wrapper">
                            <div class="navbar-header">
                                <ul class="nav navbar-nav">
                                    <li class="nav-item mobile-menu hidden-md-up float-xs-left"><a href="#" class="nav-link nav-menu-main menu-toggle hidden-xs"><i class="ft-menu font-large-1"></i></a></li>
                                    <li class="nav-item"><a href="{{route('system.dashboard')}}" class="navbar-brand"><h2 class="brand-text">SEATTLE</h2></a></li>
                                    <li class="nav-item hidden-md-up float-xs-right">
                                        <a data-toggle="collapse" data-target="#navbar-mobile" class="nav-link open-navbar-container"><i class="fa fa-ellipsis-v"></i></a></li>
                                </ul>
                            </div>
                            <div class="navbar-container content container-fluid">
                                <div id="navbar-mobile" class="collapse navbar-toggleable-sm">
                                    <ul class="nav navbar-nav">
                                        <li class="nav-item hidden-sm-down"><a href="#" class="nav-link nav-menu-main menu-toggle hidden-xs"><i class="ft-menu"></i></a></li>
                                        <li class="nav-item hidden-sm-down"><a href="#" class="nav-link nav-link-expand"><i class="ficon ft-maximize"></i></a></li>

                                    </ul>
                                    <ul class="nav navbar-nav float-xs-right">

                                        <li class="dropdown dropdown-language nav-item">
                                            <a id="dropdown-flag" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle nav-link">
                                                {{__('Data Language')}}

                                                <span class="selected-language"></span>
                                            </a>
                                            <div aria-labelledby="dropdown-flag" class="dropdown-menu">
                                                <a href="{{route('system.dashboard',['data_lang'=>'en'])}}" class="dropdown-item"><i class="flag-icon flag-icon-gb"></i> {{__('English')}}</a>
                                                <a href="{{route('system.dashboard',['data_lang'=>'ar'])}}" class="dropdown-item"><i class="flag-icon flag-icon-eg"></i> {{__('Arabic')}}</a>
                                            </div>
                                        </li>

                                        <li class="dropdown dropdown-language nav-item">
                                            <a id="dropdown-flag" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle nav-link">
                                                @if(App::isLocale('en'))
                                                    <i class="flag-icon flag-icon-gb"></i>
                                                @else
                                                    <i class="flag-icon flag-icon-eg"></i>
                                                @endif
                                                <span class="selected-language"></span>
                                            </a>
                                            <div aria-labelledby="dropdown-flag" class="dropdown-menu">
                                                <a href="{{route('system.dashboard',['lang'=>'en'])}}" class="dropdown-item"><i class="flag-icon flag-icon-gb"></i> {{__('English')}}</a>
                                                <a href="{{route('system.dashboard',['lang'=>'ar'])}}" class="dropdown-item"><i class="flag-icon flag-icon-eg"></i> {{__('Arabic')}}</a>
                                            </div>
                                        </li>




                                        @if(staffCan('system.encrypt'))
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" title="{{__('Encrypt Data')}}" onclick="$('#encrypt-modal').modal('show');" class="nav-link">
                                                <i class="fa fa-key"></i>
                                            </a>
                                        </li>
                                        @endif


                                        <li class="dropdown dropdown-notification nav-item">
                                            <a href="#" data-toggle="dropdown" class="nav-link nav-link-label">
                                                <i class="ficon ft-bell"></i>
                                                @php
                                                    $unreadNotifications = Auth::user()->unreadNotifications()->count();
                                                    //if($unreadNotifications > 5){
                                                    //    $notifications = Auth::user()->notifications()->limit($unreadNotifications)->get();
                                                    //}else{
                                                        $notifications = Auth::user()->notifications()->limit(10)->get();
                                                    //}
                                                @endphp
                                                @if($unreadNotifications)
                                                    <span class="tag tag-pill tag-default tag-danger tag-default tag-up">{{$unreadNotifications}}</span>
                                                @endif
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                                <li class="dropdown-menu-header">
                                                    <h6 class="dropdown-header m-0">
                                                        <span class="grey darken-2">{{__('Notifications')}}</span>
                                                        <span class="notification-tag tag tag-default tag-danger float-xs-right m-0">{{$unreadNotifications}} {{__('New')}}</span>
                                                    </h6>
                                                </li>
                                                <li class="list-group scrollable-container">
                                                    @foreach($notifications as $key => $value)
                                                        <a {!! iif($value->read_at == null,'style="background-color: #ffebcd;"') !!} href="{!! iif(isset($value->data['url']) && !empty($value->data['url']) ,route('system.notifications.url',$value->id),'javascript:void(0);') !!}" class="list-group-item">
                                                            <div class="media">
                                                                <div class="media-left valign-middle"><i class="ft-plus-square icon-bg-circle bg-cyan"></i></div>
                                                                <div class="media-body">
                                                                    <h6 class="media-heading">{{$value->data['title']}}</h6>
                                                                    <p class="notification-text font-small-3 text-muted">{{$value->data['description']}}</p><small>
                                                                        <time datetime="{{str_replace(' ','T',$value->created_at)}}" class="media-meta text-muted">{{$value->created_at->diffForHumans()}}</time></small>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </li>
                                                <li class="dropdown-menu-footer"><a href="{{route('system.notifications.index')}}" class="dropdown-item text-muted text-xs-center">{{__('Read all notifications')}}</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown dropdown-user nav-item">
                                            <a href="#" data-toggle="dropdown" class="dropdown-toggle nav-link dropdown-user-link">
                                                @if(Auth::user()->avatar)
                                                    <span class="avatar avatar-online">
                                <img src="{{asset('storage/'.imageResize(Auth::user()->avatar,30,30))}}" alt="{{Auth::user()->firstname}} {{Auth::user()->lastname}}">
                                <i></i>
                            </span>
                                                @endif
                                                <span style="margin-top: 8px;" class="user-name">{{Auth::user()->firstname}} {{Auth::user()->lastname}}</span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">

                                                <a href="{{route('system.change-password')}}" class="dropdown-item"><i class="fa fa-unlock-alt"></i> {{__('Change Password')}}</a>
                                                <a href="#" class="dropdown-item"><i class="ft-mail"></i>{{__('My Inbox')}}</a>
                                                <a href="#" class="dropdown-item"><i class="ft-comment-square"></i> {{__('Chats')}}</a>
                                                <div class="dropdown-divider"></div>
                                                <a href="{{route('system.logout',Auth::user()->id)}}" class="dropdown-item"><i class="ft-power"></i> Logout</a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </nav>
                    <!-- ////////////////////////////////////////////////////////////////////////////-->
                    @if(request('without_navbar') != 'true')
                        <div data-scroll-to-active="true" class="main-menu menu-fixed menu-light menu-accordion">
                            <div class="main-menu-content">
                                <ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
                                    @include('system._menus')
                                </ul>
                            </div>
                        </div>
                    @endif
                    @yield('content')
                    <div class="modal fade text-xs-left" id="modal-iframe" role="dialog" aria-labelledby="myModalLabe" aria-hidden="true">
                        <div class="modal-dialog" id="modal-iframe-width" role="document">
                            <div class="modal-content">

                                <div class="modal-body">
                                    <div class="card-body">
                                        <div class="card-block">
                                            <div class="row" style="text-align: center;">
                                                <img id="modal-iframe-image" src="{{asset('assets/system/loading.gif')}}">
                                                <iframe id="modal-iframe-url" style="display: none;" src=""></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
















                    <!-- ////////////////////////////////////////////////////////////////////////////-->
                    <footer class="footer footer-light navbar-border">
                        <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
                            <span class="float-md-left d-xs-block d-md-inline-block">Copyright  &copy; {{date('Y')}} <a href="https://www.egpay.com" target="_blank" class="text-bold-800 grey darken-2">EGPAY Software Company </a>, All rights reserved. </span>
                            {{--<span class="float-md-right d-xs-block d-md-inline-block">Build with <i style="color: red;" class="fa fa-heart text-city"></i> In Egypt</span>--}}
                        </p>
                    </footer>

                    <!-- BEGIN VENDOR JS-->
                    <script src="{{asset('assets/system/vendors/js/vendors.js')}}" type="text/javascript"></script>
                    <script src="{{asset('assets/system/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
                    <!-- BEGIN VENDOR JS-->
                    <!-- BEGIN PAGE VENDOR JS-->
                    <script src="{{asset('assets/system/vendors/js/tables/jquery.dataTables.min.js')}}" type="text/javascript"></script>
                    <script src="{{asset('assets/system/vendors/js/tables/datatable/dataTables.bootstrap4.min.js')}}" type="text/javascript"></script>
                    <!-- END PAGE VENDOR JS-->

                    <!-- BEGIN STACK JS-->
                    <script src="{{asset('assets/system/js/core/app-menu.js')}}" type="text/javascript"></script>
                    <script src="{{asset('assets/system/js/core/app.js')}}" type="text/javascript"></script>
                    <!-- END STACK JS-->
                    <!-- BEGIN PAGE LEVEL JS-->
                    <script src="{{asset('assets/system/js/scripts/tables/datatables-extensions/datatables-sources.js')}}" type="text/javascript"></script>
                    <!-- END PAGE LEVEL JS-->
                    <script src="//cdn.ckeditor.com/4.7.3/full/ckeditor.js"></script>
                    <script src="{{asset('assets/system/js/jquery.form.js')}}" type="text/javascript"></script>


                    <script src="{{asset('assets/system/node.js?time='.time())}}" type="text/javascript"></script>
                    <!-- More Footer -->
                    @yield('footer')
                    <!-- More Footer -->

<!-- Begin Inspectlet Embed Code -->
<script type="text/javascript" id="inspectletjs">
(function() {
window.__insp = window.__insp || [];
__insp.push(['wid', 1509986290]);
var ldinsp = function(){ if(typeof window.__inspld != "undefined") return; window.__inspld = 1; var insp = document.createElement('script'); insp.type = 'text/javascript'; insp.async = true; insp.id = "inspsync"; insp.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cdn.inspectlet.com/inspectlet.js?wid=1509986290&r=' + Math.floor(new Date().getTime()/3600000); var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(insp, x); };
setTimeout(ldinsp, 0);
})();
</script>
<!-- End Inspectlet Embed Code -->


                    </body>
</html>