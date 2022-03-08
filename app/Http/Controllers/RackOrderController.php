<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rack;

class RackOrderController extends Controller
{
    /**
    * method to display the rack order page
    */
    public function index($error = null){
        $ranks = [
            'rack1'=>"",
            'rack2'=>"",
            'rack3'=>"",
            'rack4'=>"",
            'rack5'=>"",
        ];

        $racks = Rack::get();
        if(count($racks) > 0){
            foreach($racks as $rack){
                $ranks[$rack->name] = $rack->rank;
            }
        }

        return view('rack-order',['ranks'=>$ranks,'error'=>$error]);
    }

    /**
    * method to display the rack order page
    */
    
    public function store(Request $request){       
        //set racks ranks
        $racks = [
            'rack1' => $request->rack1,
            'rack2' => $request->rack2,
            'rack3' => $request->rack3,
            'rack4' => $request->rack4,
            'rack5' => $request->rack5
        ];

        //check if rack order values are same or not
        $error = false;

        $count_racks = count($request->all());
        $different_count_racks = count(array_unique($request->all()));

        if($count_racks != $different_count_racks){
            $error = true;
        }

        foreach ($racks as $value) {
            if($value < 1 || $value > 5)
                $error = true;
        }

        if($error){
            return $this->index('All the racks should have different order number! The possible values of the rack order is 1 to 5');
        }

        try{
            foreach($racks as $key => $value){
                $rack = new Rack;
                $rack->name = $key;
                $rack->rank = $value;
                $rack->volume = $key;
                $rack->avail = "true";
                $rack->save();
            }
        }
        catch(\Throwable $e){
            return $this->index($e);
        }

        return redirect('/inbound');
    }
}
