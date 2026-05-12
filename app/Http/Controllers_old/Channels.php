<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use App\Models\Genre;
use App\Models\Language;
use App\Models\ChannelGenre;

class Channels extends Controller
{
    public function index()
    {
        return view('admin.channel.index');
    }

    public function deletedChannel(){
        return view('admin.channel.deleted');
    }

    public function getChannelOrderList()
    {
        $this->data['channels'] = Channel::whereNull('deleted_at')->with('language')->orderBy('channel_number','asc')->get();
        // print_r($this->data['channels']); exit();
        $lockedChannels = [];
        $allChannels = [];
        // $lastChannel = $this->data['channels'][count($this->data['channels']) - 1]->channel_number;

        $lastChannel = null;

        if (!empty($this->data['channels']) && is_array($this->data['channels'])) {
            $lastItem = end($this->data['channels']);
            $lastChannel = $lastItem->channel_number ?? null;
        }

        $dataForLoop = [];
        for ($i=1; $i < $lastChannel; $i++) { 
            # code...
            $dataForLoop[$i] = "";
        }
        foreach ($this->data['channels'] as $key => $value) {
            # code...
            $allChannels[] = $value->channel_number;
            $dataForLoop[$value->channel_number] = $value;
            if($value->position_locked == 1){
                $lockedChannels[] = $value->channel_number;
            }
        }
        // print_r($dataForLoop); exit;
        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allChannels'] = $allChannels;
        $this->data['lockedChannels'] = $lockedChannels;
        return view('admin.channel.dragdrop',$this->data);
    }

    /* Process ajax request */
    public function getChannelList(Request $request)
    {
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
        $totalRecords = Channel::select('count(*) as allcount')->whereNull('channels.deleted_at')->count();
        $inactiveRecords = Channel::select('count(*) as allcount')->where('status','0')->whereNull('channels.deleted_at')->count();
        $activeRecords = Channel::select('count(*) as allcount')->where('status','1')->whereNull('channels.deleted_at')->count();
        $deletedRecords = Channel::select('count(*) as allcount')->whereNotNull('channels.deleted_at')->count();


        $totalRecordswithFilter = Channel::select('count(*) as allcount')
        ->where('channel_name', 'like', '%' . $searchValue . '%')
        // ->where('channels.status', '=', 1)
        ->whereNull('channels.deleted_at')
        ->orWhere(function($query) use ($searchValue)
        {
            $query->where('channels.channel_number', 'like', '%' . $searchValue . '%')
            ->whereNull('channels.deleted_at');
        })

        ->orWhere(function($query)  use ($searchValue)
        {
            $query->Where('channels.channel_language', 'like', '%' . $searchValue . '%')
            ->whereNull('channels.deleted_at');
        })
        ->count();

        // Get records, also we have included search filter as well
        $records = Channel::orderBy($columnName, $columnSortOrder)
            // ->where('channels.status', '=', 1)
            ->whereNull('channels.deleted_at')
            ->where('channels.channel_name', 'like', '%' . $searchValue . '%')

            ->orWhere(function($query) use ($searchValue)
            {
                $query->where('channels.channel_number', 'like', '%' . $searchValue . '%')
                      ->whereNull('channels.deleted_at');
            })

            ->orWhere(function($query)  use ($searchValue)
            {
                $query->Where('channels.channel_language', 'like', '%' . $searchValue . '%')
                      ->whereNull('channels.deleted_at');
            })


            // ->orWhere('channels.description', 'like', '%' . $searchValue . '%')
            // ->orWhere('channels.contact_email', 'like', '%' . $searchValue . '%')
            ->select('channels.*')->with('language')
            ->orderBy('channels.updated_at','desc')
            // ->leftJoin('channels', 'channels.id', '=', 'Channel.Channel_id')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('channel/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('channel/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if($record->deleted_at){
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            }else{
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            $data_arr[] = array(
                "channel_name" => $record->channel_name,
                "channel_number" => $record->channel_number,
                "channel_logo" => $record->channel_logo,
                // "channel_genre" => $record->channel_genre,
                "channel_language" => $record->language ? $record->language->title : '',
                "channel_link" => '<a class="btn btn-primary mb-3 rounded bs-tooltip" data-toggle="tooltip" title="Click to open link" href="'.$record->channel_link.'" target="_blank" >Link</a>',
                "status" => $status,

                "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
                "action" => '<div class="action-btn">
                        <a  href="edit-channel/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="javascript:;" onclick="delete_item(\''.base64_encode($record->id).'\',\'channel\')">'.$del_icon.'</a>
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

    public function getDeletedChannelList(Request $request){
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
        $totalRecords = Channel::select('count(*) as allcount')->whereNull('channels.deleted_at')->count();
        $inactiveRecords = Channel::select('count(*) as allcount')->where('status','0')->whereNull('channels.deleted_at')->count();
        $activeRecords = Channel::select('count(*) as allcount')->where('status','1')->whereNull('channels.deleted_at')->count();
        $deletedRecords = Channel::select('count(*) as allcount')->whereNotNull('channels.deleted_at')->count();


        $totalRecordswithFilter = Channel::select('count(*) as allcount')
        ->where('channel_name', 'like', '%' . $searchValue . '%')
        // ->where('channels.status', '=', 1)
        ->whereNotNull('channels.deleted_at')
        ->orWhere(function($query) use ($searchValue)
        {
            $query->where('channels.channel_number', 'like', '%' . $searchValue . '%')
            ->whereNotNull('channels.deleted_at');
        })

        ->orWhere(function($query)  use ($searchValue)
        {
            $query->Where('channels.channel_language', 'like', '%' . $searchValue . '%')
            ->whereNotNull('channels.deleted_at');
        })
        ->count();

        // Get records, also we have included search filter as well
        $records = Channel::orderBy($columnName, $columnSortOrder)
            // ->where('channels.status', '=', 1)
            ->whereNotNull('channels.deleted_at')
            ->where('channels.channel_name', 'like', '%' . $searchValue . '%')

            ->orWhere(function($query) use ($searchValue)
            {
                $query->where('channels.channel_number', 'like', '%' . $searchValue . '%')
                      ->whereNotNull('channels.deleted_at');
            })

            ->orWhere(function($query)  use ($searchValue)
            {
                $query->Where('channels.channel_language', 'like', '%' . $searchValue . '%')
                      ->whereNotNull('channels.deleted_at');
            })


            // ->orWhere('channels.description', 'like', '%' . $searchValue . '%')
            // ->orWhere('channels.contact_email', 'like', '%' . $searchValue . '%')
            ->select('channels.*')->with('language')
            // ->leftJoin('channels', 'channels.id', '=', 'Channel.Channel_id')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('channel/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('channel/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if($record->deleted_at){
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            }else{
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            // <a href="'.route('admin.channel.recoverChannel',base64_encode($record->id)).'" data-toggle="tooltip" title="Undo Channel" class="undo-channel">'.$del_icon.'</a>

            $data_arr[] = array(
                "channel_name" => $record->channel_name,
                "channel_number" => $record->channel_number,
                "channel_logo" => $record->channel_logo,
                // "channel_genre" => $record->channel_genre,
                "channel_language" => $record->language ? $record->language->title : '',
                "channel_link" => '<a class="btn btn-primary mb-3 rounded bs-tooltip" data-toggle="tooltip" title="Click to open link" href="'.$record->channel_link.'" target="_blank" >Link</a>',
                "status" => $status,

                "created_at" => date('j M Y h:i a',strtotime($record->created_at)),
                "action" => '<div class="action-btn">

                        <a data-toggle="tooltip" title="Undo Channel" onclick="undoChannel('.$record->id.')">'.$del_icon.'</a>
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

    public function recoverChannel($id){
        $id = base64_decode($id);
        $update = Channel::where('id',$id)->update(['deleted_at'=>null]);
        if($update){
            return back()->with('message','Channel recover successfully');
        }else{
            return back()->with('message','Something went wrong');
        }
    }

    public function addChannel(){
        $this->data['languages'] = Language::where('status',1)->get();
        $this->data['genres'] = Genre::where('status',1)->get();
        return view('admin.channel.add',$this->data);
    }


    public function add(Request $request){
        $request->validate([
            'channel_name' => 'required',            
            'channel_number' => 'required',            
            'channel_genre' => 'required',
            'stream_type' => 'required',
            'channel_link' => 'required',
        ]);
        
        if(!empty($request->id)){

            $channelNumberExists = Channel::where('channel_number',$request->channel_number)->where('id','!=',$request->id)->first();
            $channelNameExists = Genre::where('title',$request->channel_name)->first();
            // if($channelNumberExists){
            //     return back()->with('message','This channel number is already exists.');
            // }
            if($channelNameExists){
                return back()->with('message','This channel name is not available.');
            }

            // if ($request->hasFile('channel_logo')) {
            //     $file = $request->file('channel_logo');
            //     $imageName=time().uniqid().$file->getClientOriginalName();
            //     $filePath = 'images/channel/' . $imageName;
            //     \Storage::disk('public')->put($filePath, file_get_contents($file));
            //     $channel_logo = $filePath;
            // }else{
            //     $channel_logo = $request->channel_logo_old;
            // }

            // if ($request->hasFile('channel_bg')) {
            //     $file = $request->file('channel_bg');
            //     $imageName=time().uniqid().$file->getClientOriginalName();
            //     $filePath = 'images/channel/' . $imageName;
            //     \Storage::disk('public')->put($filePath, file_get_contents($file));
            //     $channel_bg = $filePath;
            // }else{
            //     $channel_bg = $request->channel_logo_old;
            // }
            // echo $request->channel_description; exit;
            $channel = Channel::firstwhere('id',$request->id);
            $channel->channel_name = $request->channel_name;
            $channel->channel_number = $request->channel_number;

            $channel->channel_logo = $request->channel_logo;
            $channel->channel_bg = $request->channel_bg;
            $channel->channel_description = $request->channel_description;
            $channel->channel_language = $request->channel_language ?? null;
            $channel->channel_link = $request->channel_link;
            $channel->stream_type = $request->stream_type;
            $channel->genres = implode(',', $request->channel_genre);
            $channel->status = $request->status;
            if($channel->save()){                
                return back()->with('message', 'Channel updated successfully');
            }else{
                return back()->with('message', 'Channel not updated successfully');
            }

        }else{

            $channelNumberExists = Channel::where('channel_number',$request->channel_number)->where('deleted_at', null)->first();
            $channelNameExists = Genre::where('title',$request->channel_name)->first();
            if($channelNumberExists){
                return back()->with('error','This channel number is already exists.');
            }
            if($channelNameExists){
                return back()->with('error','This channel name is not available.');
            }

            

            // if ($request->hasFile('channel_logo')) {
            //     $file = $request->file('channel_logo');
            //     $imageName=time().uniqid().$file->getClientOriginalName();
            //     $filePath = 'images/channel/' . $imageName;
            //     \Storage::disk('public')->put($filePath, file_get_contents($file));
            //     $channel_logo = $filePath;
            // }else{
            //     $channel_logo = '';
            // }

            // if ($request->hasFile('channel_bg')) {
            //     $file = $request->file('channel_bg');
            //     $imageName=time().uniqid().$file->getClientOriginalName();
            //     $filePath = 'images/channel/' . $imageName;
            //     \Storage::disk('public')->put($filePath, file_get_contents($file));
            //     $channel_bg = $filePath;
            // }else{
            //     $channel_bg = '';
            // }

            $channel = new Channel();
            $channel->channel_number = $request->channel_number;
            $channel->channel_name = $request->channel_name;
            $channel->channel_logo = $request->channel_logo;
            $channel->channel_bg = $request->channel_bg;  
            $channel->channel_description = $request->channel_description;
            $channel->channel_language = $request->channel_language ?? null;
            $channel->stream_type = $request->stream_type;
            $channel->channel_link = $request->channel_link;
            $channel->status = $request->status;
            $channel->genres = implode(',', $request->channel_genre);
            // print_r($channel); exit();
            if($channel->save()){
                $channelCount = Channel::whereNull('deleted_at')->where('status',1)->orderBy('channel_index','desc')->first();                
                $channel->channel_index = $channelCount ? $channelCount->channel_index + 1 : 1;
                $channel->save();                
                return redirect()->route('addChannel')->with('message', 'Channel Added successfully');
            }else{
                return redirect()->route('addChannel')->with('message', 'Channel not Added successfully');
            }
        }

    }

    public function editChannel($id){
        $channel = Channel::where('id',base64_decode($id))->first();
        $this->data['channel'] = $channel;
        $this->data['languages'] = Language::where('status',1)->get();
        $this->data['genres'] = Genre::where('status',1)->get();
        $this->data['channelGenre'] = explode(',', $channel->genres);                        
        return view('admin.channel.add',$this->data);
    }


    public function destroy(Request $request){
        $channel = Channel::where('id',base64_decode($request->id))->first();
        // $channel->deleted_at = time();
        if($channel){
            $channel->delete();
            echo json_encode(['message','Channel deleted successfully']);
        }else{
            echo json_encode(['message','Channel not deleted successfully']);
        }
    }

    public function saveChannelOrders(Request $request)
    {
        // code...
        // echo "<pre>"; print_r($request->all()); die;
        $data = $request->numbers;
        $lockedChannels = json_decode($request->lockedChannels);
        $channel_no = $request->start_no;
        $new_channel_no = $request->new_channel_no;
        // echo "<br>";
        $old_channel_no = $request->old_channel_no;
        $detect_0 = 0;
        $skip = 1;

        if($channel_no == '0'){
            foreach ($data as $key => $item) {
                # code...
                Channel::where('id',$item)->update(["channel_number"=> $key+1, "position_locked"=>$request->position_locked[$key]]);
            }
            return back()->with('message','Channel added successfully');
        }

        $sd = Channel::where('channel_number',$new_channel_no)->first();
        if(!$sd && $old_channel_no != '0'){
            // echo "khjghdhfjgkhl;io'"; exit;
            Channel::where('channel_number',$old_channel_no)->update(["channel_number"=> $new_channel_no]);
            return back()->with('message','Channel added successfully');
        }
        // echo $request->checkOrder; exit;
        if($request->checkOrder == 'default'){
            foreach ($data as $key => $item) {
                $noUpdate = 0;
                if($key + 1 >= $channel_no && $detect_0 === 0){
                    // if(isset($data[$key + 1])){
                    //     $_no = Channel::where('id',$data[$key + 1])->first();
                    //     // print_r($_no); exit;
                    //     if($_no){
                    //         if(in_array($_no->channel_number, $lockedChannels)){
                    //             $skip++;
                    //             // $noUpdate = 1;
                    //         }
                    //     }
                    // }
                    if(in_array($key+1, $lockedChannels)){
                        $skip++;
                        // $noUpdate = 1;
                    }

                    $_no2 = Channel::where('id',$item)->first();
                    // print_r($_no2); exit;
                    if($_no2){
                        if(in_array($_no2->channel_number, $lockedChannels)){
                            // $skip++;
                            $noUpdate = 1;
                        }
                    }
                    // echo $skip.'<br>';
                    $number = $key + $skip;
                    if($item == 0){
                        $detect_0 = 1;
                        break;
                    }
                    // echo $item.'-'.($key + 1).'-'.$detect_0.'<br>';
                    // echo $item;
                    // exit;    
                    if($noUpdate == 0){
                        Channel::where('id',$item)->update(["channel_number"=> $number, "position_locked"=>$request->position_locked[$key]]);
                    }
                    // $channel = Channel::where('channel_number',$item)->first();
                    // $channel->channel_number = $key + 1;
                    // $channel->position_locked = $request->position_locked[$key];
                    // $channel->save();
                }
            }
        }else{
            foreach ($data as $key => $item) {
                $noUpdate = 0;
                if($key + 1 >= $request->checkOrder && $key + 1 <= $channel_no){
                    // if(isset($data[$key + 1])){
                    //     $_no = Channel::where('id',$data[$key + 1])->first();
                    //     // print_r($_no); exit;
                    //     if($_no){
                    //         if(in_array($_no->channel_number, $lockedChannels)){
                    //             $skip++;
                    //             // $noUpdate = 1;
                    //         }
                    //     }
                    // }

                    $_no2 = Channel::where('id',$item)->first();
                    // print_r($_no2); exit;
                    if($_no2){
                        if(in_array($_no2->channel_number, $lockedChannels)){
                            $skip++;
                            $noUpdate = 1;
                        }
                    }
                    // echo $skip.'<br>';
                    $number = $key + $skip;
                    // if($item == 0){
                    //     $detect_0 = 1;
                    //     break;
                    // }
                    // echo $item.'-'.($key + 1).'-'.$detect_0.'<br>';
                    // echo $item;
                    // exit;    
                    if($noUpdate == 0){
                        Channel::where('id',$item)->update(["channel_number"=> $number, "position_locked"=>$request->position_locked[$key]]);
                    }
                    // $channel = Channel::where('channel_number',$item)->first();
                    // $channel->channel_number = $key + 1;
                    // $channel->position_locked = $request->position_locked[$key];
                    // $channel->save();
                }
            }
        }
        return back()->with('message','Channel added successfully');
    }

    public function updateStatus($id){
        $channel = Channel::find(base64_decode($id));
        if($channel){
            $channel->status = $channel->status == '1' ? '0' : '1';
            $channel->save();
            echo json_encode(['message','Channel status updated successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }

    public function checkChannelName(Request $request){
        $channel = $request->channel_name;
        $query = Genre::where('title', $channel)->first();
        if($query){
            return false;
        }else{
            return true;
        }
    }

    public function checkChannelNumber(Request $request){
        $channel = $request->channel_number;
        $query = Channel::where('channel_number', $channel)->whereNull('deleted_at')->first();

        if($query){
            return false;
        }else{
            if(isset($request->id)){
                Channel::where('id',$request->id)->update(['channel_number'=> $channel]);
            }
            return true;
        }
    }
}
