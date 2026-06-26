<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(): View
    {
        $groups = Group::orderBy('_id')->get();

        return view('admin.groups.index', compact('groups'));
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $request->validate([
            'title'           => ['required', 'string', 'max:50'],
            'user_title'      => ['nullable', 'string', 'max:50'],
            'post_replies'    => ['nullable', 'boolean'],
            'post_topics'     => ['nullable', 'boolean'],
            'edit_posts'      => ['nullable', 'boolean'],
            'delete_posts'    => ['nullable', 'boolean'],
            'delete_topics'   => ['nullable', 'boolean'],
            'set_title'       => ['nullable', 'boolean'],
            'search'          => ['nullable', 'boolean'],
            'search_users'    => ['nullable', 'boolean'],
            'send_email'      => ['nullable', 'boolean'],
            'post_flood'      => ['nullable', 'integer', 'min:0'],
            'search_flood'    => ['nullable', 'integer', 'min:0'],
            'email_flood'     => ['nullable', 'integer', 'min:0'],
            'report_flood'    => ['nullable', 'integer', 'min:0'],
        ]);

        $group->fill([
            'title'          => $request->input('title'),
            'user_title'     => $request->input('user_title'),
            'post_replies'   => $request->boolean('post_replies'),
            'post_topics'    => $request->boolean('post_topics'),
            'edit_posts'     => $request->boolean('edit_posts'),
            'delete_posts'   => $request->boolean('delete_posts'),
            'delete_topics'  => $request->boolean('delete_topics'),
            'set_title'      => $request->boolean('set_title'),
            'search'         => $request->boolean('search'),
            'search_users'   => $request->boolean('search_users'),
            'send_email'     => $request->boolean('send_email'),
            'post_flood'     => (int) $request->input('post_flood', 0),
            'search_flood'   => (int) $request->input('search_flood', 0),
            'email_flood'    => (int) $request->input('email_flood', 0),
            'report_flood'   => (int) $request->input('report_flood', 0),
        ]);
        $group->save();

        return redirect()->route('admin.groups.index')->with('success', 'Group updated.');
    }
}
