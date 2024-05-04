@extends('admin.layouts.app')
@section('panel')
<section class="mt-3 rounded_box">
	<div class="container-fluid p-0 mb-3 pb-2">
		<div class="row">
			<div class="col-xl-4">
                <form action="{{route('admin.gateway.whatsapp.create')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-header">
                            {{ translate('Whatsapp Device Add')}}
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label for="name">{{ translate('Session Name')}} <span  class="text-danger">*</span>  </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror " name="name" id="name" value="{{old('name')}}" placeholder="{{ translate('Put Session Name (Any)')}}">
                                    @error('name')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label for="number">{{ translate('WhatsApp Number')}} <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('number') is-invalid @enderror " name="number" id="number" value="{{old('number')}}" placeholder="{{ translate('Put Your WhatsApp number here')}}">
                                    @error('number')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label for="min_delay">{{ translate('Message Minimum Delay Time')}}
                                        <span class="text-danger" >*</span>
                                    </label>
                                    <input type="number" class="form-control" name="min_delay" id="min_delay" value="{{old('min_delay')}}" placeholder="{{ translate('Message minimum delay time in second')}}">

                                </div>
                                <div class="col-md-12">
                                    <label for="max_delay">{{ translate('Message Maximum Delay Time')}}
                                        <span class="text-danger" >*</span>
                                    </label>
                                    <input type="number" class="form-control" name="max_delay" id="max_delay" value="{{old('max_delay')}}" placeholder="{{ translate('Message maximum delay time in second')}}">

                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary me-sm-3 me-1 float-end">{{ translate('Submit')}}</button>
                        </div>
                    </div>
                </form>
			</div>
            <div class="col-xl-8">
                <div class="card mb-3">
                    <div class="card-header">
                        {{ translate('WhatsApp Device List')}}
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="w-100 table--light table-hover nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ translate('Session Name')}}</th>
                                        <th>{{ translate('WhatsApp Number')}}</th>
                                        <th>{{ translate('Minimum Delay')}}</th>
                                        <th>{{ translate('Maximum Delay')}}</th>
                                        <th>{{ translate('Time Delay')}}</th>
                                        <th>{{ translate('Status')}}</th>
                                        <th>{{ translate('Action')}}</th>
                                    </tr>
                                </thead>
                                @forelse ($whatsapps as $item)
                                <tbody>
                                    <tr>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->number}}</td>
                                        <td>{{convertTime($item->min_delay)}}</td>
                                        <td>{{convertTime($item->max_delay)}}</td>
                                        <td>
                                            <span class="badge badge--{{$item->status == 'initiate' ? 'primary' : ($item->status == 'connected' ? 'success' : 'danger')}}">
                                                {{ucwords($item->status)}}
                                            </span>
                                        </td>
                                        <td>
                                            @if($item->status == 'initiate')
                                                <a title="Scan" href="javascript:void(0)" id="textChange" class="badge btn bg--success p-2 qrQuote textChange{{$item->id}}" value="{{$item->id}}"><i class="fas fa-qrcode"></i>&nbsp {{ translate('Scan')}}</a>
                                            @elseif($item->status == 'connected')
                                            <a title="Disconnect" href="javascript:void(0)" onclick="return deviceStatusUpdate('{{$item->id}}','disconnected','deviceDisconnection','Disconnecting','Connect')" class="btn badge bg--warning p-2 deviceDisconnection{{$item->id}}" value="{{$item->id}}"><i class="fas fa-plug"></i>&nbsp {{ translate('Disconnect')}}</a>
                                            @else
                                            <a title="Scan" href="javascript:void(0)" id="textChange" class="btn badge bg--success p-2 qrQuote textChange{{$item->id}}" value="{{$item->id}}"><i class="fas fa-qrcode"></i>&nbsp {{ translate('Scan')}}</a>
                                            @endif

                                            <a title="Delete" href="" class="badge bg-danger p-2 whatsappDelete" value="{{$item->id}}"><i class="fas fa-trash-alt"></i>&nbsp {{ translate('Trash')}}</a>
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
    </div>
</section>

{{-- whatsapp delete modal --}}
<div class="modal fade" id="whatsappDelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.gateway.whatsapp.delete')}}" method="POST">
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
                    <div class="d-flex align-items-center justify-content-center gap-3">
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
                    <li>{{ translate('1. Open WhatsApp on your phone')}}</li>
                    <li>{{ translate('2. Tap Menu  or Settings  and select Linked Devices')}}</li>
                    <li>{{ translate('3. Point your phone to this screen to capture the code')}}</li>
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

