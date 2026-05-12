<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebSeries;
use App\Models\WebSeriesSeason;
use App\Models\WebSeriesEpisode;
use App\Models\WebseriesSlider;

use App\Models\Genre;
use App\Models\WebSeriesGenre;
use App\Models\ContentNetwork;
use App\Models\WebSeriesContentNetwork;
use Illuminate\Support\Facades\DB;

class Webserieses extends Controller
{
    public function index(){
        $movie_network_ids = WebSeriesContentNetwork::pluck('network_id')->unique()->values();
        $networks = ContentNetwork::whereIn('id', $movie_network_ids)->get();
        return view('admin.webseries.index', compact('networks'));
    }
    
    public function getWebseriesOrderList()
    {
        $this->data['webseries'] = WebSeries::whereNull('deleted_at')->orderBy('series_order', 'asc')->get();

        $allWebseries = [];
        $dataForLoop = [];

        foreach ($this->data['webseries'] as $webseries) {
            $allWebseries[] = $webseries->series_order;
            $dataForLoop[$webseries->series_order] = $webseries;
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allWebseries'] = $allWebseries;

        return view('admin.webseries.dragdrop', $this->data);
    }

    /* Process ajax request */
    public function getWebseriesList(Request $request){
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


        $totalRecords = WebSeries::select('count(*) as allcount')->whereNull('web_series.deleted_at');
        $inactiveRecords = WebSeries::select('count(*) as allcount')->where('status','0')->whereNull('web_series.deleted_at');
        $activeRecords = WebSeries::select('count(*) as allcount')->where('status','1')->whereNull('web_series.deleted_at');
        $deletedRecords = WebSeries::select('count(*) as allcount')->whereNotNull('web_series.deleted_at');

        if ($request->has('network_id') && $request->network_id !== '') {
            $network_id = $request->network_id;
            $movie_ids = WebSeriesContentNetwork::whereIn('network_id', (array) $network_id)->pluck('webseries_id')->toArray();                
        }

        if (!empty($movie_ids)) {
            
            $totalRecords = $totalRecords->whereIn('id', $movie_ids);
            $inactiveRecords = $inactiveRecords->whereIn('id', $movie_ids);
            $activeRecords = $activeRecords->whereIn('id', $movie_ids);
            $deletedRecords = $deletedRecords->whereIn('id', $movie_ids);
        }

        $totalRecords = $totalRecords->count();
        $inactiveRecords = $inactiveRecords->count();
        $activeRecords = $activeRecords->count();
        $deletedRecords = $deletedRecords->count();


        $totalRecordswithFilter = WebSeries::select('count(*) as allcount')
        ->where('name', 'like', '%' . $searchValue . '%')
        // ->where('channels.status', '=', 1)
        ->when(request()->has('status') && request()->status !== null, function ($query) {
                $query->where('web_series.status', request()->status);
            })
        ->when(request()->has('network_id') && request()->network_id !== null, function ($query) {
            $movie_ids = WebSeriesContentNetwork::whereIn('network_id', (array) request()->network_id)->pluck('webseries_id')->toArray();
            $query->whereIn('web_series.id', $movie_ids);
        })
        ->whereNull('web_series.deleted_at')
        ->count();

        // Get records, also we have included search filter as well

    
        $records = WebSeries::orderBy($columnName, $columnSortOrder)->whereNull('web_series.deleted_at')
            ->where('web_series.name', 'like', '%' . $searchValue . '%')
            ->when(request()->has('status') && request()->status !== null, function ($query) {
                $query->where('web_series.status', request()->status);
            })
            ->when(request()->has('network_id') && request()->network_id !== null, function ($query) {
                $movie_ids = WebSeriesContentNetwork::whereIn('network_id', (array) request()->network_id)->pluck('webseries_id')->toArray();
                $query->whereIn('web_series.id', $movie_ids);
            })

            ->select('web_series.*')->orderBy('web_series.updated_at','desc')            
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('web-seies/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('web-seies/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if($record->deleted_at){
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            }else{
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            $data_arr[] = array(
                "name" => $record->name,                                                            
                "status" => $status,
                "banner" => '<img src="'.$record->banner.'" width="100px">',

                "created_at" => date('j M Y h:i a',strtotime($record->updated_at)),
                "action" => '<div class="action-btn">
                        <a href="edit-webseries/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="web-series-season/'.base64_encode($record->id).'" title="Manage Seasons"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path></svg></a>                        
                        <a href="javascript:;" onclick="deleteRowModal(\''.base64_encode($record->id).'\')">'.$del_icon.'</a>
                        
                      </div>',
            );

            // <a href="webseries-slider/'.base64_encode($record->id).'" title="Manage Slider"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></a>
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

    public function create(){        
        $this->data['genres'] = Genre::where('status',1)->get();
        $this->data['networks'] = ContentNetwork::where('deleted_at', null)->get();
        return view('admin.webseries.add',$this->data);
    }

    public function save(Request $request){
        $request->validate([
            'name' => 'required',            
            'webseries_genre' => 'required',            
            'banner' => 'required',                        
            'webseries_description' => 'sometimes',                                
        ]);         
        if(!empty($request->id)){

            $webseries = WebSeries::firstwhere('id',$request->id);
            
            $webseries->name = $request->name;
            $webseries->banner = $request->banner;                                  
            $webseries->description = $request->webseries_description;                        
            $webseries->release_date = $request->release_date ?? null;                                                                    
            $webseries->status = $request->status;
            $webseries->series_order = $request->order ?? 0;
            $webseries->youtube_trailer = $request->trailer_url ?? null;
            $webseries->genres = implode(',', $request->webseries_genre);
            if (!empty($request->hidden_countries) && is_array($request->hidden_countries)) {
                $webseries->countries = implode(',', $request->hidden_countries);
            } else {
                $webseries->countries = ''; // ya empty string '' rakho agar column string hai
            }
            

            // print_r($webseries); exit;
            if($webseries->save()){

                WebSeriesContentNetwork::where('webseries_id',$webseries->id)->delete();
                DB::table('content_network_log')->where('content_id', $webseries->id)->where('content_type', $webseries->content_type)->delete();                                           
                if ($request->has('content_network') && !empty($request->content_network)) {                    
                    foreach ($request->content_network as $key => $network) {
                        $MovieNetwork = new WebSeriesContentNetwork();
                        $MovieNetwork->webseries_id  = $webseries->id;
                        $MovieNetwork->network_id = $network;                    
    
                        if ($MovieNetwork->save()) {                             
                            DB::table('content_network_log')->insert([
                                'content_id' => $webseries->id,
                                'network_id' => $network,
                                'content_type' => $webseries->content_type,                            
                            ]);
                        }
                    }
                }

                return back()->with('message','Webseries updated successfully');
            }else{
                return back()->with('message','Webseries not updated successfully');
            }

        }else{

            $added_series = WebSeries::whereNull('deleted_at')->get();
            foreach ($added_series as $key => $series) {
                if($series->name == $request->name){
                    return redirect()->back()->withInput()->withErrors(['message' => 'Webseries with the same name already exists.']);
                }
            }

            $webseries = new WebSeries();
            $webseries->name = $request->name;
            $webseries->banner = $request->banner;                                  
            $webseries->description = $request->webseries_description;                        
            $webseries->release_date = $request->release_date ?? null;                                                                    
            $webseries->status = $request->status;
            $webseries->series_order = $request->order ?? 0;
            $webseries->youtube_trailer = $request->trailer_url ?? null;
            $webseries->genres = implode(',', $request->webseries_genre);
            if (!empty($request->hidden_countries) && is_array($request->hidden_countries)) {
                $webseries->countries = implode(',', $request->hidden_countries);
            } else {
                $webseries->countries = ''; // ya empty string '' rakho agar column string hai
            }
            
            if($webseries->save()){                

                if ($request->has('content_network') && !empty($request->content_network)) {
                    $cur_webseries = WebSeries::where('id', $webseries->id)->first();                
                    foreach ($request->content_network as $key => $network) {
                        $MovieNetwork = new WebSeriesContentNetwork();
                        $MovieNetwork->webseries_id  = $webseries->id;
                        $MovieNetwork->network_id = $network;                    
    
                        if ($MovieNetwork->save()) {
                            DB::table('content_network_log')->insert([
                                'content_id' => $webseries->id,
                                'network_id' => $network,
                                'content_type' => $cur_webseries->content_type,                            
                            ]);
                        }
                    }
                }                
                return redirect()->route('admin.webseries.seasons', base64_encode($webseries->id))->with('message', 'Webseries added successfully');
            }else{
                return back()->with('message','Webseries not added successfully');
            }
        }

    }

    public function edit($id){  
        $webseries = Webseries::where('id', base64_decode($id))->first();              
        $this->data['webseries']  = $webseries;
        $this->data['genres'] = Genre::where('status',1)->get();
        $this->data['networks'] = ContentNetwork::where('deleted_at', null)->get();
        
        $channelGenre = explode(',', $webseries->genres);
        $this->data['channelGenre'] = $channelGenre;
        $this->data['movieContries'] = explode(',', $webseries->countries);
        // $channelGenreIds = Genre::whereIn('title', $channelGenre)->pluck('id')->toArray();

        $seriesNetwork = WebSeriesContentNetwork::where('webseries_id', base64_decode($id))->get();
        // print_r($channelGenre); exit();
        // $this->data['channelGenre'] = [];
        $this->data['seriesNetwork'] = [];
        

        if($seriesNetwork){
            foreach ($seriesNetwork as $key => $value) {
                $this->data['seriesNetwork'][] = $value->network_id;
            }
        }
        // print_r($this->data['seriesNetwork']); exit;
        return view('admin.webseries.add',$this->data);
    }

    public function destroy(Request $request){
        $webseries = Webseries::where('id',base64_decode($request->id))->first();
        $webseries->deleted_at = time();
        if($webseries->save()){
            $seasons = WebSeriesSeason::where('web_series_id', $webseries->id)->get();

            if ($seasons) {                
                foreach ($seasons as $key => $season) {
                    $episodes = WebSeriesEpisode::where('seson_id', $season->id)->get();
                    if ($episodes) {                    
                        WebSeriesEpisode::where('season_id', $season->id)->delete();
                    }
                } 
                WebSeriesSeason::where('web_series_id', $webseries->id)->delete();           
            }
            echo json_encode(['message','Webseries deleted successfully']);
        }else{
            echo json_encode(['message','Webseries not deleted successfully']);
        }
    }

    public function saveWebseriesOrder(Request $request)
    {
        $ids = $request->ids;

        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                WebSeries::where('id', $id)->update(['series_order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Webseries order updated successfully.');
    }

    public function updateStatus($id){
        $webseries = Webseries::find(base64_decode($id));        
        if($webseries){
            $webseries->status = $webseries->status == '1' ? '0' : '1';
            $webseries->save();
            echo json_encode(['message','Webseries status updated successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }

    public function updateSliderStatus($id){
        $episode = WebseriesSlider::find(base64_decode($id));        
        if($episode){
            $episode->status = $episode->status == '1' ? '0' : '1';
            $episode->save();
            echo json_encode(['message','Status updated successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }


    public function sliders(Request $request, $id){
        $id = base64_decode($id);
        return view('admin.webseries_slider.index', compact('id'));
    }

    /*get rolse by ajax*/
    public function getSliderList(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'title',
            2=> 'created_at',
            // 4=> 'id',
        );

        $id = $request->id;
        $totalData = WebseriesSlider::whereNull('deleted_at')->where('webseries_id', $id)->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        // $order = $columns[$request->input('order[0].column')];
        if($request->input('order.0.column')){
            $order = $columns[$request->input('order.0.column')];
        }else{
            $order = 'id';
        }
        if($request->input('order.0.column')){
            $dir = $request->input('order.0.dir');
        }else{
            $dir = 'desc';
        }


        if(empty($request->input('search.value')))
        {
        $sliders = WebseriesSlider::offset($start)
        ->whereNull('deleted_at')
        ->where('webseries_id', $id)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        }
        else {
        $search = $request->input('search.value');

        $sliders = WebseriesSlider::where('id','LIKE',"%{$search}%")
        ->where('webseries_id', $id)
        ->whereNull('deleted_at')
        ->orWhere('title', 'LIKE',"%{$search}%")

        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();

        $totalFiltered = WebseriesSlider::where('id','LIKE',"%{$search}%")
        ->orWhere('title', 'LIKE',"%{$search}%")
        ->count();
        }

        $data = array();
        if(!empty($sliders))
        {
            foreach ($sliders as $slider)
            {
                // $show = route('sliders.show',$slider->id);
                // $edit = route('sliders.edit',$slider->id);

                $slidersData['image'] = '<img src="'.$slider->banner.'" width="100px">';
                $slidersData['title'] = $slider->title;
                $slidersData['content_type'] = $slider->content_type == '1' ? 'Movie' : 'Live channel';
                if($slider->status == 1){
                    // $slidersData['status'] = 'Active';
                    $slidersData['status'] = '<a onchange="updateStatus(\''.url('websereis-slider/update-status',base64_encode($slider->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$slider->id}}"><span class="slider round"></span></label> </a>';
                }else{
                     // $slidersData['status'] = 'Inactive';
                    $slidersData['status'] = '<a onchange="updateStatus(\''.url('websereis-slider/update-status',base64_encode($slider->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$slider->id}}"><span class="slider round"></span></label></a>';
                }

                $slidersData['created_at'] = date('j M Y h:i a',strtotime($slider->created_at));
                // $slidersData['action'] = '<div class="action-btn"><a></a></div>';

                $slidersData['action'] = '<div class="action-btn">

                        <a href="/edit-webseries-slider/'.base64_encode($slider->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="javascript:;" onclick="deleteRowModal(\''.base64_encode($slider->id).'\')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                      </div>';

                $data[] = $slidersData;

            }
        }

        $json_sliders_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data"=> $data
        );

        echo json_encode($json_sliders_data);
    }

    public function addslider($id){

        // $movies = Movie::whereNull('deleted_at')->get();
        // $livechannels = Channel::whereNull('deleted_at')->get();
        // $serieses  = WebSeries::whereNull('deleted_at')->get();

        $id = base64_decode($id);

        return view('admin.webseries_slider.add', compact('id'));
    }


    public function add(Request $request){
        
        $request->validate([
            'title' => 'required',
            // 'url' => 'required',                        
            'image' =>'required',
        ]);

        if(!empty($request->id)){
            $slider = WebseriesSlider::firstwhere('id',$request->id);            
            $slider->banner = $request->image;
            $slider->title = $request->title;            
            $slider->content_type = 1;            
            $slider->webseries_id = $request->webseries_id;            
            // $slider->content_id = $content_id;   
            $slider->source_type = $request->source_type ?? null;            
            $slider->url = $request->url ?? null;      
            $slider->status = $request->status;
            if($slider->save()){
                return back()->with('message','Slider updated successfully');
            }else{
                return back()->with('message','Slider not updated successfully');
            }

        }else{
            $slider = new WebseriesSlider();

            $slider->banner = $request->image;
            $slider->title = $request->title;            
            $slider->content_type = 1; 
            $slider->webseries_id = $request->webseries_id;                                 
            // $slider->content_id = $content_id;  
            $slider->source_type = $request->source_type ?? null;         
            $slider->url = $request->url ?? null;      
            $slider->status = $request->status;
            if($slider->save()){
                return back()->with('message','Slider added successfully');
            }else{
                return back()->with('message','Slider not added successfully');
            }
        }

    }

    public function editSlider($id){
        $slider = WebseriesSlider::where('id',base64_decode($id))->first();
        $this->data['slider'] = $slider;        
        return view('admin.webseries_slider.add',$this->data);
    }

    public function destroySlider(Request $request){        
        $slider = WebseriesSlider::where('id',base64_decode($request->id))->first();
        $slider->deleted_at = time();
        if($slider->save()){
            echo json_encode(['message','Slider deleted successfully']);
        }else{
            echo json_encode(['message','Slider not deleted successfully']);
        }
    }
}
