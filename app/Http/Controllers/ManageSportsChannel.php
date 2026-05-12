<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SportsCategory;
use App\Models\Language;

class ManageSportsChannel extends Controller
{
    public function index()
    {
        return view('admin.sportschannel.index');
    }

    public function getSportsCategoryList(Request $request){
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
        $totalRecords = SportsCategory::select('count(*) as allcount')->whereNull('sports_categories.deleted_at')->count();
        $inactiveRecords = SportsCategory::select('count(*) as allcount')->whereNull('sports_categories.deleted_at')->count();
        $activeRecords = SportsCategory::select('count(*) as allcount')->whereNull('sports_categories.deleted_at')->count();
        $deletedRecords = SportsCategory::select('count(*) as allcount')->whereNotNull('sports_categories.deleted_at')->count();


        $totalRecordswithFilter = SportsCategory::select('count(*) as allcount')
        ->where('title', 'like', '%' . $searchValue . '%')
        // ->where('channels.status', '=', 1)
        ->whereNull('sports_categories.deleted_at')
        ->count();

        // Get records, also we have included search filter as well
        $records = SportsCategory::orderBy($columnName, $columnSortOrder)
            // ->where('channels.status', '=', 1)
            ->whereNull('sports_categories.deleted_at')
            ->where('sports_categories.title', 'like', '%' . $searchValue . '%')                        
            ->select('sports_categories.*')->orderBy('sports_categories.created_at','asc')            
            ->when(request()->has('status') && request()->status !== null, function ($query) {
                    $query->where('sports_categories.status', request()->status);
                })
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('sports-category/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('sports-category/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if($record->deleted_at){
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            }else{
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            $data_arr[] = array(
                "title" => $record->title,                                                                                                                                                                                                                                    
                "status" => $status,                                                                                                                                                                                                                                    
                "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
                "action" => '<div class="action-btn">
                        <a href="'.route("editsportscategory",base64_encode($record->id)).'" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="sports-tournament/'.base64_encode($record->id).'" title="Manage Tournaments"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path></svg></a>
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

    public function add()
    {        
        return view('admin.sportschannel.add');
    }

    public function updateStatus($id)
    {
        $sports_cat = SportsCategory::find(base64_decode($id));

        if ($sports_cat) {
            $sports_cat->status = $sports_cat->status == 1 ? 0 : 1;
            $sports_cat->save();
            return response()->json(['message' => 'SportsCategory status updated successfully']);
        }

        return response()->json(['message' => 'Something went wrong!!']);
    }

    public function save(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        if (!empty($request->id)) {
            $sport_cat = SportsCategory::find($request->id);
        } else {
            $sport_cat = new SportsCategory();
        }

        $sport_cat->title = $request->title;        
        $sport_cat->status = $request->status;

        

        if (!empty($request->id)) {
            if ($sport_cat->save()) {
                return back()->with('message', 'Sport Category updated Successfully !');
            }
            else{
                return back()->with('message', 'Sport Category not updated Successfully !');
            }
            
        } else {
            if ($sport_cat->save()) {
                return redirect()->route('admin.sportscategory')->with('message', 'Sport Category added Successfully !');
            }
            else{
                return back()->with('message', 'Sport Category not added Successfully !');
            }
        }
        
    }

    public function edit($id)
    {
        $this->data['sportcategory'] = SportsCategory::find(base64_decode($id));        
        return view('admin.sportschannel.add', $this->data);
    }

    public function destroy(Request $request)
    {
        $tvchannel = SportsCategory::find(base64_decode($request->id));
        $tvchannel->deleted_at = time();

        if ($tvchannel->save()) {
            return response()->json(['message' => 'SportsCategory deleted successfully']);
        } else {
            return response()->json(['message' => 'SportsCategory not deleted']);
        }
    }
}
