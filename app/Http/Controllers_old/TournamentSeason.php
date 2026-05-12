<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TvChannel;
use App\Models\Language;

class TournamentSeason extends Controller
{
    public function index()
    {
        return view('admin.tvchannel.index');
    }

    public function getTvChannelList(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'created_at',
        ];

        $totalData = TvChannel::whereNull('deleted_at')->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $request->input('order.0.column') ? $columns[$request->input('order.0.column')] : 'id';
        $dir = $request->input('order.0.dir') ?? 'desc';

        if (empty($request->input('search.value'))) {
            $tvchannels = TvChannel::whereNull('deleted_at')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $tvchannels = TvChannel::where(function($query) use ($search) {
                    $query->where('id', 'LIKE', "%{$search}%")
                          ->orWhere('name', 'LIKE', "%{$search}%");
                })
                ->whereNull('deleted_at')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = TvChannel::where(function($query) use ($search) {
                $query->where('id', 'LIKE', "%{$search}%")
                      ->orWhere('name', 'LIKE', "%{$search}%");
            })
            ->whereNull('deleted_at')
            ->count();
        }

        $data = [];
        foreach ($tvchannels as $tvchannel) {
            $tvchannelsData['id'] = $tvchannel->id;
            $tvchannelsData['name'] = $tvchannel->name;
            $tvchannelsData['logo'] = $tvchannel->image ?? '';
            $tvchannelsData['language'] = $tvchannel->language ?? '';
            $tvchannelsData['description'] = $tvchannel->description ?? '';

            $statusUrl = url('tvchannel/update-status', base64_encode($tvchannel->id));
            $checked = $tvchannel->status == 1 ? 'checked' : '';
            $tvchannelsData['status'] = '<a onchange="updateStatus(\'' . $statusUrl . '\')" href="javascript:void(0);">
                <label class="switch s-primary mr-2">
                    <input type="checkbox" value="' . $tvchannel->status . '" ' . $checked . ' id="accountSwitch' . $tvchannel->id . '">
                    <span class="slider round"></span>
                </label>
            </a>';

            $tvchannelsData['action'] = '<div class="action-btn">
                                            <a href="edit-tvchannel/' . base64_encode($tvchannel->id) . '">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </a>

                                            <a href="tvshow/'.base64_encode($tvchannel->id).'" title="Manage Shows"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path></svg></a>

                                            <a href="javascript:;" onclick="delete_item(\''.base64_encode($tvchannel->id) . '\',\'tvchannel\')">
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
        return view('admin.tvchannel.add', compact('languages'));
    }

    public function updateStatus($id)
    {
        $tvchannel = TvChannel::find(base64_decode($id));

        if ($tvchannel) {
            $tvchannel->status = $tvchannel->status == 1 ? 0 : 1;
            $tvchannel->save();
            return response()->json(['message' => 'TvChannel status updated successfully']);
        }

        return response()->json(['message' => 'Something went wrong!!']);
    }

    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        if (!empty($request->id)) {
            $tvchannel = TvChannel::find($request->id);
        } else {
            $tvchannel = new TvChannel();
        }

        $tvchannel->name = $request->name;
        $tvchannel->language = $request->language ?? null;
        $tvchannel->description = $request->description ?? null;
        $tvchannel->status = $request->status;

        

        if (!empty($request->id)) {
            if ($tvchannel->save()) {
                return back()->with('message', 'TV channel updated Successfully !');
            }
            else{
                return back()->with('message', 'TV channel not updated Successfully !');
            }
            
        } else {
            if ($tvchannel->save()) {
                return redirect()->route('admin.tvshow', base64_encode($id))->with('message', 'TV channel added Successfully !');
            }
            else{
                return back()->with('message', 'TV channel not added Successfully !');
            }
        }
        
    }

    public function editTvChannel($id)
    {
        $this->data['tvchannel'] = TvChannel::find(base64_decode($id));
        $this->data['languages'] = Language::all();
        return view('admin.tvchannel.add', $this->data);
    }

    public function destroy(Request $request)
    {
        $tvchannel = TvChannel::find(base64_decode($request->id));
        $tvchannel->deleted_at = time();

        if ($tvchannel->save()) {
            return response()->json(['message' => 'TvChannel deleted successfully']);
        } else {
            return response()->json(['message' => 'TvChannel not deleted']);
        }
    }

    public function getTvChannelOrderList()
    {
        $this->data['tvchannels'] = TvChannel::orderBy('index', 'asc')->get();
        return view('admin.tvchannel.dragdrop', $this->data);
    }

    public function saveTvChannelOrders(Request $request)
    {
        foreach ($request->numbers as $key => $id) {
            $tvchannel = TvChannel::find($id);
            $tvchannel->index = $key + 1;
            $tvchannel->save();
        }

        return back()->with('message', 'TvChannel ordered successfully');
    }
}
