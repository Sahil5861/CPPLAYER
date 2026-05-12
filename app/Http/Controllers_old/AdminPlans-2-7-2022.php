<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use App\Models\Genre;
use App\Models\Language;

class Channels extends Controller
{
    public function index()
    {
        return view('admin.channel.index');
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
        ->where('channel_number', 'like', '%' . $searchValue . '%')
        ->orWhere('channels.channel_name', 'like', '%' . $searchValue . '%')
        ->orWhere('channels.channel_language', 'like', '%' . $searchValue . '%')
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
            ->select('channels.*')
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

            $data_arr[] = array(
                "channel_name" => $record->channel_name,
                "channel_number" => $record->channel_number,
                "channel_logo" => $record->channel_logo,
                "channel_genre" => $record->channel_genre,
                "channel_language" => $record->channel_language,
                "channel_link" => $record->channel_link,
                "status" => $status,

                "created_at" => date('j M Y h:i a',strtotime($record->created_at)),
                "action" => '<div class="action-btn">
                        <a  href="edit-channel/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="javascript:;" onclick="delete_item(\''.base64_encode($record->id).'\',\'channel\')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
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
            'channel_language' => 'required',
            'channel_link' => 'required',
        ]);
        
        if(!empty($request->id)){
            if ($request->hasFile('channel_logo')) {
                $path = public_path('images/channel');
                if ( ! file_exists($path) ) {
                    mkdir($path, 0777, true);
                }
                $file = $request->file('channel_logo');
                $fileName = uniqid() . '_' . trim($file->getClientOriginalName());
                $this->image = $fileName;
                $file->move($path, $fileName);
                $channel_logo = $this->image;
            }else{
                $channel_logo = $request->channel_logo_old;
            } 

            $channel = Channel::firstwhere('id',$request->id);
            $channel->channel_name = $request->channel_name;
            $channel->channel_number = $request->channel_number;
            $channel->channel_logo = $channel_logo;
            $channel->channel_genre = $request->channel_genre;
            $channel->channel_language = $request->channel_language;
            $channel->channel_link = $request->channel_link;
            $channel->status = $request->status;
            if($channel->save()){
                return back()->with('message','Channel updated successfully');
            }else{
                return back()->with('message','Channel not updated successfully');
            }
            
        }else{
            if ($request->hasFile('channel_logo')) {
                $path = public_path('images/channel');
                if ( ! file_exists($path) ) {
                    mkdir($path, 0777, true);
                }
                $file = $request->file('channel_logo');
                $fileName = uniqid() . '_' . trim($file->getClientOriginalName());
                $this->image = $fileName;
                $file->move($path, $fileName);
                $channel_logo = $this->image;
            }else{
                $channel_logo = '';
            } 

            $channel = new Channel();
            $channel->channel_name = $request->channel_name;
            $channel->channel_number = $request->channel_number;
            $channel->channel_logo = $channel_logo;
            $channel->channel_genre = $request->channel_genre;
            $channel->channel_language = $request->channel_language;
            $channel->channel_link = $request->channel_link;
            $channel->status = $request->status;
            if($channel->save()){
                return back()->with('message','Channel added successfully');
            }else{
                return back()->with('message','Channel not added successfully');
            }
        }
        
    }

    public function editChannel($id){ 
        $this->data['channel'] = Channel::where('id',base64_decode($id))->first();
        $this->data['languages'] = Language::where('status',1)->get();
        $this->data['genres'] = Genre::where('status',1)->get();
        return view('admin.channel.add',$this->data);
    }


    public function destroy(Request $request){
        $channel = Channel::where('id',base64_decode($request->id))->first();
        $channel->deleted_at = time();
        if($channel->save()){
            echo json_encode(['message','Channel deleted successfully']);
        }else{
            echo json_encode(['message','Channel not deleted successfully']);
        }
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
}