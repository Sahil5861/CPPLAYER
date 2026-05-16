<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KidsChannel;
use App\Models\KidsChannelContentNetwork;
use App\Models\ContentNetwork;
use Illuminate\Support\Facades\DB;
use App\Models\Language;


class ManageKidsChannel extends Controller
{
    public function index()
    {
        return view('admin.kidschannel.index');
    }
    public function getKidsChannelOrderList()
    {
        $this->data['kidschannels'] = KidsChannel::whereNull('deleted_at')->orderBy('order', 'asc')->get();

        $allKidsChannels = [];
        $dataForLoop = [];

        foreach ($this->data['kidschannels'] as $kidschannel) {
            $allKidsChannels[] = $kidschannel->order;
            $dataForLoop[$kidschannel->order] = $kidschannel;
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allKidsChannels'] = $allKidsChannels;

        return view('admin.kidschannel.dragdrop', $this->data);
    }

    
    public function getKidsChannelList(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'created_at',
        ];

        $totalData = KidsChannel::whereNull('deleted_at')->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $request->input('order.0.column') ? $columns[$request->input('order.0.column')] : 'id';
        $dir = $request->input('order.0.dir') ?? 'desc';

        if (empty($request->input('search.value'))) {
            $kidschannel = KidsChannel::whereNull('deleted_at')
            ->when(request()->has('status') && request()->status !== null, function ($query) {
                    $query->where('status', request()->status);
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $kidschannel = KidsChannel::where(function($query) use ($search) {
                    $query->where('id', 'LIKE', "%{$search}%")
                          ->orWhere('name', 'LIKE', "%{$search}%");
                })
                ->whereNull('deleted_at')
                ->when(request()->has('status') && request()->status !== null, function ($query) {
                    $query->where('status', request()->status);
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = KidsChannel::where(function($query) use ($search) {
                $query->where('id', 'LIKE', "%{$search}%")
                      ->orWhere('name', 'LIKE', "%{$search}%");
            })
            ->whereNull('deleted_at')
            ->count();
        }

        $data = [];
        foreach ($kidschannel as  $key => $tvchannel) {
            $kidschannelData['id'] = $key + 1;
            $kidschannelData['name'] = $tvchannel->name;
            $kidschannelData['logo'] = $tvchannel->logo ?? '';
            $kidschannelData['language'] = $tvchannel->language ?? '';
            $kidschannelData['description'] = $tvchannel->description ?? '';

            $statusUrl = url('kids-channel-update-status', base64_encode($tvchannel->id));
            $checked = $tvchannel->status == 1 ? 'checked' : '';
            $kidschannelData['status'] = '<a onchange="updateStatus(\'' . $statusUrl . '\')" href="javascript:void(0);">
                <label class="switch s-primary mr-2">
                    <input type="checkbox" value="' . $tvchannel->status . '" ' . $checked . ' id="accountSwitch' . $tvchannel->id . '">
                    <span class="slider round"></span>
                </label>
            </a>';

            $kidschannelData['action'] = '<div class="action-btn">
                                            <a href="kids-channel-edit/' . base64_encode($tvchannel->id) . '">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </a>

                                            <a href="kids-shows/'.base64_encode($tvchannel->id).'" title="Manage Kids shows"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path></svg></a>

                                            <a href="javascript:;" onclick="deleteRowModal(\''.base64_encode($tvchannel->id) . '\')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            </a>
                                        </div>';

            $data[] = $kidschannelData;
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        ]);
    }

    public function add()
    {
        $languages = Language::all();
        $content_networks = ContentNetwork::all();
        return view('admin.kidschannel.add', compact('languages', 'content_networks'));
    }

    public function updateStatus($id)
    {
        $tvchannel = KidsChannel::find(base64_decode($id));

        if ($tvchannel) {
            $tvchannel->status = $tvchannel->status == 1 ? 0 : 1;
            $tvchannel->save();
            return response()->json(['message' => 'KidsChannel status updated successfully']);
        }

        return response()->json(['message' => 'Something went wrong!!']);
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        if (!empty($request->id)) {
            $kidschannel = KidsChannel::find($request->id);
        } else {
            $kidschannel = new KidsChannel();
        }

        // print_r($request->all()); exit;

        $kidschannel->name = $request->name;
        $kidschannel->language = $request->language ?? null;
        $kidschannel->logo = $request->logo ?? null;
        $kidschannel->order = $request->order ?? 0;
        $kidschannel->description = $request->description ?? null;
        $kidschannel->status = $request->status;

        if ($kidschannel->save()) {
            KidsChannelContentNetwork::where('show_id',$kidschannel->id)->delete();
            DB::table('content_network_log')->where('content_id', $kidschannel->id)->where('content_type', '6')->delete();                                                                

            if ($request->has('content_networks') && !empty($request->content_networks)) {                                        
                foreach ($request->content_networks as $key => $network) {
                    $MovieNetwork = new KidsChannelContentNetwork();
                    $MovieNetwork->show_id = $kidschannel->id;
                    $MovieNetwork->network_id = $network;
                    if ($MovieNetwork->save()) {                           
                        DB::table('content_network_log')->insert([
                            'content_id' => $kidschannel->id,
                            'network_id' => $network,
                            'content_type' => '6',                            
                        ]);
                    }
                }
            }

            if (!empty($request->id)) {
                return back()->with('message', "Kid's Channel updated Successfully !");
            }
            else{
                return redirect()->route('admin.kidsshows', base64_encode($kidschannel->id))->with('message', "Kids Channel added Successfully !");                
            }


        }
        else{
            return back()->with('message', 'Something went wrong !');
        }

        

        if (!empty($request->id)) {
            if ($kidschannel->save()) {
                return back()->with('message', "Kid's Channel updated Successfully !");
            }
            else{
                return back()->with('message', "Kid's Channel not updated Successfully !");
            }
            
        } else {
            if ($kidschannel->save()) {
                return redirect()->route('admin.kidsshows', base64_encode($kidschannel->id))->with('message', "Kids Channel added Successfully !");                
            }
            else{
                return back()->with('message', "Kid's Channel not added Successfully !");
            }
        }
        
    }

    public function edit($id)
    {
        $this->data['tvchannel'] = KidsChannel::find(base64_decode($id));
        $this->data['languages'] = Language::all();

        $this->data['content_networks'] = ContentNetwork::all();

        $this->data['current_networks'] = KidsChannelContentNetwork::where('show_id', base64_decode($id))->get()->pluck('network_id')->toArray();        
        return view('admin.kidschannel.add', $this->data);
    }

    public function destroy(Request $request)
    {
        $tvchannel = KidsChannel::find(base64_decode($request->id));
        $tvchannel->deleted_at = time();

        if ($tvchannel->save()) {
            return response()->json(['message' => 'KidsChannel deleted successfully']);
        } else {
            return response()->json(['message' => 'KidsChannel not deleted']);
        }
    }
    public function saveKidsChannelOrder(Request $request)
    {
        $ids = $request->ids;

        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                KidsChannel::where('id', $id)->update(['order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Kids Channel order updated successfully.');
    }
}
