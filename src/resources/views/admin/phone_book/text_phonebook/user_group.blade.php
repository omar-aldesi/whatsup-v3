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
                            <h4 class="card-title">{{ translate('User Groups')}}</h4>
                        </div>
                        <div class="card-body px-0">
                            <div class="responsive-table">
                                <table >
                                    <thead>
                                    <tr>
                                        <th> {{ translate('Name')}}</th>
                                        <th> {{ translate('User')}}</th>
                                        <th> {{ translate('Contact')}}</th>
                                        <th> {{ translate('Status')}}</th>
                                    </tr>
                                    </thead>
                                    @forelse($userGroups as $group)
                                        <tr class="@if($loop->even)@endif">
                                            <td data-label=" {{ translate('Name')}}">
                                                {{$group->name}}
                                            </td>

                                            <td data-label=" {{ translate('User')}}">
                                                <a href="{{route('admin.user.details', $group->user_id)}}" class="fw-bold text-dark">{{@$group->user->email}}</a>
                                            </td>

                                            <td data-label=" {{ translate('Contact')}}">
                                                <a href="{{route('admin.group.sms.groupby', $group->id)}}" class="badge badge--primary p-2"> {{ translate('view contact')}} ({{count($group->contact)}})</a>
                                            </td>

                                            <td data-label=" {{ translate('Status')}}">
                                                @if($group->status == 1)
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

                                {{$userGroups->appends(request()->all())->onEachSide(1)->links()}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
