<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ImportJob;
use App\Models\Import;
use App\Service\ImportContactService;
use Illuminate\Http\Request;
use App\Models\EmailContact;
use App\Models\Group;
use App\Models\Contact;
use App\Models\EmailGroup;
use App\Exports\EmailContactExport;
use App\Exports\ContactExport;
use App\Rules\ExtensionCheckRule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class OwnContactController extends Controller
{
    public ImportContactService $importService ;
    public function __construct(ImportContactService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * @return View
     */
    public function emailContactIndex(): View
    {
        $title = "Manage Email Contact List";
        $groups = EmailGroup::whereNull('user_id')->get();
        $contacts = EmailContact::whereNull('user_id')->latest()->with('emailGroup')->paginate(paginateNumber());

        return view('admin.phone_book.own_email_contact', compact('title', 'contacts', 'groups'));
    }

    public function emailContactStore(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:120',
            'name' => 'required|max:90',
            'email_group_id' => 'required|exists:email_groups,id',
            'status' => 'required|in:1,2'
        ]);

        EmailContact::create($data);

        $notify[] = ['success', 'Email Contact has been created'];
        return back()->withNotify($notify);
    }

    public function emailContactUpdate(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:120',
            'name' => 'required|max:90',
            'email_group_id' => 'required|exists:email_groups,id',
            'status' => 'required|in:1,2'
        ]);

        $contact = EmailContact::whereNull('user_id')->where('id', $request->input('id'))->firstOrFail();
        $contact->update($data);

        $notify[] = ['success', 'Email Contact has been updated'];
        return back()->withNotify($notify);
    }

    public function emailContactDelete(Request $request)
    {
        $contact = EmailContact::whereNull('user_id')->where('id', $request->input('id'))->firstOrFail();
        $contact->delete();

        $notify[] = ['success', 'Email Contact has been deleted'];
        return back()->withNotify($notify);
    }

    public function emailContactImport(Request $request)
    {

        $request->validate([
            'email_group_id' => 'required|exists:email_groups,id',
            'file' => ['required', 'file',new ExtensionCheckRule()],
        ]);

        if(Import::where('group_id', $request->input('email_group_id'))->where('name', request()->file('file')->getClientOriginalName())->where('status', 0)->exists()){
            $notify[] = ['error', 'You Already Uploaded This File!! Please Wait, Your Previous Uploaded File is under Processing'];
            return back()->withNotify($notify);
        }

        $filename = $request->file('file');

        try {
            $upload = uploadNewFile($filename, filePath()['import']['path']);
            $mime = $filename->getClientMimeType();
            $imported = $this->importService->save($this->importService->prepParams($upload,$mime,null,"email", $request->input('email_group_id')));

            ImportJob::dispatch($imported->id);
        } catch (\Exception) {
            $notify[] = ['error', "There's something wrong. Please check your directory permission to 0777 or 0775"];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Email contact data has been imported, it would be sometimes to reload all data.'];
        return back()->withNotify($notify);
    }

    public function emailContactExport()
    {
        $status = true;
        return Excel::download(new EmailContactExport($status), 'email_contact.csv');
    }

    public function emailContactGroupExport($id)
    {
        $status = false;
        $groupId = $id;
        $group = EmailGroup::where('id', $groupId)->firstOrFail();
        return Excel::download(new EmailContactExport($status, $groupId), 'email_group_'.$group->name.'.csv');
    }

    /**
     * @return View
     */
    public function smsContactIndex(): View
    {
        $title = "Manage sms contact list";
        $groups = Group::whereNull('user_id')->get();
        $contacts = Contact::whereNull('user_id')->latest()->with('group')->paginate(paginateNumber());

        return view('admin.phone_book.own_sms_contact', compact('title', 'contacts', 'groups'));
    }

    public function smsContactStore(Request $request)
    {
        $data = $request->validate([
            'contact_no' => 'required|max:50',
            'name' => 'required|max:90',
            'group_id' => 'required|exists:groups,id',
            'status' => 'required|in:1,2'
        ]);

        Contact::create($data);

        $notify[] = ['success', 'SMS contact has been created'];
        return back()->withNotify($notify);
    }

    public function smsContactUpdate(Request $request)
    {
        $data = $request->validate([
            'contact_no' => 'required|max:50',
            'name' => 'required|max:90',
            'group_id' => 'required|exists:groups,id',
            'status' => 'required|in:1,2'
        ]);

        $contact = Contact::whereNull('user_id')
            ->where('id', $request->input('id'))
            ->firstOrFail();

        $contact->update($data);

        $notify[] = ['success', 'SMS contact has been updated'];
        return back()->withNotify($notify);
    }

    public function smsContactDelete(Request $request)
    {   
       
        $contact = Contact::whereNull('user_id')
            ->where('id', $request->input('id'))
            ->firstOrFail();

        $contact->delete();

        $notify[] = ['success', 'SMS contact has been deleted'];
        return back()->withNotify($notify);
    }


    public function smsContactImport(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'file' => ['required', 'file',  new ExtensionCheckRule()],
        ]);

        if(Import::where('group_id', $request->input('group_id'))->where('name', request()->file('file')->getClientOriginalName())->where('status', 0)->exists()){
            $notify[] = ['error', 'You Already Uploaded This File!! Please Wait Fora  While !! Your Previous Uploaded File is under Processing'];
            return back()->withNotify($notify);
        }

        $filename = $request->file('file');
        try {
            $upload = uploadNewFile($filename, filePath()['import']['path']);
            $mime = $filename->getClientMimeType();
            $imported = $this->importService->save($this->importService->prepParams($upload,$mime, null, "sms", $request->input('group_id')));

            ImportJob::dispatch($imported->id);
        } catch (\Exception) {

            $notify[] = ['error', "There's something wrong. Please check your directory permission"];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Contact data has been imported, it would be sometimes to reload all data.'];
        return back()->withNotify($notify);
    }

    public function smsContactExport()
    {
        $status = true;
        return Excel::download(new ContactExport($status), 'sms_contact.csv');
    }

    public function smsContactGroupExport($groupId)
    {
        $status = false;
        $group = Group::where('id', $groupId)->firstOrFail();
        return Excel::download(new ContactExport($status, $groupId), 'sms_contact_'.$group->name.'.csv');
    }
}
