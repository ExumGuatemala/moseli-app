@vite('resources/css/app.css')
<nav class="flex items-center justify-between flex-wrap bg-blue-900 p-6">
  <div class="flex items-center flex-shrink-0 text-white mr-6">
    <span class="font-semibold text-xl tracking-tight">Moseli La Casa del Pants</span>
  </div>
  <div class="block lg:hidden">
    <button class="flex items-center px-3 py-2 border rounded text-teal-200 border-teal-400 hover:text-white hover:border-white">
      <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>Menu</title><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
    </button>
  </div>
</nav>
<div class="m-10">
    <h1>{{ $institution->name }}</h1>
    <h1>Total: Q. {{ number_format($institution->orders->sum("total"), 2) }}</h1>
</div>
<div class="m-10 overflow-hidden">
  <table class="font-inter w-full table-auto border-separate border-spacing-y-1 overflow-scroll text-left md:overflow-auto">
    <thead class="w-full rounded-lg bg-[#222E3A]/[6%] text-base font-semibold text-white">
      <tr class="">
        <th class="whitespace-nowrap py-3 pl-3 text-sm font-normal text-[#212B36]" width="10%">Cliente</th>
        <th class="whitespace-nowrap py-3 pl-1 text-sm font-normal text-[#212B36]" width="15%">Fecha de Orden</th>
        <th class="whitespace-nowrap py-3 text-sm font-normal text-[#212B36]">Descripcion</th>
        <th class="whitespace-nowrap py-3 text-sm font-normal text-[#212B36]" width="10%">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($institution->orders as $order)
      <tr class="cursor-pointer bg-[#f6f8fa] drop-shadow-[0_0_10px_rgba(34,46,58,0.02)] hover:shadow-2xl">
        <td class="py-4 pl-3 text-sm font-normal text-[#637381]">{{ $order->client->name }}</td>
        <td class="px-1 py-4 text-sm font-normal text-[#637381]">{{ date_format($order->created_at,"d/m/Y H:i:s") }}</td>
        <td class="px-1 py-4 text-sm font-normal text-[#637381]">{{ $order->description }}</td>
        <td class="px-1 py-4 text-sm font-normal text-[#637381]">Q. {{ $order->total }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="m-10 text-[#212B36]">
    <h1>Total: Q. {{ number_format($institution->orders->sum("total"), 2) }}</h1>
</div>