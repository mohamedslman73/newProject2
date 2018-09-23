<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>

<div>
    <div class="row">

        <div class="col-md-4">
            <h3>Categories</h3>
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                </tr>
                </thead>
                <tbody>

                @foreach($getNewCategories as $key => $value)
                    <tr id="new-category-{{$value['id']}}">
                        <td>{{$value['id']}}</td>
                        <td><a href="javascript:openNewProvider({{$value['id']}})">{{$value['name']}}</a></td>
                    </tr>
                @endforeach

                @foreach($getOldCategories as $key => $value)
                    <tr id="new-category-{{$value['id']}}">
                        <td>{{$value['id']}}</td>
                        <td><a href="javascript:openNewProvider({{$value['id']}})">{{$value['name_en']}}</a></td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>


        <div class="col-md-4" style="display: none;" id="providers-div">
            <h3>Providers</h3>

            @foreach($getNewProviders as $key => $value)
                <table class="table getNewProviders" id="new-provider-{{$key}}">
                    <thead>
                    <tr>
                        <th>Category ID</th>
                        <th>ID</th>
                        <th>Name</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($value as $newKey => $newValue)
                        <tr style="background: red;color: #FFF;">
                            <td>{{$newValue['providerGroupId']}}</td>
                            <td>{{$newValue['id']}}</td>
                            <td><a href="javascript:openNewServices({{$newValue['id']}})">{{$newValue['name']}}</a></td>
                        </tr>
                    @endforeach

                        @if(isset($getOldProviders[$key]))
                            @foreach($getOldProviders[$key] as $newKey2 => $newValue2)
                                <tr style="background: red;color: #FFF;">
                                    <td>{{$newValue2['payment_service_provider_category_id']}}</td>
                                    <td>{{$newValue2['id']}}</td>
                                    <td><a href="javascript:openNewServices({{$newValue2['id']}})">{{$newValue2['name_en']}}</a></td>
                                </tr>
                            @endforeach
                        @endif

                    </tbody>
                </table>
            @endforeach

            @foreach($getOldProviders as $key => $value)
                @if(in_array($key,$getNewProvidersKeys))
                    @php
                        continue;
                    @endphp
                @endif

                <table class="table getNewProviders" id="new-provider-{{$key}}">
                    <thead>
                    <tr>
                        <th>Category ID</th>
                        <th>ID</th>
                        <th>Name</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($value as $newKey => $newValue)
                            <tr>
                                <td>{{$newValue['payment_service_provider_category_id']}}</td>
                                <td>{{$newValue['id']}}</td>
                                <td><a href="javascript:openNewServices({{$newValue['id']}})">{{$newValue['name_en']}}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach

        </div>

        <div class="col-md-4" style="display: none;" id="services-div">
            <h3>Service</h3>
            @foreach($getNewServices as $key => $value)
                <table class="table getNewServices" id="new-services-{{$key}}">
                    <thead>
                    <tr>
                        <th>Provider ID</th>
                        <th>ID</th>
                        <th>Name</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($value as $newKey => $newValue)
                        <tr style="background: red;color: #FFF;">
                            <td>{{$newValue['providerId']}}</td>
                            <td>{{$newValue['accountId']}}</td>
                            <td>{{$newValue['name']}}</td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>


    </div>
</div>





<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>
<script>
    function openNewProvider($id){
        $('#providers-div').show();
        $('#services-div').hide();

        $('.getNewProviders').hide();
        $('#new-provider-'+$id).show();
    }

    function openNewServices($id){
        $('#services-div').show();
        $('.getNewServices').hide();
        $('#new-services-'+$id).show();
    }

    $(document).ready(function(){
        // Providers
        $('.getNewProviders').each(function($key,$value){
            $('#new-category-'+($(this).attr('id')).replace('new-provider-','')).css('background','red').css('color','#FFF');
        });


        // Services
        $('.getNewServices').each(function($key,$value){
            $('#new-provider-'+($(this).attr('id')).replace('new-services-','')).css('background','red').css('color','#FFF');
        });



    });

</script>