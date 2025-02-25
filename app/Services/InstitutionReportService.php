<?php

namespace App\Services;

use App\Models\Institution;
use App\Models\Product;
use Illuminate\Support\Facades\Crypt;

class InstitutionReportService
{
    public function getInstitutionOrdersByProduct($request)
    {
        $decrypted_institution_id = Crypt::decryptString(strval($request->institution_hash));

        $products = Product::where('institution_id', $decrypted_institution_id)
            ->with(['orders.client'])
            ->get();

        $availableSizes = [
            '2', '4', '6', '8', '10', '12', '14', 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL'
        ];

        $groupedOrders = [];

        foreach ($products as $product) {
            $groupedOrders[$product->id] = [
                'product' => $product->name,
                'orders' => []
            ];
            $ordersByClient = [];
            foreach ($product->orders as $order) {
                if (!isset($ordersByClient[$order->client->id])) {
                    $ordersByClient[$order->client->id] = [
                        'client' => $order->client->name,
                    ];
                    foreach ($availableSizes as $size) {
                        $ordersByClient[$order->client->id][$size] = 0;
                    }
                }
                $ordersByClient[$order->client->id][$order->pivot->size] += $order->pivot->quantity;
            }
            $groupedOrders[$product->id]['orders'] = $ordersByClient;
        }     
        
        return [
            'institution' => Institution::find($decrypted_institution_id),
            'orders' => $products->flatMap(function ($product) {
                return $product->orders;
            }),
            'groupedOrders' => $groupedOrders,
        ];
    }
}