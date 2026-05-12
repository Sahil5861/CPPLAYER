<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TournamentMatches;
use App\Models\WebSeries;
use App\Models\Genre;
use App\Models\WebSeriesSeason;

use carbon;



class ManageTournamentMatches extends Controller
{
    public function index($id)    
    {   
        // echo $id; exit;     
        $id = base64_decode($id);
        // $playlist_ids = TournamentMatches::where('playlist_id', '!=', null)->whereNull('deleted_at')->pluck('playlist_id')->where('tournament_season_id', $id)->unique();        
        $playlist_ids = TournamentMatches::where('playlist_id', '!=', null)->whereNull('deleted_at')->where('tournament_season_id', $id)->pluck('playlist_id')->unique();        
                
        
        return view('admin.tournamentmatches.index', compact('id', 'playlist_ids'));
    }

    public function getTournamentMatchesOrderList($id)
    {
        $id = base64_decode($id);
        $this->data['tournamentmatch'] = TournamentMatches::whereNull('deleted_at')->where('tournament_season_id', $id)->orderBy('match_order', 'asc')->get();

        $this->data['id'] = $id;
        $allTvShowEpisodes = [];
        $dataForLoop = [];

        foreach ($this->data['tournamentmatch'] as $episode) {
            $allTvShowEpisodes[] = $episode->match_order;
            $dataForLoop[$episode->match_order] = $episode;
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allTvShowEpisodes'] = $allTvShowEpisodes;

        return view('admin.tournamentmatches.dragdrop', $this->data);
    }


    public function getSportsTournamentSeasonEpisodeList(Request $request, $id){
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

        // $playlist_id = $request->input('playlist_id');
        $status = $request->input('status');
        $playlist_id = $request->input('playlist_id');
        $status = number_format($status);  

        $myquery = TournamentMatches::query()->whereNull('tournament_matches.deleted_at');
        if ($request->has('playlist_id') && $playlist_id != '') {           
            $myquery->where('playlist_id', $playlist_id);
        }

        if ($request->has('status') && $status != '') {                        
            $myquery->where('status', $status);
        }
        
        $totalRecords = TournamentMatches::select('count(*) as allcount')->whereNull('tournament_matches.deleted_at')->where('tournament_season_id', $id);
        $inactiveRecords = TournamentMatches::select('count(*) as allcount')->whereNull('tournament_matches.deleted_at')->where('tournament_season_id', $id)->where('status', 0);
        $activeRecords = TournamentMatches::select('count(*) as allcount')->whereNull('tournament_matches.deleted_at')->where('tournament_season_id', $id)->where('status', 1);
        $deletedRecords = TournamentMatches::select('count(*) as allcount')->whereNotNull('tournament_matches.deleted_at')->where('tournament_season_id', $id);


        $totalRecords = $totalRecords->count();
        $inactiveRecords = $inactiveRecords->count();
        $activeRecords = $activeRecords->count();
        $deletedRecords = $deletedRecords->count();


        $totalRecordswithFilter = (clone $myquery)->select('count(*) as allcount')
        ->where('match_title', 'like', '%' . $searchValue . '%')
        // ->where('channels.status', '=', 1)
        ->where('tournament_season_id', $id)
        ->count();



        // Get records, also we have included search filter as well
        $records = (clone $myquery)->where(function ($query) use ($searchValue) {
                $query->where('tournament_matches.match_title', 'like', '%' . $searchValue . '%')
                    ->orWhere('tournament_matches.match_type', 'like', '%' . $searchValue . '%')
                    ->orWhere('tournament_matches.streaming_info', 'like', '%' . $searchValue . '%');
            })                    
            ->where('tournament_season_id', $id)
            ->orderBy($columnName, $columnSortOrder)
            ->orderBy('tournament_matches.created_at', 'asc')
            ->select('tournament_matches.*')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('tournament-season-episodes/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('tournament-season-episodes/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if($record->deleted_at){
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            }else{
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            $matchdateTime = '';

            if (!empty($record->match_date) && !empty($record->match_time)) {
                $matchdateTime = \Carbon\Carbon::parse($record->match_date)->format("D M Y") . ', ' . $record->match_time;
            } elseif (!empty($record->match_date)) {
                $matchdateTime = \Carbon\Carbon::parse($record->match_date)->format('D M Y');
            } elseif (!empty($record->match_time)) {
                $matchdateTime = $record->match_time;
            }

            $data_arr[] = array(
                "id" => $record->id,
                "match_title" => $record->match_title,                                                            
                "match_type" => $record->match_type,                                                            
                "playlist_id" => $record->playlist_id,
                "desc" => $record->description,                                                            
                "streaming_info" => $record->streaming_info,                                                            
                "match_datetime" => $matchdateTime,                                                            
                "status" => $status, 
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
                "start_date" => $record->start_date,                
                "end_date" => $record->end_date,                
                "logo" => '<img src="'.$record->logo.'" width="100px;">',                                                                              
                "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
                "action" => '<div class="action-btn">
                        <a href="'.route("editsportstournamentseasonepisodes",base64_encode($record->id)).'" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>                        
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

        return view('admin.tournamentmatches.add', compact('id'));
    }

    public function updateStatus($id)
    {
        $tournamentseason = TournamentMatches::find(base64_decode($id));

        if ($tournamentseason) {
            $tournamentseason->status = $tournamentseason->status == 1 ? 0 : 1;
            $tournamentseason->save();
            return response()->json(['message' => 'TournamentMatches status updated successfully']);
        }

        return response()->json(['message' => 'Something went wrong!!']);
    }

    public function save(Request $request)
    {
        $request->validate([
            'match_title' => 'required',
            'video_url' => 'required'
        ]);

        if (!empty($request->id)) {
            $match = TournamentMatches::find($request->id);
        } else {
            $match = new TournamentMatches();
            $match->match_order = $request->match_order ?? 0;
        }

        // print_r($request->all()); exit;

        $match->match_title = $request->match_title;
        $match->tournament_season_id  = $request->tournament_season_id;        
        $match->match_type  = $request->match_type;        
        $match->match_date  = $request->match_date;        
        $match->match_time = $request->match_time ?? null;        
        $match->streaming_info = $request->streaming_info ?? null;        
        $match->video_url = $request->video_url ?? null;   
        $match->backup_url = $request->bk_url ?? null;                 
        $match->stream_type = $request->stream_type ?? null;                    
        $match->thumbnail_url = $request->thumbnail_url ?? null;                    
        $match->description = $request->description;
        $match->status = $request->status;

        // print_r($match); exit;


        if (!empty($request->id)) {
            if ($match->save()) {
                return back()->with('message','Tournament Match updated successfully');
            } else {
                return back()->with('message','Tournament Match not updated');
            }
        }
        else{
            if ($match->save()) {
                return back()->with('message', $request->id ? 'TournamentMatches not updated' : 'Tournament Match added');
            } else {
                return back()->with('message', $request->id ? 'TournamentMatches not updated' : 'Tournament Match not added');
            }
        }
    }

    public function edit($id)
    {        
        $match = TournamentMatches::find(base64_decode($id));   
        // print_r($tournamentseason); exit;     
        $this->data['id'] = $match->tournament_season_id ;                             
        $this->data['match'] = $match;                             
        return view('admin.tournamentmatches.add', $this->data);
    }

    public function destroy(Request $request)
    {
        $tournamentseason = TournamentMatches::find(base64_decode($request->id));
        $tournamentseason->deleted_at = time();

        if ($tournamentseason->save()) {
            return response()->json(['message' => 'Tournament Matches deleted successfully']);
        } else {
            return response()->json(['message' => 'Tournament Matches not deleted']);
        }
    }
    
    public function saveTournamentMatchesOrders(Request $request)
    {
        $ids = $request->ids; 
        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                TournamentMatches::where('id', $id)->update(['match_order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Matches order updated successfully.');
    }

    public function importPlaylist(Request $request){

        
        $request->validate([
            'playlist_id' => 'required',   
            'match_type' => 'required',   
            'stream_type' => 'required'            
        ]);

        
        
        $playlistId = $request->input('playlist_id');
        $match_type = $request->input('match_type');
        $stream_type = $request->input('stream_type');
        $tournament_season_id = $request->input('tournament_season_id');
        // print_r($request->all()); exit;
        
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
                $match_title = $snippet['title'] ?? null;
                $video_url = $snippet['resourceId']['videoId'] ?? null;

                if ($this->checkIsExist($match_title, $video_url) || trim($match_title) == 'Private video') {
                    continue;
                }

                $rawBannerUrl = $snippet['thumbnails']['high']['url'] ?? null;
                $cleanBannerUrl = $rawBannerUrl ? explode('?', $rawBannerUrl)[0] : null;
                        
                $match = new TournamentMatches();

                $channel_number = TournamentMatches::whereNull('deleted_at')->where('tournament_season_id', $tournament_season_id)->count();
                $formated_number = $channel_number + 1;   

                $match->match_title = $match_title;
                $match->tournament_season_id  = $tournament_season_id;
                $match->match_type = $match_type;
                $match->match_order = $formated_number;
                $match->description = $snippet['description'] ?? null;
                $match->streaming_info = $stream_type;                            
                $match->video_url = $video_url;                
                $match->stream_type = 'youtube';                
                $match->playlist_id = $playlistId;
                $match->thumbnail_url = $cleanBannerUrl;                
                $match->status = 0;

                $match->save();

                        
            }

            $nextPageToken = $data['nextPageToken'] ?? null;                    
        } while ($nextPageToken);  

        

        return back()->with('message','Playlist Uploaded successfully');

    }


    public function checkIsExist($match_title, $video_url){
        return TournamentMatches::where(function ($query) use ($match_title, $video_url){
            $query->whereRaw('LOWER(TRIM(match_title)) = ?', [strtolower(trim($match_title))])
                    ->orWhereRaw('LOWER(TRIM(video_url)) = ?', [strtolower(trim($video_url))]);
        })
        ->whereNull('deleted_at')
        ->first();
    }
}
