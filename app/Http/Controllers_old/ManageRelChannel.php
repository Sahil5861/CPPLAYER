<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RelChannel;
use App\Models\Language;
use App\Models\ContentNetwork;
use Illuminate\Support\Facades\DB;
use App\Models\RelChannelContentNetwork;


class ManageRelChannel extends Controller
{
    public function index()
    {
        return view('admin.relchannel.index');
    }
    
    public function getRelChannelOrderList()
    {
        $this->data['relchannels'] = RelChannel::whereNull('deleted_at')->orderBy('rel_order', 'asc')->get();

        $allRelChannels = [];
        $dataForLoop = [];

        foreach ($this->data['relchannels'] as $relchannel) {
            $allRelChannels[] = $relchannel->rel_order;
            $dataForLoop[$relchannel->rel_order] = $relchannel;
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allRelChannels'] = $allRelChannels;

        return view('admin.relchannel.dragdrop', $this->data);
    }

    public function getRelChannelList(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'created_at',
        ];

        $totalData = RelChannel::whereNull('deleted_at')->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $request->input('order.0.column') ? $columns[$request->input('order.0.column')] : 'id';
        $dir = $request->input('order.0.dir') ?? 'desc';

        if (empty($request->input('search.value'))) {
            $relchannel = RelChannel::whereNull('deleted_at')
            ->when(request()->has('status') && request()->status !== null, function ($query) {
                    $query->where('status', request()->status);
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $relchannel = RelChannel::where(function($query) use ($search) {
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

            $totalFiltered = RelChannel::where(function($query) use ($search) {
                $query->where('id', 'LIKE', "%{$search}%")
                      ->orWhere('name', 'LIKE', "%{$search}%");
            })
            ->whereNull('deleted_at')
            ->count();
        }

        $data = [];
        foreach ($relchannel as  $key => $channel) {
            $relchannelData['id'] = $key + 1;
            $relchannelData['name'] = $channel->name;
            $relchannelData['logo'] = $channel->logo ?? '';            
            $relchannelData['description'] = $channel->description ?? '';

            $statusUrl = url('religious-channel-update-status', base64_encode($channel->id));
            $checked = $channel->status == 1 ? 'checked' : '';
            $relchannelData['status'] = '<a onchange="updateStatus(\'' . $statusUrl . '\')" href="javascript:void(0);">
                <label class="switch s-primary mr-2">
                    <input type="checkbox" value="' . $channel->status . '" ' . $checked . ' id="accountSwitch' . $channel->id . '">
                    <span class="slider round"></span>
                </label>
            </a>';

            $relchannelData['action'] = '<div class="action-btn">
                                            <a href="religious-channel-edit/' . base64_encode($channel->id) . '">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </a>

                                            <a href="religious-shows/'.base64_encode($channel->id).'" title="Manage Religious Channels"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path></svg></a>

                                            <a href="javascript:;" onclick="deleteRowModal(\''.base64_encode($channel->id) . '\')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            </a>
                                        </div>';

            $data[] = $relchannelData;
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
        return view('admin.relchannel.add', compact('languages', 'content_networks'));
    }

    public function updateStatus($id)
    {
        $channel = RelChannel::find(base64_decode($id));

        if ($channel) {
            $channel->status = $channel->status == 1 ? 0 : 1;
            $channel->save();
            return response()->json(['message' => 'RelChannel status updated successfully']);
        }

        return response()->json(['message' => 'Something went wrong!!']);
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        if (!empty($request->id)) {
            $relchannel = RelChannel::find($request->id);
        } else {
            $relchannel = new RelChannel();
            $relchannel->rel_order = $request->rel_order ?? 0;
        }

        // print_r($request->all()); exit;

        $relchannel->name = $request->name;
        $relchannel->language = $request->language ?? null;
        $relchannel->logo = $request->logo ?? null;
        $relchannel->description = $request->description ?? null;
        $relchannel->status = $request->status;

        if ($relchannel->save()) {
            RelChannelContentNetwork::where('show_id',$relchannel->id)->delete();
            DB::table('content_network_log')->where('content_id', $relchannel->id)->where('content_type', '7')->delete();                                                                

            if ($request->has('content_networks') && !empty($request->content_networks)) {                                        
                foreach ($request->content_networks as $key => $network) {
                    $MovieNetwork = new RelChannelContentNetwork();
                    $MovieNetwork->show_id = $relchannel->id;
                    $MovieNetwork->network_id = $network;
                    if ($MovieNetwork->save()) {                           
                        DB::table('content_network_log')->insert([
                            'content_id' => $relchannel->id,
                            'network_id' => $network,
                            'content_type' => '7',                            
                        ]);
                    }
                }
            }
        }

        

        if (!empty($request->id)) {
            if ($relchannel->save()) {
                return back()->with('message', "Channel updated Successfully !");
            }
            else{
                return back()->with('message', "Channel not updated Successfully !");
            }
            
        } else {
            if ($relchannel->save()) {
                return redirect()->route('admin.Relshows', base64_encode($relchannel->id))->with('message', "Kids Channel added Successfully !");                
            }
            else{
                return back()->with('message', "Channel not added Successfully !");
            }
        }
        
    }

    public function edit($id)
    {
        $this->data['channel'] = RelChannel::find(base64_decode($id));
        $this->data['languages'] = Language::all();

        $this->data['content_networks'] = ContentNetwork::all();
        $this->data['current_networks'] = RelChannelContentNetwork::where('show_id', base64_decode($id))->get()->pluck('network_id')->toArray();        

        return view('admin.relchannel.add', $this->data);
    }

    public function destroy(Request $request)
    {
        $channel = RelChannel::find(base64_decode($request->id));
        $channel->deleted_at = time();

        if ($channel->save()) {
            return response()->json(['message' => 'RelChannel deleted successfully']);
        } else {
            return response()->json(['message' => 'RelChannel not deleted']);
        }
    }
    public function saveRelChannelOrder(Request $request)
    {
        $ids = $request->ids;

        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                RelChannel::where('id', $id)->update(['rel_order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Relegious Channel order updated successfully.');
    }
}
