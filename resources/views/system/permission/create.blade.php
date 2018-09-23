@extends('system.layouts')

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/system/vendors/css/forms/selects/select2.min.css')}}">
@endsection

@section('content')

    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-xs-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-xs-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">DataTables</a>
                                </li>
                                <li class="breadcrumb-item active">Sources Datatable
                                </li>
                            </ol>
                        </div>
                    </div>
                    <h3 class="content-header-title mb-0">Sources Datatable</h3>
                </div>
                <div class="content-header-right col-md-6 col-xs-12">
                    <div role="group" aria-label="Button group with nested dropdown" class="btn-group float-md-right">
                        <div role="group" class="btn-group">
                            <button id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-outline-primary dropdown-toggle dropdown-menu-right"><i class="ft-cog icon-left"></i> Settings</button>
                            <div aria-labelledby="btnGroupDrop1" class="dropdown-menu"><a href="card-bootstrap.html" class="dropdown-item">Bootstrap Cards</a><a href="component-buttons-extended.html" class="dropdown-item">Buttons Extended</a></div>
                        </div><a href="calendars-clndr.html" class="btn btn-outline-primary"><i class="ft-mail"></i></a><a href="timeline-center.html" class="btn btn-outline-primary"><i class="ft-pie-chart"></i></a>
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
                                    <h4 class="card-title">Server-side processing</h4>
                                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                            <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                            <li><a data-action="close"><i class="ft-x"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body collapse in">
                                    @if(Session::has('status'))
                                        <div class="alert alert-{{Session::get('status')}}">
                                            {{ Session::get('msg') }}
                                        </div>
                                    @endif
                                    <div class="card-block card-dashboard">
                                        {!! Form::open(['route' => isset($area->id) ? ['area.update',$area->id]:'area.store','files'=>true, 'method' => isset($area->id) ?  'PATCH' : 'POST']) !!}

                                        <div class="form-group col-sm-12{!! formError($errors,'area_type_id',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('area_type_id', __('Area type').':') !!}
                                                {!! Form::select('area_type_id',$AreaTypes,isset($area->id) ? $area->area_type_id:old('area_type_id'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'area_type_id') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'name_en',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('name_en', __('English Name').':') !!}
                                                {!! Form::text('name_en',isset($area->id) ? $area->name_en:old('name_en'),['class'=>'form-control']) !!}
                                            </div>
                                            {!! formError($errors,'name_en') !!}
                                        </div>

                                        <div class="form-group col-sm-6{!! formError($errors,'name_ar',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('name_ar', __('Arabic Name').':') !!}
                                                {!! Form::text('name_ar',isset($area->id) ? $area->name_ar:old('name_ar'),['class'=>'form-control ar']) !!}
                                            </div>
                                            {!! formError($errors,'name_ar') !!}
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2>{{__('Determine Location')}}</h2>
                                                </div>
                                                <div class="card-block card-dashboard">
                                                    <input id="pac-input" class="controls form-control" type="text" placeholder="{{__('Search Box')}}">
                                                    <div id="map-events" class="height-400"></div>
                                                    <br>
                                                    <div class="form-group col-sm-6{!! formError($errors,'latitude',true) !!}">
                                                        <div class="controls">
                                                            {!! Form::label('latitude', __('Latitude').':') !!}
                                                            {!! Form::text('latitude',isset($area->id) ? $area->latitude:old('latitude'),['class'=>'form-control']) !!}
                                                        </div>
                                                        {!! formError($errors,'latitude') !!}
                                                    </div>

                                                    <div class="form-group col-sm-6{!! formError($errors,'longitude',true) !!}">
                                                        <div class="controls">
                                                            {!! Form::label('longitude', __('Longitude').':') !!}
                                                            {!! Form::text('longitude',isset($area->id) ? $area->longitude:old('longitude'),['class'=>'form-control']) !!}
                                                        </div>
                                                        {!! formError($errors,'longitude') !!}
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-sm-12{!! formError($errors,'parent_id',true) !!}">
                                            <div class="controls">
                                                {!! Form::label('parent_id', __('Sub-Area Of').':') !!}
                                                @if(isset($area->parent_id))
                                                    {!! Form::select('parent_id',[$area->parent()->first()->id => $area->parent()->first()->name_ar.' - '.$area->parent()->first()->name_en],isset($area->id) ? $area->parent_id:old('parent_id'),['class'=>'select2 form-control']) !!}
                                                @else
                                                    {!! Form::select('parent_id',[],old('parent_id'),['class'=>'select2 form-control']) !!}
                                                @endif
                                            </div>
                                            {!! formError($errors,'parent_id') !!}
                                        </div>

                                        {!! Form::submit(__('Save'),['class'=>'btn btn-success pull-right']) !!}

                                        {!! Form::close() !!}

                                    </div>
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
    <script src="{{asset('assets/system')}}/vendors/js/forms/select/select2.full.min.js" type="text/javascript"></script>
    <script src="{{asset('assets/system')}}/js/scripts/select2/select2.custom.js" type="text/javascript"></script>
    <script>
        $(function(){
            CustomSelect2('#parent_id','{{route('ajax.findarea')}}');
        });
    </script>

    <script src="//maps.googleapis.com/maps/api/js?key={{env('gmap_key')}}&libraries=places&callback=initAutocomplete" type="text/javascript" async defer></script>
    <script src="{{asset('assets/system')}}/vendors/js/charts/gmaps.min.js" type="text/javascript"></script>
    <script>
        markers = [];
        var map = '';
        function initAutocomplete() {
            map = new google.maps.Map(document.getElementById('map-events'), {
                @if(isset($area->id))
                    center: {lat: {{$area->latitude}}, lng: {{$area->longitude}}},
                    zoom: 16,
                @else
                    center: {lat: 27.02194154036109, lng: 31.148436963558197},
                    zoom: 6,
                @endif


                mapTypeId: 'roadmap'
            });

            // Create the search box and link it to the UI element.
            var input = document.getElementById('pac-input');
            var searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            // Bias the SearchBox results towards current map's viewport.
            map.addListener('bounds_changed', function() {
                searchBox.setBounds(map.getBounds());
            });

            map.addListener('click', function(e) {
                placeMarker(e.latLng,map);
            });

            @if(isset($area->id))
                var marker = new google.maps.Marker({
                    position: {lat: {{$area->latitude}}, lng: {{$area->longitude}}},
                    map: map
                });
                markers.push(marker);
            @endif

            searchBox.addListener('places_changed', function() {
                var places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                // Clear out the old markers.
                markers.forEach(function(marker) {
                    marker.setMap(null);
                });


                // For each place, get the icon, name and location.
                var bounds = new google.maps.LatLngBounds();
                places.forEach(function(place) {
                    if (!place.geometry) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
        }

        function placeMarker(location,map) {
            clearOverlays();
            var marker = new google.maps.Marker({
                position: location,
                map: map,
            });
            var lng = location.lng();
            $('#latitude').val(location.lat());
            $('#longitude').val(location.lng());
            //console.log(lat+' And Long is: '+lng);
            markers.push(marker);
            //map.setCenter(location);
        }

        function clearOverlays() {
            for (var i = 0; i < markers.length; i++ ) {
                markers[i].setMap(null);
            }
            markers.length = 0;
        }


        </script>
@endsection