<?php

namespace App\Services;

use App\Models\Institution;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\OrderProductPart;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class InstitutionReportService
{
    public function getInstitutionOrdersByProduct($request)
    {
        $decrypted_institution_id = Crypt::decryptString(strval($request->institution_hash));

        $availableSizes = [
            '2', '4', '6', '8', '10', '12', '14', 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL'
        ];

        $products = Product::query()
            ->where('institution_id', $decrypted_institution_id)
            ->whereHas('orders', function ($query) use ($request) {
                $query->whereBetween('orders.created_at', [$request->start_date, $request->end_date]);
            })
            ->with(['orders' => function ($query) use ($request) {
                $query->whereBetween('orders.created_at', [$request->start_date, $request->end_date])
                    ->with('client')
                    ->withPivot([
                        'id',
                        'quantity',
                        'has_embroidery',
                        'embroidery',
                        'has_special_size',
                        'special_size',
                    ]);
            }])
            ->get();

        $pivotIds = $products
            ->flatMap(fn ($p) => $p->orders->pluck('pivot.id'))
            ->filter()
            ->values()
            ->all();

        $partsByOrderProductId = OrderProductPart::query()
            ->whereIn('order_product_id', $pivotIds)
            ->with([
                'productPart:id,name',
                'color:id,name',
            ])
            ->get()
            ->groupBy('order_product_id');

        $groupedOrders = [];
        $totalSum = 0;

        foreach ($products as $product) {
            $groupedOrders[$product->id] = [
                'product' => $product->name,
                'parts' => [], 
            ];

            foreach ($product->orders as $order) {
                $clientId = $order->client->id;
                $clientName = $order->client->name;

                $orderProductId = $order->pivot->id ?? null;
                if (! $orderProductId) {
                    continue;
                }

                $quantity = (int) ($order->pivot->quantity ?? 0);
                if ($quantity <= 0) {
                    continue;
                }

                $partsRows = $partsByOrderProductId[$orderProductId] ?? collect();
                if ($partsRows->isEmpty()) {
                    continue;
                }

                foreach ($partsRows as $partRow) {
                    $partName = $partRow->productPart?->name ?? 'Parte';
                    $size = $partRow->size ?? null;

                    if (is_null($size) || $size === '') {
                        continue;
                    }

                    $colorName = $partRow->color?->name ?? 'Sin Color';
                    $partKey = $partName . '|' . $colorName; 

                    if (!isset($groupedOrders[$product->id]['parts'][$partKey])) {
                        $groupedOrders[$product->id]['parts'][$partKey] = [
                            'part' => $partName,
                            'color' => $colorName,
                            'orders' => [], 
                            'totals' => array_fill_keys($availableSizes, 0),
                        ];
                    }

                    $ordersByClient = &$groupedOrders[$product->id]['parts'][$partKey]['orders'];

                    if (!isset($ordersByClient[$clientId])) {
                        $ordersByClient[$clientId] = [
                            'client' => $clientName,
                            'embroidery' => '',
                            'special_size' => '',
                        ];
                        foreach ($availableSizes as $s) {
                            $ordersByClient[$clientId][$s] = 0;
                        }
                    }

                    if (($order->pivot->has_embroidery ?? false) || !is_null($order->pivot->embroidery)) {
                        $embroideryText = "Bordado ({$partName}, {$colorName}, Talla {$size}): {$order->pivot->embroidery}";
                        $ordersByClient[$clientId]['embroidery'] .= ($ordersByClient[$clientId]['embroidery'] ? ', ' : '') . $embroideryText;
                    }

                    if (($order->pivot->has_special_size ?? false) || !is_null($order->pivot->special_size)) {
                        $specialSizeText = "Talla Especial ({$partName}, {$colorName}, Talla {$size}): {$order->pivot->special_size}";
                        $ordersByClient[$clientId]['special_size'] .= ($ordersByClient[$clientId]['special_size'] ? ', ' : '') . $specialSizeText;
                    }

                    if (array_key_exists($size, $ordersByClient[$clientId])) {
                        $ordersByClient[$clientId][$size] += $quantity;
                    }
                    if (array_key_exists($size, $groupedOrders[$product->id]['parts'][$partKey]['totals'])) {
                        $groupedOrders[$product->id]['parts'][$partKey]['totals'][$size] += $quantity;
                    }

                    $totalSum += $quantity;
                }
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
            'orders' => $products->flatMap(fn ($product) => $product->orders),
            'totalSum' => $totalSumForSuperAdmin,
            'groupedOrders' => $groupedOrders,
            'dates' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ],
        ];
    }
}