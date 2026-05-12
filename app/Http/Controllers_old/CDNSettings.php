<?php

namespace App\Http\Controllers;

use App\Models\CDNDomain;
use App\Models\CdnSetting;
use Illuminate\Http\Request;



class CDNSettings extends Controller
{
    public function index(){

        $cdn_settings = CdnSetting::with('domains')->first();

        // print_r($cdn_settings);exit;

        return view('admin.cdn_settings.index', compact('cdn_settings'));
    }


    public function update(Request $request){
        

        $cdn_settings = !empty($request->id) ? CdnSetting::where('id', $request->id)->first() : new CdnSetting();

        // print_r($request->domains);exit;

        $cdn_settings->status = $request->status ?? 0;
        // $cdn_settings->cdn_links = $request->cdn_links;


        if ($cdn_settings->save()) {            

            $cdn_id = $cdn_settings->id;

            $old_domains = CDNDomain::where('cdn_setting_id', $cdn_id)->get();

            if (count($old_domains) > 0) {                
                CDNDomain::where('cdn_setting_id', $cdn_id)->delete();
            }
        
            foreach($request->domains as $row){

                // print_r($row);exit;
                CDNDomain::create([
                    'cdn_setting_id' => $cdn_id,
                    'domain_name' => $row['domain_name'],
                    'url' => $row['url'],
                ]);
            }





            return back()->with('message', 'CDN settings updated !');
        }
        else{
            return back()->with('error', 'Something went wrong !');
        }
        
        
    }


    public function removeDomain(Request $request){
        $id = $request->id;

        $domain = CDNDomain::where('id', $id)->first();

        if($domain){
            $domain->delete();

            return response()->json([
                'status' => true,
                'message' => 'Deleted successfully !'
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'something went wrong !'
            ]);
        }
    }
}
