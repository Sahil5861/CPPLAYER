<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppDomainContent;
use App\Models\Channel;

class AppDomainContentController extends Controller
{
    public function index($admin_id)
    {

        $id = base64_decode($admin_id);
        $channels = Channel::all();
        // $edit = null;
        // // If record exists for this admin, load edit mode
        if ($id) {
            $edit = AppDomainContent::where('admin_id', $id)->first();
        //     if ($edit && $edit->live_channels) {
        //         $edit->live_channels = explode(',', $edit->live_channels);
        //     }
        }

        // print_r($id); exit;
        return view('admin.user.app_domain_content', compact('channels', 'edit', 'id'));
    }

    public function store(Request $request)
    {
        // Try to find an existing record with this admin_id
        $domain = AppDomainContent::where('admin_id', $request->admin_id)->first();

        if ($domain) {
            // If found, update the existing record
            $domain->domain = $request->domain;
            $domain->content = $request->content;
            $domain->logo = $request->logo;
            $domain->app_name = $request->app_name;
            $domain->theme_color = $request->theme_color;
            $domain->live_channels = implode(',', $request->live_channels ?? []);
            $domain->movies = $request->input('movies');
            $domain->webseries = $request->input('webseries');
            $domain->tvshow = $request->input('tvshow');
            $domain->tvshow_pak = $request->input('tvshow_pak');
            $domain->kids_show = $request->input('kids_show');
            $domain->religious = $request->input('religious');
            $domain->sports = $request->input('sports');
            $domain->stage_shows = $request->input('stage_shows');
            $domain->laughter_shows = $request->input('laughter_shows');
            $domain->content_network = $request->input('content_network');
            $domain->search = $request->input('search');
        } else {
            // If not found, create a new record
            $domain = new AppDomainContent();
            $domain->admin_id = $request->admin_id;
            $domain->domain = $request->domain;
            $domain->content = $request->content;
            $domain->logo = $request->logo;
            $domain->app_name = $request->app_name;
            $domain->theme_color = $request->theme_color;
            $domain->live_channels = implode(',', $request->live_channels ?? []);
            $domain->movies = $request->input('movies');
            $domain->webseries = $request->input('webseries');
            $domain->tvshow = $request->input('tvshow');
            $domain->tvshow_pak = $request->input('tvshow_pak');
            $domain->kids_show = $request->input('kids_show');
            $domain->religious = $request->input('religious');
            $domain->sports = $request->input('sports');
            $domain->stage_shows = $request->input('stage_shows');
            $domain->laughter_shows = $request->input('laughter_shows');
            $domain->content_network = $request->input('content_network');
            $domain->search = $request->input('search');
        }

        // Save the record (for both create or update)
        $domain->save();

        return redirect()->back()->with('message', 'App Domain saved successfully');
    }



    public function update(Request $request, $id)
    {
        $domain = AppDomainContent::findOrFail($id);
        $domain->domain = $request->domain;
        $domain->content = $request->content;
        $domain->logo = $request->logo;
        $domain->app_name = $request->app_name;
        $domain->theme_color = $request->theme_color;
        $domain->live_channels = implode(',', $request->live_channels ?? []);
        $domain->movies = $request->has('movies');
        $domain->webseries = $request->has('webseries');
        $domain->tvshow = $request->has('tvshow');
        $domain->tvshow_pak = $request->has('tvshow_pak');
        $domain->kids_show = $request->has('kids_show');
        $domain->religious = $request->has('religious');
        $domain->sports = $request->has('sports');
        $domain->stage_shows = $request->has('stage_shows');
        $domain->laughter_shows = $request->has('laughter_shows');
        $domain->save();

        return redirect()->back()->with('message', 'App Domain updated successfully');
    }
}
