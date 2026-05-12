<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Language;

class ManageLanguage extends Controller
{
    public function index()
    {
        return view('admin.language.index');
    }

    /*get rolse by ajax*/
    public function getLanguageList(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'title',
            2 =>'logo',
            // 2=> 'body',
            3=> 'created_at',
            // 4=> 'id',
        );

        $totalData = Language::whereNull('deleted_at')->count();
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
            $dir = 'asc';
        }
        

        if(empty($request->input('search.value')))
        {
        $languages = Language::offset($start)
        ->whereNull('deleted_at')
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        }
        else {
        $search = $request->input('search.value');

        $languages = Language::where('id','LIKE',"%{$search}%")
        ->whereNull('deleted_at')
        ->orWhere('title', 'LIKE',"%{$search}%")

        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();

        $totalFiltered = Language::where('id','LIKE',"%{$search}%")
        ->orWhere('title', 'LIKE',"%{$search}%")
        ->count();
        }

        $data = array();
        if(!empty($languages))
        {
            foreach ($languages as $language)
            {
                // $show = route('languages.show',$language->id);
                // $edit = route('languages.edit',$language->id);

                $languagesData['id'] = $language->id;
                $languagesData['title'] = $language->title;
                $languagesData['logo'] = '<img src="'.$language->logo.'" alt="" width="70">';
                if($language->status == 1){
                    // $languagesData['status'] = 'Active';
                    $languagesData['status'] = '<a onchange="updateStatus(\''.url('language/update-status',base64_encode($language->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$language->id}}"><span class="slider round"></span></label> </a>';
                }else{
                    $languagesData['status'] = '<a onchange="updateStatus(\''.url('language/update-status',base64_encode($language->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$language->id}}"><span class="slider round"></span></label></a>';
                    $languagesData['status'] = 'Inactive';
                }
                
                $languagesData['created_at'] = date('j M Y h:i a',strtotime($language->created_at));
                // $languagesData['action'] = '<div class="action-btn"><a></a></div>';

                $languagesData['action'] = '<div class="action-btn">
                        <a  href="edit-language/'.base64_encode($language->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="javascript:;" onclick="deleteRowModal(\''.base64_encode($language->id).'\')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                        <a href="'.route('admin.sliders.language', base64_encode($language->id)).'" title="Manage Slider"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></a>
                      </div>';

                $data[] = $languagesData;

            }
        }

        $json_languages_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data"=> $data
        );

        echo json_encode($json_languages_data);
    }

    public function updateStatus($id){ 
        $language = Language::find(base64_decode($id));

        if($language){
            $language->status = $language->status == '1' ? '0' : '1';
            $language->save();
            echo json_encode(['message','Language status successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);   
        }
    }

    public function addlanguage(){
        return view('admin.language.add');
    }


    public function add(Request $request){
        $request->validate([
            'title' => 'required',
        ]);

        if(!empty($request->id)){
            $language = Language::firstwhere('id',$request->id);
            $language->title = $request->title;
            $language->logo = $request->logo;
            $language->status = $request->status;
            if($language->save()){
                return back()->with('message','Language updated successfully');
            }else{
                return back()->with('message','Language not updated successfully');
            }
            
        }else{
            $language = new Language();
            $language->title = $request->title;
            $language->logo = $request->logo;
            $language->status = $request->status;
            if($language->save()){
                return back()->with(['message','Language added successfully']);
            }else{
                return back()->with(['message','Language not added successfully']);
            }
        }
        
    }

    public function editlanguage($id){ 
        $this->data['language'] = Language::where('id',base64_decode($id))->first();
        // print_r($this->data['language']);die;
        return view('admin.language.add',$this->data);
    }

    public function destroy(Request $request){
        // $language = language::firstwhere('id',$request->id);
        $language = language::where('id',base64_decode($request->id))->first();
        $language->deleted_at = time();
        if($language->save()){
            // echo json_encode('message','Language deleted successfully');

            return response()->json([
                'status' => true,
                'message' => 'Language Deleted'
            ]);
        }else{
            echo json_encode('message','Language not deleted successfully');
        }
    }
}
