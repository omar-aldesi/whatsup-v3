@extends('user.layouts.app')
@section('panel')
    <section>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ translate('Whatsapp logs')}}</h4>
            </div>

            <div class="card-filter">
                <form action="{{route('user.whatsapp.search')}}" method="GET">
                    <div class="filter-form">
                        <div class="filter-item">
                            <select name="status" class="form-select">
                                <option value="all" @if(@$status == "all") selected @endif>{{translate('All')}}</option>
                                <option value="pending" @if(@$status == "pending") selected @endif>{{translate('Pending')}}</option>
                                <option value="schedule" @if(@$status == "schedule") selected @endif>{{translate('Schedule')}}</option>
                                <option value="fail" @if(@$status == "fail") selected @endif>{{translate('Fail')}}</option>
                                <option value="delivered" @if(@$status == "delivered") selected @endif>{{translate('Delivered')}}</option>
                                <option value="processing" @if(@$status == "processing") selected @endif>{{translate('Processing')}}</option>
                            </select>
                        </div>

                        <div class="filter-item">
                            <input type="text" autocomplete="off" name="search" placeholder="{{ translate('Search with User or Recipient number')}}" class="form-control" id="search" value="{{@$search}}">
                        </div>

                        <div class="filter-item">
                            <input type="text" class="form-control datepicker-here" name="date" value="{{@$searchDate}}" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position="bottom right" autocomplete="off" placeholder="{{ translate('From Date-To Date')}}" id="date">
                        </div>

                        <div class="filter-action">
                            <button class="i-btn primary--btn btn--md" type="submit">
                                <i class="fas fa-search"></i> {{ translate('Search')}}
                            </button>
                            <div class="statusUpdateBtn d-none">
                                <a class="i-btn success--btn btn--md statusupdate"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Status Update"
                                        data-bs-toggle="modal"
                                        data-bs-target="#smsstatusupdate"
                                        type="submit">
                                    <i class="fas fa-gear"></i> {{translate('Action')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body px-0">
                <div class="responsive-table">
                    <div class="responsive-table">
                        <table>
                            <thead>
                            <tr>
                                <th>{{ translate('#')}}</th>
                                <th>{{ translate('Sender')}}</th>
                                <th>{{ translate('To')}}</th>
                                <th>{{ translate('Credit')}}</th>
                                <th>{{ translate('Initiated')}}</th>
                                <th>{{ translate('Schedule')}}</th>
                                <th>{{ translate('Status')}}</th>
                                <th>{{ translate('Action')}}</th>
                            </tr>
                            </thead>
                            @forelse($whatsAppLogs as $smsLog)
                                <tr class="@if($loop->even)@endif">
                                    @if($smsLog->status == 1)
                                        <td class="d-none d-md-flex align-items-center">
                                            <input class="form-check-input mt-0 me-2" type="checkbox" name="whatsappid" value="{{$smsLog->id}}" aria-label="Checkbox for following text input">
                                            {{$loop->iteration}}
                                        </td>
                                    @else
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                    @endif

                                    <td data-label="{{ translate('Sender') }}">

                                        @if($smsLog->whatsappGateway)
                                            {{ translate('WhatsApp Gateway')}} <i class="las la-arrow-right"></i> <span class="text--success fw-bold">{{$smsLog->whatsappGateway->name}}</span>
                                        @else
                                            <span>{{translate('N/A')}}</span>
                                        @endif
                                    </td>

                                    <td data-label="{{ translate('To')}}">
                                        {{$smsLog->to}}
                                    </td>

                                    <td data-label="{{ translate('Credit')}}">
                                        @php
                                            $getMessageCountWord = $general->whatsapp_word_count;
                                            $messages = str_split($smsLog->message,$getMessageCountWord);
                                            $totalMessage = count($messages);
                                        @endphp
                                        {{$totalMessage}} {{ translate('Credit')}}
                                    </td>

                                    <td data-label="{{ translate('Initiated')}}">
                                        {{getDateTime($smsLog->created_at)}}
                                    </td>

                                    <td data-label="{{ translate('Schedule')}}">
                                        @if(!is_null($smsLog->initiated_time))
                                            {{getDateTime($smsLog->initiated_time)}}
                                        @else
                                            <span>{{translate('N/A')}}</span>
                                        @endif
                                    </td>

                                    <td data-label="{{ translate('Status')}}">
                                        @if($smsLog->status == 1)
                                            <span class="badge badge--primary">{{ translate('Pending')}}</span>
                                        @elseif($smsLog->status == 2)
                                            <span class="badge badge--info">{{ translate('Schedule')}}</span>
                                        @elseif($smsLog->status == 3)
                                            <span class="badge badge--danger">{{ translate('Fail')}}</span>
                                        @else
                                            <span class="badge badge--success">{{ translate('Delivered')}}</span>
                                        @endif
                                    </td>

                                    <td data-label={{ translate('Action')}}>
                                        <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                            <a class="i-btn primary--btn btn--sm details"
                                            data-message="{{$smsLog->message}}"
                                            data-response_gateway="{{$smsLog->response_gateway}}"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Details"
                                            data-bs-target="#smsdetails"
                                            ><i class="las la-desktop"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ translate('No Data Found')}}</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                    <div class="m-3">
                        {{$whatsAppLogs->appends(request()->all())->onEachSide(1)->links()}}
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="smsdetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg--lite--violet">
                            <div class="card-title text-center text--light">{{ translate('Message')}}</div>
                        </div>
                        <div class="card-body mb-3">
                            <p id="message--text"></p>
                        </div>
                    </div>
                </div>

                <div class="modal_button2 modal-footer">
                    <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="smsstatusupdate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('user.whatsapp.status.update')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <input type="hidden" name="smslogid">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-header bg--lite--violet">
                                <div class="card-title text-center text--light">{{ translate('WhatsApp Status Update')}}</div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="status" class="form-label">{{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-control" name="status" id="status" required>
                                        <option value="" selected="" disabled="">{{ translate('Select Status')}}</option>
                                        <option value="1">{{ translate('Pending')}}</option>
                                        <option value="4">{{ translate('Success')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal_button2 modal-footer">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn success--btn btn--md">{{ translate('Submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection


@push('script-push')
    <script>
        (function($){
            "use strict";
            $('.details').on('click', function(){
                var modal = $('#smsdetails');
                var message = $(this).data('message');
                var response_gateway = $(this).data('response_gateway');
			    $("#message--text").html(`${message} :: <span class="text-danger"> ${response_gateway} </span>`);
                modal.modal('show');
            });

            $('.checkAll').click(function(){
                $('input:checkbox').not(this).prop('checked', this.checked);
            });

            $('.statusupdate').on('click', function(){
                var modal = $('#smsstatusupdate');
                var newArray = [];
                $("input:checkbox[name=whatsappid]:checked").each(function(){
                    newArray.push($(this).val());
                });
                modal.find('input[name=smslogid]').val(newArray.join(','));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
