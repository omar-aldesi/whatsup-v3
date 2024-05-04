@extends('user.layouts.app')
@section('panel')
    <section>
        <div>
            <div class="table_heading d-flex align--center justify--between">
                <nav  aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('user.sms.gateway.sendmethod.api')}}">{{ translate('Api Gateway')}}</a></li>
                        <li class="breadcrumb-item" aria-current="page"> {{ucfirst($smsGateway->name)}}</li>
                    </ol>
                </nav>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> {{ucfirst($smsGateway->name)}} {{ translate('Gateway Update')}}</h4>
                </div>

                <div class="card-body">
                    <form action="{{route('user.sms.gateway.update', $smsGateway->id)}}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @foreach($credentials as $key => $parameter)
                            <div class="mb-3">
                                <label for="{{$key}}" class="form-label">{{ucwords(str_replace('_', ' ', $key))}} <sup class="text--danger">*</sup></label>
                                <input type="text" name="{{$key}}" id="{{$key}}" value="{{$parameter}}" class="form-control" placeholder="{{ translate('Enter Valid API Data')}}" required>
                            </div>
                        @endforeach

                        <div>
                            <button type="submit" class="i-btn primary--btn btn--md text-light">{{ translate('Submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

