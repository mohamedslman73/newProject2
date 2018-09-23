@extends('system.layouts')

@section('content')
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-xs-12">
                    <h4>
                        {{$pageTitle}}
                    </h4>
                </div>
                <div class="content-header-right col-md-8 col-xs-12">
                    <div class=" content-header-title mb-0" style="float: right;">
                        @include('system.breadcrumb')
                    </div>
                </div>
            </div>
            <div class="content-body"><!-- Spacing -->
                <div class="row">



                    <div class="col-md-12">

                            <div class="card-body collapse in">
                                <div class="card-block">
                                    <div class="table-responsive">
                                    @php
                                    echo $result;
                                    @endphp
                                    </div>
                                </div>
                            </div>

                    </div>



                </div>
            </div>
        </div>
    </div>


@endsection

