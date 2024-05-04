<div class="col-lg-auto">
    <div class="vertical-tab card sticky-item">
        <div class="flex-column nav-pills gap-2">
            <a class="nav-link {{ request()->routeIs('admin.group.own.sms.group') ? 'active' : ' ' }}" href="{{route('admin.group.own.sms.group')}}">{{translate('Admin Groups')}}
                 <span><i class="las la-angle-right"></i></span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.group.own.sms.user.group') ? 'active' : ' ' }}" href="{{route('admin.group.own.sms.user.group')}}">{{translate('User Groups')}}
                 <span><i class="las la-angle-right"></i></span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.group.own.sms.contacts') ? 'active' : ' ' }}" href="{{route('admin.group.own.sms.contacts')}}">{{translate('Admin Contacts')}}
                <span><i class="las la-angle-right"></i></span>
               </a>
            <a class="nav-link {{ request()->routeIs('admin.group.own.sms.user.contact') ? 'active' : ' ' }}" href="{{route('admin.group.own.sms.user.contact')}}">{{translate('User Contacts')}}
                <span><i class="las la-angle-right"></i></span>
            </a>
        </div>
    </div>
</div>