@extends('admin.layouts.app')
@section('panel')
    <section>
        <div class="container-fluid p-0">
            <div class="row gy-4">
                @include('admin.phone_book.text_phonebook.text_phonebook_tab')
                <div class="col">
                    <div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ translate('Admin Contacts')}}</h4>

                                <div class="d-flex align-items-center gap-3">
                                    <button class="i-btn primary--btn btn--md" data-bs-toggle="modal" data-bs-target="#contactImport"><i class="las la-upload"></i> {{ translate('Import Contact')}}</button>
                                    @if(@$group)
                                        <a href="{{route('admin.contact.sms.own.group.export', $group->id)}}" class="i-btn info--btn btn--md"><i class="las la-cloud-download-alt"></i> {{ translate('Export Contact')}}</a>
                                    @else
                                        <a href="{{route('admin.contact.sms.own.export')}}" class="i-btn info--btn btn--md"><i class="las la-cloud-download-alt"></i> {{ translate('Export Contact')}}</a>
                                    @endif
                                </div>
                            </div>
                            <div class="card-filter">

                                <form action="{{route(request()->route()->getName(), 'admin_contact')}}" method="get">
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


                                            <a class="i-btn danger--btn btn--md" href="{{route(request()->route()->getName(), 'admin_contact')}}">
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
                                                        <a class="i-btn danger--btn btn--sm delete-contact" data-bs-toggle="modal" data-bs-target="#delete" href="javascript:void(0)" data-id="{{$contact->id}}" ><i class="las la-trash"></i></a>
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
                                    {{ $contacts->appends(request()->all())->onEachSide(1)->links() }}
                                </div>
                            </div>
                        </div>

                        <a href="javascript:void(0);" class="support-ticket-float-btn" data-bs-toggle="modal" data-bs-target="#creategroupContact" title=" {{ translate('Create New SMS Group')}}">
                            <i class="fa fa-plus ticket-float"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
<!-- Admin group Modal -->

    <div class="modal fade" id="creategroup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Add New SMS Group')}}</h5>
                    <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
                </div>
                <form action="{{route('admin.group.own.sms.store')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-item mb-3">
                                    <label for="name" class="form-label"> {{ translate('Name')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder=" {{ translate('Enter Name')}}" required>
                                </div>

                                <div class="form-item ">
                                    <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-select" name="status" id="status" required>
                                        <option value="1"> {{ translate('Active')}}</option>
                                        <option value="2"> {{ translate('Inactive')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn primary--btn btn--md"> {{ translate('Submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updategroup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Update SMS Group')}}</h5>
                    <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
                </div>
                <form action="{{route('admin.group.own.sms.update')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label"> {{ translate('Name')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder=" {{ translate('Enter Name')}}" required>
                                </div>

                                <div>
                                    <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-control" name="status" id="status" required>
                                        <option value="1"> {{ translate('Active')}}</option>
                                        <option value="2"> {{ translate('Inactive')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn primary--btn btn--md"> {{ translate('Submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deletegroup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.group.own.sms.delete')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal_body2">
                        <div class="modal_icon2">
                            <i class="las la-trash"></i>
                        </div>
                        <div class="modal_text2 mt-3">
                            <h6> {{ translate('Are you sure to want delete this sms group?')}}</h6>
                        </div>
                    </div>
                    <div class="modal_button2 modal-footer">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn danger--btn btn--md"> {{ translate('Delete')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Admin Group Modal End -->

    <!-- Admin Contact Modal Start -->
    <div class="modal fade" id="creategroupContact" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Add New Contact')}} ({{ translate('Current Country Code')}} : {{$general->country_code}})</h5>
                    <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
                </div>

                <form action="{{route('admin.contact.sms.own.store')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="card">
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
                                    <select class="form-select" name="group_id" id="group_id" required>
                                        <option value="">{{ translate('Select Group')}}</option>
                                        @foreach($groupContacts as $group)
                                            <option value="{{$group->id}}">{{$group->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="status" class="form-label">{{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-select" name="status" id="status" required>
                                        <option value="1">{{ translate('Active')}}</option>
                                        <option value="2">{{ translate('Inactive')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn primary--btn btn--md"> {{ translate('Submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateContact" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Update Contact')}}</h5>
                    <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
                </div>

                <form action="{{route('admin.contact.sms.own.update')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="card">
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
                                    <select class="form-select" name="group_id" id="group_id" required>
                                        <option value="">{{ translate('Select Group')}}</option>
                                        @foreach($groupContacts as $group)
                                            <option value="{{$group->id}}">{{$group->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="status" class="form-label">{{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-select" name="status" id="status" required>
                                        <option value="1">{{ translate('Active')}}</option>
                                        <option value="2">{{ translate('Inactive')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn primary--btn btn--md"> {{ translate('Submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteContact" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.contact.sms.own.delete')}}" method="POST">
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

    <div class="modal fade" id="contactImport" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Import Contact')}}</h5>
                    <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
                </div>

                <form action="{{route('admin.contact.sms.own.import')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="group_id" class="form-label">{{ translate('Group')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-select" name="group_id" id="group_id" required>
                                        <option value="">{{ translate('Select Group')}}</option>
                                        @foreach($groupContacts as $group)
                                            <option value="{{$group->id}}">{{$group->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="file" class="form-label">{{ translate('File')}} <sup class="text--danger">*</sup></label>
                                    <input type="file" name="file" id="file" class="form-control" required="">
                                    <div class="form-text">{{ translate('Supported files: csv & exel')}}</div>
                                    <div class="form-text">{{ translate('Download file format from here')}} <a href="{{route('phone.book.demo.import.file')}}">{{ translate('csv')}}</a> ,
                                        <a href="{{route('demo.file.download','xlsx')}}">{{ translate('exel')}}</a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn primary--btn btn--md"> {{ translate('Submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Admin Contact Modal End -->

    <!-- User Contacts Modal Start -->
    <div class="modal fade" id="contactExport" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Export Contact')}}</h5>
                    <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
                </div>

                <form action="{{route('admin.contact.sms.export')}}" method="GET">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <label for="user_id" class="form-label"> {{ translate('User')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-select" name="user_id" id="user_id" required>
                                        <option value="all"> {{ translate('All')}}</option>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}">{{@$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn primary--btn btn--md"> {{ translate('Export')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- User Contacts Modal End -->
@endsection

@push('script-push')
<script>
	(function($){
		"use strict";
		$('.group').on('click', function(){
			var modal = $('#updategroup');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.find('input[name=name]').val($(this).data('name'));
			modal.find('select[name=status]').val($(this).data('status'));
			modal.modal('show');
		});

		$('.delete-group').on('click', function(){
			var modal = $('#deletegroup');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});
        $('.contact').on('click', function(){
			var modal = $('#updateContact');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.find('select[name=group_id]').val($(this).data('group_id'));
			modal.find('input[name=contact_no]').val($(this).data('contact_no'));
			modal.find('input[name=name]').val($(this).data('name'));
			modal.find('select[name=status]').val($(this).data('status'));
			modal.modal('show');
		});

		$('.delete-contact').on('click', function(){
			var modal = $('#deleteContact');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush

