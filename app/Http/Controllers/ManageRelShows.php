<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RelShow;
use App\Models\WebSeries;
use App\Models\Genre;
use App\Models\WebSeriesSeason;

class ManageRelShows extends Controller
{
    public function index($id)    
    {        
        $id = base64_decode($id);
        return view('admin.relshows.index', compact('id'));
    }

    public function getRelShowOrderList($id)
    {   $id = base64_decode($id);
        $this->data['relshows'] = RelShow::whereNull('deleted_at')->where('channel_id',$id)->orderBy('rel_order', 'asc')->get();

        $allRelShows = [];
        $dataForLoop = [];

        foreach ($this->data['relshows'] as $relshow) {
            $allRelShows[] = $relshow->rel_order;
            $dataForLoop[$relshow->rel_order] = $relshow;
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allRelShows'] = $allRelShows;

        return view('admin.relshows.dragdrop', $this->data);
    }

    public function getRelShowList(Request $request, $id){
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
        $totalRecords = RelShow::select('count(*) as allcount')->whereNull('rel_shows.deleted_at')->where('channel_id', $id)->count();
        $inactiveRecords = RelShow::select('count(*) as allcount')->where('status','0')->whereNull('rel_shows.deleted_at')->where('channel_id', $id)->count();
        $activeRecords = RelShow::select('count(*) as allcount')->where('status','1')->whereNull('rel_shows.deleted_at')->where('channel_id', $id)->count();
        $deletedRecords = RelShow::select('count(*) as allcount')->whereNotNull('rel_shows.deleted_at')->where('channel_id', $id)->count();


        $totalRecordswithFilter = RelShow::select('count(*) as allcount')
        ->where('title', 'like', '%' . $searchValue . '%')
        // ->where('channels.status', '=', 1)
        ->whereNull('rel_shows.deleted_at')->where('channel_id', $id)
        ->count();

        // Get records, also we have included search filter as well
        $records = RelShow::orderBy($columnName, $columnSortOrder)
            // ->where('channels.status', '=', 1)
            ->whereNull('rel_shows.deleted_at')
            ->where('rel_shows.title', 'like', '%' . $searchValue . '%')
            ->where('channel_id', $id)
            ->when(request()->has('status') && request()->status !== null, function ($query) {
                    $query->where('rel_shows.status', request()->status);
                })

            // ->orWhere('channels.description', 'like', '%' . $searchValue . '%')
            // ->orWhere('channels.contact_email', 'like', '%' . $searchValue . '%')
            ->select('rel_shows.*')->orderBy('rel_shows.created_at','asc')            
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('kids-shows-update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('kids-shows-update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if($record->deleted_at){
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            }else{
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            $data_arr[] = array(
                "title" => $record->title,                                                            
                "desc" => $record->description,                                                            
                "status" => $status,                
                "image" => '<img src="'.$record->thumbnail.'" width="100px;">',                                                                              
                "created_at" => date('j M Y h:i a',strtotime($record->created_at)),
                "action" => '<div class="action-btn">
                        <a href="'.route("admin.Relshows.edit",base64_encode($record->id)).'" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="'.route("admin.rel_episodes.episodes", base64_encode($record->id)) .'" title="Manage Episodes"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path></svg></a>
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
        $genres = Genre::all();

        return view('admin.relshows.add', compact('id', 'genres'));
    }

    public function updateStatus($id)
    {
        $relshows = RelShow::find(base64_decode($id));

        if ($relshows) {
            $relshows->status = $relshows->status == 1 ? 0 : 1;
            $relshows->save();
            return response()->json(['message' => 'RelShow status updated successfully']);
        }

        return response()->json(['message' => 'Something went wrong!!']);
    }

    public function save(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        if (!empty($request->id)) {
            $relshows = RelShow::find($request->id);
        } else {
            $relshows = new RelShow();
            $relshows->rel_order = $request->rel_order ?? 0;

        }

        // print_r($request->all()); exit;

        $relshows->title = $request->title;
        $relshows->channel_id = $request->channel_id;
        $relshows->thumbnail = $request->thumbnail ?? null;
        
        $relshows->genre = implode(',', $request->genre);
        if (!empty($request->hidden_countries) && is_array($request->hidden_countries)) {
            $relshows->countries = implode(',', $request->hidden_countries);
        } else {
            $relshows->countries = ''; // ya empty string '' rakho agar column string hai
        }
        $relshows->description = $request->description;
        $relshows->status = $request->status;


        if (!empty($request->id)) {
            if ($relshows->save()) {
                return back()->with('message', $request->id ? 'RelShow updated successfully' : 'Relegious Show added successfully');
            } else {
                return back()->with('message', $request->id ? 'RelShow not updated' : 'RelShow not added');
            }
        }
        else{
            if ($relshows->save()) {
                // return redirect()->route('admin.kidshowsseason', base64_encode($relshows->id))->with('message', $request->id ? 'RelShow updated successfully' : 'Relegious Show added successfully');
                return back()->with('message', 'Relegious Show added successfully !');
            } else {
                return back()->with('message', 'RelShow not added');
            }
        }
    }

    public function edit($id)
    {
        $relshow = RelShow::find(base64_decode($id));
        $this->data['relshow'] = $relshow;
        $this->data['id'] = $relshow->channel_id;
        $genres = Genre::all();

        $currentGenres = explode(',', $relshow->genre);
        $this->data['genres'] = $genres;
        $this->data['movieContries'] = explode(',', $relshow->countries);
        $this->data['currentGenres'] = $currentGenres;
        return view('admin.relshows.add', $this->data);
    }

    public function destroy(Request $request)
    {
        $relshows = RelShow::find(base64_decode($request->id));
        $relshows->deleted_at = time();

        if ($relshows->save()) {
            return response()->json(['message' => 'RelShow deleted successfully']);
        } else {
            return response()->json(['message' => 'RelShow not deleted']);
        }
    }

    public function saveRelShowOrder(Request $request)
    {
        $ids = $request->ids;

        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                RelShow::where('id', $id)->update(['rel_order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Relegious Show order updated successfully.');
    }
}
