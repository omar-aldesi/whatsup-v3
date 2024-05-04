@extends('admin.layouts.app')
@section('panel')
    <section>
        <div class="container-fluid p-0">
            <div class="row gy-4">
                @include('admin.phone_book.mail_phonebook.mail_phonebook_tab')
                <div class="col">
                    <!-- Start User Email group !-->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"> {{ translate('User Groups')}}</h4>
                        </div>
                        <div class="card-body px-0">
                            <div class="responsive-table">
                                <table>
                                    <thead>
                                    <tr>
                                        <th> {{ translate('Name')}}</th>
                                        <th> {{ translate('User')}}</th>
                                        <th> {{ translate('Contact')}}</th>
                                        <th> {{ translate('Status')}}</th>
                                    </tr>
                                    </thead>
                                    @forelse($userEmailGroups as $userEmailGroup)
                                        <tr class="@if($loop->even)@endif">
                                            <td data-label=" {{ translate('Name')}}">
                                                {{$userEmailGroup->name}}
                                            </td>
                                            <td data-label=" {{ translate('User')}}">
                                                <a href="{{route('admin.user.details', $userEmailGroup->user_id)}}" class="fw-bold text-dark">{{@$userEmailGroup->user->email}}</a>
                                            </td>

                                            <td data-label=" {{ translate('Contact')}}">
                                                <a href="{{route('admin.group.email.groupby', $userEmailGroup->id)}}" class="badge badge--primary p-2"> {{ translate('view email')}} ({{count($userEmailGroup->contact)}})</a>
                                            </td>
                                            <td data-label=" {{ translate('Status')}}">
                                                @if($userEmailGroup->status == 1)
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
                                {{ $userEmailGroups->appends(request()->all())->onEachSide(1)->links() }}
                            </div>
                        </div>
                    </div>
                    <!-- End User Email group !-->
                </div>
            </div>
        </div>
    </section>
 <!-- Admin Groups Modal Start-->
 <div class="modal fade" id="creategroup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('admin.group.own.email.store')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg--lite--violet">
                            <div class="card-title text-center text--light">{{ translate('Add New Email Group')}}</div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ translate('Name')}} <sup class="text--danger">*</sup></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="{{ translate('Enter Name')}}" required>
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
<div class="modal fade" id="updategroup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('admin.group.own.email.update')}}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg--lite--violet">
                            <div class="card-title text-center text--light">{{ translate('Update Email Group')}}</div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ translate('Name')}} <sup class="text--danger">*</sup></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="{{ translate('Enter Name')}}" required>
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
<div class="modal fade" id="deleteGroup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('admin.group.own.email.delete')}}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal_body2">
                    <div class="modal_icon2">
                        <i class="las la-trash"></i>
                    </div>
                    <div class="modal_text2 mt-3">
                        <h6>{{ translate('Are you sure to want delete this email group?')}}</h6>
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
<!-- Admin Groups Modal End-->
<!-- user groups Modal Start -->
<div class="modal fade" id="createContactNew" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('admin.contact.email.own.store')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg--lite--violet">
                            <div class="card-title text-center text--light"> {{ translate('Add New Contact')}}</div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="email" class="form-label"> {{ translate('Email')}} <sup class="text--danger">*</sup></label>
                                <input type="text" class="form-control" id="email" name="email" placeholder=" {{ translate('Enter Email')}}" required>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label"> {{ translate('Name')}} <sup class="text--danger">*</sup></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder=" {{ translate('Enter Name')}}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email_group_id" class="form-label"> {{ translate('Group')}} <sup class="text--danger">*</sup></label>
                                <select class="form-control" name="email_group_id" id="email_group_id" required>
                                    <option value=""> {{ translate('Select Group')}}</option>
                                    @foreach($groups as $group)
                                        <option value="{{$group->id}}">{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="1"> {{ translate('Active')}}</option>
                                    <option value="2"> {{ translate('Inactive')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal_button2 modal-footer">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn success--btn btn--md"> {{ translate('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updateContact" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('admin.contact.email.own.update')}}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg--lite--violet">
                            <div class="card-title text-center text--light"> {{ translate('Update Contact')}}</div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="email" class="form-label"> {{ translate('Email')}} <sup class="text--danger">*</sup></label>
                                <input type="text" class="form-control" id="email" name="email" placeholder=" {{ translate('Enter Email')}}" required>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label"> {{ translate('Name')}}<sup class="text--danger">*</sup></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder=" {{ translate('Enter Name')}}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email_group_id" class="form-label"> {{ translate('Group')}} <sup class="text--danger">*</sup></label>
                                <select class="form-control" name="email_group_id" id="email_group_id" required>
                                    <option value=""> {{ translate('Select Group')}}</option>
                                    @foreach($groups as $group)
                                        <option value="{{$group->id}}">{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label"> {{ translate('Status')}}<sup class="text--danger">*</sup></label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="1"> {{ translate('Active')}}</option>
                                    <option value="2"> {{ translate('Inactive')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal_button2 modal-footer">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn success--btn btn--md"> {{ translate('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteContact" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('admin.contact.email.own.delete')}}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal_body2">
                    <div class="modal_icon2">
                        <i class="las la-trash"></i>
                    </div>
                    <div class="modal_text2 mt-3">
                        <h6> {{ translate('Are you sure to want delete this email contact?')}}</h6>
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

<div class="modal fade" id="contactImport" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('admin.contact.email.own.import')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg--lite--violet">
                            <div class="card-title text-center text--light"> {{ translate('Update Contact')}}</div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="email_group_id" class="form-label"> {{ translate('Group')}} <sup class="text--danger">*</sup></label>
                                <select class="form-control" name="email_group_id" id="email_group_id" required>
                                    <option value=""> {{ translate('Select Group')}}</option>
                                    @foreach($groups as $group)
                                        <option value="{{$group->id}}">{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="file" class="form-label"> {{ translate('File')}} <sup class="text--danger">*</sup></label>
                                <input type="file" name="file" id="file" class="form-control" required="">
                                <div class="form-text"> {{ translate('Supported files: csv')}}</div>
                                <div class="form-text"> {{ translate('Download file format from here')}} <a href="{{route('email.contact.demo.import')}}"> {{ translate('csv')}}</a>,
                                    <a href="{{route('demo.email.file.download','xlsx')}}">{{ translate('exel')}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal_button2 modal-footer">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn success--btn btn--md"> {{ translate('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- user groups Modal End -->
<!-- User Contact Modal Start -->
<div class="modal fade" id="contactExport" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('admin.contact.email.export')}}" method="GET">
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
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{@$user->name}}</option>
                                    @endforeach
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
<!-- User Contact Modal End -->
@endsection



