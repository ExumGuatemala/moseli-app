<style>
table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  text-align: left;
  padding: 8px;
  border: 1px solid black;
}

th {
  background-color: #D3E1F1;
  color: black;
  text-align: center;
}

tr:nth-child(even) {
  background-color: white;
}

tr:nth-child(odd) {
  background-color: white;
}

</style>

<div class="flex items-center">
    <img style="width: 100%;" src="https://moseli-media.nyc3.cdn.digitaloceanspaces.com/moseli-pdf-header.png" />
</div>
<div>
    <h1>Orden #{{ $order->key }}</h1>
</div>
<div style="background-color: black; height: 1px; margin-bottom: 20px;"></div>
<div>
    <table>
        <tr>
            <th colspan="4">DATOS DEL CLIENTE</th>
        </tr>
        <tr>
            <td>CLIENTE:</td>
            <td colspan="3">{{ $order->client->name }}</td>
        </tr>
        <tr>
            <td>DIRECCIÓN:</td>
            <td>{{ $order->client->address }}</td>
            <td>TELÉFONO:</td>
            <td>{{ $order->client->phone1 }}</td>
        </tr>
        <tr>
            <td>NIT:</td>
            <td>{{ $order->client->nit }}</td>
            <td>FECHA:</td>
            <td>{{ $order->created_at }}</td>
        </tr>
    </table>
</div>
<div>
    <h3>Descripción de la Orden: </h3>
    <p>
        {{ $order->description ?? "No hay descripción." }} 
    </p>
</div>

<div>
    <h3>Productos</h3>
    <table>
        <tr>
            <th style="width:50%">Nombre de Producto</th>
            <th style="width:10%">Talla</th>
            <th style="width:10%">Precio Unitario</th>
            <th style="width:10%">Cantidad</th>
            <th style="width:10%">Subtotal</th>
        </tr>
        @foreach ($order->products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $product->pivot->size }}</td>
            <td>Q.{{ $product->sale_price }}</td>
            <td>{{ $product->pivot->quantity }}</td>
            <td>Q.{{ $product->sale_price * $product->pivot->quantity }}</td>
        </tr>
        @endforeach
    </table>
</div>
<div style="margin-top: 20px;">
    <div>Total: Q{{ $order->total }}</div>
    <div>Anticipo: Q{{ $order->total - $order->balance }}</div>
    <div>Saldo: Q{{ $order->balance }}</div>
</div>
<div style="background-color: black; height: 1px; margin-bottom: 100px;"></div>
<div>
    <div>
        Firma de Recibido del Cliente
    </div>
    <div style="background-color: black; height: 2px; margin-bottom: 20px;"></div>
</div>
<div style="background-color: gray; height: 1px; width: 35%;"></div>
<div style="font-size: 12px; color: gray;">Documento generado el {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', Carbon\Carbon::now(), 'UTC')->setTimezone('America/Guatemala') }}</div>


