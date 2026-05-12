<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SportsTournament;
use App\Models\Language;

class ManageSportsTournament extends Controller
{
    public function index($id)
    {
        $id = base64_decode($id);
        return view('admin.sportsctournament.index', compact('id'));
    }

    public function getSportsTournamentList(Request $request, $id)
{
    $draw = $request->get('draw');
    $start = $request->get("start");
    $rowperpage = $request->get("length");

    $columnIndex_arr = $request->get('order');
    $columnName_arr = $request->get('columns');
    $order_arr = $request->get('order');
    $search_arr = $request->get('search');

    $columnIndex = $columnIndex_arr[0]['column'];
    $columnName = $columnName_arr[$columnIndex]['data'];
    $columnSortOrder = $order_arr[0]['dir'];
    $searchValue = $search_arr['value'];

    // Record counts
    $totalRecords = SportsTournament::whereNull('sports_tournaments.deleted_at')
        ->where('sports_tournaments.sports_category_id', $id)
        ->count();

    $inactiveRecords = SportsTournament::whereNull('sports_tournaments.deleted_at')
        ->where('sports_tournaments.sports_category_id', $id)
        ->where('status', 0)
        ->count();

    $activeRecords = SportsTournament::whereNull('sports_tournaments.deleted_at')
        ->where('sports_tournaments.sports_category_id', $id)
        ->where('status', 1)
        ->count();

    $deletedRecords = SportsTournament::whereNotNull('sports_tournaments.deleted_at')
        ->where('sports_tournaments.sports_category_id', $id)
        ->count();

    $query = SportsTournament::where('sports_category_id', $id)
        ->whereNull('deleted_at');

    if (!empty($searchValue)) {
        $query->where(function ($q) use ($searchValue) {
            $q->where('id', 'like', '%' . $searchValue . '%')
              ->orWhere('title', 'like', '%' . $searchValue . '%');
        });
    }

    $totalRecordswithFilter = $query->count();

    $records = $query->orderBy($columnName, $columnSortOrder)
        ->when(request()->has('status') && request()->status !== null, function ($query) {
                    $query->where('status', request()->status);
                })
        ->skip($start)
        ->take($rowperpage)
        ->get();

    $data_arr = [];

    foreach ($records as $key => $record) {
        $statusUrl = url('tournament/update-status', base64_encode($record->id));
        $checked = $record->status == 1 ? 'checked' : '';

        $status = '<a onchange="updateStatus(\'' . $statusUrl . '\')" href="javascript:void(0);">
            <label class="switch s-primary mr-2">
                <input type="checkbox" value="' . $record->status . '" ' . $checked . ' id="accountSwitch' . $record->id . '">
                <span class="slider round"></span>
            </label>
        </a>';

        if($record->deleted_at){
            $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
        }else{
            $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
        }

        $data_arr[] = array(
            'id' => $key + 1,
            "title" => $record->title,
            "logo" => $record->logo ?? '',
            "description" => $record->description ?? '',
            "status" => $status,
            "created_at" => date('j M Y h:i a', strtotime($record->created_at)),
            "action" => '<div class="action-btn">
                        <a href="'.route("editsportstournament",base64_encode($record->id)).'" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="'. route("admin.sporttournamentseasons",base64_encode($record->id)).'" title="Manage Seasons"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path></svg></a>
                        <a href="javascript:;" onclick="deleteRowModal(\''.base64_encode($record->id).'\')">'.$del_icon.'</a>
                      </div>'
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

    

    public function add($id)
    {
        $id = base64_decode($id);
        return view('admin.sportsctournament.add', compact('id'));
    }

    public function updateStatus($id)
    {
        $tournament = SportsTournament::find(base64_decode($id));

        if ($tournament) {
            $tournament->status = $tournament->status == 1 ? 0 : 1;
            $tournament->save();
            return response()->json(['message' => 'SportsTournament status updated successfully']);
        }

        return response()->json(['message' => 'Something went wrong!!']);
    }

    public function save(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        if (!empty($request->id)) {
            $tournament = SportsTournament::find($request->id);
        } else {
            $tournament = new SportsTournament();
            $tournament->sports_cat_order  = $request->sports_cat_order ?? 0;

        }

        $tournament->title = $request->title;        
        $tournament->logo = $request->logo;        
        $tournament->sports_category_id  = $request->sport_id;        
        $tournament->description = $request->description ?? null;
        if (!empty($request->hidden_countries) && is_array($request->hidden_countries)) {
            $tournament->countries = implode(',', $request->hidden_countries);
        } else {
            $tournament->countries = ''; // ya empty string '' rakho agar column string hai
        }
        $tournament->status = $request->status;

        // print_r($tournament); exit;

        

        if (!empty($request->id)) {
            if ($tournament->save()) {
                return back()->with('message', 'Tournament updated Successfully !');
            }
            else{
                return back()->with('message', 'Tournament not updated Successfully !');
            }
            
        } else {
            if ($tournament->save()) {
                // return redirect()->route('admin.tvshow', base64_encode($id))->with('message', 'Tournament added Successfully !');
                return redirect()->route('admin.sporttournament', base64_encode($request->sport_id))->with('message', 'Tournament added Successfully !');
            }
            else{
                return back()->with('message', 'Tournament not added Successfully !');
            }
        }
        
    }

    public function edit($id)
    {
        $tournament = SportsTournament::find(base64_decode($id));        
        $this->data['tournament'] = $tournament;
        $this->data['id'] = $tournament->sports_category_id;
        $this->data['movieContries'] = explode(',', $tournament->countries);
        return view('admin.sportsctournament.add', $this->data);
    }

    public function destroy(Request $request)
    {
        $tournament = SportsTournament::find(base64_decode($request->id));
        $tournament->deleted_at = time();

        if ($tournament->save()) {
            return response()->json(['message' => 'SportsTournament deleted successfully']);
        } else {
            return response()->json(['message' => 'SportsTournament not deleted']);
        }
    }

    public function getSportsTournamentOrderList($id)
    {   $id = base64_decode($id);
        $this->data['sportstournament'] = SportsTournament::whereNull('deleted_at')->where('sports_category_id',$id)->orderBy('sports_cat_order', 'asc')->get();

        $allsportstournament = [];
        $dataForLoop = [];

        foreach ($this->data['sportstournament'] as $sportstournament) {
            $allsportstournament[] = $sportstournament->sports_cat_order;
            $dataForLoop[$sportstournament->sports_cat_order] = $sportstournament;
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allsportstournament'] = $allsportstournament;

        return view('admin.sportsctournament.dragdrop', $this->data);
    }

    public function saveSportsTournamentOrder(Request $request)
    {
        $ids = $request->ids;

        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                SportsTournament::where('id', $id)->update(['sports_cat_order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Sports Tournament order updated successfully.');
    }
}
