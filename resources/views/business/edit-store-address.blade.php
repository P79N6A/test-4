@extends('business.layouts.frame-parent')
@section('page-title','修改地址信息')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-8 col-md-6">
                            <form action="{{ route('business.edit-store-address') }}" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $store->id }}">
                                <div class="form-group">
                                    <label>地区</label>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <select class="form-control" id="province">
                                                <option value="0">请选择省份</option>
                                                @if(!empty($provinces))
                                                    @foreach($provinces as $province)
                                                        <option value="{{ $province->id }}"
                                                                @if($province->id == $store->province_id) selected @endif>
                                                            {{ $province->province }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <select class="form-control" id="city">
                                                <option value="0">请选择城市</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <select class="form-control" id="district" name="region_id">
                                                <option value="0">请选择县区</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>详细地址</label>
                                    <div class="input-group">
                                        <input type="text" name="address" value="{{ $store->address }}"
                                               class="form-control" id="address"
                                               placeholder="请输入详细地址">
                                        <span class="input-group-btn">
                                        <button type="button" id="reposition" class="btn btn-primary">定位</button>
                                    </span>
                                    </div>
                                </div>
                                <input type="hidden" id="longitude" name="longitude">
                                <input type="hidden" id="latitude" name="latitude">
                                <div>
                                    <div id="map-container" style="height:400px;"></div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <button type="submit" class="btn btn-sm btn-primary">保存</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript"
            src="https://webapi.amap.com/maps?v=1.3&key=546ebd00cfd608f8ab470d79aa6f6982"></script>
    <script>
        $(function () {
            var $province = $('#province');
            var $city = $('#city');
            var $district = $('#district');
            var $address = $('#address');
            var $reposition = $('#reposition');
            var $longitude = $('#longitude');
            var $latitude = $('#latitude');

            var map = new AMap.Map("map-container", {
                resizeEnable: true,
                //center: [116.397428, 39.90923],
                zoom: 8
            });
            var marker = new AMap.Marker({
                position: map.getCenter(),
                draggable: true,
                cursor: 'move',
                raiseOnDrag: true
            });
            var MGeocoder;
            //加载地理编码插件
            AMap.service(["AMap.Geocoder"], function () {
                MGeocoder = new AMap.Geocoder();
            });
            marker.setMap(map);
            AMap.event.addListener(marker, "mouseup", function () {
                var p = marker.getPosition();
                $longitude.val(p.lng);
                $latitude.val(p.lat);
            });
            $reposition.on("click", function () {
                var address = ($province.find('option:selected').html() || '')
                    + ($city.find('option:selected').html() || '')
                    + ($district.find('option:selected').html() || '')
                    + $address.val();

                MGeocoder.getLocation(address, function (status, result) {
                    if (status === 'complete' && result.info === 'OK') {
                        //console.log(result);
                        //var p = new AMap.LngLat(result.geocodes[0].location.lng, result.geocodes[0].location.lat);

                        //map.setCenter(p);
                        map.setZoomAndCenter(16, result.geocodes[0].location);
                        marker.setPosition(result.geocodes[0].location);
                        $longitude.val(result.geocodes[0].location.lng);
                        $latitude.val(result.geocodes[0].location.lat);
                    }
                });
            });

            if ($longitude.val() != "" || $latitude.val() != "") {
                var p = new AMap.LngLat($longitude.val(), $latitude.val());
                map.setCenter(p);
                map.setZoomAndCenter(16, p);
                marker.setPosition(p);
            }


            $province.change(function () {
                youyibao.getCities($(this).val(), function (data) {
                    var list = '<option value="0">请选择城市</option>';
                    var cityId = parseInt("{{ $store->city_id }}") > 0 ? parseInt("{{ $store->city_id }}") : 0;
                    var selects = [];
                    $.each(data.data, function (index, value) {
                        if (parseInt(value.id) === cityId) {
                            selects[index] = 'selected';
                        } else {
                            selects[index] = '';
                        }
                        list += '<option value="' + value.id + '"' + selects[index] + '>' + value.city + '</option>';
                    });
                    $city.empty().append($(list));
                    $city.trigger('change');
                });
                $reposition.trigger('click');
            });

            $city.change(function () {
                youyibao.getBlocks($(this).val(), function (data) {
                    var blocks = '<option value="0">请选择县区</option>';
                    var distId = parseInt("{{ $store->district_id }}") > 0 ? parseInt("{{ $store->district_id }}") : 0;
                    var distSelects = [];
                    $.each(data.data, function (index, value) {
                        if (parseInt(value.id) === distId) {
                            distSelects[index] = 'selected';
                        } else {
                            distSelects[index] = '';
                        }
                        blocks += '<option value="' + value.id + '"' + distSelects[index] + '>' + value.county + '</option>';
                    });
                    $district.empty().append($(blocks));
                    $reposition.trigger('click');
                });
            });

            $district.change(function () {
                $reposition.trigger('click');
            });

            $address.on('input propertychange', function () {
                $('#reposition').trigger('click');
            });

            $province.trigger('change');

            $('form').submit(function (e) {
                e.preventDefault();
                youyibao.httpSend($(this), 'post', 1);
            });

        });
    </script>
@endsection