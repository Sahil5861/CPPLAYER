<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use App\Models\Genre;
use App\Models\Language;
use App\Models\ChannelGenre;

class Channels extends Controller
{
    public function index()
    {
        return view('admin.channel.index');
    }

    public function deletedChannel()
    {
        return view('admin.channel.deleted');
    }

    public function getChannelOrderList()
    {
        $this->data['channels'] = Channel::whereNull('deleted_at')->with('language')->orderBy('channel_number', 'asc')->get();
        // print_r($this->data['channels']); exit();
        $lockedChannels = [];
        $allChannels = [];
        // $lastChannel = $this->data['channels'][count($this->data['channels']) - 1]->channel_number;

        $lastChannel = null;

        if (!empty($this->data['channels']) && is_array($this->data['channels'])) {
            $lastItem = end($this->data['channels']);
            $lastChannel = $lastItem->channel_number ?? null;
        }

        $dataForLoop = [];
        for ($i = 1; $i < $lastChannel; $i++) {
            # code...
            $dataForLoop[$i] = "";
        }
        foreach ($this->data['channels'] as $key => $value) {
            # code...
            $allChannels[] = $value->channel_number;
            $dataForLoop[$value->channel_number] = $value;
            if ($value->position_locked == 1) {
                $lockedChannels[] = $value->channel_number;
            }
        }

        $this->data['dataForLoop'] = $dataForLoop;
        $this->data['allChannels'] = $allChannels;
        $this->data['lockedChannels'] = $lockedChannels;


        $this->data['genres'] = Genre::where('status', 1)->get();
        $this->data['total'] = $this->data['channels']->count();
        $this->data['languages'] = Language::where('status', 1)->whereNull('deleted_at')->get();

        $minChannelNo = $this->data['channels']->first()->channel_number ?? 1;
        $this->data['min_channel_no'] = $minChannelNo;

        // print_r($this->data['total']); exit();


        return view('admin.channel.dragdrop', $this->data);
    }


    public function getOrderedList(Request $request)
    {
        $query = Channel::whereNull('deleted_at')->with('language');

        // ✅ Filters
        if ($request->stream_type && $request->stream_type != '=') {
            $query->where('stream_type', $request->stream_type);
        }

        if ($request->genre && $request->genre != '=') {
            $query->where('genres', 'like', '%' . $request->genre . '%');
        }

        if ($request->language && $request->language != '=') {
            $query->where('channel_language', $request->language);
        }

        $channels = $query->orderBy('channel_number')->get();

        // 🔢 Prepare data (same as your logic)
        $lockedChannels = [];
        $allChannels = [];
        $lastChannel = $channels->last()->channel_number ?? 0;

        $dataForLoop = [];
        for ($i = 1; $i <= $lastChannel; $i++) {
            $dataForLoop[$i] = "";
        }

        foreach ($channels as $value) {
            $allChannels[] = $value->channel_number;
            $dataForLoop[$value->channel_number] = $value;

            if ($value->position_locked == 1) {
                $lockedChannels[] = $value->channel_number;
            }
        }

        $minChannelNo = $channels->first()->channel_number ?? 1;

        // ✅ AJAX response
        return response()->json([
            'html' => view('admin.channel.partials.dragdrop_list', compact('dataForLoop', 'lockedChannels', 'allChannels'))->render(),
            'min_channel_no' => $minChannelNo,
            'total' => $channels->count()
        ]);

    }

    
    // ─────────────────────────────────────────────────────────────────────────────
    // saveChannelOrders — filtered channels ko min_range se start karke update karo
    // ─────────────────────────────────────────────────────────────────────────────


    // public function saveChannelOrders(Request $request){
    //     $ids            = $request->input('ids', []);
    //     $positionLocked = $request->input('position_locked', []); // [0,0,1,0,0] per-channel lock status
    //     $minRange       = (int) $request->input('min_range', 1);

    //     if ($minRange < 1) {
    //         $minRange = 1;
    //     }

    //     if (empty($ids)) {
    //         return back()->with('error', 'No channels to update.');
    //     }

    //     // ── Channel number assign karo, locked wale skip ──────────────────────────
    //     // 
    //     // Example:
    //     //   ids             = [1623, 4440, 5124, 5087, 5758]
    //     //   position_locked = [0,    0,    1,    0,    0   ]
    //     //   min_range       = 1
    //     //
    //     //   Expected channel_numbers = [1, 2, SKIP(5124), 3, 4]
    //     //   BUT: locked channel occupies its SLOT — numbers around it shift
    //     //
    //     // Sahi behaviour:
    //     //   Locked channel apni current channel_number pe rehti hai
    //     //   Unlocked channels baaki numbers le lete hain (locked slot ko skip karke)
    //     // ─────────────────────────────────────────────────────────────────────────

    //     // Step 1: Locked channels ki current channel_numbers fetch karo
    //     // (unhe touch nahi karna)


        
    //     $lockedIds = [];
    //     foreach ($ids as $index => $id) {
    //         $locked = isset($positionLocked[$index]) ? (int) $positionLocked[$index] : 0;
    //         if ($locked == 1) {
    //             $lockedIds[] = $id;
    //         }
    //     }

        

    //     // Step 2: Locked channels ki existing channel_numbers DB se lao
    //     // Taki woh slots "occupied" consider hon
    //     $lockedChannelNumbers = [];
    //     if (!empty($lockedIds)) {
    //         $lockedChannelNumbers = Channel::whereIn('id', $lockedIds)
    //             ->pluck('channel_number')
    //             ->toArray();
    //     }


    //     // print_r($request->all());
        
    //     // print_r($lockedIds);

    //     // print_r($lockedChannelNumbers);
    //     // exit();

    //     // Step 3: Available numbers generate karo (min se start, locked slots skip)
    //     $totalItems     = count($ids);
    //     $maxNeeded      = $minRange + $totalItems + count($lockedChannelNumbers);
    //     $availableNums  = [];
    //     $counter        = $minRange;

    //     while (count($availableNums) < ($totalItems - count($lockedIds))) {
    //         if (!in_array($counter, $lockedChannelNumbers)) {
    //             $availableNums[] = $counter;
    //         }
    //         $counter++;
    //         if ($counter > $maxNeeded + 100) break; // safety
    //     }

    //     // Step 4: Update

    //     // print_r($request->all());exit();
    //     $numPointer = 0;
    //     foreach ($ids as $index => $id) {
    //         $locked = isset($positionLocked[$index]) ? (int) $positionLocked[$index] : 0;

    //         // print_r($id);
    //         // exit();
    //         if ($locked == 1) {
    //             // Locked channel — channel_number mat badlo, sirf position_locked confirm karo
    //             Channel::where('id', $id)->update([
    //                 'position_locked' => 1,
    //             ]);
    //         } else {
    //             // Unlocked — next available number assign karo
    //             if (isset($availableNums[$numPointer])) {
    //                 Channel::where('id', $id)->update([
    //                     'channel_number'  => $availableNums[$numPointer],
    //                     'position_locked' => 0,
    //                 ]);
    //                 $numPointer++;
    //             }
    //         }
    //     }

    //     return back()->with('message', 'Channel order updated successfully.');
    // }

    // public function saveChannelOrders(Request $request)
    // {
    //     $ids            = $request->input('ids', []);
    //     $positionLocked = $request->input('position_locked', []);
    //     $minRange       = (int) $request->input('min_range', 1);
    
    //     if ($minRange < 1) $minRange = 1;
    
    //     if (empty($ids)) {
    //         return response()->json(['success' => false, 'message' => 'No channels to update.']);
    //     }
    
    //     // Locked channel IDs collect karo
    //     $lockedIds = [];
    //     foreach ($ids as $index => $id) {
    //         if (isset($positionLocked[$index]) && (int)$positionLocked[$index] === 1) {
    //             $lockedIds[] = (int)$id;
    //         }
    //     }
    
    //     // Locked channels ki current channel_numbers lao (ye slots occupied hain)
    //     $lockedChannelNumbers = [];
    //     if (!empty($lockedIds)) {
    //         $lockedChannelNumbers = Channel::whereIn('id', $lockedIds)
    //             ->pluck('channel_number')
    //             ->toArray();
    //     }
    
    //     // Available numbers generate karo (locked slots skip)
    //     $unlockedCount = count($ids) - count($lockedIds);
    //     $availableNums = [];
    //     $counter       = $minRange;
    //     $safety        = 0;
    
    //     while (count($availableNums) < $unlockedCount) {
    //         if (!in_array($counter, $lockedChannelNumbers)) {
    //             $availableNums[] = $counter;
    //         }
    //         $counter++;
    //         if (++$safety > 10000) break;
    //     }
    
    //     // Update karo
    //     $numPointer = 0;
    //     foreach ($ids as $index => $id) {
    //         $locked = isset($positionLocked[$index]) ? (int)$positionLocked[$index] : 0;
    
    //         if ($locked === 1) {
    //             Channel::where('id', $id)->update(['position_locked' => 1]);
    //         } else {
    //             if (isset($availableNums[$numPointer])) {
    //                 Channel::where('id', $id)->update([
    //                     'channel_number'  => $availableNums[$numPointer],
    //                     'position_locked' => 0,
    //                 ]);
    //                 $numPointer++;
    //             }
    //         }
    //     }
    
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Channel order updated successfully.',
    //     ]);
    // }


    public function saveChannelOrders(Request $request)
    {
        $channels = $request->input('channels', []);
        $minRange = (int) $request->input('min_range', 1);

        if ($minRange < 1) $minRange = 1;

        if (empty($channels)) {
            return response()->json([
                'success' => false,
                'message' => 'No channels to update.'
            ]);
        }

        // 🔒 Locked channel IDs collect karo
        $lockedIds = [];
        foreach ($channels as $ch) {
            if ((int)$ch['position_locked'] === 1) {
                $lockedIds[] = (int)$ch['id'];
            }
        }

        // 🔢 Locked channels ke current numbers lao
        $lockedChannelNumbers = [];
        if (!empty($lockedIds)) {
            $lockedChannelNumbers = Channel::whereIn('id', $lockedIds)
                ->pluck('channel_number')
                ->toArray();
        }

        // 🎯 Available numbers generate karo (locked skip)
        $unlockedCount = count($channels) - count($lockedIds);
        $availableNums = [];
        $counter       = $minRange;
        $safety        = 0;

        while (count($availableNums) < $unlockedCount) {
            if (!in_array($counter, $lockedChannelNumbers)) {
                $availableNums[] = $counter;
            }
            $counter++;
            if (++$safety > 10000) break;
        }

        // 🔄 Update channels
        $numPointer = 0;

        foreach ($channels as $ch) {
            $id     = (int) $ch['id'];
            $locked = (int) $ch['position_locked'];

            if ($locked === 1) {
                Channel::where('id', $id)->update([
                    'position_locked' => 1
                ]);
            } else {
                if (isset($availableNums[$numPointer])) {
                    Channel::where('id', $id)->update([
                        'channel_number'  => $availableNums[$numPointer],
                        'position_locked' => 0,
                    ]);
                    $numPointer++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Channel order updated successfully.',
        ]);
    }

    public function getChannelList(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        $status = $request->input('status');

        // ✅ BASE QUERY
        $baseQuery = Channel::query()->whereNull('channels.deleted_at');

        // ✅ APPLY STATUS FILTER
        if ($request->has('status') && $status != '') {
            $baseQuery->where('channels.status', $status);
        }

        // ✅ TOTAL COUNTS (NO SEARCH)
        $totalRecords = (clone $baseQuery)->count();

        $inactiveRecords = Channel::where('status', '0')->whereNull('deleted_at')->count();
        $activeRecords = Channel::where('status', '1')->whereNull('deleted_at')->count();
        $deletedRecords = Channel::whereNotNull('deleted_at')->count(); 

        // ✅ APPLY SEARCH (ON CLONE)
        $filteredQuery = clone $baseQuery;

        if (!empty($searchValue)) {
            $filteredQuery->where(function ($q) use ($searchValue) {
                $q->where('channel_name', 'like', "%$searchValue%")
                    ->orWhere('channel_number', 'like', "%$searchValue%")
                    ->orWhere('channel_language', 'like', "%$searchValue%");
            });
        }

        // ✅ COUNT AFTER FILTER
        $totalRecordswithFilter = $filteredQuery->count();

        // ✅ FETCH DATA
        $records = $filteredQuery->with('language')
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];

        foreach ($records as $record) {


            if ($record->deleted_at) {
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            } else {
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            if ($record->status == 1) {
                $status = '<a onchange="updateStatus(\'' . url('channel/update-status', base64_encode($record->id)) . '\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            } else {
                $status = '<a onchange="updateStatus(\'' . url('channel/update-status', base64_encode($record->id)) . '\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }


            $link = $record->channel_link;

            $data_arr[] = [
                "channel_name" => $record->channel_name,
                "channel_number" => $record->channel_number,
                "channel_logo" => '<img src="' . $record->channel_logo . '" alt="" width="70">',
                "channel_language" => $record->language ? $record->language->title : '',
                // "channel_link" => '<a class="btn btn-primary mb-3 rounded bs-tooltip" data-toggle="tooltip" title="Click to open link" href="' . $record->channel_link . '" target="_blank" >Link</a>',

                "channel_link" => '<a class="btn btn-primary mb-3 rounded bs-tooltip" data-toggle="tooltip"  data-html="true" title="Click to open link<br>'.$link.' " href="' . $record->channel_link . '" target="_blank" >Link</a>',
                "status" => $status,
                "created_at" => date('j M Y h:i a', strtotime($record->updated_at)),
                "action" => '<div class="action-btn">
                                <a data-toggle="tooltip" class="swap-btn" data-number="'.$record->channel_number.'" title="Swap Channel"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw"><path d="M23 4v8h-8"></path><path d="M1 20v-8h8"></path><polyline points="16 4 20 4 20 8"></polyline><polyline points="8 20 4 20 4 16"></polyline></svg></a>
                                <a  href="edit-channel/' . base64_encode($record->id) . '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                                <a href="javascript:;" onclick="delete_item(\'' . base64_encode($record->id) . '\',\'channel\')">' . $del_icon . '</a>
                            </div>',
            ];
        }

        return response()->json([
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
            "totalRecords" => number_format($totalRecords),
            "activeRecords" => number_format($activeRecords),
            "inactiveRecords" => number_format($inactiveRecords),
            "deletedRecords" => number_format($deletedRecords),
        ]);
    }

    public function getDeletedChannelList(Request $request)
    {
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

        // Total records
        // Total records
        $totalRecords = Channel::select('count(*) as allcount')->whereNull('channels.deleted_at')->count();
        $inactiveRecords = Channel::select('count(*) as allcount')->where('status', '0')->whereNull('channels.deleted_at')->count();
        $activeRecords = Channel::select('count(*) as allcount')->where('status', '1')->whereNull('channels.deleted_at')->count();
        $deletedRecords = Channel::select('count(*) as allcount')->whereNotNull('channels.deleted_at')->count();


        $totalRecordswithFilter = Channel::select('count(*) as allcount')
            ->where('channel_name', 'like', '%' . $searchValue . '%')
            // ->where('channels.status', '=', 1)
            ->whereNotNull('channels.deleted_at')
            ->orWhere(function ($query) use ($searchValue) {
                $query->where('channels.channel_number', 'like', '%' . $searchValue . '%')
                    ->whereNotNull('channels.deleted_at');
            })

            ->orWhere(function ($query)  use ($searchValue) {
                $query->Where('channels.channel_language', 'like', '%' . $searchValue . '%')
                    ->whereNotNull('channels.deleted_at');
            })
            ->count();

        // Get records, also we have included search filter as well
        $records = Channel::orderBy($columnName, $columnSortOrder)
            // ->where('channels.status', '=', 1)
            ->whereNotNull('channels.deleted_at')
            ->where('channels.channel_name', 'like', '%' . $searchValue . '%')

            ->orWhere(function ($query) use ($searchValue) {
                $query->where('channels.channel_number', 'like', '%' . $searchValue . '%')
                    ->whereNotNull('channels.deleted_at');
            })

            ->orWhere(function ($query)  use ($searchValue) {
                $query->Where('channels.channel_language', 'like', '%' . $searchValue . '%')
                    ->whereNotNull('channels.deleted_at');
            })


            // ->orWhere('channels.description', 'like', '%' . $searchValue . '%')
            // ->orWhere('channels.contact_email', 'like', '%' . $searchValue . '%')
            ->select('channels.*')->with('language')
            // ->leftJoin('channels', 'channels.id', '=', 'Channel.Channel_id')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if ($record->status == 1) {
                $status = '<a onchange="updateStatus(\'' . url('channel/update-status', base64_encode($record->id)) . '\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            } else {
                $status = '<a onchange="updateStatus(\'' . url('channel/update-status', base64_encode($record->id)) . '\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }

            if ($record->deleted_at) {
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>';
            } else {
                $del_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            }

            // <a href="'.route('admin.channel.recoverChannel',base64_encode($record->id)).'" data-toggle="tooltip" title="Undo Channel" class="undo-channel">'.$del_icon.'</a>

            $data_arr[] = array(
                "channel_name" => $record->channel_name,
                "channel_number" => $record->channel_number,
                "channel_logo" => $record->channel_logo,
                // "channel_genre" => $record->channel_genre,
                "channel_language" => $record->language ? $record->language->title : '',
                // "channel_link" => '<a class="btn btn-primary mb-3 rounded bs-tooltip" data-toggle="tooltip"  data-html="true" title="Click to open link<br>Hello Wolrd !!" href="' . $record->channel_link . '" target="_blank" >Link</a>',
                "channel_link" => '
                    <div class="custom-tooltip-wrapper">
                        <a class="btn btn-primary mb-3 rounded"
                        href="' . $record->channel_link . '"
                        target="_blank">
                        Link
                        </a>

                        <div class="custom-tooltip-box">
                            <div>Click to open link</div>
                            <div>Hello World !!</div>
                        </div>
                    </div>
                    ',
                "status" => $status,

                "created_at" => date('j M Y h:i a', strtotime($record->created_at)),
                "action" => '<div class="action-btn">

                        <a data-toggle="tooltip" title="Undo Channel" onclick="undoChannel(' . $record->id . ')">' . $del_icon . '</a>
                      </div>',
            );
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

    public function recoverChannel($id)
    {
        $id = base64_decode($id);
        $update = Channel::where('id', $id)->update(['deleted_at' => null]);
        if ($update) {
            return back()->with('message', 'Channel recover successfully');
        } else {
            return back()->with('message', 'Something went wrong');
        }
    }

    public function addChannel()
    {
        $this->data['languages'] = Language::where('status', 1)->get();
        $this->data['genres'] = Genre::where('status', 1)->get();
        return view('admin.channel.add', $this->data);
    }


    public function add(Request $request)
    {
        $request->validate([
            'channel_name' => 'required',
            'channel_number' => 'required',
            'channel_genre' => 'required',
            'stream_type' => 'required',
            'channel_link' => 'required',
        ]);

        // print_r($request->all());exit;

        if (!empty($request->id)) {

            $channelNumberExists = Channel::where('channel_number', $request->channel_number)->where('id', '!=', $request->id)->first();

            $channelNameExists = Genre::where('title', $request->channel_name)->first();
            if ($channelNumberExists) {
                return back()->with('error', 'This channel number is already exists.');
            }
            // if ($channelNameExists) {
            //     return back()->with('error', 'This channel name is not available.');
            // }

            $channel = Channel::firstwhere('id', $request->id);
            $channel->channel_name = $request->channel_name;
            $channel->channel_number = $request->channel_number;

            $channel->channel_logo = $request->channel_logo;
            $channel->channel_bg = $request->channel_bg;
            $channel->channel_description = $request->channel_description;
            $channel->channel_language = $request->channel_language ?? null;
            $channel->channel_link = $request->channel_link;
            $channel->backup_url = $request->bk_url ?? null;
            $channel->stream_type = $request->stream_type;
            $channel->genres = implode(',', $request->channel_genre);
            $channel->status = $request->status;
            $channel->sport_flag = $request->sport_flag ?? 0;
            if ($channel->save()) {
                return back()->with('message', 'Channel updated successfully');
            } else {
                return back()->with('message', 'Channel not updated successfully');
            }
        } else {

            $channelNumberExists = Channel::where('channel_number', $request->channel_number)->where('deleted_at', null)->first();
            $channelNameExists = Genre::where('title', $request->channel_name)->first();
            if ($channelNumberExists) {
                return back()->with('error', 'This channel number is already exists.');
            }
            // if ($channelNameExists) {
            //     return back()->with('error', 'This channel name is not available.');
            // }



            // if ($request->hasFile('channel_logo')) {
            //     $file = $request->file('channel_logo');
            //     $imageName=time().uniqid().$file->getClientOriginalName();
            //     $filePath = 'images/channel/' . $imageName;
            //     \Storage::disk('public')->put($filePath, file_get_contents($file));
            //     $channel_logo = $filePath;
            // }else{
            //     $channel_logo = '';
            // }

            // if ($request->hasFile('channel_bg')) {
            //     $file = $request->file('channel_bg');
            //     $imageName=time().uniqid().$file->getClientOriginalName();
            //     $filePath = 'images/channel/' . $imageName;
            //     \Storage::disk('public')->put($filePath, file_get_contents($file));
            //     $channel_bg = $filePath;
            // }else{
            //     $channel_bg = '';
            // }

            $channel = new Channel();

            $channel->channel_number = $request->channel_number;
            $channel->channel_name = $request->channel_name;
            $channel->channel_logo = $request->channel_logo;
            $channel->channel_bg = $request->channel_bg;
            $channel->channel_description = $request->channel_description;
            $channel->channel_language = $request->channel_language ?? null;
            $channel->stream_type = $request->stream_type;
            $channel->channel_link = $request->channel_link;
            $channel->backup_url = $request->bk_url ?? null;
            $channel->status = $request->status;
            $channel->sport_flag = $request->sport_flag ?? 0;
            $channel->genres = implode(',', $request->channel_genre);
            if ($channel->save()) {
                $channelCount = Channel::whereNull('deleted_at')->where('status', 1)->orderBy('channel_index', 'desc')->first();
                $channel->channel_index = $channelCount ? $channelCount->channel_index + 1 : 1;
                $channel->save();
                return back()->with('message', 'Channel Added successfully');
            } else {
                return back()->with('message', 'Channel not Added successfully');
            }
        }
    }

    public function editChannel($id)
    {
        $channel = Channel::where('id', base64_decode($id))->first();
        $this->data['channel'] = $channel;
        $this->data['languages'] = Language::where('status', 1)->get();
        $this->data['genres'] = Genre::where('status', 1)->get();
        $this->data['channelGenre'] = explode(',', $channel->genres);
        return view('admin.channel.add', $this->data);
    }


    public function destroy(Request $request)
    {
        $channel = Channel::where('id', base64_decode($request->id))->first();
        // $channel->deleted_at = time();
        if ($channel) {
            $channel->delete();
            echo json_encode(['message', 'Channel deleted successfully']);

            // return response()->json([
            //     'status' => true,
            //     'message' => 'Channel Deleted Successfully !'
            // ]);
        } else {
            echo json_encode(['message', 'Channel not deleted successfully']);
            // return response()->json([
            //     'status' => false,
            //     'message' => 'Channel not  Deleted Successfully !'
            // ]);
        }
    }

    // public function saveChannelOrders(Request $request)
    // {
    //     // code...
    //     $data = $request->numbers;
    //     $lockedChannels = json_decode($request->lockedChannels);
    //     $channel_no = $request->start_no;
    //     $new_channel_no = $request->new_channel_no;
    //     // echo "<br>";
    //     $old_channel_no = $request->old_channel_no;
    //     $detect_0 = 0;
    //     $skip = 1;


    //     if ($channel_no == '0') {
    //         foreach ($data as $key => $item) {
    //             # code...
    //             Channel::where('id', $item)->update(["channel_number" => $key + 1, "position_locked" => $request->position_locked[$key]]);
    //         }
    //         return back()->with('message', 'Channel added successfully');
    //     }

    //     $sd = Channel::where('channel_number', $new_channel_no)->first();



    //     if (!$sd && $old_channel_no != '0') {

    //         exit;
    //         Channel::where('channel_number', $old_channel_no)->update(["channel_number" => $new_channel_no]);
    //         return back()->with('message', 'Channel added successfully');
    //     }



    //     if ($request->checkOrder == 'default') {
    //         foreach ($data as $key => $item) {


    //             $noUpdate = 0;


    //             if ($key + 1 >= $channel_no && $detect_0 === 0) {
    //                 // if(isset($data[$key + 1])){
    //                 //     $_no = Channel::where('id',$data[$key + 1])->first();

    //                 //     if($_no){
    //                 //         if(in_array($_no->channel_number, $lockedChannels)){
    //                 //             $skip++;
    //                 //             // $noUpdate = 1;
    //                 //         }
    //                 //     }
    //                 // }



    //                 if (in_array($key + 1, $lockedChannels)) {
    //                     $skip++;
    //                     // $noUpdate = 1;
    //                 }

    //                 $_no2 = Channel::where('id', $item)->first();
    //                 if ($_no2) {
    //                     if (in_array($_no2->channel_number, $lockedChannels)) {
    //                         // $skip++;
    //                         $noUpdate = 1;
    //                     }
    //                 }
    //                 // echo $skip.'<br>';
    //                 $number = $key + $skip;
    //                 if ($item == 0) {
    //                     $detect_0 = 1;
    //                     break;
    //                 }
    //                 // echo $item.'-'.($key + 1).'-'.$detect_0.'<br>';
    //                 // echo $item;
    //                 // exit;    
    //                 if ($noUpdate == 0) {
    //                     Channel::where('id', $item)->update(["channel_number" => $number, "position_locked" => $request->position_locked[$key]]);
    //                 }
    //                 // $channel = Channel::where('channel_number',$item)->first();
    //                 // $channel->channel_number = $key + 1;
    //                 // $channel->position_locked = $request->position_locked[$key];
    //                 // $channel->save();
    //             }
    //         }
    //     } else {
    //         foreach ($data as $key => $item) {
    //             $noUpdate = 0;
    //             if ($key + 1 >= $request->checkOrder && $key + 1 <= $channel_no) {
    //                 // if(isset($data[$key + 1])){
    //                 //     $_no = Channel::where('id',$data[$key + 1])->first();
    //                 //     // print_r($_no); exit;
    //                 //     if($_no){
    //                 //         if(in_array($_no->channel_number, $lockedChannels)){
    //                 //             $skip++;
    //                 //             // $noUpdate = 1;
    //                 //         }
    //                 //     }
    //                 // }

    //                 $_no2 = Channel::where('id', $item)->first();
    //                 // print_r($_no2); exit;
    //                 if ($_no2) {
    //                     if (in_array($_no2->channel_number, $lockedChannels)) {
    //                         $skip++;
    //                         $noUpdate = 1;
    //                     }
    //                 }
    //                 // echo $skip.'<br>';
    //                 $number = $key + $skip;
    //                 // if($item == 0){
    //                 //     $detect_0 = 1;
    //                 //     break;
    //                 // }
    //                 // echo $item.'-'.($key + 1).'-'.$detect_0.'<br>';
    //                 // echo $item;
    //                 // exit;    
    //                 if ($noUpdate == 0) {
    //                     Channel::where('id', $item)->update(["channel_number" => $number, "position_locked" => $request->position_locked[$key]]);
    //                 }
    //                 // $channel = Channel::where('channel_number',$item)->first();
    //                 // $channel->channel_number = $key + 1;
    //                 // $channel->position_locked = $request->position_locked[$key];
    //                 // $channel->save();
    //             }
    //         }
    //     }
    //     return back()->with('message', 'Channel added successfully');
    // }




    // ─────────────────────────────────────────────────────────────────────────────
    // getChannelOrderList — AJAX: filter ke hisaab se channels return karo
    // ─────────────────────────────────────────────────────────────────────────────

    // public function getChannelOrderList(Request $request)
    // {
    //     $query = Channel::query();

    //     // Stream Type filter
    //     if ($request->stream_type && $request->stream_type !== '=') {
    //         $query->where('stream_type', $request->stream_type);
    //     }

    //     // Genre filter
    //     if ($request->genre && $request->genre !== '=') {
    //         $query->where('genre', $request->genre);  // apna column name adjust karo
    //     }

    //     // Language filter
    //     if ($request->language && $request->language !== '=') {
    //         $query->where('language_id', $request->language);
    //     }

    //     $channels = $query->orderBy('channel_number', 'asc')->get();
    //     $total    = $channels->count();

    //     if ($total === 0) {
    //         return response()->json(['total' => 0, 'html' => '']);
    //     }

    //     $html = view('admin.channel.partials.dragdrop_list', [
    //         'dataForLoop' => $channels,
    //     ])->render();

    //     return response()->json([
    //         'total' => $total,
    //         'html'  => $html,
    //     ]);
    // }

    public function updateStatus($id)
    {
        $channel = Channel::find(base64_decode($id));
        if ($channel) {
            $channel->status = $channel->status == '1' ? '0' : '1';
            $channel->save();
            echo json_encode(['message', 'Channel status updated successfully']);
        } else {
            echo json_encode(['message', 'Something went wrong!!']);
        }
    }

    public function checkChannelName(Request $request)
    {
        $channel = $request->channel_name;
        $query = Genre::where('title', $channel)->first();
        if ($query) {
            return false;
        } else {
            return true;
        }
    }

    public function checkChannelNumber(Request $request)
    {
        $channel = $request->channel_number;
        $query = Channel::where('channel_number', $channel)->whereNull('deleted_at')->first();

        if ($query) {
            return false;
        } else {
            if (isset($request->id)) {
                Channel::where('id', $request->id)->update(['channel_number' => $channel]);
            }
            return true;
        }
    }

    public function downloadSampleCsv()
    {

        $last_channel_number = \App\Models\Channel::max('channel_number');


        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="channels_sample.csv"',
        ];

        $columns = [
            'Channel Number',
            'Channel Name',
            'Channel Logo',
            'Channel Background',
            'Genre',
            'Language',
            'Stream Type',    // M3u8 / YoutubeLive
            'Channel Link',
            'Backup Url',
            'Status',         // Active / Inactive
            'Channel Description',
        ];

        $sampleRow = [
            $last_channel_number + 1,
            'Sports HD',
            'https://example.com/logo.png',
            'https://example.com/bg.png',
            'Sports',
            'English - US',
            'M3u8',
            'https://example.com/stream.m3u8',
            'https://example.com/backup.m3u8',
            'Active',
            'Live sports channel',
        ];

        $callback = function () use ($columns, $sampleRow) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);   // header row
            fputcsv($file, $sampleRow); // sample data row
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    // public function importCsv(Request $request)
    // {
    //     $request->validate([
    //         'csv_file' => 'required|file|mimes:csv,txt'
    //     ]);

    //     $file = fopen($request->file('csv_file')->getRealPath(), 'r');

    //     $header = fgetcsv($file); // skip header row

    //     $imported = 0;
    //     while (($row = fgetcsv($file)) !== false) {
    //         $data = array_combine($header, $row);


    //         $language = Language::where('title', $data['Language'])->first();

    //         Channel::updateOrCreate(
    //             ['channel_number' => $data['Channel Number']],
    //             [   
    //                 'channel_name'          => $data['Channel Name'],
    //                 'channel_logo'          => $data['Channel Logo'],
    //                 'channel_bg'            => $data['Channel Background'],
    //                 'genres'                => $data['Genre'],
    //                 'channel_language'      => $language->id,
    //                 'stream_type'           => $data['Stream Type'],
    //                 'channel_link'          => $data['Channel Link'],
    //                 'backup_url'            => $data['Backup Url'],
    //                 'status'                => $data['Status'] === 'Active' ? 1 : 0,
    //                 'channel_description'   => $data['Channel Description'],
    //             ]
    //         );
    //         $imported++;
    //     }

    //     fclose($file);

    //     return response()->json([
    //         'message' => $imported . ' channels imported successfully!'
    //     ]);
    // }


    public function importCsv(Request $request)
    {
        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt'
            ]);

            $filePath = $request->file('csv_file')->getRealPath();

            if (!file_exists($filePath)) {
                return response()->json([
                    'status' => false,
                    'message' => 'File not found!'
                ], 400);
            }

            $file = fopen($filePath, 'r');

            if (!$file) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unable to open file!'
                ], 400);
            }

            $header = fgetcsv($file);

            if (!$header) {
                return response()->json([
                    'status' => false,
                    'message' => 'CSV header missing or invalid!'
                ], 400);
            }

            $imported = 0;
            $failedRows = [];

            while (($row = fgetcsv($file)) !== false) {

                try {
                    $data = array_combine($header, $row);

                    if (!$data) {
                        throw new \Exception('Header mismatch or invalid row format');
                    }

                    // Language check
                    $language = Language::where('title', $data['Language'])->first();

                    if (!$language) {
                        throw new \Exception('Language not found: ' . $data['Language']);
                    }


                    $types = ['M3u8', 'YoutubeLive', 'Custom'];


                    if (!in_array($data['Stream Type'], $types)) {
                        $failedRows[] = [
                            'channel_number' => $data['Channel Number'] ?? 'N/A',
                            'error' => 'Invalid Stream Type: ' . $data['Stream Type']
                        ];
                        continue; // skip this row
                    }



                    $is_exists = Channel::where('channel_number', $data['Channel Number'])->first();
                    if ($is_exists){
                        // $failedRows[] = [
                        //     'channel_number' => $data['Channel Number'] ?? 'N/A',
                        //     'error' => 'Channel number already exists'
                        // ];
                        // continue; // skip this row

                        // update existing channel
                        $is_exists->update(
                            [
                                'channel_name'         => $data['Channel Name'] ?? null,
                                'channel_logo'         => $data['Channel Logo'] ?? null,
                                'channel_bg'           => $data['Channel Background'] ?? null,
                                'genres'               => $data['Genre'] ?? null,
                                'channel_language'     => $language->id,
                                'stream_type'          => $data['Stream Type'] ?? null,
                                'channel_link'         => $data['Channel Link'] ?? null,
                                'backup_url'           => $data['Backup Url'] ?? null,
                                'status'               => ($data['Status'] ?? '') === 'Active' ? 1 : 0,
                                'channel_description'  => $data['Channel Description'] ?? null,
                            ]
                        );
                    }

                    Channel::create(
                        [
                            'channel_number'       => $data['Channel Number'] ?? null,
                            'channel_name'         => $data['Channel Name'] ?? null,
                            'channel_logo'         => $data['Channel Logo'] ?? null,
                            'channel_bg'           => $data['Channel Background'] ?? null,
                            'genres'               => $data['Genre'] ?? null,
                            'channel_language'     => $language->id,
                            'stream_type'          => $data['Stream Type'] ?? null,
                            'channel_link'         => $data['Channel Link'] ?? null,
                            'backup_url'           => $data['Backup Url'] ?? null,
                            'status'               => ($data['Status'] ?? '') === 'Active' ? 1 : 0,
                            'channel_description'  => $data['Channel Description'] ?? null,
                        ]
                    );

                    $imported++;
                } catch (\Exception $e) {

                    $failedRows[] = [
                        'channel_number' => $data['Channel Number'] ?? 'N/A',
                        'error' => $e->getMessage()
                    ];

                    continue; // skip this row
                }
            }

            fclose($file);

            return response()->json([
                'status' => true,
                'message' => "$imported channels imported successfully!",
                'failed_count' => count($failedRows),
                'failed_rows' => $failedRows
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function swapChannelNumber(Request $request)
    {

        // print_r($request->all()); exit;


        $channel1 = Channel::where('channel_number', $request->old_channel_number)->first();
        $channel2 = Channel::where('channel_number', $request->new_channel_number)->first();

        if ($request->old_channel_number == $request->new_channel_number) {
            return response()->json([ 'success' => false, 'message' => 'Both channel numbers are the same'], 200);
        }

        if (!$channel1) {
            return response()->json([ 'success' => false, 'message' => 'Channel with number ' . $request->old_channel_number . ' not found'], 200);
        }

        if (!$channel2) {
            return response()->json([ 'success' => false, 'message' => 'Channel with number ' . $request->new_channel_number . ' not found'], 200);
        }



        if ($channel1 && $channel2) {
            $tempNumber = $channel1->channel_number;
            $channel1->channel_number = $channel2->channel_number;
            $channel2->channel_number = $tempNumber;

            $channel1->save();
            $channel2->save();

            return response()->json([ 'success' => true, 'message' => 'Channels swapped successfully']);
        } else {
            return response()->json([ 'success' => false, 'message' => 'One or both channels not found'], 404);
        }
    }
}
