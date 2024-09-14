<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Media;
use App\Models\Cut;
use App\Models\OrderState;
use App\Repositories\OrderRepository;
use App\Repositories\OrdersProductsRepository;
use App\Repositories\OrderStateRepository;

class OrderService
{
    protected $orderRepository;
    protected $ordersProductsRepository;
    protected $orderStateRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository(new Order);
        $this->ordersProductsRepository = new OrdersProductsRepository(new Order);
        $this->orderStateRepository = new OrderStateRepository(new OrderState);
    }

    public function updateTotal($orderId): string
    {
        $order = $this->orderRepository->get($orderId)[0];
        $order->total = 0;
        $productsOrder = $this->ordersProductsRepository->allForOrder($order->id);
        foreach($productsOrder as $product)
        {
            $order->total += $product->sale_price * $product->pivot->quantity;
        }
        $order->save();
        // self::updateBalance($orderId);
        return strval($order->total);
    }

    public function setAKey($orderKey){
        $result = $orderKey;
        $is_new = false;
        while (!$is_new){
            if ($this->orderRepository->countByKey($result) == 0)
            {
                $is_new = true;
            } else {
                $result = strtoupper(substr(bin2hex(random_bytes(ceil(8 / 2))), 0, 8));
            }
        }
        return $result;
    }

    public function updateBalance($orderId): string
    {
        $order = $this->orderRepository->get($orderId)[0];
        
        $order->balance = 0;
        
        $paymentsOrder = $this->orderRepository->getOrders($orderId);
        $totalPayed = 0;
        foreach($paymentsOrder as $payment)
        {
            $totalPayed += $payment->amount;
        }
        $order->balance = $order->total - $totalPayed;
        
        $order->save();
        return strval($order->balance);
    }

    public function getNextOrderStatus($stateId): OrderState
    {
        $current_state = $this->orderStateRepository->get($stateId)[0];
        $next_state = $this->orderStateRepository->findBy(['process_order' => $current_state->process_order + 1]);
        return $next_state ? $next_state : $current_state;
    }

    public function changeToNextOrderStatus($orderId, $stateId): bool
    {
        $next_state = $this->getNextOrderStatus($stateId);
        $current_order = Order::whereId($orderId)->first();
        if($next_state->name == "Corte"){
            //get associated media
            $media = $current_order->getMedia()->first();

            $description = "Productos del Pedido \n ***************************************** \n";

            //Create a text with all product information
            if($current_order->products->count() > 0)
            {
                $current_order->products->map(function ($item) use (&$description) {
                    $description = $description .  "Nombre: " . $item->name . " / Talla: " . $item->pivot->size . " / Cantidad: " . $item->pivot->quantity . " / Color: " . $item->pivot->colors . " \n"; 
                });
            }

            //create cut order
            $cut_order = Cut::create([
                'order_id'      => $orderId,
                'state'         => $next_state->name,
                'start_date'    => now(),
                'description'   => $description . "\n Descripcion y Detalles \n ***************************************** \n " . $current_order->description,
            ]);

            //create associated media
            if($media){
                $media->copy($cut_order);
            }
            
        }

        return $this->orderRepository->updateById($orderId, ['state_id' => $next_state->id]) ? true : false;
    }
}
