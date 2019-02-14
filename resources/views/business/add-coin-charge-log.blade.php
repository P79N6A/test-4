@extends('business.layouts.frame-parent')
@section('page-title','新增添币记录')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-6">
                            <form action="{{ route('business.add-coin-charge-log') }}" method="post">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label>门店</label>
                                    <select class="form-control" name="store_id" id="store_id">
                                        <option value="0">请选择门店</option>
                                        @if(!empty($stores))
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>机台</label>
                                    <select class="form-control" name="machine_id" id="machine_id">
                                        <option value="0">请选择机台</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>添加币数</label>
                                    <input type="number" class="form-control" name="coin" min="1" placeholder="大于0">
                                </div>
                                <div class="hr-line-dashed"></div>
                                <button class="btn btn-sm btn-primary">提交</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {

            var $storeId = $('#store_id');

            if (parseInt($storeId.val()) === 0) {
                $('#machine_id').attr('disabled', true);
            } else {
                $('#machine_id').attr('disabled', false);
            }

            $storeId.change(function () {
                if (parseInt($storeId.val()) === 0) {
                    $('#machine_id').attr('disabled', true);
                } else {
                    $('#machine_id').attr('disabled', false);
                }

                $.ajax({
                    type: 'get',
                    url: "{{ route('business.add-coin-charge-log') }}",
                    data: {
                        action: 'getMachines',
                        storeId: $(this).val()
                    },
                    success: function (data) {
                        var $machineId = $('#machine_id');
                        $machineId.empty();
                        if (data.data !== undefined && data.data.length > 0) {
                            $machineId.append('<option value="0">请选择机台</option>');
                            $.each(data.data, function (index, value) {
                                var machine = '<option value="' + value.id + '">' + value.name + '</option>';
                                $machineId.append(machine);
                            });
                        } else {
                            $machineId.append('<option value="0">该门店无可用机台</option>');
                        }
                    }
                });
            });

            $('form').submit(function (e) {
                e.preventDefault();
                youyibao.httpSend($(this), 'post', 1);
            });
        });
    </script>
@endsection
