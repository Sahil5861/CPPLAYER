<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TvChannelPak;
use App\Models\Language;
use App\Models\TvShowPakContentNetwork;
use App\Models\ContentNetwork;
use Illuminate\Support\Facades\DB;

class ManageTvChannelPak extends Controller
{
    public function index()
    {        
        return view('admin.tvchannelpak.index');
    }

    public function getTvChannelPakOrderList()
    {
        $this->data['tvchannels'] = TvChannelPak::whereNull('deleted_at')->orderBy('order', 'asc')->get();

        $allTvChannelPaks = [];
        $dataForLoop = [];

        foreach ($this->data['tvchannels'] as $tvchannel) {
            $allTvChannelPaks[] = $tvchannel->order;
            $dataForLoop[$tvchannel->order] = $tvchannel;
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allTvChannelsPak'] = $allTvChannelPaks;

        return view('admin.tvchannelpak.dragdrop', $this->data);
    }

    public function getTvChannelpakList(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'created_at',
        ];

        $totalData = TvChannelPak::whereNull('deleted_at')->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $request->input('order.0.column') ? $columns[$request->input('order.0.column')] : 'id';
        $dir = $request->input('order.0.dir') ?? 'desc';

        if (empty($request->input('search.value'))) {
            $tvchannels = TvChannelPak::whereNull('deleted_at')
                ->when(request()->has('status') && request()->status !== null, function ($query) {
                            $query->where('tv_channels_pak.status', request()->status);
                        })                    
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $tvchannels = TvChannelPak::where(function($query) use ($search) {
                    $query->where('id', 'LIKE', "%{$search}%")
                          ->orWhere('name', 'LIKE', "%{$search}%");
                })
                ->whereNull('deleted_at')
                ->when(request()->has('status') && request()->status !== null, function ($query) {
                        $query->where('tv_channels_pak.status', request()->status);
                    })                
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = TvChannelPak::where(function($query) use ($search) {
                $query->where('id', 'LIKE', "%{$search}%")
                      ->orWhere('name', 'LIKE', "%{$search}%");
            })
            ->whereNull('deleted_at')
            ->count();
        }

        $data = [];
        foreach ($tvchannels as  $key => $tvchannel) {
            $tvchannelsData['id'] = $key + 1;
            $tvchannelsData['name'] = $tvchannel->name;
            $tvchannelsData['logo'] = $tvchannel->logo ?? '';
            $tvchannelsData['language'] = $tvchannel->language ?? '';
            $tvchannelsData['description'] = $tvchannel->description ?? '';

            $statusUrl = url('tv-channel-pak/update-status', base64_encode($tvchannel->id));
            $checked = $tvchannel->status == 1 ? 'checked' : '';
            $tvchannelsData['status'] = '<a onchange="updateStatus(\'' . $statusUrl . '\')" href="javascript:void(0);">
                <label class="switch s-primary mr-2">
                    <input type="checkbox" value="' . $tvchannel->status . '" ' . $checked . ' id="accountSwitch' . $tvchannel->id . '">
                    <span class="slider round"></span>
                </label>
            </a>';

            $tvchannelsData['action'] = '<div class="action-btn">
                                            <a href="edit-tv-channel-pak/' . base64_encode($tvchannel->id) . '">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </a>

                                            <a href="tv-show-pak/'.base64_encode($tvchannel->id).'" title="Manage Shows"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path></svg></a>

                                            <a href="javascript:;" onclick="deleteRowModal(\''.base64_encode($tvchannel->id) . '\')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            </a>
                                        </div>';

            $data[] = $tvchannelsData;
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        ]);
    }

    public function addtvchannel()
    {
        $languages = Language::all();
        $content_networks = ContentNetwork::all();
        return view('admin.tvchannelpak.add', compact('languages', 'content_networks'));
    }

    public function updateStatus($id)
    {
        $tvchannel = TvChannelPak::find(base64_decode($id));

        if ($tvchannel) {
            $tvchannel->status = $tvchannel->status == 1 ? 0 : 1;
            $tvchannel->save();
            return response()->json(['message' => 'TvChannelPak status updated successfully']);
        }

        return response()->json(['message' => 'Something went wrong!!']);
    }

    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        if (!empty($request->id)) {
            $tvchannel = TvChannelPak::find($request->id);
        } else {
            $tvchannel = new TvChannelPak();
            $tvchannel->order = $request->order ?? 0;
        }

        $tvchannel->name = $request->name;
        $tvchannel->language = $request->language ?? null;
        $tvchannel->logo = $request->logo ?? null;
        $tvchannel->description = $request->description ?? null;
        $tvchannel->status = $request->status;

        
        if ($tvchannel->save()) {
            TvShowPakContentNetwork::where('show_id',$tvchannel->id)->delete();
            DB::table('content_network_log')->where('content_id', $tvchannel->id)->where('content_type', '5')->delete();                                                                

            if ($request->has('content_networks') && !empty($request->content_networks)) {                                        
                foreach ($request->content_networks as $key => $network) {
                    $MovieNetwork = new TvShowPakContentNetwork();
                    $MovieNetwork->show_id = $tvchannel->id;
                    $MovieNetwork->network_id = $network;
                    if ($MovieNetwork->save()) {                           
                        DB::table('content_network_log')->insert([
                            'content_id' => $tvchannel->id,
                            'network_id' => $network,
                            'content_type' => '5',                            
                        ]);
                    }
                }
            }

            if (!empty($request->id)) {
                return back()->with('message', 'TV channel updated Successfully !');
            }
            else{
                return redirect()->route('admin.tvshow', base64_encode($tvchannel->id))->with('message', 'TV channel added Successfully !');
            }
        }
        else{
            return back()->with('message', 'Something went wrong !');
        }
        
    }

    public function editTvChannelPak($id)
    {
        $this->data['tvchannel'] = TvChannelPak::find(base64_decode($id));
        $this->data['languages'] = Language::all();
        $this->data['content_networks'] = ContentNetwork::all();

        $this->data['current_networks'] = TvShowPakContentNetwork::where('show_id', base64_decode($id))->get()->pluck('network_id')->toArray();
        return view('admin.tvchannelpak.add', $this->data);
    }

    public function destroy(Request $request)
    {
        $tvchannel = TvChannelPak::find(base64_decode($request->id));
        $tvchannel->deleted_at = time();

        if ($tvchannel->save()) {
            return response()->json(['message' => 'TvChannelPak deleted successfully']);
        } else {
            return response()->json(['message' => 'TvChannelPak not deleted']);
        }
    }
    public function saveTvChannelPakOrder(Request $request)
    {
        $ids = $request->ids;

        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                TvChannelPak::where('id', $id)->update(['order' => $index + 1]);
            }
        }

        return redirect()->back()->with('success', 'Tv Channel Pak order updated successfully.');
    }
}
