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
        
        // Start by getting the query without pagination
        $query = Stock_transfer::with(['getSource', 'getDestination'])
            ->orderBy('status', 'asc')
            ->orderBy('id', 'desc');
        
        // Apply search filtering if necessary
        if ($search) {
            $query->where('id', $search);
            // You can add additional orWhere conditions if needed to search related fields as well
        }
        
        // Get the total number of records before applying pagination
        $totalRecords = $query->count();
        
        // Apply pagination (skip and take) after getting the total number of records
        $stocks = $query->skip($offset)->take($perPage)->get();
        
        // Return response for DataTables
        return response()->json([
            'draw' => intval($draw), // Draw counter
            'recordsTotal' => $totalRecords, // Total number of records in the table
            'recordsFiltered' => $totalRecords, // Number of records after filtering
            'data' => $stocks // Data to display
        ]);
        
	}
}
