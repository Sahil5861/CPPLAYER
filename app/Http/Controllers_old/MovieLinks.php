<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MovieLink;
use App\Models\Movie;

class MovieLinks extends Controller
{
    public function index(Request $request, $id){
        $id = base64_decode($id);
        return view('admin.movie_link.index', compact('id'));
    }

    public function getMoviesLinkList(Request $request, $id)
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
        $totalRecords = MovieLink::select('count(*) as allcount')->whereNull('movie_play_links.deleted_at')->where('movie_play_links.movie_id', $id)->count();
        $inactiveRecords = MovieLink::select('count(*) as allcount')->where('status','0')->whereNull('movie_play_links.deleted_at')->where('movie_play_links.movie_id', $id)->count();
        $activeRecords = MovieLink::select('count(*) as allcount')->where('status','1')->whereNull('movie_play_links.deleted_at')->where('movie_play_links.movie_id', $id)->count();
        $deletedRecords = MovieLink::select('count(*) as allcount')->whereNotNull('movie_play_links.deleted_at')->where('movie_play_links.movie_id', $id)->count();


        $totalRecordswithFilter = MovieLink::select('count(*) as allcount')
        ->where('name', 'like', '%' . $searchValue . '%')
        // ->where('channels.status', '=', 1)
        ->whereNull('movie_play_links.deleted_at')
        ->where('movie_play_links.movie_id', $id)
        ->count();

        // Get records, also we have included search filter as well
        $records = MovieLink::orderBy($columnName, $columnSortOrder)
            // ->where('channels.status', '=', 1)
            ->whereNull('movie_play_links.deleted_at')
            ->where('movie_play_links.name', 'like', '%' . $searchValue . '%')
            ->where('movie_play_links.movie_id', $id)

            // ->orWhere('channels.description', 'like', '%' . $searchValue . '%')
            // ->orWhere('channels.contact_email', 'like', '%' . $searchValue . '%')
            ->select('movie_play_links.*')->orderBy('movie_play_links.updated_at','desc')            
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('movie-link/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('movie-link/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if($record->deleted_at){
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            }else{
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            $data_arr[] = array(
                "name" => $record->name,                                                            
                "status" => $status,

                "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
                "action" => '<div class="action-btn">
                        <a  href="'.route("edit-movie-link", [base64_encode($id),base64_encode($record->id)]).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>                        
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

    public function addMovieLink(Request $request, $id){   
        $this->data['movie']  = Movie::where('id', base64_decode($id))->first();          
        return view('admin.movie_link.add',$this->data);
    }

    public function add(Request $request){
        $request->validate([
            'name' => 'required',            
            'source_url'  => 'required',           
            'type'  => 'required'           
        ]);
        // print_r($request->all()); exit();
        if(!empty($request->id)){

            $movieLink = MovieLink::firstwhere('id',$request->id);            
            $movieLink->movie_id = $request->movie_id;                       
            $movieLink->name = $request->name;            
            $movieLink->order = $request->order ?? 0;                        
            $movieLink->quality = $request->quality;                                                         
            $movieLink->size = $request->size;                        
            $movieLink->source = $request->source ?? null;
            $movieLink->type = $request->type;                        
            $movieLink->status = $request->status;
            $movieLink->source_url = $request->source_url ?? null;
            $movieLink->skip_available = $request->skip_available ?? 0;
            $movieLink->intro_start = $request->intro_start ?? null;
            $movieLink->intro_end = $request->intro_end ?? null;

            if($movieLink->save()){
                return back()->with('message','Movie Link updated successfully');
            }else{
                return back()->with('message','Movie Link not updated successfully');
            }

        }else{

            $movieLink = new MovieLink();
            $movieLink->movie_id = $request->movie_id;                       
            $movieLink->name = $request->name;            
            $movieLink->order = $request->order;                        
            $movieLink->quality = $request->quality;                                                         
            $movieLink->size = $request->size;                        
            $movieLink->source = $request->source ?? null;
            $movieLink->type = $request->type;                        
            $movieLink->status = $request->status;
            $movieLink->source_url = $request->source_url ?? null;
            $movieLink->skip_available = $request->skip_available ?? 0;
            $movieLink->intro_start = $request->intro_start ?? null;
            $movieLink->intro_end = $request->intro_end ?? null;
            if($movieLink->save()){                
                
                return back()->with('message','Movie Link added successfully');
            }else{
                return back()->with('message','Movie Link not added successfully');
            }
        }
    }

    public function edit(Request $request, $movie_id, $id){  
        $this->data['movie']  = Movie::where('id', base64_decode($movie_id))->first();          
        $this->data['movieLink'] = MovieLink::where('id', base64_decode($id))->first();                                      
        return view('admin.movie_link.add',$this->data);
    }

    public function destroy(Request $request){        
        $movieLink = MovieLink::where('id',base64_decode($request->id))->first();
        $movieLink->deleted_at = time();
        if($movieLink->save()){
            echo json_encode(['message','Movie Link deleted successfully']);
        }else{
            echo json_encode(['message','Movie Link not deleted successfully']);
        }
    }

    public function updateStatus(Request $request, $id){
        
        $movie = MovieLink::find(base64_decode($id));        
        if($movie){
            $movie->status = $movie->status == '1' ? '0' : '1';
            $movie->save();
            echo json_encode(['message','Movie Link status updated successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }
}
