<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);

            $orders = Order::orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'message' => 'Orders retrieved successfully',
                'data' => $orders->items(),
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'total' => 'required|numeric|min:0',
                'status' => 'required|string|in:waiting,processing,shipped,delivered,cancelled',
                'shipping_address' => 'required|string|max:50',
                'shipping_city' => 'required|string|max:50',
                'shipping_state' => 'required|string|max:50',
                'shipping_zip' => 'required|string|max:50',
                'shipping_country' => 'required|string|max:50',
                'customer_name' => 'required|string|max:50',
                'customer_note' => 'nullable|string',
                'platform' => 'required|string|in:etsy,amazon,tiktok_shop,fba,tiktok',
                'type' => 'required|string|in:email,import_excel,extension,api',
            ]);

            $order = Order::create([
                ...$validated,
                'origin_id' => 'ORD-' . time() . '-' . rand(1000, 9999),
                'date_at' => now()->toDateString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Order retrieved successfully',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            $validated = $request->validate([
                'status' => 'sometimes|string|in:waiting,processing,shipped,delivered,cancelled',
                'shipping_address' => 'sometimes|string|max:50',
                'shipping_city' => 'sometimes|string|max:50',
                'shipping_state' => 'sometimes|string|max:50',
                'shipping_zip' => 'sometimes|string|max:50',
                'shipping_country' => 'sometimes|string|max:50',
                'customer_name' => 'sometimes|string|max:50',
                'customer_note' => 'nullable|string',
                'total' => 'sometimes|numeric|min:0',
            ]);

            $order->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified order.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
