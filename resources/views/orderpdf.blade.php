<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>

<div class="flex items-center">
    <h1>Moseli La casa del Pants</h1>
</div>
<div>
    <h1>Orden #{{ $order->key }}</h1>
</div>
<hr>
<div>
    <h4>Cliente: {{ $order->client->name }}</h4>
    <h4>Teléfono: {{ $order->client->phone1 }}</h4>
    <h4>Fecha de Creación: {{ $order->created_at }}</h4>
    <h4>Descripción: {{ $order->description ?? "No hay descripción." }} </h4>
</div>
<div>
    <h2>Productos</h2>
    <table>
        <tr>
            <th>Nombre de Producto</th>
            <th>Precio</th>
            <th>Talla</th>
        </tr>
        @foreach ($order->products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>Q.{{ $product->sale_price }}</td>
            <td>{{ $product->pivot->size }}</td>
        </tr>
        @endforeach
    </table>
</div>


