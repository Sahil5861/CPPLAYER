<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LanguageSlider;
use App\Models\Language;


class LanuguageSlider extends Controller
{
    public function index($id)
    {
        $id = base64_decode($id);        
        return view('admin.language_slider.index', compact('id'));
    }

    /*get rolse by ajax*/
    public function getSliderList(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'title',
            2 =>'banner',
            3=> 'created_at',
            // 4=> 'id',
        );

        $id = $request->id;
        $totalData = LanguageSlider::whereNull('deleted_at')->where('language_id', $id)->count();
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
        $sliders = LanguageSlider::offset($start)
        ->whereNull('deleted_at')
        ->where('language_id', $id)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        }
        else {
        $search = $request->input('search.value');

        $sliders = LanguageSlider::where('id','LIKE',"%{$search}%")
        ->whereNull('deleted_at')
        ->where('language_id', $id)
        ->orWhere('title', 'LIKE',"%{$search}%")

        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();

        $totalFiltered = LanguageSlider::where('id','LIKE',"%{$search}%")
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
                if($slider->status == 1){
                    // $slidersData['status'] = 'Active';
                    $slidersData['status'] = '<a onchange="updateStatus(\''.url('language-slider/update-status',base64_encode($slider->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$slider->id}}"><span class="slider round"></span></label> </a>';
                }else{
                     // $slidersData['status'] = 'Inactive';
                    $slidersData['status'] = '<a onchange="updateStatus(\''.url('language-slider/update-status',base64_encode($slider->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$slider->id}}"><span class="slider round"></span></label></a>';
                }

                $slidersData['created_at'] = date('j M Y h:i a',strtotime($slider->created_at));
                // $slidersData['action'] = '<div class="action-btn"><a></a></div>';

                $slidersData['action'] = '<div class="action-btn">

                        <a href="/edit-language-slider/'.base64_encode($slider->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
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

        return view('admin.language_slider.add', compact('id'));

    }

    public function updateStatus($id){
        $slider = LanguageSlider::find(base64_decode($id));        

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
            $slider = LanguageSlider::firstwhere('id',$request->id);            
            $slider->banner = $request->image;
            $slider->language_id = $request->language_id;
            $slider->title = $request->title;                                                        
            $slider->status = $request->status;
            if($slider->save()){
                return back()->with('message','Slider updated successfully');
            }else{
                return back()->with('message','Slider not updated successfully');
            }

        }else{
            $slider = new LanguageSlider();

            $slider->banner = $request->image;
            $slider->language_id = $request->language_id;
            $slider->title = $request->title;                                               
            $slider->status = $request->status;
            if($slider->save()){
                return back()->with('message','Slider added successfully');
            }else{
                return back()->with('message','Slider not added successfully');
            }
        }

    }

    public function editSlider($id){
        $slider = LanguageSlider::where('id',base64_decode($id))->first();
        $this->data['slider'] = $slider;                
        return view('admin.language_slider.add',$this->data);
    }

    public function destroy(Request $request){
        // $slider = slider::firstwhere('id',$request->id);
        $slider = LanguageSlider::where('id',base64_decode($request->id))->first();
        $slider->deleted_at = time();
        if($slider->save()){
            echo json_encode(['message','Slider deleted successfully']);
        }else{
            echo json_encode(['message','Slider not deleted successfully']);
        }
    }
}
