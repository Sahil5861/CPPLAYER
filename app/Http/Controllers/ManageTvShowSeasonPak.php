<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TvShowSeasonPak;

class ManageTvShowSeasonPak extends Controller
{
    public function index($id)    
    {        
        $id = base64_decode($id);
        return view('admin.tvshow_season_pak.index', compact('id'));
    }
    
    public function getShowSeasonsPakOrderList($id)
    {
        $id = base64_decode($id);
        $this->data['tvshowseasons'] = TvShowSeasonPak::whereNull('deleted_at')
            ->where('show_id', $id)
            ->orderBy('season_order', 'asc')
            ->get();

        $this->data['id'] = $id;
        $allTvShowSeasons = [];
        $dataForLoop = [];

        foreach ($this->data['tvshowseasons'] as $season) {
            $allTvShowSeasons[] = $season->season_order;
            $dataForLoop[$season->season_order] = $season;
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allTvShowSeasons'] = $allTvShowSeasons;

        return view('admin.tvshow_season_pak.dragdrop', $this->data);
    }

    public function getTvShowSeasonPakList(Request $request, $id){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column title
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        // Total records
        $totalRecords = TvShowSeasonPak::select('count(*) as allcount')->whereNull('shows_seasons_pak.deleted_at')->where('show_id', $id)->count();
        $inactiveRecords = TvShowSeasonPak::select('count(*) as allcount')->where('status','0')->whereNull('shows_seasons_pak.deleted_at')->where('show_id', $id)->count();
        $activeRecords = TvShowSeasonPak::select('count(*) as allcount')->where('status','1')->whereNull('shows_seasons_pak.deleted_at')->where('show_id', $id)->count();
        $deletedRecords = TvShowSeasonPak::select('count(*) as allcount')->whereNotNull('shows_seasons_pak.deleted_at')->where('show_id', $id)->count();


        $totalRecordswithFilter = TvShowSeasonPak::select('count(*) as allcount')
        ->where('title', 'like', '%' . $searchValue . '%')
        // ->where('channels.status', '=', 1)
        ->whereNull('shows_seasons_pak.deleted_at')->where('show_id', $id)
        ->count();

        // Get records, also we have included search filter as well
        $records = TvShowSeasonPak::orderBy($columnName, $columnSortOrder)
            // ->where('channels.status', '=', 1)
            ->whereNull('shows_seasons_pak.deleted_at')
            ->where('shows_seasons_pak.title', 'like', '%' . $searchValue . '%')
            ->where('show_id', $id)

            ->when(request()->has('status') && request()->status !== null, function ($query) {
                        $query->where('shows_seasons_pak.status', request()->status);
                    }) 

            // ->orWhere('channels.description', 'like', '%' . $searchValue . '%')
            // ->orWhere('channels.contact_email', 'like', '%' . $searchValue . '%')
            ->select('shows_seasons_pak.*')->orderBy('shows_seasons_pak.created_at','asc')            
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('tv-show-season-pak/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('tv-show-season-pak/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if($record->deleted_at){
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            }else{
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            $data_arr[] = array(
                "title" => $record->title,                                                                            
                "status" => $status,                
                "poster" => '<img src="'.$record->poster.'" width="100px;">',                                                                              
                "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
                "action" => '<div class="action-btn">
                                <a href="'.route("editTvShowSeasonpak",base64_encode($record->id)).'" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                                <a href="'.route("admin.tvshowpak.episode", base64_encode($record->id)).'" title="Manage Episodes"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path></svg></a>
                                <a href="javascript:;" onclick="deleteRowModal(\''.base64_encode($record->id).'\')">'.$del_icon.'</a>
                            </div>',
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
            "totalRecords" => number_format($totalRecords),
            "activeRecords" => number_format($activeRecords),
            "inactiveRecords" => number_format($inactiveRecords),
            "deletedRecords" => number_format($deletedRecords),
        );
        echo json_encode($response);
    }

    public function addtvshow(Request $request, $id)
    {
        $id = base64_decode($id);                

        return view('admin.tvshow_season_pak.add', compact('id'));
    }

    public function updateStatus($id)
    {
        $tvshow = TvShowSeasonPak::find(base64_decode($id));

        if ($tvshow) {
            $tvshow->status = $tvshow->status == 1 ? 0 : 1;
            $tvshow->save();
            return response()->json(['message' => 'TvShowSeason status updated successfully']);
        }

        return response()->json(['message' => 'Something went wrong!!']);
    }

    public function add(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        if (!empty($request->id)) {
            $tvshow = TvShowSeasonPak::find($request->id);
        } else {
            $tvshow = new TvShowSeasonPak();
            $tvshow->season_order = $request->season_order ?? 0;
        }

        // print_r($request->all()); exit;

        $tvshow->title = $request->title;
        $tvshow->show_id = $request->show_id;
        $tvshow->poster = $request->banner ?? null;
        $tvshow->release_year = $request->release_year ?? null;
        $tvshow->status = $request->status;

        if (!empty($request->id)) {
            if ($tvshow->save()) {
                return back()->with('message','Season updated successfully');
            } else {
                return back()->with('message','Season not updated');
            }
        }
        else{
            if ($tvshow->save()) {
                return redirect()->route('admin.tvshowpak.episode', base64_encode($tvshow->id))->with('message','Season added successfully');
                // return back()->with('message', $request->id ? 'Season added' : 'Season added');
            } else {
                return back()->with('message', $request->id ? 'Season not added' : 'Season not added');
            }
        }
    }

    public function editTvShowSeason($id)
    {
        $season = TvShowSeasonPak::find(base64_decode($id));
        $this->data['season'] = $season;
        $this->data['id'] = $season->show_id;
        return view('admin.tvshow_season_pak.add', $this->data);
    }

    public function destroy(Request $request)
    {
        $tvshow = TvShowSeasonPak::find(base64_decode($request->id));
        $tvshow->deleted_at = time();

        if ($tvshow->save()) {
            return response()->json(['message' => 'TvShowSeasonPak deleted successfully']);
        } else {
            return response()->json(['message' => 'TvShowSeasonPak not deleted']);
        }
    }

    public function saveTvShowPakSeasonOrders(Request $request)
    {
        $ids = $request->ids;
        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                TvShowSeasonPak::where('id', $id)->update(['season_order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Seasons order updated successfully.');
    }
}
