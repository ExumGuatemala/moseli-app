<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InstitutionReportService;

class InstitutionOrdersReportController extends Controller
{
    public function __invoke(Request $request, InstitutionReportService $institutionReportService)
    {
        $availableSizes = [
            '2' => '2',
            '4' => '4',
            '6' => '6',
            '8' => '8',
            '10' => '10',
            '12' => '12',
            '14' => '14',
            'XS' => 'XS',
            'S' => 'S',
            'M' => 'M',
            'L' => 'L',
            'XL' => 'XL',
            'XXL' => 'XXL',
            '3XL' => '3XL',
            '4XL' => '4XL',
        ];
        
        $viewData = $institutionReportService->getInstitutionOrdersByProduct($request);
        $viewData['availableSizes'] = $availableSizes;
        
        return view('institution.orders', $viewData); 
    }
}
