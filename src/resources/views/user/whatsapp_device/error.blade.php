@extends('user.layouts.app')
@section('panel')
<section>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">
                {{ translate('Error Notice')}}
            </h4>
            <span>
                <a href="" class="i-btn btn--info btn--md"> <i class="fas fa-refresh"></i>  {{ translate('Try Again') }}</a>
            </span>
        </div>
        
        <div class="card-body">
            <p class="text--danger fs-6">{{ translate($message) }}</p>
        </div>
    </div> 

</section>
@endsection