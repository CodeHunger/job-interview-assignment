<?php

namespace App\Http\Controllers;

use App\Events\LowProductStockEvent;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    private const int MAX_ORDER_AMOUNT = 1000000;
    private const float STOCK_WARNING_STRESHOLD = 0.2;


    public function index(Request $request): JsonResponse {
        $userId = intval($request->query('userId'));
        if ($userId === 0) {
            abort(403, 'userId is a required query param.');
        }

        /** @var Order[] $orders */
        $orders = Order::where('user_id', '=', $userId)->with('products')->get();
        $result = [];
        foreach ($orders as $order) {
            $result[] = OrderViewModel::fromOrder($order);
        }

        return response()->json($result);
    }

    public function store(Request $request): JsonResponse {
        // Process inputs
        $userId = $request->input('userId');
        $productsData = $request->input('products');

        // Validate inputs
        if ( $userId === null ) {
            abort(403, 'userId is a required property.');
        }

        if ( $productsData === null ) {
            abort(403, 'products is a required property.');
        }

        // Process data for validation
        $productIds = array_column($request->input('products'), 'id');
        $products = Product::whereIn('id', $productIds)->get();

        // Validate in database
        if ( User::find($userId) === null) {
            abort(403, 'Unknown user');
        }

        if (count($productIds) !== count($products)) {
            abort(403, 'Unknown products');
        }

        foreach ($productsData as $productData) {
            $product = $products->where('id', $productData['id'])->first();
            if ($product->stock - $productData['amount'] < 0) {
                abort(403, "Product out of stock");
            }
        }

        // Create order
        $order = new Order(['user_id' => $request->input('userId')]);
        $order->save();

        $stockNotifications = [];

        foreach ($productsData as $productData) {
            if($productData['amount'] > self::MAX_ORDER_AMOUNT) {
                abort(403, "Maximum order size exceeded");
            }
            $order->products()->attach($productData['id'], ['amount' => $productData['amount']]);
            $product = $products->where('id', $productData['id'])->first();

            $originalStockPercentile = $product->stock / $product->initialStock;
            $product->stock = $product->stock - $productData['amount'];
            $newStockPercentile = $product->stock / $product->initialStock;

            if (
                $originalStockPercentile > self::STOCK_WARNING_STRESHOLD &&
                $newStockPercentile < self::STOCK_WARNING_STRESHOLD
            ) {
                $stockNotifications[] = $product->id;
            }

            $product->save();
        }

        if (count($stockNotifications)) {
            LowProductStockEvent::dispatch($stockNotifications);
        }

        return response()->json(['orderId' => $order->id]);

    }
}
