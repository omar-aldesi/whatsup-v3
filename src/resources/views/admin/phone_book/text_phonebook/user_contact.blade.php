@extends('admin.layouts.app')
@section('panel')
    <section>
    <div class="container-fluid p-0">
        <div class="row gy-4">
            @include('admin.phone_book.text_phonebook.text_phonebook_tab')
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ translate('User Contacts')}}</h4>
                        <button class="i-btn info--btn btn--md" data-bs-toggle="modal" data-bs-target="#contactExport"><i class="las la-plus"></i>  {{ translate('Export Contact')}}</button>
                    </div>
                    <div class="card-filter">

                        <form action="{{route(request()->route()->getName(), 'user_contact')}}" method="get">
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


                                    <a class="i-btn danger--btn btn--md" href="{{route(request()->route()->getName(), 'user_contact')}}">
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
                                    <th> #</th>

                                    <th> {{ translate('User')}}</th>
                                    <th> {{ translate('Name')}}</th>
                                    <th> {{ translate('Phone Number')}}</th>
                                    <th> {{ translate('Group')}}</th>
                                    <th> {{ translate('Status')}}</th>
                                </tr>
                                </thead>
                                @forelse($userContacts as $userContact)
                                    <tr class="@if($loop->even)@endif">
                                        <td data-label=" #">
                                            {{$loop->iteration}}
                                        </td>

                                        <td data-label=" {{ translate('User')}}">
                                            <a href="{{route('admin.user.details', $userContact->user_id)}}" class="fw-bold text-dark">{{@$userContact->user->email}}</a>
                                        </td>
                                        <td data-label=" {{ translate('Name')}}">
                                            {{$userContact->name}}
                                        </td>
                                        <td data-label=" {{ translate('Phone Number')}}">
                                            {{$userContact->contact_no}}
                                        </td>
                                        <td data-label=" {{ translate('Group')}}">
                                            {{$userContact->group->name}}
                                        </td>





                                        <td data-label=" {{ translate('Status')}}">
                                            @if($userContact->status == 1)
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
                            {{ $userContacts->appends(request()->all())->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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

