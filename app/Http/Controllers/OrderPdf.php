<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\OrderProductPart;

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
        $order = Order::query()
            ->whereKey($request->order)
            ->with([
                'client',
                'products' => function ($q) {
                    $q->withPivot('id', 'quantity', 'size', 'embroidery', 'sublimate', 'has_embroidery', 'has_sublimate', 'colors', 'has_special_size', 'special_size');
                },
            ])
            ->firstOrFail();

        $pivotIds = $order->products
            ->pluck('pivot.id')
            ->filter()
            ->values()
            ->all();

        $partsByOrderProductId = OrderProductPart::query()
            ->whereIn('order_product_id', $pivotIds)
            ->with(['productPart:id,name', 'color:id,name'])
            ->get()
            ->groupBy('order_product_id');

        foreach ($order->products as $product) {
            $orderProductId = $product->pivot->id ?? null;
            $product->order_parts = $orderProductId
                ? ($partsByOrderProductId[$orderProductId] ?? collect())
                : collect();
        }

        $pdf = Pdf::loadView('orderpdf', ['order' => $order]);
        return $pdf->stream('orden-' . $request->order . '.pdf');
    }
}
