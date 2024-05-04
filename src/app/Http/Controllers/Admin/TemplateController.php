<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function userTemplate() {

        $view = 'admin_view';
    	$title = "User Template List";
        $userTemplates = Template::whereNotNull('user_id')->paginate(paginateNumber());
    	return view('admin.template.tabs.user', compact('title','view', 'userTemplates'));
    }
    public function adminTemplate() {
		
        $view = 'admin_view';
    	$title = "Admin Template List";
    	$templates = Template::whereNull('user_id')->paginate(paginateNumber());
    	return view('admin.template.tabs.admin', compact('title', 'templates','view'));
    }

    public function store(Request $request)
    {
    	$request->validate([
    		'name' => 'required|max:255',
    		'message' => 'required',
    	]);

        $message = '';
    	Template::create([
			'name' => $request->input('name'),
			'message' => offensiveMsgBlock($request->input('message')),
		]);

        if (offensiveMsgBlock($request->input('message')) != $request->input('message') ){
            $message = session()->get('offsensiveNotify') ;
        }

    	$notify[] = ['success', 'Template has been created'.$message];
    	return back()->withNotify($notify);
    }

    public function update(Request $request)
    {
        $message = '';
    	$request->validate([
    		'name' => 'required|max:255',
    		'message' => 'required',
    	]);

    	$template = Template::whereNull('user_id')->where('id', $request->input('id'))->firstOrFail();
    	$template->update([
			'name' => $request->input('name'),
			'message' => offensiveMsgBlock($request->input('message')),
		]);

        if (offensiveMsgBlock($request->input('message')) != $request->input('message') ){
            $message = session()->get('offsensiveNotify') ;
        }

    	$notify[] = ['success','Template has been updated'.$message];
    	return back()->withNotify($notify);
    }

    public function delete(Request $request)
    {
    	$template = Template::where('id', $request->input('id'))->firstOrFail();
        $template->delete();

    	$notify[] = ['success', 'Template has been deleted'];
    	return back()->withNotify($notify);
    }


    /**
     * @return View
     */
    public function userIndex(): View
    {
    	$title = "Manage User Template List";
		$view = 'user_view';

    	$templates = Template::whereNotNull('user_id')->paginate(paginateNumber());
    	return view('admin.template.index', compact('title', 'templates', 'view'));
    }

    public function updateStatus(Request $request)
	{
		$request->validate([
			'id' => 'required|exists:templates,id',
			'status' => 'required|in:1,2,3'
		]);

		$template = Template::where('id', $request->input('id'))->first();
		$template->status = $request->input('status');
		$template->save();

		$notify[] = ['success', 'Status Updated Successfully'];
    	return back()->withNotify($notify);
	}


}
