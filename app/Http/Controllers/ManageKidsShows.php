<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KidsShow;
use App\Models\WebSeries;
use App\Models\Genre;
use App\Models\WebSeriesSeason;

class ManageKidsShows extends Controller
{
    public function index($id)    
    {        
        $id = base64_decode($id);
        return view('admin.kidsshows.index', compact('id'));
    }

    public function getKidsShowOrderList($id)
    {   $id = base64_decode($id);
        $this->data['kidsshows'] = KidsShow::whereNull('deleted_at')->where('kid_channel_id',$id)->orderBy('order', 'asc')->get();

        $allKidsShows = [];
        $dataForLoop = [];

        foreach ($this->data['kidsshows'] as $kidsshow) {
            $allKidsShows[] = $kidsshow->order;
            $dataForLoop[$kidsshow->order] = $kidsshow;
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allKidsShows'] = $allKidsShows;

        return view('admin.kidsshows.dragdrop', $this->data);
    }

    public function getKidsShowList(Request $request, $id){
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
        $totalRecords = KidsShow::select('count(*) as allcount')->whereNull('kids_shows.deleted_at')->where('kid_channel_id', $id)->count();
        $inactiveRecords = KidsShow::select('count(*) as allcount')->where('status','0')->whereNull('kids_shows.deleted_at')->where('kid_channel_id', $id)->count();
        $activeRecords = KidsShow::select('count(*) as allcount')->where('status','1')->whereNull('kids_shows.deleted_at')->where('kid_channel_id', $id)->count();
        $deletedRecords = KidsShow::select('count(*) as allcount')->whereNotNull('kids_shows.deleted_at')->where('kid_channel_id', $id)->count();


        $totalRecordswithFilter = KidsShow::select('count(*) as allcount')
        ->where('name', 'like', '%' . $searchValue . '%')
        // ->where('channels.status', '=', 1)
        ->whereNull('kids_shows.deleted_at')->where('kid_channel_id', $id)
        ->count();

        // Get records, also we have included search filter as well
        $records = KidsShow::orderBy($columnName, $columnSortOrder)
            // ->where('channels.status', '=', 1)
            ->whereNull('kids_shows.deleted_at')
            ->where('kids_shows.name', 'like', '%' . $searchValue . '%')
            ->where('kid_channel_id', $id)
            ->when(request()->has('status') && request()->status !== null, function ($query) {
                    $query->where('kids_shows.status', request()->status);
                })

            // ->orWhere('channels.description', 'like', '%' . $searchValue . '%')
            // ->orWhere('channels.contact_email', 'like', '%' . $searchValue . '%')
            ->select('kids_shows.*')->orderBy('kids_shows.created_at','asc')            
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
                "name" => $record->name,                                                            
                "desc" => $record->description,                                                            
                "status" => $status,                
                "image" => '<img src="'.$record->thumbnail.'" width="100px;">',                                                                              
                "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
                "action" => '<div class="action-btn">
                        <a href="'.route("admin.kidsshows.edit",base64_encode($record->id)).'" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="'.route("admin.kidshowsseason", base64_encode($record->id)) .'" title="Manage Seasons"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path></svg></a>
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

        return view('admin.kidsshows.add', compact('id', 'genres'));
    }

    public function updateStatus($id)
    {
        $kidsshows = KidsShow::find(base64_decode($id));

        if ($kidsshows) {
            $kidsshows->status = $kidsshows->status == 1 ? 0 : 1;
            $kidsshows->save();
            return response()->json(['message' => 'KidsShow status updated successfully']);
        }

        return response()->json(['message' => 'Something went wrong!!']);
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        if (!empty($request->id)) {
            $kidsshows = KidsShow::find($request->id);
        } else {
            $kidsshows = new KidsShow();
        }

        // print_r($request->all()); exit;

        $kidsshows->name = $request->name;
        $kidsshows->kid_channel_id = $request->kid_channel_id;
        $kidsshows->thumbnail = $request->thumbnail ?? null;
        
        $kidsshows->genre = implode(',', $request->genre);
        if (!empty($request->hidden_countries) && is_array($request->hidden_countries)) {
            $kidsshows->countries = implode(',', $request->hidden_countries);
        } else {
            $kidsshows->countries = ''; // ya empty string '' rakho agar column string hai
        }
        $kidsshows->description = $request->description;
        $kidsshows->status = $request->status;
        $kidsshows->order = $request->order ?? 0;


        if (!empty($request->id)) {
            if ($kidsshows->save()) {
                return back()->with('message', $request->id ? 'KidsShow updated successfully' : 'KidsShow added successfully');
            } else {
                return back()->with('message', $request->id ? 'KidsShow not updated' : 'KidsShow not added');
            }
        }
        else{
            if ($kidsshows->save()) {
                return redirect()->route('admin.kidshowsseason', base64_encode($kidsshows->id))->with('message', $request->id ? 'KidsShow updated successfully' : 'KidsShow added successfully');
                // return back()->with('message', 'KidsShow added successfully !');
            } else {
                return back()->with('message', 'KidsShow not added');
            }
        }
    }

    public function edit($id)
    {
        $kidsshow = KidsShow::find(base64_decode($id));
        $this->data['kidsshow'] = $kidsshow;
        $this->data['id'] = $kidsshow->kid_channel_id;
        $genres = Genre::all();

        $currentGenres = explode(',', $kidsshow->genre);
        $this->data['genres'] = $genres;
        $this->data['movieContries'] = explode(',', $kidsshow->countries);
        $this->data['currentGenres'] = $currentGenres;
        return view('admin.kidsshows.add', $this->data);
    }

    public function destroy(Request $request)
    {
        $kidsshows = KidsShow::find(base64_decode($request->id));
        $kidsshows->deleted_at = time();

        if ($kidsshows->save()) {
            return response()->json(['message' => 'KidsShow deleted successfully']);
        } else {
            return response()->json(['message' => 'KidsShow not deleted']);
        }
    }


    public function saveKidsShowOrder(Request $request)
    {
        $ids = $request->ids;

        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                KidsShow::where('id', $id)->update(['order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Kids Show order updated successfully.');
    }
}
