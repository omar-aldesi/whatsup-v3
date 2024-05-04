<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\EmailGroup;
use App\Models\Contact;
use App\Models\EmailContact;
use Illuminate\View\View;

class OwnGroupController extends Controller
{
    /**
     * @return View
     */
	public function smsGroups(Request $request): View
    {   
    	$title = "Manage own sms group";
        $users = User::select('id', 'name')->get();
        $groupContacts = Group::whereNull('user_id')->get();
        $search = $request->search;
        $status = $request->status;
        $data = ['title', 'users', 'search', 'status', 'groupContacts'];

        if($request->routeIs('admin.group.own.sms.group')){
            $groups = Group::filter($request)->whereNull('user_id')->paginate(paginateNumber());
            array_push($data, 'groups');
            return view("admin.phone_book.text_phonebook.admin_group", compact($data));
        }
        else{
            $userGroups = Group::filter($request)->whereNotNull('user_id')->latest()->with('user')->paginate(paginateNumber());
            array_push($data, 'userGroups');
            return view("admin.phone_book.text_phonebook.user_group", compact($data));
        }
    }

    /**
     * @return View
     */
	public function smsContacts(Request $request): View
    {   
    	$title = "Manage own sms group";
        $users = User::select('id', 'name')->get();
        $groupContacts = Group::whereNull('user_id')->get();
        $search = $request->search;
        $status = $request->status;
        $data = ['title', 'users', 'search', 'status', 'groupContacts'];

        if($request->routeIs('admin.group.own.sms.contacts')){
            $contacts = Contact::filter($request)->whereNull('user_id')->latest()->with('group')->paginate(paginateNumber());
            array_push($data, 'contacts');
            return view("admin.phone_book.text_phonebook.admin_contact", compact($data));
        }
        else{
            $userContacts = Contact::filter($request)->whereNotNull('user_id')->latest()->with('user', 'group')->paginate(paginateNumber());
            array_push($data, 'userContacts');
            return view("admin.phone_book.text_phonebook.user_contact", compact($data));
        }
    }

    public function smsStore(Request $request)
    {
    	$data = $request->validate([
    		'name' => 'required|max:255',
    		'status' => 'required|in:1,2'
    	]);

    	Group::create($data);

    	$notify[] = ['success', 'SMS group has been created'];
    	return back()->withNotify($notify);
    }

    public function smsUpdate(Request $request)
    {
    	$data = $request->validate([
    		'name' => 'required|max:255',
    		'status' => 'required|in:1,2'
    	]);

    	$group = Group::whereNull('user_id')->where('id', $request->input('id'))->firstOrFail();
    	$group->update($data);

    	$notify[] = ['success', 'SMS group has been created'];
    	return back()->withNotify($notify);
    }

    public function smsDelete(Request $request)
    {
    	$group = Group::whereNull('user_id')->where('id', $request->input('id'))->firstOrFail();
    	Contact::whereNull('user_id')->where('group_id', $group->id)->delete();
    	$group->delete();

    	$notify[] = ['success', 'SMS group has been deleted'];
    	return back()->withNotify($notify);
    }

    /**
     * @param $id
     * @return View
     */
    public function smsOwnContactByGroup(Request $request, $id): View
    {
        $group = Group::findOrFail($id);
        $title = "SMS contact list by ".$group->name;
        $groups = Group::whereNull('user_id')->get();
        $search = $request->search;
        $status = $request->status;
        $contacts = Contact::filter($request)->whereNull('user_id')->where('group_id', $id)->latest()->with('group')->paginate(paginateNumber());

        return view('admin.phone_book.own_sms_contact', compact('title', 'contacts', 'groups', 'group', 'search', 'status'));
    }

    /**
     * @param $id
     * @return View
     */
    public function emailOwnContactByGroup(Request $request, $id): View
    {
        $group = EmailGroup::findOrFail($id);
        $title = "Email contact list by ".$group->name;
        $groups = EmailGroup::whereNull('user_id')->get();
        $contacts = EmailContact::filter($request)->whereNull('user_id')->where('email_group_id', $id)->latest()->with('user', 'emailGroup')->paginate(paginateNumber());
        $search = $request->search;
        $status = $request->status;
        return view('admin.phone_book.own_email_contact', compact('title', 'contacts', 'groups', 'group', 'search', 'status'));
    }


    /**
     * @return View
     */
    public function emailGroups(Request $request): View
    {

    	$title = "Manage own email group";
        $groups = EmailGroup::whereNull('user_id')->get();
        $users = User::select('id', 'name')->get();
        $search = $request->search;
        $status = $request->status;
        $data = ['title', 'users', 'search', 'status', 'groups'];

        if($request->routeIs('admin.group.own.email.group')) {

            $emailGroups = EmailGroup::filter($request)->whereNull('user_id')->paginate(paginateNumber());
            array_push($data, 'emailGroups');
            return view("admin.phone_book.mail_phonebook.admin_group", compact($data));
        }
    	else {
            $userEmailGroups = EmailGroup::filter($request)->whereNotNull('user_id')->latest()->with('user')->paginate(paginateNumber());
            array_push($data, 'userEmailGroups');
            return view("admin.phone_book.mail_phonebook.user_group", compact($data));
        }
    }


    /**
     * @return View
     */
    public function emailContact(Request $request): View
    {
        

        $title = "Manage own email group";
        $groups = EmailGroup::whereNull('user_id')->get();
        $users = User::select('id', 'name')->get();
        $search = $request->search;
        $status = $request->status;
        $data = ['title', 'users', 'search', 'status', 'groups'];


        if($request->routeIs('admin.group.own.email.contacts')) {

            $contacts = EmailContact::filter($request)->whereNull('user_id')->latest()->with('emailGroup')->paginate(paginateNumber());
            array_push($data, 'contacts');
            return view("admin.phone_book.mail_phonebook.admin_contact", compact($data));
        }
        else{
            $emailContacts = EmailContact::filter($request)->whereNotNull('user_id')->latest()->with('user', 'emailGroup')->paginate(paginateNumber());
            array_push($data, 'emailContacts');
            return view("admin.phone_book.mail_phonebook.user_contact", compact($data));
        }
        
    	
    }

    public function emailStore(Request $request)
    {
    	$data = $request->validate([
    		'name' => 'required|max:255',
    		'status' => 'required|in:1,2'
    	]);

    	EmailGroup::create($data);

    	$notify[] = ['success', 'Email group has been created'];
    	return back()->withNotify($notify);
    }

    public function emailUpdate(Request $request)
    {
    	$data = $request->validate([
    		'name' => 'required|max:255',
    		'status' => 'required|in:1,2'
    	]);
    	$group = EmailGroup::whereNull('user_id')
            ->where('id', $request->input('id'))
            ->firstOrFail();

    	$group->update($data);

    	$notify[] = ['success', 'Email Group has been created'];
    	return back()->withNotify($notify);
    }

    public function emailDelete(Request $request)
    {
    	$group = EmailGroup::whereNull('user_id')->where('id', $request->id)->firstOrFail();
    	EmailContact::whereNull('user_id')->where('email_group_id', $group->id)->delete();
    	$group->delete();

    	$notify[] = ['success', 'SMS Group has been deleted'];
    	return back()->withNotify($notify);
    }
}
