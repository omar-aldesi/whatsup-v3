@extends('admin.layouts.app')
@section('panel')

<section>
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title"> {{ translate('Select Options')}}</h4>
            <div class="">
                <div class="d-flex align-items-center justify-content-start justify-content-md-end flex-wrap gap-3">
                    <div>
                        <button class="w-100 i-btn info--btn btn--md border-0 px-1 py-2 rounded ms-2" data-bs-toggle="modal" data-bs-target="#contactExport"><i class="las la-plus"></i>  {{ translate('Export Contact')}}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-filter">
            <form action="{{route(request()->route()->getName(),$id)}}" method="get">
                @csrf
                <div class="filter-form">
                    <div class="filter-item">
                        <select name="status" class="form-select">
                            <option value="all" selected disabled @if(@$status == "all") selected @endif>{{translate('All')}}</option>
                            <option value="1" @if(@$status == "1") selected @endif>{{translate('Active')}}</option>
                            <option value="2" @if(@$status == "2") selected @endif>{{translate('Inactive')}}</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <input type="text" autocomplete="off" name="search" placeholder="{{translate('Search with Phone Number or Name')}}" class="form-control" id="search" value="{{@$search}}">
                    </div>

                    <div class="filter-action">
                        <button class="i-btn primary--btn btn--md" type="submit">
                            <i class="fas fa-search"></i> {{ translate('Search')}}
                        </button>

                        <a class="i-btn danger--btn btn--md" href="{{route(request()->route()->getName(),$id)}}">
                            <i class="las la-sync"></i>  {{translate('reset')}}
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body px-0">
            <div class="responsive-table">
                <table>
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> {{ translate('User')}}</th>
                            <th> {{ translate('Name')}}</th>
                            <th> {{ translate('Phone Number')}}</th>
                            <th> {{ translate('Group')}}</th>
                            <th> {{ translate('Status')}}</th>
                        </tr>
                    </thead>
                    @forelse($contacts as $contact)
                        <tr class="@if($loop->even)@endif">
                            <td data-label=" #">
                                {{$loop->iteration}}
                            </td>

                            <td data-label=" {{ translate('User')}}">
                                <a href="{{route('admin.user.details', $contact->user_id)}}" class="fw-bold text-dark">{{@$contact->user->email}}</a>
                            </td>

                            <td data-label=" {{ translate('Name')}}">
                                {{$contact->name}}
                            </td>
                            <td data-label=" {{ translate('Phone Number')}}">
                                {{$contact->contact_no}}
                            </td>
                            <td data-label=" {{ translate('Group')}}">
                                {{$contact->group->name}}
                            </td>

                            <td data-label=" {{ translate('Status')}}">
                                @if($contact->status == 1)
                                    <span class="badge badge--success"> {{ translate('Active')}}</span>
                                @else
                                    <span class="badge badge--danger"> {{ translate('Inactive')}}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-muted text-center" colspan="100%"> {{ translate('No Data Found')}}</td>
                        </tr>
                    @endforelse
                </table>
            </div>
            <div class="m-3">
                {{$contacts->appends(request()->all())->onEachSide(1)->links()}}
            </div>
        </div>
    </div>
</section>



<div class="modal fade" id="contactExport" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<form action="{{route('admin.contact.sms.export')}}" method="GET">
	            <div class="modal-body">
	            	<div class="card">
	            		<div class="card-header bg--lite--violet">
	            			<div class="card-title text-center text--light"> {{ translate('Export Contact')}}</div>
	            		</div>
		                <div class="card-body">
							<div class="mb-3">
								<label for="user_id" class="form-label"> {{ translate('User')}} <sup class="text--danger">*</sup></label>
								<select class="form-control" name="user_id" id="user_id" required>
									<option value="all"> {{ translate('All')}}</option>
                                    <option value="{{$contact->user->id}}">{{@$contact->user->name}}</option>
								</select>
							</div>
						</div>
	            	</div>
	            </div>

	            <div class="modal_button2 modal-footer">
                    <div class="d-flex align-items-center justify-content-center gap-3">
	                <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
	                <button type="submit" class="i-btn success--btn btn--md"> {{ translate('Export')}}</button>
                    </div>
	            </div>
	        </form>
        </div>
    </div>
</div>
@endsection

