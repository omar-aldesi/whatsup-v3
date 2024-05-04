@extends('user.layouts.app')
@section('panel')
    <section>
        <div class="card">
            <div class="card-header">
                <h4>{{ translate('SMS Gateway List')}}</h4>

                <div class="row justify-content-end">
                    <form action="{{route('user.sms.gateway.default')}}" method="POST" class="form-inline float-sm-right text-end">
                        @csrf
                        <div class="input-group">
                            <select class="form-select" name="default_gateway_id" required="">
                                @foreach($smsGateways as $gateway)
                                    <option value="{{$gateway->id}}" @if($defaultGateway == $gateway->id) selected @endif>{{strtoupper($gateway->name)}}</option>
                                @endforeach
                            </select>
                            <button class="i-btn btn--primary btn--md" id="basic-addon2" type="submit">{{ translate('Send SMS Method')}}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body px-0">
                <div class="responsive-table">
                    <table>
                        <thead>
                        <tr>
                            <th>{{ translate('Gateway Name')}}</th>
                            <th>{{ translate('Action')}}</th>
                        </tr>
                        </thead>
                        @forelse($smsGateways as $smsGateway)
                            <tr class="@if($loop->even)@endif">
                                <td data-label="{{ translate('Gateway Name')}}">
                                    {{ucfirst($smsGateway->name)}}
                                    @if($defaultGateway == $smsGateway->id)
                                        <span class="text--success fs-5">
                                        <i class="las la-check-double"></i>
                                    </span>
                                    @endif
                                </td>

                                <td data-label="{{ translate('Action')}}">
                                    <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                        <a href="{{route('user.sms.gateway.edit', $smsGateway->id)}}" class="i-btn primary--btn btn--sm brand" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="las la-pen"></i></a>
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
                    {{$smsGateways->appends(request()->all())->onEachSide(1)->links()}}
                </div>
            </div>
        </div>
    </section>
@endsection
