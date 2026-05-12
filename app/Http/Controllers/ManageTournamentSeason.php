<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TournamentSeason;
use App\Models\WebSeries;
use App\Models\Genre;
use App\Models\WebSeriesSeason;



class ManageTournamentSeason extends Controller
{
    public function index($id)    
    {        
        $id = base64_decode($id);
        return view('admin.tournamentseasons.index', compact('id'));
    }

    

    public function getSportsTournamentSeasonList(Request $request, $id){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        // Total records

        
        $totalRecords = TournamentSeason::select('count(*) as allcount')->whereNull('tournament_seasons.deleted_at')->where('sports_tournament_id', $id)->count();
        $inactiveRecords = TournamentSeason::select('count(*) as allcount')->where('status','0')->whereNull('tournament_seasons.deleted_at')->where('sports_tournament_id', $id)->count();
        $activeRecords = TournamentSeason::select('count(*) as allcount')->where('status','1')->whereNull('tournament_seasons.deleted_at')->where('sports_tournament_id', $id)->count();
        $deletedRecords = TournamentSeason::select('count(*) as allcount')->whereNotNull('tournament_seasons.deleted_at')->where('sports_tournament_id', $id)->count();


        $totalRecordswithFilter = TournamentSeason::select('count(*) as allcount')
        ->where('season_title', 'like', '%' . $searchValue . '%')
        // ->where('channels.status', '=', 1)
        ->whereNull('tournament_seasons.deleted_at')->where('sports_tournament_id', $id)
        ->count();

        // Get records, also we have included search filter as well
        $records = TournamentSeason::orderBy($columnName, $columnSortOrder)
            // ->where('channels.status', '=', 1)
            ->whereNull('tournament_seasons.deleted_at')
            ->where('tournament_seasons.season_title', 'like', '%' . $searchValue . '%')
            ->where('sports_tournament_id', $id)

            ->when(request()->has('status') && request()->status !== null, function ($query) {
                    $query->where('tournament_seasons.status', request()->status);
                })        

            // ->orWhere('channels.description', 'like', '%' . $searchValue . '%')
            // ->orWhere('channels.contact_email', 'like', '%' . $searchValue . '%')
            ->select('tournament_seasons.*')->orderBy('tournament_seasons.created_at','asc')            
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('tournament-season/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('tournament-season/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if($record->deleted_at){
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            }else{
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            $data_arr[] = array(
                "season_title" => $record->season_title,                                                            
                "desc" => $record->description,                                                            
                "status" => $status,                
                "start_date" => $record->start_date,                
                "end_date" => $record->end_date,                
                "logo" => '<img src="'.$record->logo.'" width="100px;">',                                                                              
                "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
                "action" => '<div class="action-btn">
                        <a href="'.route("editsportstournamentseason",base64_encode($record->id)).'" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="'.route("admin.sporttournamentseasonsepisodes", base64_encode($record->id)) .'" title="Manage Seasons"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path></svg></a>
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

    public function add(Request $request, $id)
    {
        $id = base64_decode($id);        

        return view('admin.tournamentseasons.add', compact('id'));
    }

    public function updateStatus($id)
    {
        $tournamentseason = TournamentSeason::find(base64_decode($id));

        if ($tournamentseason) {
            $tournamentseason->status = $tournamentseason->status == 1 ? 0 : 1;
            $tournamentseason->save();
            return response()->json(['message' => 'TournamentSeason status updated successfully']);
        }

        return response()->json(['message' => 'Something went wrong!!']);
    }

    public function save(Request $request)
    {
        $request->validate([
            'season_title' => 'required',
        ]);

        if (!empty($request->id)) {
            $tournamentseason = TournamentSeason::find($request->id);
        } else {
            $tournamentseason = new TournamentSeason();
            $tournamentseason->season_order = $request->season_order ?? 0;
        }

        // print_r($request->all()); exit;

        $tournamentseason->season_title = $request->season_title;
        $tournamentseason->sports_tournament_id  = $request->sports_tournament_id;
        $tournamentseason->logo = $request->logo ?? null;
        $tournamentseason->start_date = $request->start_date ?? null;
        $tournamentseason->end_date = $request->end_date ?? null;
            
        $tournamentseason->description = $request->description;
        $tournamentseason->status = $request->status;

        // print_r($tournamentseason); exit;


        if (!empty($request->id)) {
            if ($tournamentseason->save()) {
                return back()->with('message','TournamentSeason updated successfully');
            } else {
                return back()->with('message','TournamentSeason not updated');
            }
        }
        else{
            if ($tournamentseason->save()) {
                return redirect()->route('admin.sporttournamentseasons', base64_encode($tournamentseason->sports_tournament_id ))->with('message', $request->id ? 'TournamentSeason updated successfully' : 'TournamentSeason added successfully');
            } else {
                return back()->with('message', $request->id ? 'TournamentSeason not updated' : 'TournamentSeason not added');
            }
        }
    }

    public function edit($id)
    {        
        $tournamentseason = TournamentSeason::find(base64_decode($id));   
        // print_r($tournamentseason); exit;     
        $this->data['id'] = $tournamentseason->sports_tournament_id ;                             
        $this->data['tournamentseason'] = $tournamentseason;                             
        return view('admin.tournamentseasons.add', $this->data);
    }

    public function destroy(Request $request)
    {
        $tournamentseason = TournamentSeason::find(base64_decode($request->id));
        $tournamentseason->deleted_at = time();

        if ($tournamentseason->save()) {
            return response()->json(['message' => 'TournamentSeason deleted successfully']);
        } else {
            return response()->json(['message' => 'TournamentSeason not deleted']);
        }
    }

    public function getTournamentSeasonOrderList($id)
    {
        $id = base64_decode($id);
        $this->data['tournamentseasons'] = TournamentSeason::whereNull('deleted_at')
            ->where('sports_tournament_id', $id)
            ->orderBy('season_order', 'asc')
            ->get();

        $this->data['id'] = $id;
        $allTournamentSeasons = [];
        $dataForLoop = [];

        foreach ($this->data['tournamentseasons'] as $season) {
            $allTournamentSeasons[] = $season->season_order;
            $dataForLoop[$season->season_order] = $season;
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allTournamentSeasons'] = $allTournamentSeasons;

        return view('admin.tournamentseasons.dragdrop', $this->data);
    }

    public function savetournamentSeasonOrders(Request $request)
    {
        $ids = $request->ids;
        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                TournamentSeason::where('id', $id)->update(['season_order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Seasons order updated successfully.');
    }
}
