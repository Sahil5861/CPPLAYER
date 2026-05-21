<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\HelpLine;


class HelpController extends Controller
{



    public function add(){
        $helplines = HelpLine::first();                
        return view('admin.helps.add', compact('helplines'));
    }
    
    public function save(Request $request){
        $request->validate([               
            'whatsapp' => 'required',
            'telegram' => 'nullable'
        ]);



        if (!empty($request->id)) {
            $help = HelpLine::where('id', $request->id)->first();
        }
        else{
            $help = new HelpLine();
        }

        $help->whatsapp_url = $request->whatsapp;
        $help->telegram_url = $request->telegram ?? null;      
    
        if ($help->save()) {            
            return back()->with('message', 'Helplines Updated');
        }                    
    }

}
