<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class OrderPdf extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //Get all the information of the order
        $order = Order::whereId($request->order)->with('products', 'client')->first();
        $pdf = Pdf::loadView('orderpdf', ['order' => $order]);
        return $pdf->stream('orden-' . $request->order.'.pdf');
    }
}
