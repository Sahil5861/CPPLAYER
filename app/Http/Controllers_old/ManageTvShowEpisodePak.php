<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TvShowEpisodePak;
use App\Models\Genre;
use App\Models\ContentNetwork;

class ManageTvShowEpisodePak extends Controller
{
    public function index($id)    
    {        
        $id = base64_decode($id);   
        $content_networks = ContentNetwork::where('status', 1)->get();
        $genres = Genre::where('status',1)->get();    
        $playlist_ids = TvShowEpisodePak::where('playlist_id', '!=', null)->where('season_id', $id)->whereNull('deleted_at')->pluck('playlist_id')->unique()->values();         
        return view('admin.tvshow_episode_pak.index', compact('id', 'content_networks', 'genres', 'playlist_ids'));
    }

    public function getTvShowEpisodesOrderList($id)
    {
        $id = base64_decode($id);
        $this->data['tvshowepisode'] = TvShowEpisodePak::whereNull('deleted_at')->where('season_id', $id)->orderBy('episoade_order', 'asc')->get();

        $this->data['id'] = $id;
        $allTvShowEpisodes = [];
        $dataForLoop = [];

        foreach ($this->data['tvshowepisode'] as $episode) {
            $allTvShowEpisodes[] = $episode->episoade_order;
            $dataForLoop[$episode->episoade_order] = $episode;
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allTvShowEpisodes'] = $allTvShowEpisodes;

        return view('admin.tvshow_episode.dragdrop', $this->data);
    }

    public function getshowSeasonList(Request $request, $id){
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

        $playlist_id = $request->input('playlist_id');
        $status = $request->input('status');
        $status = number_format($status);   

        
        $query = TvShowEpisodePak::select('count(*) as allcount')->whereNull('shows_episodes_pak.deleted_at');
        if ($request->has('playlist_id') && $playlist_id != '') {          
            $query->where('playlist_id', $playlist_id);
        }

        if ($request->has('status') && $status != '') {                           
            $query->where('status', $status);
        }

        $totalRecords = TvShowEpisodePak::select('count(*) as allcount')->whereNull('shows_episodes_pak.deleted_at')->where('season_id', $id);
        $inactiveRecords = TvShowEpisodePak::select('count(*) as allcount')->whereNull('shows_episodes_pak.deleted_at')->where('season_id', $id)->where('status', 0);
        $activeRecords = TvShowEpisodePak::select('count(*) as allcount')->whereNull('shows_episodes_pak.deleted_at')->where('season_id', $id)->where('status', 1);
        $deletedRecords = TvShowEpisodePak::select('count(*) as allcount')->whereNotNull('shows_episodes_pak.deleted_at')->where('season_id', $id);


        $totalRecords = $totalRecords->count();
        $inactiveRecords = $inactiveRecords->count();
        $activeRecords = $activeRecords->count();
        $deletedRecords = $deletedRecords->count();
        

        $totalRecordswithFilter = (clone $query)->where(function($query) use ($searchValue){
                $query->where('shows_episodes_pak.title', 'like', '%' . $searchValue . '%')
                    ->orWhere('shows_episodes_pak.playlist_id', 'like', '%' . $searchValue . '%');
        }) 
        // ->where('channels.status', '=', 1)
        ->whereNull('shows_episodes_pak.deleted_at')->where('season_id', $id)
        ->count();

        // Get records, also we have included search filter as well
        $records = (clone $query)->orderBy($columnName, $columnSortOrder)
            // ->where('channels.status', '=', 1)            
            ->where(function($query) use ($searchValue){
                $query->where('shows_episodes_pak.title', 'like', '%' . $searchValue . '%')
                    ->orWhere('shows_episodes_pak.playlist_id', 'like', '%' . $searchValue . '%');
            })        
            ->where('season_id', $id)
            ->when(request()->has('status') && request()->status !== null, function ($query) {
                        $query->where('shows_episodes_pak.status', request()->status);
                    }) 

            // ->orWhere('channels.description', 'like', '%' . $searchValue . '%')
            // ->orWhere('channels.contact_email', 'like', '%' . $searchValue . '%')
            ->select('shows_episodes_pak.*')->orderBy('shows_episodes_pak.created_at','asc')            
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('tv-show-pak-episode/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('tv-show-pak-episode/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if($record->deleted_at){
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            }else{
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            $rem = $record->duration % 60;
            $hours = floor($record->duration / 60);
            $minutes = $rem > 0 ? $rem.' min' : '';
            
            $duration = $hours > 0 ?  $hours.' h '.$minutes : $minutes;

            $data_arr[] = array(
                "id" => $record->id,
                "title" => $record->title,    
                "playlist_id" => $record->playlist_id,                                                                                                       
                "episode_number" => $record->episode_number,                                                                                                           
                "duration" => $duration,                                                                                                           
                "status" => $status,                                                                                                           
                "url" => $record->video_url,  
                "play_btn" => '<a href="javascript:void(0);" class="btn btn-primary play-video" data-video-id="'.$record->video_url.'" onclick="openVideoModal(this)"><svg xmlns="http://www.w3.org/2000/svg" 
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
                "thumbnail" => '<img src="'.$record->thumbnail.'" width="100px;">',                                                                              
                "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
                "action" => '<div class="action-btn">
                        <a href="'.route("editTvShowEpisodepak",base64_encode($record->id)).'" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>                        
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

    public function addtvshow(Request $request, $id)
    {
        $id = base64_decode($id);               

        return view('admin.tvshow_episode_pak.add', compact('id'));
    }

    public function updateStatus($id)
    {
        $tvshow = TvShowEpisodePak::find(base64_decode($id));

        if ($tvshow) {
            $tvshow->status = $tvshow->status == 1 ? 0 : 1;
            $tvshow->save();
            return response()->json(['message' => 'Tv Show Episode Pak status updated successfully']);
        }

        return response()->json(['message' => 'Something went wrong!!']);
    }

    public function add(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        if (!empty($request->id)) {
            $episode = TvShowEpisodePak::find($request->id);
        } else {
            $episode = new TvShowEpisodePak();
        }

        // print_r($request->all()); exit;

        $episode->title = $request->title;
        $episode->episode_number = $request->episode_number;
        $episode->episoade_order = $request->episode_number;
        $episode->season_id = $request->season_id;
        $episode->video_url = $request->video_url;
        $episode->streaming_type = $request->streaming_type ?? 'youtube';
        $episode->description = $request->description;
        $episode->thumbnail = $request->thumbnail ?? null;        
        $episode->duration = $request->duration ?? null;
        $episode->release_date = $request->release_date ?? null;

        if (!empty($request->id)) {
            if ($episode->save()) {
                return back()->with('message','Episode updated successfully');
            } else {
                return back()->with('message','Episode not updated');
            }
        }
        else{
            if ($episode->save()) {
                // return redirect()->route('admin.tvshow.season', base64_encode($tvshow->id))->with('message', $request->id ? 'Season updated successfully' : 'TvShow added successfully');
                return back()->with('message','Episode added');
            } else {
                return back()->with('message','Episode not added');
            }
        }
    }

    public function editTvShowEpisode($id)
    {
        $episode = TvShowEpisodePak::find(base64_decode($id));
        $this->data['episode'] = $episode;
        $this->data['id'] = $episode->season_id;
        return view('admin.tvshow_episode_pak.add', $this->data);
    }

    public function destroy(Request $request)
    {
        $tvshow = TvShowEpisodePak::find(base64_decode($request->id));
        $tvshow->deleted_at = time();

        if ($tvshow->save()) {
            return response()->json(['message' => 'TvShowEpisodePak deleted successfully']);
        } else {
            return response()->json(['message' => 'TvShowEpisodePak not deleted']);
        }
    }

    public function saveTvShowEpisodesOrders(Request $request)
    {
        $ids = $request->ids; 
        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                TvShowEpisodePak::where('id', $id)->update(['episoade_order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Episodes order updated successfully.');
    }


    public function importPlaylist(Request $request){
        $playlistId = $request->input('playlist_id');        

        $apiKey = 'AIzaSyBrsmSKZ5yG6BFkVHsHMxLCkSsvzaH7szk';
        $baseurl = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId={$playlistId}&key={$apiKey}";
        $nextPageToken = null;
        $season_id = $request->id;

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
            $snippet = $data['items'][0];
                                                
            foreach ($data['items'] as $key => $item) {    
                $snippet = $item['snippet'];
                $title = $snippet['title'] ?? null;
                $url = $snippet['resourceId']['videoId'] ?? null;

                if ($this->checkIsExist($title, $url, $season_id) || trim($title) == 'Private video') {
                    continue;
                }

                $rawBannerUrl = $snippet['thumbnails']['high']['url'] ?? null;
                $cleanBannerUrl = $rawBannerUrl ? explode('?', $rawBannerUrl)[0] : null;
                        
                $episode = new TvShowEpisodePak();

                $count = TvShowEpisodePak::whereNull('deleted_at')->where('season_id', $season_id)->count();
                $count = $count + 1;

                $episode->title = $title;
                $episode->episode_number = $count;
                $episode->episoade_order = $count;
                $episode->season_id = $season_id;
                $episode->video_url = $url;
                $episode->status = 0;
                $episode->streaming_type = 'youtube';
                $episode->description = $snippet['description'] ?? null;    
                $episode->thumbnail = $cleanBannerUrl;        
                $episode->duration = 0;
                $episode->playlist_id = $playlistId;
                $episode->release_date = '';
                $episode->save();            
            }            
            $nextPageToken = $data['nextPageToken'] ?? null;                    
        } while ($nextPageToken);  


        

        return back()->with('message','Playlist Uploaded successfully');

    }

    public function checkIsExist($movie_name, $url, $season_id){
        return TvShowEpisodePak::where(function ($query) use ($movie_name, $url) {
                $query->whereRaw('LOWER(TRIM(title)) = ?', [strtolower(trim($movie_name))])
                    ->orWhereRaw('LOWER(TRIM(video_url)) = ?', [strtolower(trim($url))]);
            })
            ->whereNull('deleted_at')
            ->where('season_id', $season_id)
            ->first();
    }

}
