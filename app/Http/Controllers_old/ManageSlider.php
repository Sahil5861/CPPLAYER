<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slider;
use App\Models\Movie;
use App\Models\Channel;
use App\Models\WebSeries;

class ManageSlider extends Controller
{
    public function index()
    {
        return view('admin.slider.index');
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

        $totalData = Slider::whereNull('deleted_at')->count();
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
        $sliders = Slider::offset($start)
        ->whereNull('deleted_at')
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        }
        else {
        $search = $request->input('search.value');

        $sliders = Slider::where('id','LIKE',"%{$search}%")
        ->whereNull('deleted_at')
        ->orWhere('title', 'LIKE',"%{$search}%")

        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();

        $totalFiltered = Slider::where('id','LIKE',"%{$search}%")
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
                    $slidersData['status'] = '<a onchange="updateStatus(\''.url('slider/update-status',base64_encode($slider->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$slider->id}}"><span class="slider round"></span></label> </a>';
                }else{
                     // $slidersData['status'] = 'Inactive';
                    $slidersData['status'] = '<a onchange="updateStatus(\''.url('slider/update-status',base64_encode($slider->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$slider->id}}"><span class="slider round"></span></label></a>';
                }

                $slidersData['created_at'] = date('j M Y h:i a',strtotime($slider->created_at));
                // $slidersData['action'] = '<div class="action-btn"><a></a></div>';

                $slidersData['action'] = '<div class="action-btn">

                        <a  href="edit-slider/'.base64_encode($slider->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="javascript:;" onclick="delete_item(\''.base64_encode($slider->id).'\',\'slider\')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
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

    public function addslider(){

        // $movies = Movie::whereNull('deleted_at')->get();
        // $livechannels = Channel::whereNull('deleted_at')->get();
        // $serieses  = WebSeries::whereNull('deleted_at')->get();

        return view('admin.slider.add');
    }

    public function updateStatus($id){
        $slider = Slider::find(base64_decode($id));        

        if($slider){
            $slider->status = $slider->status == '1' ? '0' : '1';
            $slider->save();
            echo json_encode(['message','Slider status successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }


    public function add(Request $request){
        
        $request->validate([
            'title' => 'required',
            // 'url' => 'required',                        
            'image' =>'required',
        ]);

        // $content_id = '';
        // if ($request->has('content_id_movie') && $request->content_id_movie != '') {
        //     $content_id = $request->content_id_movie;
        // }
        // else if ($request->has('content_id_series') && $request->content_id_series != '') {
        //     $content_id = $request->content_id_series;
        // }
        // else{
        //     $content_id = $request->content_id_channel;
        // }

        if(!empty($request->id)){
            $slider = slider::firstwhere('id',$request->id);            
            $slider->banner = $request->image;
            $slider->title = $request->title;            
            $slider->content_type = 1;            
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
            $slider = new slider();

            $slider->banner = $request->image;
            $slider->title = $request->title;            
            $slider->content_type = 1;                      
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
        $slider = Slider::where('id',base64_decode($id))->first();
        $this->data['slider'] = $slider;        
        // print_r($this->data['slider']);die;
        // $this->data['movies'] = Movie::whereNull('deleted_at')->get();
        // $this->data['livechannels'] = Channel::whereNull('deleted_at')->get();
        // $this->data['serieses'] = WebSeries::whereNull('deleted_at')->get();

        return view('admin.slider.add',$this->data);
    }

    public function destroy(Request $request){
        // $slider = slider::firstwhere('id',$request->id);
        $slider = Slider::where('id',base64_decode($request->id))->first();
        $slider->deleted_at = time();
        if($slider->save()){
            echo json_encode(['message','Slider deleted successfully']);
        }else{
            echo json_encode(['message','Slider not deleted successfully']);
        }
    }


    public function getMovieById(Request $request){
        $id = $request->id;

        $movie = Movie::where('id', $id)->first();
        if($movie){
            return response()->json([
                'success' => true,
                'title' => $movie->name,
                'banner' => $movie->banner,
                'url' => $movie->youtube_trailer,
                'source_type' => 'Youtube',
            ]);
        }
    }

    public function getChannelById(Request $request){
        $id = $request->id;

        $channel = Channel::where('id', $id)->first();
        if($channel){
            return response()->json([
                'success' => true,
                'title' => $channel->channel_name,
                'banner' => $channel->channel_bg,
                'url' => $channel->channel_link,
                'source_type' => 'm3u8',
            ]);
        }
    }

    public function getSeriesById(Request $request){
        $id = $request->id;

        $series = WebSeries::where('id', $id)->first();
        if($series){
            return response()->json([
                'success' => true,
                'title' => $series->name,
                'banner' => $series->banner,
                'url' => $series->youtube_trailer,
                'source_type' => 'Youtube',
            ]);
        }
    }
}
