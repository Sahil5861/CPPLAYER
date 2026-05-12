<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ContentNetwork;
use App\Models\Movie;
use App\Models\Channel;
use App\Models\WebSeriesEpisode;
use App\Models\TvShowEpisode;
use App\Models\TvShowEpisodePak;
use App\Models\KidsshowsEpisode;
use App\Models\RelshowsEpisode;
use App\Models\TournamentMatches;
use App\Models\StageshowPak;
use App\Models\Laughterhow;
use App\Models\UrlSetting;


use App\Models\MovieContentNetwork;
use App\Models\WebSeriesContentNetwok;
use App\Models\TvShowContentNetwok;
use App\Models\TvShowPakContentNetwok;
use App\Models\KidsChannelContentNetwork;
use App\Models\RelChannelContentNetwork;
use App\Models\SportCategoryContentNetwork;
use App\Models\StageshowPakContentNetwork;
use App\Models\LaugtershowContentNetwork;


use App\Models\TvShow;
use App\Models\TvShowPak;
use App\Models\KidsShow;
use App\Models\RelShow;
use App\Models\SportsTournament;


use App\Models\TournamentSeason;
use App\Models\KidShowsSeason;
use App\Models\TvShowSeasonPak;
use App\Models\TvShowSeason;
use App\Models\WebSeriesSeason;



use App\Models\Language;


// use App\Models\TvShowPakContentNetwok;



class SettingsController extends Controller
{
    public function index(Request $request){

        $content_networks = ContentNetwork::all();

       
        return view('admin.url_settings.index', compact('content_networks', 'languages'));
    }

    public function getList(Request $request){
        if ($request->ajax()) {
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

            $totalRecords = UrlSetting::select('count(*) as allcount')->whereNull('url_settings.deleted_at')->count();            
            $deletedRecords = UrlSetting::select('count(*) as allcount')->whereNotNull('url_settings.deleted_at')->count();

            $totalRecordswithFilter = UrlSetting::select('count(*) as allcount')
            ->where('old_url', 'like', '%' . $searchValue . '%')
            // ->where('channels.status', '=', 1)
            ->whereNull('url_settings.deleted_at')
            ->count();

            // Get records, also we have included search filter as well
            $records = UrlSetting::orderBy($columnName, $columnSortOrder)
                // ->where('channels.status', '=', 1)
                ->whereNull('url_settings.deleted_at')
                ->where('url_settings.old_url', 'like', '%' . $searchValue . '%')
                            
                ->select('url_settings.*')->orderBy('url_settings.created_at','asc')            
                ->skip($start)
                ->take($rowperpage)
                ->get();
            
            $data_arr = array();

            foreach ($records as $record) {                
                if($record->deleted_at){
                    $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
                }else{
                    $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
                }

                $content_type = '';
                switch ($record->content_type) {
                    case '1':
                        $content_type = 'Movies';
                        break;
                    
                    case '2':
                        $content_type = 'Web Series';
                        break;
                    
                    case '4':
                        $content_type = 'Tv Shows';
                        break;

                    case '5':
                        $content_type = 'Tv Shows Pak';
                        break;
                    
                    case '6':
                        $content_type = 'Kids Shows';
                        break;

                    case '7':
                        $content_type = 'Religious shows';
                        break;

                    case '8':
                        $content_type = 'Sports';
                        break;

                    case '9':
                        $content_type = 'Stage Shows Pak';
                        break;

                    case '10':
                        $content_type = 'Laughter Shows';
                        break;
                    default:
                        $content_type = 'N/A';
                        break;
                }

                // $record->content_network_id

                $data_arr[] = array(
                    'id' => $record->id,
                    "old_url" => $record->old_url,                                                            
                    "new_url" => $record->new_url,                                                            
                    "category" => $content_type,
                    "network" => ContentNetwork::where('id', $record->content_network_id)->first()->name,                                                                                                              
                    "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
                    "action" => '<div class="action-btn">
                            <a href="'.route("url-settings-edit",base64_encode($record->id)).'" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>                                                        
                        </div>',
                );
            }

            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,   
                "iTotalDisplayRecords" => $totalRecordswithFilter,
                "aaData" => $data_arr,
                "totalRecords" => number_format($totalRecords),                
                "deletedRecords" => number_format($deletedRecords),
            );
            echo json_encode($response);
        }
    }


    public function add(){
        $content_networks = ContentNetwork::all();
        $languages = Language::whereNull('deleted_at')->where('status', 1)->get();               
        return view('admin.url_settings.add', compact('content_networks', 'languages'));
    }
    
    public function save(Request $request){
        $request->validate([
            'content_network_id' => 'required',
            'content_type' => 'required',
            'old_url' => 'required',
            'new_url' => 'required'
        ]);

        // print_r($request->all());exit;


        $old_url = $request->old_url;
        $new_url = $request->new_url;
        $content_type = $request->content_type;  
        
        $network_id = $request->content_network_id;

        $content = collect();

        switch ($content_type) {
            case '1':
                $ids = MovieContentNetwork::where('network_id', $network_id)->get()->pluck('movie_id')->toArray();
                $content = Movie::whereIn('id', $ids)->where('movie_url', 'LIKE', '%' . $old_url . '%')->get();
                break;
            
            case '2':
                $series_ids = WebSeriesContentNetwork::where('network_id', $network_id)->get()->pluck('webseries_id')->toArray();
                $ids = WebSeriesSeason::whereIn('web_series_id', $series_ids)->get()->pluck('id')->toArray();

                $content = WebSeriesEpisode::whereIn('season_id', $ids)->where('url', 'LIKE', '%' . $old_url . '%')->get();
                break;

            case '3':
                $content = Channel::whereIn('id', $ids)->where('channel_link', 'LIKE', '%' . $old_url . '%')->get();
                break;

            case '4':
                $channel_ids = TvShowContentNetwok::where('network_id', $network_id)->get()->pluck('show_id')->toArray();
                $show_ids = TvShow::whereIn('tv_channel_id', $channel_ids)->get()->pluck('id')->toArray();
                $ids = TvShowSeason::whereIn('show_id', $show_ids)->get()->pluck('id')->toArray();

                $content = TvShowEpisode::whereIn('season_id', $ids)->where('video_url', 'LIKE', '%' . $old_url . '%')->get();
                break;

            case '5':
                $channel_ids = TvShowPakContentNetwok::where('network_id', $network_id)->get()->pluck('show_id')->toArray();
                $show_ids = TvShowPak::whereIn('tv_channel_id', $channel_ids)->get()->pluck('id')->toArray();
                $ids = TvShowSeasonPak::whereIn('show_id', $show_ids)->get()->pluck('id')->toArray();

                $content = TvShowEpisodePak::whereIn('season_id', $ids)->where('video_url', 'LIKE', '%' . $old_url . '%')->get();
                break;

            case '6':
                $channel_ids = KidsChannelContentNetwork::where('network_id', $network_id)->get()->pluck('show_id')->toArray();
                $show_ids = KidsShow::whereIn('kid_channel_id', $channel_ids)->get()->pluck('id')->toArray();
                $ids = KidShowsSeason::whereIn('show_id', $show_ids)->get()->pluck('id')->toArray();

                $content = KidsshowsEpisode::whereIn('season_id', $ids)->where('url', 'LIKE', '%' . $old_url . '%')->get();
                break;

            case '7':
                $channel_ids = RelChannelContentNetwork::where('network_id', $network_id)->get()->pluck('show_id')->toArray();
                $ids = RelShow::whereIn('channel_id', $channel_ids)->get()->pluck('id')->toArray();
               
                $content = RelshowsEpisode::whereIn('show_id', $ids)->where('url', 'LIKE', '%' . $old_url . '%')->get();
                break;

            case '8':
                $channel_ids = SportCategoryContentNetwork::where('network_id', $network_id)->get()->pluck('sport_category_id')->toArray();
                $show_ids = SportsTournament::whereIn('sports_category_id', $channel_ids)->get()->pluck('id')->toArray();
                $ids = TournamentSeason::whereIn('sports_tournament_id', $show_ids)->get()->pluck('id')->toArray();

                $content = TournamentMatches::whereIn('tournament_season_id', $ids)->where('video_url', 'LIKE', '%' . $old_url . '%')->get();
                break;

            case '9':
                $ids = StageshowPakContentNetwork::where('network_id', $network_id)->get()->pluck('movie_id')->toArray();

                $content = StageshowPak::whereIn('id', $ids)->where('movie_url', 'LIKE', '%' . $old_url . '%')->get();
                break;

            case '10':
                $ids = LaugtershowContentNetwork::where('network_id', $network_id)->get()->pluck('movie_id')->toArray();

                $content = Laughterhow::whereIn('id', $ids)->where('movie_url', 'LIKE', '%' . $old_url . '%')->get();
                break;
            default:
                $content = [];

                return back()->with('error', 'Invalid Content Type');
                break;
        }

        if (count($content) < 1) {
            return back()->with('message', 'Data Not found');
        }
        else{

            // print_r($content);exit;
            foreach ($content as $item) {
                // detect the correct field name
                $field = null;

                if (isset($item->movie_url)) $field = 'movie_url';
                elseif (isset($item->url)) $field = 'url';
                elseif (isset($item->video_url)) $field = 'video_url';
                elseif (isset($item->channel_link)) $field = 'channel_link';

                if ($field) {
                    $updated_url = str_replace($old_url, $new_url, $item->$field);
                    $item->$field = $updated_url;
                    $item->save();
                }
            }
        }



        if (!empty($request->id)) {
            $url_settings = UrlSetting::where('id', $request->id)->first();
        }
        else{
            $url_settings = new UrlSetting();
        }

        $url_settings->old_url = $request->old_url;
        $url_settings->new_url = $request->new_url;
        $url_settings->content_network_id = $request->content_network_id;
        $url_settings->content_type = $request->content_type;

    
        if ($url_settings->save()) {            
            return back()->with('message', 'Urls Updated');
        }                    
    }


    public function saveChannel(Request $request){
        $request->validate([
            'old_url' => 'required',
            'new_url' => 'required'
        ]);

        $old_url = $request->old_url;
        $new_url = $request->new_url;

        $content = Channel::where('channel_link', 'LIKE', '%' . $old_url . '%')->get();


        // print_r($content);exit;
        foreach ($content as $item) {
            // detect the correct field name
            $field = null;

            if (isset($item->movie_url)) $field = 'movie_url';
            elseif (isset($item->url)) $field = 'url';
            elseif (isset($item->video_url)) $field = 'video_url';
            elseif (isset($item->channel_link)) $field = 'channel_link';

            if ($field) {
                $updated_url = str_replace($old_url, $new_url, $item->$field);
                $item->$field = $updated_url;
                $item->save();
            }
        }


        return back()->with('message', 'Urls Updated');




    }

    public function edit($id){
        $url_setting = UrlSetting::where('id', base64_decode($id))->first();
        $content_networks = ContentNetwork::all();
        return view('admin.url_settings.add', compact('content_networks', 'url_setting'));
    }
}
