<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rack;
use App\Models\Product;

class InboundController extends Controller
{
    /**
    * method to display the inbound screen page
    */
    public function index(){
        return view('inbound-screen');
    }

    /**
    * Method to manage and store / update the racks with sku
    */
    public function store(Request $request){
        if($request['actionType'] === 'manage'){
            return $this->calculate($request);
        }
        
        //suggested rack
        $suggestedRack = "";
        //change rack name
        if($request->rack !== ""){
            $suggestedRack = str_replace('R','rack', $request->rack);
        }
        
        //store the product and update the rack table
        try {
            //store the product
            $product = new Product;
            $product->product = $request->name;
            $product->quantity = $request->qty;
            $product->challan = $request->challan;
            $product->rack = $suggestedRack;
            $product->save();

            //update rack
            $rack = Rack::where('name',$suggestedRack)->update(['avail'=>'false']);
        } catch (\Throwable $th) {
            return 0;
        }
        return 1;
        
    }

    /**
    * method to calculate the storage for sku per rack
    */
    
    public function calculate(Request $request){
        //get racks volume and ranks
        //set default variable
        $RR = [
            'rack1'=>['rank'=>"",'volume'=>''],
            'rack2'=>['rank'=>"",'volume'=>''],
            'rack3'=>['rank'=>"",'volume'=>''],
            'rack4'=>['rank'=>"",'volume'=>''],
            'rack5'=>['rank'=>"",'volume'=>''],
        ];

        $racks = Rack::get();
        if(count($racks) > 0){
            foreach($racks as $rack){
                if($rack->avail === "true"){
                    $RR[$rack->name]['rank'] = $rack->rank;
                    $RR[$rack->name]['volume'] = $rack->volume;
                }
            }
        }

        //racks ranks from manager
        $rackRanks = [
            'R1'=>$RR['rack1']['rank'],
            'R2'=>$RR['rack2']['rank'],
            'R3'=>$RR['rack3']['rank'],
            'R4'=>$RR['rack4']['rank'],
            'R5'=>$RR['rack5']['rank'],
        ];        

        //racks volume values
        $rackVolumes = [
            'R1'=>$RR['rack1']['volume'],
            'R2'=>$RR['rack2']['volume'],
            'R3'=>$RR['rack3']['volume'],
            'R4'=>$RR['rack4']['volume'],
            'R5'=>$RR['rack5']['volume'],
        ];

        //declare ranks based on volume
        $vranks = $rackVolumes;
        arsort($vranks);
        $vranks = array_flip(array_keys($vranks));

        //calculate the storage coefficients
        $S = [];
        foreach($vranks as $rack => $RC){
            if($rackRanks[$rack] !== ""){
                $RD = $rackRanks[$rack];
                $storage = (($RC+1)*0.3) + ($RD*0.7);
                $S[$rack] = $storage;
            }
        }

        $RS = array_search(min($S),$S);

        //calculate 90% of the rack's capacity
        $cap = $rackVolumes[$RS] * 0.9;

        $sku = $request->name;
        $qty = $request->qty;

        //skus volume values
        $skus = [
            'a'=>1,
            'b'=>2,
            'c'=>0.5,
            'd'=>0.8,
            'e'=>2.5,
        ];

        //calculate the storage taken by an sku
        $sku_space = $skus[$sku] * $qty;

        if($sku_space > $cap){
            return json_encode(['status'=>1,'ss'=>$RS]);
        }

        return json_encode(['status'=>0,'ss'=>""]);

    }
}
