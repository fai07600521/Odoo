<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stock_transfer;

class StockController extends Controller
{
    public function getStockTransferWithPagination(Request $request){
        $page = $request->get('page', 1);
		$perPage = $request->get('per_page', 10); // Get per_page from the DataTables request
		$offset = ($page - 1) * $perPage;
		$draw = $request->get('draw');
        $totalRecords = Stock_transfer::count();

        $stocks = Stock_transfer::orderBy('status', 'asc')
            ->orderBy('id', 'desc')
            ->skip($offset)
            ->take($perPage)
            ->with(['getSource', 'getDestination'])
            ->get();


        return response()->json([
            'draw' => intval($draw), // Draw counter
            'recordsTotal' => $totalRecords, // Total records
            'recordsFiltered' => $totalRecords, // Filtered records count
            'data' => $stocks // Data array
        ]);
	}
}