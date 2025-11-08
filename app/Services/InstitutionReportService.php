<?php

namespace App\Services;

use App\Models\Institution;
use App\Models\Product;
use App\Models\ProductColor;
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
                'colors' => [], // Group by color
            ];

            foreach ($product->orders as $order) {
                $colorIds = json_decode($order->pivot->colors, true); // Decode colors JSON
                $firstColorId = is_array($colorIds) && count($colorIds) > 0 ? $colorIds[0] : null;
            
                if ($firstColorId) {
                    $colorName = ProductColor::find($firstColorId)?->name ?? 'Sin Color Especificado';
                } else {
                    $colorName = 'Sin Color Especificado';
                }
            
                if (!isset($groupedOrders[$product->id]['colors'][$colorName])) {
                    $groupedOrders[$product->id]['colors'][$colorName] = [
                        'color' => $colorName,
                        'orders' => [],
                        'totals' => array_fill_keys($availableSizes, 0),
                    ];
                }
            
                $ordersByClient = &$groupedOrders[$product->id]['colors'][$colorName]['orders'];
            
                if (!isset($ordersByClient[$order->client->id])) {
                    $ordersByClient[$order->client->id] = [
                        'client' => $order->client->name,
                        'embroidery' => '', // Initialize the "Bordado" column
                    ];
                    foreach ($availableSizes as $size) {
                        $ordersByClient[$order->client->id][$size] = 0;
                    }
                }
            
                // New requirement: some pivot items might not have a size. Skip those items.
                $size = $order->pivot->size ?? null;
                if (is_null($size) || $size === '') {
                    // Skip products that do not have a size per new business rule
                    continue;
                }

                // Check for embroidery and append to the "Bordado" column
                if ($order->pivot->has_embroidery || !is_null($order->pivot->embroidery)) {
                    $embroideryText = "Bordado Talla {$size}: {$order->pivot->embroidery}";
                    $ordersByClient[$order->client->id]['embroidery'] .= ($ordersByClient[$order->client->id]['embroidery'] ? ', ' : '') . $embroideryText;
                }

                $ordersByClient[$order->client->id][$size] += $order->pivot->quantity;
                $groupedOrders[$product->id]['colors'][$colorName]['totals'][$size] += $order->pivot->quantity;
                $totalSum += $order->pivot->quantity;
            }
        }

        $currentUser = Auth::user();
        $institution = Institution::find($decrypted_institution_id);
        $totalSumForSuperAdmin = null;
        if ($currentUser && $currentUser->hasRole('Administrador')) {
            $totalSumForSuperAdmin = $institution->orders()
                ->whereBetween('created_at', [$request->start_date, $request->end_date])
                ->sum('total');
        }
        
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