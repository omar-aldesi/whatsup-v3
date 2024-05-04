@extends('user.layouts.app')
@section('panel')
<section class="mt-3">
    <div class="container-fluid p-0">
	    <div class="row">
	 		<div class="col-lg-12 p-1">
	            <div class="card">
					<div class="card-header">
						<h4 class="card-title">{{ translate('Select SMS Contact Options')}}</h4>
						<div class="d-flex align-items-center justify-content-md-end justify-content-start flex-wrap gap-3">
								<div>
			                    	<button class="i-btn primary--btn btn--md" data-bs-toggle="modal" data-bs-target="#creategroup"><i class="las la-plus"></i> {{ translate('Add New Contact')}}</button>
			                    </div>
			                    <div>
			                    	<button class="i-btn success--btn btn--md" data-bs-toggle="modal" data-bs-target="#contactImport"><i class="las la-upload"></i> {{ translate('Import Contact')}}</button>
			                    </div>

			                    <div>
                                    @if(@$group)
                                        <a href="{{route('user.phone.book.contact.group.export', $group->id)}}" class="i-btn info--btn btn--md"><i class="las la-cloud-download-alt"></i> {{ translate('Export Contact')}}</a>
                                    @else
			                    	    <a href="{{route('user.phone.book.contact.export')}}" class="i-btn info--btn btn--md"><i class="las la-cloud-download-alt"></i> {{ translate('Export Contact')}}</a>
                                    @endif
			                    </div>
						</div>
					</div>
					<div class="card-filter">
						<form action="{{route(request()->route()->getName(), @$group->id)}}" method="get">
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

									<a class="i-btn danger--btn btn--md" href="{{route(request()->route()->getName(), @$group->id)}}">
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
										<th>#</th>
										<th>{{ translate('Name')}}</th>
										<th>{{ translate('Phone Number')}}</th>
										<th>{{ translate('Group')}}</th>
										<th>{{ translate('Status')}}</th>
										<th>{{ translate('Action')}}</th>
									</tr>
								</thead>
								@forelse($contacts as $contact)
									<tr class="@if($loop->even)@endif">
										<td data-label="#">
											{{$loop->iteration}}
										</td>
										<td data-label="{{ translate('Name')}}">
											{{$contact->name}}
										</td>
										<td data-label="{{ translate('Phone Number')}}">
											{{$contact->contact_no}}
										</td>
										<td data-label="{{ translate('Group')}}">
											{{$contact->group->name}}
										</td>
										<td data-label="{{ translate('Status')}}">
											@if($contact->status == 1)
												<span class="badge badge--success">{{ translate('Active')}}</span>
											@else
												<span class="badge badge--danger">{{ translate('Inactive')}}</span>
											@endif
										</td>

										<td data-label={{ translate('Action')}}>
											<div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
												<a class="i-btn primary--btn btn--sm contact" data-bs-toggle="modal" data-bs-target="#updatebrand" href="javascript:void(0)"
													data-id="{{$contact->id}}"
													data-group_id="{{$contact->group_id}}"
													data-contact_no="{{$contact->contact_no}}"
													data-name="{{$contact->name}}"
													data-status="{{$contact->status}}"><i class="las la-pen"></i></a>
												<a class="i-btn danger--btn btn--sm delete" data-bs-toggle="modal" data-bs-target="#delete" href="javascript:void(0)" data-id="{{$contact->id}}"><i class="las la-trash"></i></a>
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
							{{$contacts->appends(request()->all())->onEachSide(1)->links()}}
						</div>
					</div>
	            </div>
	        </div>
	    </div>
	</div>
</section>


<div class="modal fade" id="creategroup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<form action="{{route('user.phone.book.contact.store')}}" method="POST">
				@csrf
	            <div class="modal-body">
	            	<div class="card">
	            		<div class="card-header bg--lite--violet">
	            			<div class="card-title text-center text--light">{{ translate('Add New Contact')}} ({{ translate('Current Country Code')}} : {{$general->country_code}})</div>
	            		</div>
		                <div class="card-body">
		                	<div class="mb-3">
								<label for="contact_no" class="form-label">{{ translate('Contact Number')}}<sup class="text--danger">*</sup></label>
								<input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="{{ translate('Enter Contact Number')}}" required>
							</div>

							<div class="mb-3">
								<label for="name" class="form-label">{{ translate('Name')}} <sup class="text--danger">*</sup></label>
								<input type="text" class="form-control" id="name" name="name" placeholder="{{ translate('Enter Name')}}" required>
							</div>

							<div class="mb-3">
								<label for="group_id" class="form-label">{{ translate('Group')}} <sup class="text--danger">*</sup></label>
								<select class="form-control" name="group_id" id="group_id" required>
									<option value="">{{ translate('Select Group')}}</option>
									@foreach($user->group as $group)
										<option value="{{$group->id}}">{{$group->name}}</option>
									@endforeach
								</select>
							</div>

							<div class="mb-3">
								<label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
								<select class="form-control" name="status" id="status" required>
									<option value="1">{{ translate('Active')}}</option>
									<option value="2">{{ translate('Inactive')}}</option>
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


<div class="modal fade" id="updateContact" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<form action="{{route('user.phone.book.contact.update')}}" method="POST">
				@csrf
				<input type="hidden" name="id">
	            <div class="modal-body">
	            	<div class="card">
	            		<div class="card-header bg--lite--violet">
	            			<div class="card-title text-center text--light">{{ translate('Update Contact')}}</div>
	            		</div>
		                <div class="card-body">
							<div class="mb-3">
								<label for="contact_no" class="form-label">{{ translate('Contact Number')}} <sup class="text--danger">*</sup></label>
								<input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="{{ translate('Enter Contact Number')}}" required>
							</div>

							<div class="mb-3">
								<label for="name" class="form-label">{{ translate('Name')}} <sup class="text--danger">*</sup></label>
								<input type="text" class="form-control" id="name" name="name" placeholder="{{ translate('Enter Name')}}" required>
							</div>

							<div class="mb-3">
								<label for="group_id" class="form-label">{{ translate('Group')}} <sup class="text--danger">*</sup></label>
								<select class="form-control" name="group_id" id="group_id" required>
									<option value="">{{ translate('Select Group')}}</option>
									@foreach($user->group as $group)
										<option value="{{$group->id}}">{{$group->name}}</option>
									@endforeach
								</select>
							</div>

							<div class="mb-3">
								<label for="status" class="form-label">{{ translate('Status')}} <sup class="text--danger">*</sup></label>
								<select class="form-control" name="status" id="status" required>
									<option value="1">{{ translate('Active')}}</option>
									<option value="2">{{ translate('Inactive')}}</option>
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



<div class="modal fade" id="deletegroup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{route('user.phone.book.contact.delete')}}" method="POST">
				@csrf
				<input type="hidden" name="id">
				<div class="modal_body2">
					<div class="modal_icon2">
						<i class="las la-trash"></i>
					</div>
					<div class="modal_text2 mt-3">
						<h6>{{ translate('Are you sure to want delete this contact?')}}</h6>
					</div>
				</div>
				<div class="md modal-footer justify-content-center">
					<div class="d-flex align-items-center justify-content-center gap-3">
						<button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
						<button type="submit" class="i-btn danger--btn btn--md">{{ translate('Delete')}}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="contactImport" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<form action="{{route('user.phone.book.contact.import')}}" method="POST" enctype="multipart/form-data">
				@csrf
	            <div class="modal-body">
	            	<div class="card">
	            		<div class="card-header bg--lite--violet">
	            			<div class="card-title text-center text--light">{{ translate('Update Contact')}}</div>
	            		</div>
		                <div class="card-body">
							<div class="mb-3">
								<label for="group_id" class="form-label">{{ translate('Group')}} <sup class="text--danger">*</sup></label>
								<select class="form-control" name="group_id" id="group_id" required>
									<option value="">{{ translate('Select Group')}}</option>
									@foreach($user->group as $group)
										<option value="{{$group->id}}">{{$group->name}}</option>
									@endforeach
								</select>
							</div>

							<div class="mb-3">
								<label for="file" class="form-label">{{ translate('File')}} <sup class="text--danger">*</sup></label>
								<input type="file" name="file" id="file" class="form-control" required="">
								<div class="form-text">{{ translate('Supported files: csv,')}}</div>
								<div class="form-text">{{ translate('Download file format from here')}} <a href="{{route('phone.book.demo.import.file')}}">{{ translate('csv')}}</a>,
								<a href="{{route('demo.file.download','xlsx')}}">{{ translate('exel')}}</a>

								</div>
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
		$('.contact').on('click', function(){
			var modal = $('#updateContact');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.find('select[name=group_id]').val($(this).data('group_id'));
			modal.find('input[name=contact_no]').val($(this).data('contact_no'));
			modal.find('input[name=name]').val($(this).data('name'));
			modal.find('select[name=status]').val($(this).data('status'));
			modal.modal('show');
		});

		$('.delete').on('click', function(){
			var modal = $('#deletegroup');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush
