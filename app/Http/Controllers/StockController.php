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
        $query = null;
        $stocks = Stock_transfer::with(['getSource', 'getDestination'])        
            ->orderBy('status', 'asc')
            ->orderBy('id', 'desc');

        if($query){
            $users = User::where('brand_name', 'like', '%'.$query.'%')->get();
            $stocks = $stocks->whereIn('dst_id', $users->pluck('id')->toArray());
        }

        $stocks = $stocks
            ->skip($offset)
            ->take($perPage)
            ->get();
        return response()->json([
            'draw' => intval($draw), // Draw counter
            'recordsTotal' => $stocks->count(), // Total records
            'recordsFiltered' => $stocks->count(), // Filtered records count
            'data' => $stocks // Data array
        ]);
	}
}
