<?php

namespace App\Services;

use App\Models\Institution;
use App\Models\Product;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class InstitutionReportService
{
    public function getInstitutionOrdersByProduct($request)
    {
        $decrypted_institution_id = Crypt::decryptString(strval($request->institution_hash));

        $products = Product::where('institution_id', $decrypted_institution_id)
            ->whereHas('orders', function ($query) use ($request) {
                $query->whereBetween('orders.created_at', [$request->start_date, $request->end_date]);
            })
            ->with(['orders' => function ($query) use ($request) {
                $query->whereBetween('orders.created_at', [$request->start_date, $request->end_date])
                  ->with('client');
            }])
            ->get();

        $availableSizes = [
            '2', '4', '6', '8', '10', '12', '14', 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL'
        ];

        $groupedOrders = [];
        $totalSum = 0;

        foreach ($products as $product) {
            $groupedOrders[$product->id] = [
                'product' => $product->name,
                'orders' => [],
                'totals' => array_fill_keys($availableSizes, 0),
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
                $groupedOrders[$product->id]['totals'][$order->pivot->size] += $order->pivot->quantity;
                $totalSum += $order->pivot->quantity;
            }
            $groupedOrders[$product->id]['orders'] = $ordersByClient;
        }     

        $currentUser = Auth::user();
        $institution = Institution::find($decrypted_institution_id);
        $totalSumForSuperAdmin = $currentUser && $currentUser->hasRole('super_admin') ? $institution->orders->sum("total") : null;
        
        return [
            'institution' => $institution,
            'orders' => $products->flatMap(function ($product) {
                return $product->orders;
            }),
            'totalSum' => $totalSumForSuperAdmin,
            'groupedOrders' => $groupedOrders,
            'dates' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ],
        ];
    }
}