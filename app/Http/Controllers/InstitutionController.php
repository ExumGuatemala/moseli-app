<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institution;
use Illuminate\Support\Facades\Crypt;


class InstitutionController extends Controller
{
    public function getOrders(Request $request){
        $decrypted_institution_id = Crypt::decryptString(strval($request->institution_hash));
        $institution = Institution::where("id", $decrypted_institution_id)->with("orders.client")->first();
        return view('institution.orders', ['institution' => $institution ]);
    }
}
