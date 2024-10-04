<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stock_transfer;
use App\User;

class StockController extends Controller
{
    public function getStockTransferWithPagination(Request $request){
        $page = $request->get('page', 1);
		$perPage = $request->get('per_page', 10); // Get per_page from the DataTables request
		$offset = ($page - 1) * $perPage;
		$draw = $request->get('draw');
		$search = $request->get('search');
 

        $stocks = Stock_transfer::orderBy('status', 'asc')
            ->orderBy('id', 'desc')
            ->skip($offset)
            ->take($perPage)
            ->with(['getSource', 'getDestination']);
        if($search){
            $stocks = $stocks->where('id', $search);
        }
        $totalRecords = $stocks->count();
        $stocks = $stocks->get();

        return response()->json([
            'draw' => intval($draw), // Draw counter
            'recordsTotal' => $totalRecords, // Total records
            'recordsFiltered' => $totalRecords, // Filtered records count
            'data' => $stocks // Data array
        ]);
	}
}
