<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContentNetwork;

class ContentNetworks extends Controller
{
    public function index(Request $request){            
        return view('admin.network.index');
    }

    /* Process ajax request */
    public function getNetworkList(Request $request){
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
        $totalRecords = ContentNetwork::select('count(*) as allcount')->whereNull('networks.deleted_at')->count();
        $inactiveRecords = ContentNetwork::select('count(*) as allcount')->where('status','0')->whereNull('networks.deleted_at')->count();
        $activeRecords = ContentNetwork::select('count(*) as allcount')->where('status','1')->whereNull('networks.deleted_at')->count();
        $deletedRecords = ContentNetwork::select('count(*) as allcount')->whereNotNull('networks.deleted_at')->count();


        $totalRecordswithFilter = ContentNetwork::select('count(*) as allcount')
        ->where('name', 'like', '%' . $searchValue . '%')
        // ->where('channels.status', '=', 1)
        ->whereNull('networks.deleted_at')
        ->count();

        // Get records, also we have included search filter as well
        $records = ContentNetwork::orderBy($columnName, $columnSortOrder)
            // ->where('channels.status', '=', 1)
            ->whereNull('networks.deleted_at')
            ->where('networks.name', 'like', '%' . $searchValue . '%')            

            // ->orWhere('channels.description', 'like', '%' . $searchValue . '%')
            // ->orWhere('channels.contact_email', 'like', '%' . $searchValue . '%')
            ->select('networks.*')->orderBy('networks.updated_at','desc')            
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $key => $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('content-network/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('content-network/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if($record->deleted_at){
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            }else{
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            $type = $record->type == 1 ? 'Premiun' : 'Free';
            $downloadable = $record->downloadable == 1 ? 'Yes' : 'No';

            $data_arr[] = array(                
                "name" => $record->name,                                                            
                "status" => $status,                
                "logo" => '<img src="'.$record->logo.'" width="100px;">',                
                "networks_order" => $record->networks_order,                                
                "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
                "action" => '<div class="action-btn">
                        <a href="#" onclick="editNetwork(\''.base64_encode($record->id).'\')" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>                        
                        <a href="javascript:;" onclick="delete_item(\''.base64_encode($record->id).'\',\'network\')">'.$del_icon.'</a>
                        <a href="'.route('admin.sliders.contentNetwork', base64_encode($record->id)).'" title="Manage Slider"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></a>
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

    public function create(Request $request){                                
        return view('admin.network.add',$this->data);
    }

    public function edit(Request $request){                
        $id = base64_decode($request->id);

        $network = ContentNetwork::where('id', $id)->first();                    
        // return view('admin.webseriesepisode.add',$this->data);

        return response()->json([
            'status' => true,
            'network' => $network,
        ]);
    }

    public function save(Request $request){
        
        $request->validate([
            'networkName' => 'required',            
            'networkLogo' => 'required',            
            'status'  => 'sometimes',                       
            'networOrder'  => 'required',                                   
        ]); 

        if(!empty($request->id)){
            $network = ContentNetwork::firstwhere('id',$request->id);                        

            $network->name = $request->networkName;                                  
            $network->networks_order = $request->networOrder;            
            $network->logo = $request->networkLogo;            
            $network->is_content = $request->has('is_content') ? 1 : 0;
            
            if($network->save()){                
                return back()->with('message','Content Network updated successfully');
            }else{
                return back()->with('message','Content Network not updated successfully');
            }

        }else{
            $network = new ContentNetwork();
            $network->name = $request->networkName;            
            $network->status = $request->status;            
            $network->networks_order = $request->networOrder;            
            $network->logo = $request->networkLogo;            
            $network->is_content = $request->has('is_content') ? 1 : 0;

            

            if($network->save()){                                
                return back()->with('message','Content Network added successfully');
            }else{
                return back()->with('message','Content Network not added successfully');
            }
        }

    }

    public function updateStatus($id){
        $episode = ContentNetwork::find(base64_decode($id));        
        if($episode){
            $episode->status = $episode->status == '1' ? '0' : '1';
            $episode->save();
            echo json_encode(['message','Content Network status updated successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }

    public function destroy(Request $request){
        $id = base64_decode($request->id);
        $episode = ContentNetwork::find($id);
        if($episode){
            $episode->deleted_at = time();
            $episode->save();
            echo json_encode(['message','Content Network deleted successfully']);
        }
        else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }

    public function orderList(Request $request){
        $this->data['content_networks'] = ContentNetwork::whereNull('deleted_at')->orderBy('networks_order', 'asc')->get();
        
        $allContentNetwork = [];
        $dataForLoop = [];

        foreach ($this->data['content_networks'] as $network) {
            $allContentNetwork[] = $network->networks_order;
            $dataForLoop[$network->networks_order] = $network;

        }
        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allContentNetwork'] = $allContentNetwork;

        return view('admin.network.dragdrop', $this->data);
    }

    public function saveOrder(Request $request)
    {
        $ids = $request->ids; 
        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                ContentNetwork::where('id', $id)->update(['networks_order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Networks order updated successfully.');
    }
}
