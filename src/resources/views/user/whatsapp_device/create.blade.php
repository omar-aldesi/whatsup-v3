@extends('user.layouts.app')
@section('panel')
    <section>
        <div class="row g-4">
            <div class="col-xl-4">
                <form action="{{route('user.gateway.whatsapp.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                {{ translate('Whatsapp Device Add')}}
                            </h4>
                        </div>

                        <div class="card-body">
                            <div class="mb-4">
                                <div class="mb-3">
                                    <label for="name">{{ translate('Session/Device Name')}} <span  class="text-danger">*</span>  </label>
                                    <input type="text" class="mt-2 form-control @error('name') is-invalid @enderror " name="name" id="name" value="{{old('name')}}" placeholder="{{ translate('Put Session Name (Any)')}}">
                                    @error('name')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="min_delay">{{ translate('Message Minimum Delay Time')}} <span class="text-danger" >*</span></label>
                                    <input type="number" class="mt-2 form-control @error('min_delay') is-invalid @enderror " name="min_delay" id="min_delay" value="{{old('min_delay')}}" placeholder="{{ translate('Message minimum delay time in second')}}">
                                    @error('min_delay')min_delay
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="max_delay">{{ translate('Message Maximum Delay Time')}}
                                        <span class="text-danger" >*</span>
                                    </label>
                                    <input type="number" class="mt-2 form-control @error('max_delay') is-invalid @enderror " name="max_delay" id="max_delay" value="{{old('max_delay')}}" placeholder="{{ translate('Message maximum delay time in second')}}">
                                    @error('max_delay')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="i-btn primary--btn btn--md">{{ translate('Submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {{ translate('WhatsApp Device List')}}
                        </h4>
                    </div>

                    <div class="card-body px-0">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>{{ translate('Session Name')}}</th>
                                        <th>{{ translate('WhatsApp Number')}}</th>
                                        <th>{{ translate('Minimum Delay Time')}}</th>
                                        <th>{{ translate('Maximum Delay Time')}}</th>
                                        <th>{{ translate('Status')}}</th>
                                        <th>{{ translate('Action')}}</th>
                                    </tr>
                                </thead>
                                @forelse ($whatsapps as $item)
                                    <tbody>
                                        <tr>
                                            <td data-label="{{ translate('Session Name')}}">{{$item->name}}</td>
                                            <td data-label="{{ translate('WhatsApp Number')}}">{{$item->number ? $item->number : 'N/A'}}</td>
                                            <td data-label="{{ translate('Minimum Delay Time')}}">{{convertTime($item->min_delay)}}</td>
                                            <td data-label="{{ translate('Maximum Delay Time')}}">{{convertTime($item->max_delay)}}</td>
                                            <td data-label="{{ translate('Status')}}">
                                                <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                                    <span class="badge badge--{{$item->status == 'initiate' ? 'primary' : ($item->status == 'connected' ? 'success' : 'danger')}}">
                                                        {{ucwords($item->status)}}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                                    <a title="Edit" href="javascript:void(0)" class="i-btn primary--btn btn--sm whatsappEdit"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#whatsappEdit"
                                                        data-id="{{$item->id}}"
                                                        data-name="{{$item->name}}"
                                                        data-min_delay="{{$item->min_delay}}"
                                                        data-max_delay="{{$item->max_delay}}">
                                                        <i class="las la-pen"></i>{{translate('Edit')}}
                                                    </a>
                                                    @if($item->status == 'initiate')
                                                    <a title="Scan" href="javascript:void(0)" id="textChange" class="i-btn success--btn btn--sm qrQuote textChange{{$item->id}}" value="{{$item->id}}"><i class="fas fa-qrcode"></i>{{translate('Scan')}}</a>
                                                    @elseif($item->status == 'connected')
                                                        <a title="Disconnect" href="javascript:void(0)" onclick="return deviceStatusUpdate('{{$item->id}}','disconnected','deviceDisconnection','Disconnecting','Connect')" class="i-btn warning--btn btn--sm deviceDisconnection{{$item->id}}" value="{{$item->id}}"><i class="fas fa-plug"></i>{{translate('Disconnect')}}</a>
                                                    @else
                                                        <a title="Scan" href="javascript:void(0)" id="textChange" class="i-btn success--btn btn--sm qrQuote textChange{{$item->id}}" value="{{$item->id}}"><i class="fas fa-qrcode"></i>{{translate('Scan')}}</a>
                                                    @endif

                                                    <a title="Delete" href="" class="i-btn danger--btn btn--sm whatsappDelete" value="{{$item->id}}"><i class="fas fa-trash-alt"></i>&nbsp {{ translate('Trash')}}</a>

                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                @empty
                                    <tbody>
                                        <tr>
                                            <td colspan="50"><span class="text-danger">{{ translate('No data Available')}}</span></td>
                                        </tr>
                                    </tbody>
                                @endforelse
                            </table>
                        </div>
                        <div class="m-3">
                            {{$whatsapps->appends(request()->all())->onEachSide(1)->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Whats app Edit modal --}}
    <div class="modal fade" id="whatsappEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Update Whatsapp Gateway')}}</h5>
                     <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
                </div>
                <form action="{{route('user.gateway.whatsapp.update')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="row gx-4 gy-3">
                            <div class="col-lg-12">
                                <label for="min_delay" class="form-label">{{ translate('Minimum Delay Time')}} <sup class="text--danger">*</sup></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="min_delay" name="min_delay" placeholder="{{ translate('Enter Minimum Delay Time')}}">
                                </div>
                                <label for="max_delay" class="mt-3 form-label">{{ translate('Maximum Delay Time')}} <sup class="text--danger">*</sup></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="max_delay" name="max_delay" placeholder="{{ translate('Enter Maximum Delay Time')}}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                            <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn primary--btn btn--md">{{ translate('Submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- whatsapp delete modal --}}
    <div class="modal fade" id="whatsappDelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('user.gateway.whatsapp.delete')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <div class="modal_body2">
                        <div class="modal_icon2">
                            <i class="las la-trash"></i>
                        </div>
                        <div class="modal_text2 mt-3">
                            <h6>{{ translate('Are you sure to delete')}}</h6>
                        </div>
                    </div>
                    <div class="modal_button2 modal-footer">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn danger--btn btn--md">{{ translate('Delete')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- whatsapp qrQoute scan --}}
    <div class="modal fade" id="qrQuoteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ translate('Scan Device')}}</h5>
                    <button type="button" class="btn-close" aria-label="Close" onclick="return deviceStatusUpdate('','initiate','','','')"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="scan_id" id="scan_id" value="">
                    <div>
                        <h4 class="py-3">{{ translate('To use WhatsApp')}}</h4>
                        <ul>
                            <li>{{ translate('1.Open WhatsApp on your phone')}}</li>
                            <li>{{ translate('2.Tap Menu  or Settings  and select Linked Devices')}}</li>
                            <li>{{ translate('3.Point your phone to this screen to capture the code')}}</li>
                        </ul>
                    </div>
                    <div class="text-center">
                        <img id="qrcode" class="w-50" src="" alt="">
                    </div>
                    <div class="text-center">
                        <small><a href="https://faq.whatsapp.com/1317564962315842/?cms_platform=web&lang=en" target="_blank"><i class="fas fa-info"></i>{{translate('More Guide')}}</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-push')
<script>
	(function($){
		"use strict";
		$('.whatsappEdit').on('click', function(){
            const modal = $('#whatsappEdit');
            modal.find('input[name=id]').val($(this).data('id'));
			modal.find('input[name=name]').val($(this).data('name'));
			modal.find('input[name=min_delay]').val($(this).data('min_delay'));
			modal.find('input[name=max_delay]').val($(this).data('max_delay'));
			modal.find('select[name=status]').val($(this).data('status'));
			modal.modal('show');
		});

        $(document).on('click', '.whatsappDelete', function(e){
            e.preventDefault()
            var id = $(this).attr('value')
            var modal = $('#whatsappDelete');
            modal.find('input[name=id]').val(id);
            modal.modal('show');
        })

        // qrQuote scan
        $(document).on('click', '.qrQuote', function(e){
            e.preventDefault()
            var id = $(this).attr('value')
            var url = "{{route('user.gateway.whatsapp.qrcode')}}"

            $.ajax({
                headers: {'X-CSRF-TOKEN': "{{csrf_token()}}"},
                url:url,
                data: {id:id},
                dataType: 'json',
                method: 'post',
                beforeSend: function(){
                    $('.textChange'+id).html(`<i class="fas fa-refresh"></i>&nbsp{{ translate('Loading...')}}`);
                },
                success: function(res){
                    $("#scan_id").val(res.response.id);
                    if (res.data.message && res.data.qr && res.data.status===200) {
                        $('#qrcode').attr('src', res.data.qr);
                        notify('success', res.data.message);
                        $('#qrQuoteModal').modal('show');
                        sleep(10000).then(() => {
                            wapSession(res.response.id);
                        });
                    } else if (res.data.message) {
                        notify('error', res.data.message);
                    }

                },
                complete: function(){
                    $('.textChange'+id).html(`<i class="fas fa-qrcode"></i>&nbsp {{ translate('Scan')}}`);
                },
                error: function(e) {
                    notify('error','Something went wrong')
                }
            })

        })


        function wapSession(id) {
            $.ajax({
                headers: {'X-CSRF-TOKEN': "{{csrf_token()}}"},
                url:"{{route('user.gateway.whatsapp.device.status')}}",
                data: {id:id},
                dataType: 'json',
                method: 'post',
                success: function(res){
                    $("#scan_id").val(res.response.id);
                    if (res.data.qr!=='')
                    {
                        $('#qrcode').attr('src',res.data.qr);
                    }

                    if (res.data.status===301)
                    {
                        sleep(2500).then(() => {
                            $('#qrQuoteModal').modal('hide');
                            location.reload();
                        });
                    }else{
                        sleep(10000).then(() => {
                            wapSession(res.response.id);
                        });
                    }
                }
            })
        }
    })(jQuery);
    function deviceStatusUpdate(id,status,className='',beforeSend='',afterSend='') {
        if (id=='') {
            id = $("#scan_id").val();
        }
        $('#qrQuoteModal').modal('hide');
        $.ajax({
            headers: {'X-CSRF-TOKEN': "{{csrf_token()}}"},
            url:"{{route('user.gateway.whatsapp.status-update')}}",
            data: {id:id,status:status},
            dataType: 'json',
            method: 'post',
            beforeSend: function(){
                if (beforeSend!='') {
                    $('.'+className+id).html(beforeSend);
                }
            },
            success: function(res){
                sleep(500).then(()=>{
                    location.reload();
                })
            },
            complete: function(){
                if (afterSend!='') {
                    $('.'+className+id).html(afterSend);
                }
            }
        })
    }
</script>
@endpush

