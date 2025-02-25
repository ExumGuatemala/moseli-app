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
    <h1>Rango de fechas: Desde {{ $dates['start_date'] }} hasta {{ $dates['end_date'] }}</h1>
</div>
<div class="m-10">
    @foreach ($groupedOrders as $group)
        <h2>{{ $group['product'] }}</h2>
        <table class="font-inter w-full table-auto border-separate border-spacing-y-1 overflow-scroll text-left md:overflow-auto">
            <thead class="w-full rounded-lg bg-[#222E3A]/[6%] text-base font-semibold text-white">
                <tr>
                    <th class="whitespace-nowrap py-3 pl-3 text-sm font-normal text-[#212B36]" width="20%">Client</th>
                    @foreach ($availableSizes as $size)
                        <th class="whitespace-nowrap py-3 text-sm font-normal text-[#212B36]" width="5%">{{ $size }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($group['orders'] as $clientId => $order)
                    <tr>
                        <td class="whitespace-nowrap py-3 pl-3 text-sm font-normal text-[#212B36]">{{ $order['client'] }}</td>
                        @foreach ($availableSizes as $size)
                            <td class="whitespace-nowrap py-3 text-sm font-normal text-[#212B36]">
                                {{ $order[$size] ?? 0 }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>