<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebSeriesSeason;
use App\Models\WebSeriesEpisode;


class WebseriesSeasons extends Controller
{
    public function index(Request $request, $id){
        $id = base64_decode($id);        
        return view('admin.webseriesseason.index', compact('id'));
    }
    public function getWebseriesSeasonsOrderList($id)
    {
        $id = base64_decode($id);
        $this->data['webseriesseasons'] = WebSeriesSeason::whereNull('deleted_at')
            ->where('web_series_id', $id)
            ->orderBy('season_order', 'asc')
            ->get();

        $this->data['id'] = $id;
        $allWebseriesSeason = [];
        $dataForLoop = [];

        foreach ($this->data['webseriesseasons'] as $season) {
            $allWebseriesSeason[] = $season->season_order;
            $dataForLoop[$season->season_order] = $season;
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allWebseriesSeason'] = $allWebseriesSeason;

        return view('admin.webseriesseason.dragdrop', $this->data);
    }

    /* Process ajax request */
    public function getWebseriesSeasonList(Request $request, $id){
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
        $totalRecords = WebSeriesSeason::select('count(*) as allcount')->whereNull('web_series_seasons.deleted_at')->where('web_series_id', $id)->count();
        $inactiveRecords = WebSeriesSeason::select('count(*) as allcount')->where('status','0')->whereNull('web_series_seasons.deleted_at')->where('web_series_id', $id)->count();
        $activeRecords = WebSeriesSeason::select('count(*) as allcount')->where('status','1')->whereNull('web_series_seasons.deleted_at')->where('web_series_id', $id)->count();
        $deletedRecords = WebSeriesSeason::select('count(*) as allcount')->whereNotNull('web_series_seasons.deleted_at')->where('web_series_id', $id)->count();


        $totalRecordswithFilter = WebSeriesSeason::select('count(*) as allcount')
        ->where('Session_Name', 'like', '%' . $searchValue . '%')
        // ->where('channels.status', '=', 1)
        ->whereNull('web_series_seasons.deleted_at')->where('web_series_id', $id)
        ->count();

        // Get records, also we have included search filter as well
        $records = WebSeriesSeason::orderBy($columnName, $columnSortOrder)
            // ->where('channels.status', '=', 1)
            ->whereNull('web_series_seasons.deleted_at')
            ->where('web_series_seasons.Session_Name', 'like', '%' . $searchValue . '%')
            ->where('web_series_id', $id)

            ->when(request()->has('status') && request()->status !== null, function ($query) {
                $query->where('web_series_seasons.status', request()->status);
            })
            ->select('web_series_seasons.*')->orderBy('web_series_seasons.created_at','asc')            
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('web-seies-season/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('web-seies-season/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if($record->deleted_at){
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            }else{
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            $data_arr[] = array(
                "Session_Name" => $record->Session_Name,                                                            
                "banner" => $record->banner ? '<img src="'.$record->banner.'" style="width: 100px;">' :'',                                                            
                "status" => $status,
                "order" => $record->season_order,

                "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
                "action" => '<div class="action-btn">
                        <a href="'.route("edit-webseries-season", base64_encode($record->id)).'" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="'.route("admin.webseries.seasons.episodes", base64_encode($record->id)).'" title="Manage Episodes"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path></svg></a>

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

    public function create(Request $request, $id){                
        $id = base64_decode($id);
        $this->data['id'] = $id;        
        return view('admin.webseriesseason.add',$this->data);
    }

    public function save(Request $request){
        $request->validate([
            'name' => 'required',            
            'order' => 'required',            
            'status'  => 'required',                       
            'banner'  => 'required',                       
        ]); 
        
        
        if(!empty($request->id)){
            $season = WebSeriesSeason::firstwhere('id',$request->id);                        

            $season->Session_Name = $request->name;            
            $season->season_order = $request->order;            
            $season->status = $request->status;            
            $season->banner = $request->banner;            
            $season->web_series_id = $request->webseries_id;            
            
            if($season->save()){                
                return back()->with('message','Season updated successfully');                
            }else{
                return back()->with('message','Season not updated successfully');
            }

        }else{


            $season = new WebSeriesSeason();
            $season->Session_Name = $request->name;            
            $season->season_order = $request->order;            
            $season->status = $request->status; 
            $season->banner = $request->banner;  
            $season->web_series_id = $request->webseries_id;            

            if($season->save()){                                
                // return back()->with('message','Season added successfully');
                return redirect()->route('admin.webseries.seasons.episodes', base64_encode($season->id))->with('message', 'Season added successfully');
            }else{
                return back()->with('message','Season not added successfully');
            }
        }

    }

    public function edit($id){  
        $season = WebSeriesSeason::where('id', base64_decode($id))->first();              
        $this->data['id'] =  $season->web_series_id;
        $this->data['season'] = $season;
        // print_r($season); exit();
        
        return view('admin.webseriesseason.add',$this->data);
    }

    public function destroy(Request $request){
        
        $season = WebSeriesSeason::where('id',base64_decode($request->id))->first();        
        $season->deleted_at = time();
        if($season->save()){
            $episodes = WebSeriesEpisode::where('season_id', $season->id)->get();
            if ($episodes) {
                WebSeriesEpisode::where('season_id', $season->id)->delete();
            }
            echo json_encode(['message','Season deleted successfully']);
        }else{
            echo json_encode(['message','Season not deleted successfully']);
        }
    }

    public function saveWebseriesSeasonsOrder(Request $request)
    {
        $ids = $request->ids;
        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                WebSeriesSeason::where('id', $id)->update(['season_order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Seasons order updated successfully.');
    }
    
    public function updateStatus($id){
        $season = WebSeriesSeason::find(base64_decode($id));        
        if($season){
            $season->status = $season->status == '1' ? '0' : '1';
            $season->save();
            echo json_encode(['message','Season status updated successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }
}
