@extends('system.layouts')
<!-- Modal -->



@section('content')

    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">

                <div class="content-header-left col-md-4 col-xs-12">
                    <h4>
                        {{$pageTitle}}
                    </h4>
                </div>
                <div class="content-header-right col-md-8 col-xs-12 mb-2">
                    <div class=" content-header-title mb-0" style="float: right;">
                        @include('system.breadcrumb')
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Server-side processing -->
                <section id="server-processing">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{$pageTitle}}</h4>
                                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block card-dashboard" id="notification-data">
                                        Loading ...
                                    </div>

                                    <div id="loading-notification"></div>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--/ Javascript sourced data -->
            </div>
        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->

@endsection

@section('footer')

    <script type="text/javascript">
        //notification-data

        $(document).ready(function(){
            getNotification('{{route("system.notifications.index")}}?page=1',true);
        });

        function getNotification($url,isFirst = false){
            $('#loading-notification-text').removeAttr('onclick').html('<h3>{{__('Loading...')}}</h3>');
            $.getJSON($url,function($data){
                if($data.next){
                    $('#loading-notification').html('<a href="javascript:void(0);" id="loading-notification-text" onclick="getNotification(\''+$data.next+'\')" class="dropdown-item text-muted text-xs-center"><h3>{{__('Load More...')}}</h3></a>');
                }else{
                    $('#loading-notification').remove();
                }
                if(!empty($data)){
                    if(isFirst){
                        $('#notification-data').html($data.content);
                    }else{
                        $('#notification-data').append($data.content);
                    }
                }
            });
        }
    </script>

@endsection