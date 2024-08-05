<?php

namespace App\Http\Controllers\Pos\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Pos\Product\Product\Product;
use Illuminate\Http\Request;

class LastAddedProductController extends Controller
{
    public function index()
    {
        try {
            $lastAddedProduct = Product::latest()->first();

            // Construct the response array to match the selectable-variants format
            $response = [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => $lastAddedProduct->id,
                        'name' => $lastAddedProduct->name,
                        // Add other fields as needed
                    ]
                ],
                'first_page_url' => '',
                'from' => 1,
                'last_page' => 1,
                'last_page_url' => '',
                'links' => [],
                'next_page_url' => null,
                'path' => '',
                'per_page' => 1,
                'prev_page_url' => null,
                'to' => 1,
                'total' => 1
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
