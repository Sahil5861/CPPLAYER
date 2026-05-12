<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;

class ManageGenre extends Controller
{
    public function index()
    {
        return view('admin.genre.index');
    }

    /*get rolse by ajax*/
    public function getGenreList(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'title',
            // 2=> 'body',
            2=> 'created_at',
            // 4=> 'id',
        );

        $totalData = Genre::whereNull('deleted_at')->count();
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
        $genres = Genre::offset($start)
        ->whereNull('deleted_at')
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        }
        else {
        $search = $request->input('search.value');

        $genres = Genre::where('id','LIKE',"%{$search}%")
        ->whereNull('deleted_at')
        ->orWhere('title', 'LIKE',"%{$search}%")

        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();

        $totalFiltered = Genre::where('id','LIKE',"%{$search}%")
        ->orWhere('title', 'LIKE',"%{$search}%")
        ->count();
        }

        $data = array();
        if(!empty($genres))
        {
            foreach ($genres as $genre)
            {
                // $show = route('genres.show',$genre->id);
                // $edit = route('genres.edit',$genre->id);

                $genresData['id'] = $genre->id;
                $genresData['title'] = $genre->title;
                if($genre->status == 1){
                    // $genresData['status'] = 'Active';
                    $genresData['status'] = '<a onchange="updateStatus(\''.url('genre/update-status',base64_encode($genre->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$genre->id}}"><span class="slider round"></span></label> </a>';
                }else{
                     // $genresData['status'] = 'Inactive';
                    $genresData['status'] = '<a onchange="updateStatus(\''.url('genre/update-status',base64_encode($genre->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$genre->id}}"><span class="slider round"></span></label></a>';
                }

                $genresData['created_at'] = date('j M Y h:i a',strtotime($genre->created_at));
                // $genresData['action'] = '<div class="action-btn"><a></a></div>';

                $genresData['action'] = '<div class="action-btn">
                        <a  href="edit-genre/'.base64_encode($genre->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="javascript:;" onclick="delete_item(\''.base64_encode($genre->id).'\',\'genre\')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                      </div>';

                $data[] = $genresData;

            }
        }

        $json_genres_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data"=> $data
        );

        echo json_encode($json_genres_data);
    }

    public function addgenre(){
        return view('admin.genre.add');
    }

    public function updateStatus($id){
        $genre = Genre::find(base64_decode($id));

        if($genre){
            $genre->status = $genre->status == '1' ? '0' : '1';
            $genre->save();
            echo json_encode(['message','Genre status successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }


    public function add(Request $request){
        $request->validate([
            'title' => 'required',
        ]);

        if(!empty($request->id)){
            $genre = genre::firstwhere('id',$request->id);

            // if ($request->hasFile('image')) {
            //     $file = $request->file('image');
            //     $imageName=time().uniqid().$file->getClientOriginalName();
            //     $filePath = 'images/genre/' . $imageName;
            //     \Storage::disk('public')->put($filePath, file_get_contents($file));
            //     $image = $filePath;
            // }else{
            //     $image = $request->icon_image_old;
            // }
            // if ($request->hasFile('genre_bg')) {
            //     $file = $request->file('genre_bg');
            //     $imageName=time().uniqid().$file->getClientOriginalName();
            //     $filePath = 'images/genre/' . $imageName;
            //     \Storage::disk('public')->put($filePath, file_get_contents($file));
            //     $genre_bg = $filePath;
            // }else{
            //     $genre_bg = $request->icon_image_old;
            // }
            $genre->title = $request->title;
            $genre->genre_bg = $request->genre_bg ?? null;
            $genre->image = $request->image ?? null;
            $genre->description = $request->description;
            $genre->status = $request->status;
            if($genre->save()){
                return back()->with('message','Genre updated successfully');
            }else{
                return back()->with('message','Genre not updated successfully');
            }

        }else{
            $genre = new genre();
            // if ($request->hasFile('image')) {
            //     $file = $request->file('image');
            //     $imageName=time().uniqid().$file->getClientOriginalName();
            //     $filePath = 'images/genre/' . $imageName;
            //     \Storage::disk('public')->put($filePath, file_get_contents($file));
            //     $image = $filePath;
            // }else{
            //     $image = "";
            // }

            // if ($request->hasFile('genre_bg')) {
            //     $file = $request->file('genre_bg');
            //     $imageName=time().uniqid().$file->getClientOriginalName();
            //     $filePath = 'images/genre/' . $imageName;
            //     \Storage::disk('public')->put($filePath, file_get_contents($file));
            //     $genre_bg = $filePath;
            // }else{
            //     $genre_bg = "";
            // }
            $genre->title = $request->title;
            $genre->genre_bg = $request->genre_bg ?? null;
            $genre->image = $request->image ?? null;
            $genre->description = $request->description;
            $genre->status = $request->status;
            if($genre->save()){
                return back()->with('message','Genre added successfully');
            }else{
                return back()->with('message','Genre not added successfully');
            }
        }

    }

    public function editGenre($id){
        $this->data['genre'] = Genre::where('id',base64_decode($id))->first();
        // print_r($this->data['genre']);die;
        return view('admin.genre.add',$this->data);
    }

    public function destroy(Request $request){
        // $genre = genre::firstwhere('id',$request->id);
        $genre = Genre::where('id',base64_decode($request->id))->first();
        $genre->deleted_at = time();
        if($genre->save()){
            echo json_encode(['message','Genre deleted successfully']);
        }else{
            echo json_encode(['message','Genre not deleted successfully']);
        }
    }

    public function getGenreOrderList(){
        $this->data['genres'] = Genre::orderBy('index','asc')->get();
        return view('admin.genre.dragdrop',$this->data);
    }

    public function saveGenreOrders(Request $request)
    {
        // code...
        // echo "<pre>"; print_r($request->all()); die;
        $data = $request->numbers;
       

       
        foreach ($data as $key => $item) {
            
            $genre = Genre::where('id',$item)->first();
            // print_r($genre); exit();
            $genre->index = $key + 1;
            $genre->save();
            
        }
        
        return back()->with('message','Genre ordered successfully');
    }

}
