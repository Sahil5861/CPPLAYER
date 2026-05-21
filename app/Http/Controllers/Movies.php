<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\MovieLink;
use App\Models\Genre;
use App\Models\MovieGenre;
use App\Models\ContentNetwork;
use App\Models\MovieContentNetwork;
use Illuminate\Support\Facades\DB;




class Movies extends Controller
{
    public function index(Request $request)
    {
        $content_networks = ContentNetwork::where('status', 1)->get();
        $genres = Genre::where('status',1)->get();

        $movie_network_ids = MovieContentNetwork::pluck('network_id')->unique()->values();

        $networks = ContentNetwork::whereIn('id', $movie_network_ids)->get();
        
        $playlist_ids = Movie::where('playlist_id', '!=', null)->whereNull('deleted_at')->pluck('playlist_id')->unique()->values();        

        $deleteMulti = $request->boolean('delete-multi');
        return view('admin.movie.index', compact('content_networks', 'genres', 'playlist_ids', 'networks', 'deleteMulti'));
    }
    
    public function getMovieOrderList()
    {
        $this->data['movies'] = Movie::whereNull('deleted_at')->orderBy('movie_order', 'asc')->get();
       
        $allMovies = [];
        $dataForLoop = [];

        foreach ($this->data['movies'] as $movie) {
            $allMovies[] = $movie->movie_order;
            $dataForLoop[$movie->movie_order] = $movie;
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allMovies'] = $allMovies;

        return view('admin.movie.dragdrop', $this->data);
    }
    

    public function getRecentMovieOrderList()
    {
        // $this->data['movies'] = Movie::whereNull('deleted_at')->where('is_recent',1)->orderBy('recent_index', 'asc')->get();
        $movies = Movie::whereNull('deleted_at')->where('is_recent',1)->where('is_sd', 0)->orderBy('recent_index', 'asc')->get();
       
        // $allMovies = [];
        // $dataForLoop = [];

        // foreach ($this->data['movies'] as $movie) {
        //     $allMovies[] = $movie->recent_index;
        //     $dataForLoop[$movie->recent_index] = $movie;
        // }

        $this->data['dataForLoop'] = $movies;
        // $this->data['allMovies'] = $allMovies;

        return view('admin.movie.dragdroprecent', $this->data);
    }

    public function getRecentMovieSDOrderList()
    {
        // $this->data['movies'] = Movie::whereNull('deleted_at')->where('is_recent',1)->orderBy('recent_index', 'asc')->get();
        $movies = Movie::whereNull('deleted_at')->where('is_recent',1)->where('is_sd', 1)->orderBy('recent_index', 'asc')->get();
       
        // $allMovies = [];
        // $dataForLoop = [];

        // foreach ($this->data['movies'] as $movie) {
        //     $allMovies[] = $movie->recent_index;
        //     $dataForLoop[$movie->recent_index] = $movie;
        // }

        $this->data['dataForLoop'] = $movies;
        // $this->data['allMovies'] = $allMovies;

        return view('admin.movie.dragdroprecentSD', $this->data);
    }


    public function saveRecentMovieOrder(Request $request)
    {
        $ids = $request->ids;
        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                Movie::where('id', $id)->update(['recent_index' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Recent Movie order updated successfully.');
    }

    public function saveRecentSDMovieOrder(Request $request)
    {
        $ids = $request->ids;
        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                Movie::where('id', $id)->update(['recent_index' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Recent Movie order updated successfully.');
    }
    
    public function deletedChannel(){
        return view('admin.movie.deleted');
    }
    

    /* Process ajax request */
    // public function getMoviesList(Request $request)
    // {
    //     $contentNetworkId = $request->input('content_network');

    //     $draw = $request->get('draw');
    //     $start = $request->get("start");
    //     $rowperpage = $request->get("length"); // total number of rows per page

    //     $columnIndex_arr = $request->get('order');
    //     $columnName_arr = $request->get('columns');
    //     $order_arr = $request->get('order');
    //     $search_arr = $request->get('search');

    //     $columnIndex = $columnIndex_arr[0]['column']; // Column index
    //     $columnName = $columnName_arr[$columnIndex]['data']; // Column name
    //     $columnSortOrder = $order_arr[0]['dir']; // asc or desc
    //     $searchValue = $search_arr['value']; // Search value

    //     // Total records
    //     // Total records
    //     $totalRecords = Movie::select('count(*) as allcount')->whereNull('movies.deleted_at')->count();
    //     $inactiveRecords = Movie::select('count(*) as allcount')->where('status','0')->whereNull('movies.deleted_at')->count();
    //     $activeRecords = Movie::select('count(*) as allcount')->where('status','1')->whereNull('movies.deleted_at')->count();
    //     $deletedRecords = Movie::select('count(*) as allcount')->whereNotNull('movies.deleted_at')->count();


    //     $totalRecordswithFilter = Movie::select('count(*) as allcount')
    //     ->where('name', 'like', '%' . $searchValue . '%')
    //     // ->where('channels.status', '=', 1)
    //     ->whereNull('movies.deleted_at')
    //     ->count();

    //     // Get records, also we have included search filter as well
    //     $records = Movie::orderBy($columnName, $columnSortOrder)
    //         // ->where('channels.status', '=', 1)
    //         ->whereNull('movies.deleted_at')
    //         ->where('movies.name', 'like', '%' . $searchValue . '%')

    //         // ->orWhere('channels.description', 'like', '%' . $searchValue . '%')
    //         // ->orWhere('channels.contact_email', 'like', '%' . $searchValue . '%')
    //         ->select('movies.*')->orderBy('movies.updated_at','desc')            
    //         ->skip($start)
    //         ->take($rowperpage)
    //         ->get();

    //     $data_arr = array();

    //     foreach ($records as $record) {
    //         if($record->status == 1){
    //             $status = '<a onchange="updateStatus(\''.url('movie/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
    //         }else{
    //             $status = '<a onchange="updateStatus(\''.url('movie/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
    //         }

    //         if($record->deleted_at){
    //             $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
    //         }else{
    //             $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
    //         }

    //         $data_arr[] = array(
    //             "name" => $record->name,                                                            
    //             "status" => $status,                
    //             "banner" => '<img src="'.$record->banner.'" width="100px">',
    //             "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
    //             "action" => '<div class="action-btn">
    //                     <a href="edit-movie/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>                        
    //                     <a href="javascript:;" onclick="delete_item(\''.base64_encode($record->id).'\',\'movie\')">'.$del_icon.'</a>
    //                   </div>',
    //         );
    //     }

    //     $response = array(
    //         "draw" => intval($draw),
    //         "iTotalRecords" => $totalRecords,
    //         "iTotalDisplayRecords" => $totalRecordswithFilter,
    //         "aaData" => $data_arr,
    //         "totalRecords" => number_format($totalRecords),
    //         "activeRecords" => number_format($activeRecords),
    //         "inactiveRecords" => number_format($inactiveRecords),
    //         "deletedRecords" => number_format($deletedRecords),
    //     );
    //     echo json_encode($response);
    // }

    public function getMoviesList(Request $request)
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


        $playlist_id = $request->input('playlist_id');
        $network_id = $request->input('network_id');
        $status = $request->input('status');
        $stream_type = $request->input('stream_type');
        $type = $request->input('type');
        $movie_networl_type = $request->input('movie_networl_type') == '1' ? 1 : 0;
        // $status = number_format($status);

        $content_network = ContentNetwork::where('name', 'Latest Movies SD')->first();

        $ids = MovieContentNetwork::where('network_id', $content_network->id)->get()->pluck('movie_id')->toArray();


        $movie_ids = [];

        $movieQuery = Movie::query()->whereNull('movies.deleted_at');
        if ($request->has('playlist_id') && $playlist_id != '') {         
            $movieQuery->where('playlist_id', $playlist_id);
        }

        if ($request->has('stream_type') && $stream_type != '') {
            $movieQuery->where('source_type', $stream_type);
        }

        if ($request->has('status') && $status != '') {             
            $movieQuery->where('movies.status', (int)$status);                        
        }

        if ($request->has('type') && $type != '') {             
            $movieQuery->where('movies.is_recent', 1);                        
        }


        if ($request->has('movie_networl_type') && $movie_networl_type != '') {             
            // $movieQuery->whereIn('movies.id', $ids);                        
            $movieQuery->where('movies.is_sd', $movie_networl_type);                        
        }

    
        if ($request->has('network_id') && !empty($network_id)) {
            $movie_ids = MovieContentNetwork::whereIn('network_id', (array) $network_id)->pluck('movie_id')->toArray();

            $movieQuery->whereIn('movies.id', $movie_ids);
        }

        

        $totalRecords = Movie::select('count(*) as allcount')->whereNull('movies.deleted_at');
        $inactiveRecords = Movie::select('count(*) as allcount')->whereNull('movies.deleted_at')->where('status','0');
        $activeRecords = Movie::select('count(*) as allcount')->whereNull('movies.deleted_at')->where('status','1');
        $deletedRecords = Movie::select('count(*) as allcount')->whereNotNull('movies.deleted_at');
        $recentdRecords = Movie::select('count(*) as allcount')->where('movies.is_recent', 1);

        if ($request->has('playlist_id') && $playlist_id != '') {

            $totalRecords = $totalRecords->where('playlist_id', $playlist_id);
            $inactiveRecords = $inactiveRecords->where('playlist_id', $playlist_id);
            $activeRecords = $activeRecords->where('playlist_id', $playlist_id);
            $deletedRecords = $deletedRecords->where('playlist_id', $playlist_id);
            $recentdRecords = $recentdRecords->where('playlist_id', $playlist_id);

        }

        if ($request->has('stream_type') && $stream_type != '') {

            $totalRecords = $totalRecords->where('source_type', $stream_type);
            $inactiveRecords = $inactiveRecords->where('source_type', $stream_type);
            $activeRecords = $activeRecords->where('source_type', $stream_type);
            $deletedRecords = $deletedRecords->where('source_type', $stream_type);
            $recentdRecords = $recentdRecords->where('source_type', $stream_type);

        }

        if ($request->has('type') && $type != '') {

            $totalRecords = $totalRecords->where('is_recent', 1);
            $inactiveRecords = $inactiveRecords->where('is_recent', 1);
            $activeRecords = $activeRecords->where('is_recent', 1);
            $deletedRecords = $deletedRecords->where('is_recent', 1);
            $recentdRecords = $recentdRecords->where('is_recent', 1);

        }   
        
        if ($request->has('movie_networl_type') && $movie_networl_type != '') {

            $totalRecords = $totalRecords->where('is_sd', $movie_networl_type);
            $inactiveRecords = $inactiveRecords->where('is_sd', $movie_networl_type);
            $activeRecords = $activeRecords->where('is_sd', $movie_networl_type);
            $deletedRecords = $deletedRecords->where('is_sd', $movie_networl_type);
            $recentdRecords = $recentdRecords->where('is_sd', $movie_networl_type);

        }   

        if (!empty($movie_ids)) {
            
            $totalRecords = $totalRecords->whereIn('id', $movie_ids);
            $inactiveRecords = $inactiveRecords->whereIn('id', $movie_ids);
            $activeRecords = $activeRecords->whereIn('id', $movie_ids);
            $deletedRecords = $deletedRecords->whereIn('id', $movie_ids);
            $recentdRecords = $recentdRecords->whereIn('id', $movie_ids);
        }

        $totalRecords = $totalRecords->count();
        $inactiveRecords = $inactiveRecords->count();
        $activeRecords = $activeRecords->count();
        $deletedRecords = $deletedRecords->count();
        $recentdRecords = $recentdRecords->count();



        $totalRecordswithFilter = (clone $movieQuery)
        ->where(function($query) use ($searchValue) {
            $query->where('movies.name', 'like', '%' . $searchValue . '%')
                ->orWhere('movies.playlist_id', 'like', '%' . $searchValue . '%')
                ->orWhere('movies.source_type', 'like', '%' . $searchValue . '%');
        })
        ->count();

        // Get records, also we have included search filter as well
        
        $records = (clone $movieQuery)
                    ->where(function($query) use ($searchValue) {
                        $query->where('movies.name', 'like', '%' . $searchValue . '%')
                            ->orWhere('movies.playlist_id', 'like', '%' . $searchValue . '%')
                            ->orWhere('movies.source_type', 'like', '%' . $searchValue . '%');
                    })
                    ->orderBy($columnName, $columnSortOrder)
                    ->select('movies.*')
                    ->orderBy('movies.updated_at', 'desc')
                    ->skip($start)
                    ->take($rowperpage)
                    ->get();
        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateMovieStatus(\''.url('movie/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateMovieStatus(\''.url('movie/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }


            if($record->is_recent == 1){
                $recent = '<a onchange="updateIsRecent(\''.url('movie/update-recent',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $recent = '<a onchange="updateIsRecent(\''.url('movie/update-recent',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if($record->deleted_at){
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            }else{
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            $image = $record->banner ? $record->banner : $record->poster;
            $data_arr[] = array(
                "id" => $record->id,                
                "name" => $record->name,                                                            
                "status" => $status, 
                "recent" => $recent, 
                "stream_type" => $record->source_type == 'YoutubeLive' ? 'Youtube' : 'M3U8',
                'playlist_id' => $record->playlist_id ?? '',               
                "play_btn" => '<a href="javascript:void(0);" class="btn btn-primary play-video" data-video-id="'.$record->movie_url.'" onclick="openVideoModal(this)"><svg xmlns="http://www.w3.org/2000/svg" 
                    width="20" height="20" 
                    viewBox="0 0 24 24" 
                    fill="none" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    class="feather feather-eye">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                </a>',
                "banner" => '<img src="'.$image.'" width="100px">',
                "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
                "action" => '<div class="action-btn">
                        <a href="edit-movie/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>                        
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
            "recentdRecords" => number_format($recentdRecords),
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
                "id" => $record->id,
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
        $this->data['genres'] = Genre::where('status',1)->get();
        $this->data['networks'] = ContentNetwork::where('deleted_at', null)->get();
        return view('admin.movie.add',$this->data);
    }

    public function add(Request $request){
        $request->validate([
            'name' => 'required',            
            'movie_url' => 'required',            
            'banner' => 'required',            
            'movie_genre' => 'required',                                 
        ]);

        


        if(!empty($request->id)){

            $movie = Movie::firstwhere('id',$request->id);
            $movie->name = $request->name;
            $movie->banner = $request->banner;                                  
            $movie->description = $request->movie_description;                        
            $movie->release_date = $request->release_date ?? null;                                                                    
            $movie->status = $request->status;
            $movie->index = $request->index ?? 0;
            $movie->movie_order = $request->index ?? 0;
            $movie->is_recent = $request->is_recent == 'on' ? 1 : 0;
            $movie->is_sd = $request->is_sd == 'on' ? 1 : 0;
            $movie->recent_date = $request->recent_date ?? now();          
            $movie->source_type = $request->source_type;
            $movie->youtube_trailer = $request->trailer_url ?? null;
            $movie->movie_url = $request->movie_url ?? null;
            $movie->backup_url = $request->bk_url ?? null;
            $movie->genres = implode(',', $request->movie_genre);
            if (!empty($request->hidden_countries) && is_array($request->hidden_countries)) {
                $movie->countries = implode(',', $request->hidden_countries);
            } else {
                $movie->countries = ''; // ya empty string '' rakho agar column string hai
            }

            // print_r($movie); exit;
            if($movie->save()){
                
                MovieContentNetwork::where('movie_id',$movie->id)->delete();
                DB::table('content_network_log')->where('content_id', $movie->id)->where('content_type', $movie->content_type)->delete();                                                                
                if ($request->has('content_network') && !empty($request->content_network)) {                                        
                    foreach ($request->content_network as $key => $network) {
                        $MovieNetwork = new MovieContentNetwork();
                        $MovieNetwork->movie_id = $movie->id;
                        $MovieNetwork->network_id = $network;
                        if ($MovieNetwork->save()) {                           
                            DB::table('content_network_log')->insert([
                                'content_id' => $movie->id,
                                'network_id' => $network,
                                'content_type' => $movie->content_type,                            
                            ]);
                        }
                    }
                }


                return back()->with('message','Movie updated successfully');
            }else{
                return back()->with('message','Movie not updated successfully');
            }

        }else{            

            $is_exists = $this->checkInMoviecontent($request->name, $request->content_network);                

            if ($is_exists == 1) {
                return redirect()->back()->withInput()->withErrors([
                    'message' => 'Movie with the Same Network Already exists !'
                ]);
            }

            
            $movie = new Movie();
            $movie->name = $request->name;
            $movie->banner = $request->banner;                                  
            $movie->description = $request->movie_description ?? null;                        
            $movie->release_date = $request->release_date ?? null;                                                                    
            $movie->status = $request->status;
            $movie->index = $request->index ?? 0;
            $movie->movie_order = $request->index ?? 0;
            $movie->is_recent = $request->is_recent == 'on' ? 1 : 0;

            if ($request->is_recent == 'on') {
                // updaet recent value for all old movies 
                
                $recent_movies = Movie::whereNull('deleted_at')->where('is_recent',1)->get();
                
                foreach ($recent_movies as $key => $r_movie) {
                    $r_movie->recent_index = $r_movie->recent_index + 1;

                    $r_movie->save();
                }
                $movie->recent_index = 1;

            }
            $movie->is_sd = $request->is_sd == 'on' ? 1 : 0;
            $movie->recent_date = $request->recent_date ?? now();     
            $movie->source_type = $request->source_type;    
            $movie->youtube_trailer = $request->trailer_url ?? null;
            $movie->movie_url = $request->movie_url ?? null;
            $movie->backup_url = $request->bk_url ?? null;
            $movie->genres = implode(',', $request->movie_genre);

            // $last_index = Movie::max('recent_index');
            // $movie->recent_index = $last_index + 1;


            if (!empty($request->hidden_countries) && is_array($request->hidden_countries)) {
                $movie->countries = implode(',', $request->hidden_countries);
            } else {
                $movie->countries = ''; // ya empty string '' rakho agar column string hai
            }


            
            if($movie->save()){                                
                if ($request->has('content_network') && !empty($request->content_network)) {
                    $cur_movies = Movie::where('id', $movie->id)->first();
                    $newtworkMovies = MovieContentNetwork::where('movie_id',$movie->id)->get();
                    if ($newtworkMovies) {
                        MovieContentNetwork::where('movie_id',$movie->id)->delete();
                    }
                    foreach ($request->content_network as $key => $network) {
                        $MovieNetwork = new MovieContentNetwork();
                        $MovieNetwork->movie_id = $movie->id;
                        $MovieNetwork->network_id = $network;
                        
                        if ($MovieNetwork->save()) {
                            DB::table('content_network_log')->insert([
                                'content_id' => $movie->id,
                                'network_id' => $network,
                                'content_type' => $cur_movies->content_type,                            
                            ]);
                        }
                    }
                }

                return back()->with('message','Movie added successfully');
            }else{
                return back()->with('message','Movie not added successfully');
            }
        }
    }

    public function editChannel($id){  
        $movie = Movie::where('id', base64_decode($id))->first();              
        $this->data['movie'] = $movie;
        $this->data['genres'] = Genre::where('status',1)->get();
        $this->data['networks'] = ContentNetwork::where('deleted_at', null)->get();
        // $channelGenre = MovieGenre::where('movie_id',base64_decode($id))->get();

        $this->data['channelGenre'] = explode(',', $movie->genres);
        $this->data['movieContries'] = explode(',', $movie->countries);
        $movieNetwork = MovieContentNetwork::where('movie_id', base64_decode($id))->get();        
        // print_r($channelGenre); exit();
        // $this->data['channelGenre'] = [];
        $this->data['movieNetwork'] = [];
        // if($channelGenre){
        //     foreach ($channelGenre as $key => $value) {
        //         $this->data['channelGenre'][] = $value->genre_id;
        //     }
        // }

        if($movieNetwork){
            foreach ($movieNetwork as $key => $value) {
                $this->data['movieNetwork'][] = $value->network_id;
            }
        }
        // print_r($this->data['movieNetwork']); exit;
        return view('admin.movie.add',$this->data);
    }

    public function destroy(Request $request){
        $movie = Movie::where('id',base64_decode($request->id))->first();

        
        $movie->deleted_at = time();
        if($movie->save()){

            MovieContentNetwork::where('movie_id',$movie->id)->delete();
            $links = MovieLink::where('movie_id', $movie->id)->get();
            if ($links) {
                MovieLink::where('movie_id', $movie->id)->delete();
            }
            echo json_encode(['message','Movie deleted successfully']);
        }else{
            echo json_encode(['message','Movie not deleted successfully']);
        }
    }
    
    public function saveMovieOrder(Request $request)
    {
        $ids = $request->ids;
        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                Movie::where('id', $id)->update(['movie_order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Movie order updated successfully.');
    }

    

    




    public function updateStatus($id){
        $movie = Movie::find(base64_decode($id));        
        if($movie){
            $movie->status = $movie->status == 1 ? 0 : 1;
            $movie->save();
            echo json_encode(['message','Movie status updated successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }

    public function updateRecent($id){
        $movie = Movie::find(base64_decode($id));        
        if($movie){
            $movie->is_recent = $movie->is_recent == 1 ? 0 : 1;
            $movie->save();
            echo json_encode(['message','Movie updated successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }

    public function importmovies(Request $request){
        $request->validate([
            'playlist_id' => 'required',
            'genre' => 'required',
            'content_network' => 'required'
        ]);
        $playlistId = $request->input('playlist_id');
        $genres = implode(',', $request->genre) ?? '';

        $apiKey = 'AIzaSyBrsmSKZ5yG6BFkVHsHMxLCkSsvzaH7szk';
        $baseurl = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId={$playlistId}&key={$apiKey}";
        $nextPageToken = null;

        do {
            $url = $baseurl;
            if ($nextPageToken) {
                $url .= "&pageToken=" . $nextPageToken;
            }

            $ch = curl_init($url);
    
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
            $response = curl_exec($ch);
    
            if(curl_errno($ch)) {
                $error_msg = curl_error($ch);
                curl_close($ch);
                return response()->json(['success' => false, 'error' => $error_msg], 500);
            }
            curl_close($ch);
            
            $data = json_decode($response, true);        

            if (!isset($data['items'])) break;

            foreach ($data['items'] as $key => $item) {    
                $snippet = $item['snippet'];
                $movieName = $snippet['title'] ?? null;
                $movie_url = $snippet['resourceId']['videoId'] ?? null;

                if ($this->checkIsExist($movieName, $movie_url) || trim($movieName) == 'Private video') {
                    continue;
                }

                $rawBannerUrl = $snippet['thumbnails']['high']['url'] ?? null;
                $cleanBannerUrl = $rawBannerUrl ? explode('?', $rawBannerUrl)[0] : null;
                        
                $movie = new Movie();

                $channel_number = Movie::whereNull('deleted_at')->count();
                $formated_number = $channel_number + 1;   

                $movie->name = $movieName;
                $movie->description = $snippet['description'] ?? null;

                $last_index = Movie::max('recent_index');
                
                $movie->banner = $cleanBannerUrl;
                $movie->status = 0;
                $movie->source_type = 'YoutubeLive';            
                $movie->youtube_trailer = '' ?? null;
                $movie->movie_url = $movie_url;
                $movie->genres = $genres;
                $movie->index = $formated_number;
                $movie->movie_order = $formated_number;
                $movie->playlist_id = $playlistId;
                $movie->recent_index = $last_index + 1;

                if ($movie->save()) {
                    if ($request->has('content_network') && !empty($request->content_network)) {
                        $cur_movies = Movie::where('id', $movie->id)->first();                        
                        foreach ($request->content_network as $key => $network) {
                            $MovieNetwork = new MovieContentNetwork();
                            $MovieNetwork->movie_id = $movie->id;
                            $MovieNetwork->network_id = $network;
                            
                            if ($MovieNetwork->save()) {
                                DB::table('content_network_log')->insert([
                                    'content_id' => $movie->id,
                                    'network_id' => $network,
                                    'content_type' => $cur_movies->content_type,                            
                                ]);
                            }
                        }
                    }
                }                
            }

            $nextPageToken = $data['nextPageToken'] ?? null;                    
        } while ($nextPageToken);  

        

        return back()->with('message','Playlist Uploaded successfully');

    }


    public function checkIsExist($movie_name, $url){
        return Movie::where(function ($query) use ($movie_name, $url){
            $query->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($movie_name))])
                    ->orWhereRaw('LOWER(TRIM(movie_url)) = ?', [strtolower(trim($url))]);
        })        
        ->first();
    }

    public function checkInMoviecontent($movie_name, $content_network){
        $movie_ids = Movie::where('name', $movie_name)->get()->pluck('id')->toArray();
        $movie_contents = MovieContentNetwork::whereIn('movie_id', $movie_ids)->pluck('network_id')->toarray();

        // print_r($movie_contents);exit;
        $in_array = count(array_intersect($content_network, $movie_contents)) > 0;
        
        return $in_array;
    }


    public function updateColumn(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'table' => 'required|string',
            'column' => 'required|string',
            'value' => 'required'
        ]);

        // Sanitize table and column names to prevent SQL injection
        $allowedTables = ['movies', 'shows_episodes', 'rel_episodes', 'stage_shows_pak', 'web_series_episoade', 'kids_shows_episodes', 'adult_movies', 'tournament_matches']; // add more if needed
        $allowedColumns = ['name', 'title', 'Episoade_Name', 'match_title']; // add other editable columns if needed

        if (!in_array($request->table, $allowedTables) || !in_array($request->column, $allowedColumns)) {
            return response()->json(['error' => 'Invalid table or column'], 400);
        }
        
        $isExist = DB::table($request->table)->whereRaw('LOWER(TRIM('.$request->column.')) = ?', [strtolower(trim($request->value))])->whereNull('deleted_at')->first();

        if ($isExist) {
            return response()->json(['message' => 'This '.$request->column.' already exists']);
        }
        
        try {
            // Update the record
            DB::table($request->table)
                ->where('id', $request->id)
                ->update([
                    $request->column => $request->value
                ]);

            return response()->json(['success' => true, 'message' => 'Record updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Update failed.', 'error' => $e->getMessage()], 500);
        }
    }


    public function deleteMulti(Request $request){
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'table' => 'required|string'
        ]);

        $allowedTables = ['movies']; 

        if (!in_array($request->table, $allowedTables)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid table selected.'
            ], 400);
        }

        
        $ids = $request->ids;
        $table = $request->table;

        $records = DB::table($table)->whereIn('id', $ids)->get();
        // return response()->json([
        //     'data' => $request->all(),
        //     'records' => $records,

        // ]);


        DB::table($table)->whereIn('id', $ids)->delete();
        return response()->json([
            'status' => true,
            'message'=> 'Records Deleted'
        ]);
    }

    public function updateMulti(Request $request){
        $request->validate([
            'table' => 'required|string'
        ]);

        $allowedTables = ['movies']; 

        if (!in_array($request->table, $allowedTables)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid table selected.'
            ], 400);
        }

        
        $ids = $request->ids;
        $table = $request->table;
        $status = $request->status;

        $records = DB::table($table)->whereIn('id', $ids)->get();
        // return response()->json([
        //     'data' => $request->all(),
        //     'records' => $records,

        // ]);

        if ($records) {
            DB::table($table)->whereIn('id', $ids)->update([
                'status' => $status
            ]);
        }

        return response()->json([
            'status' => true,
            'message'=> 'Records Updated'
        ]);
    }


    public function downloadSampleCsv()
    {
        $last_order = Movie::max('movie_order');

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="movies_sample.csv"',
        ];

        $columns = [
            'Movie Name',
            'Movie Index',
            'Stream Type',   // M3u8 / YoutubeLive
            'Movie Url',
            'Movie Poster',
            'Genre',         // comma separated e.g: Action,Drama
            'Content Networks', // comma separated network ids e.g: 1,2
            'Status',        // Active / Inactive
            'Movie Description',
        ];

        $sampleRow = [
            'Avengers',
            $last_order + 1,
            'M3u8',
            'https://example.com/movie.m3u8',
            'https://example.com/poster.jpg',
            'Action,Drama',
            'Netflix',
            'Active',
            'A great movie',
        ];

        $callback = function () use ($columns, $sampleRow) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $sampleRow);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header = fgetcsv($file); // skip header row

        $imported = 0;

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            $movie = Movie::updateOrCreate(
                ['name' => $data['Movie Name']],
                [
                    'index'       => $data['Movie Index'] ?? 0,
                    'movie_order' => $data['Movie Index'] ?? 0,
                    'source_type' => $data['Stream Type'],
                    'movie_url'   => $data['Movie Url'],
                    'poster'      => $data['Movie Poster'],
                    'banner'      => $data['Movie Poster'],
                    'genres'      => $data['Genre'],
                    'status'      => $data['Status'] === 'Active' ? 1 : 0,
                    'description' => $data['Movie Description'],
                ]
            );

            // Content Networks
            if (!empty($data['Content Networks'])) {
                MovieContentNetwork::where('movie_id', $movie->id)->delete();
                DB::table('content_network_log')
                    ->where('content_id', $movie->id)
                    ->where('content_type', $movie->content_type)
                    ->delete();


                

                // $networkIds = explode(',', $data['Content Networks']);
                $networkNames = array_map(function($name) {
                    // normal trim + non-breaking space (\xc2\xa0) bhi remove karo
                    return trim(preg_replace('/[\x00-\x1F\x80-\xFF\s]+/', ' ', $name));
                }, explode(',', $data['Content Networks']));
                $networkIds = ContentNetwork::whereIn('name', $networkNames)
                ->pluck('id')
                ->toArray();



                foreach ($networkIds as $networkId) {
                    $networkId = trim($networkId);
                    if (!$networkId) continue;

                    $movieNetwork = new MovieContentNetwork();
                    $movieNetwork->movie_id   = $movie->id;
                    $movieNetwork->network_id = $networkId;
                    if ($movieNetwork->save()) {
                        DB::table('content_network_log')->insert([
                            'content_id'   => $movie->id,
                            'network_id'   => $networkId,
                            'content_type' => $movie->content_type,
                        ]);
                    }
                }
            }

            $imported++;
        }

        fclose($file);

        return response()->json([
            'message' => $imported . ' movies imported successfully!'
        ]);
    }



}
