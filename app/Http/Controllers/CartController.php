<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        return view('cart', compact('cartItems', 'total'));
    }

    public function addToCart($id)
    {
        $cart = Cart::where('user_id', Auth::id())->where('product_id', $id)->first();

        if ($cart) {
            $cart->increment('quantity');
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $id,
                'quantity' => 1
            ]);
        }

        return redirect()->back()->with('success', 'Berhasil ditambah ke keranjang!');
    }

    // --- FUNGSI BELI LANGSUNG ---
    public function buyNow($id)
    {
        $product = Product::findOrFail($id);

        // Membuat object item pura-pura agar view checkout bisa membaca format yang sama
        $items = collect([(object)[
            'product' => $product,
            'quantity' => 1,
            'id' => null
        ]]);

        $total = $product->price;

        return view('checkout', compact('items', 'total'));
    }

    public function update(Request $request, $id)
    {
        $cart = Cart::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $cart->update(['quantity' => $request->quantity]);
        return redirect()->back()->with('success', 'Keranjang diperbarui!');
    }

    public function destroy($id)
    {
        Cart::where('id', $id)->where('user_id', Auth::id())->delete();
        return redirect()->back()->with('success', 'Item dihapus!');
    }

    // --- LOGIC PROSES ORDER (Support Keranjang & Beli Langsung) ---
    public function processOrder(Request $request)
    {
        return DB::transaction(function () use ($request) {
            // Cek apakah ini beli langsung (lewat parameter products di AJAX) atau dari keranjang
            $productsInput = json_decode($request->products, true);

            if ($productsInput) {
                // Jalur Beli Langsung / Custom Checkout
                $itemsToOrder = [];
                foreach ($productsInput as $item) {
                    $product = Product::find($item['product_id']);
                    $itemsToOrder[] = (object)[
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price
                    ];
                }
                $total = collect($itemsToOrder)->sum(fn($i) => $i->price * $i->quantity);
            } else {
                // Jalur Keranjang Biasa
                $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
                if ($cartItems->isEmpty()) {
                    return response()->json(['message' => 'Keranjang kosong'], 400);
                }
                $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
                $itemsToOrder = $cartItems;
            }

            // 1. Buat Header Order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'total_price' => $total,
                'status' => 'pending',
                'address' => $request->address ?? 'Alamat belum diisi'
            ]);

            // 2. Simpan Detail Item
            foreach ($itemsToOrder as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price ?? $item->price
                ]);
            }

            // Return response JSON agar dibaca sukses oleh AJAX di frontend
            return response()->json($order);
        });
    }

    public function ordersIndex()
    {
        $orders = Order::where('user_id', Auth::id())
                        ->with('items.product')
                        ->latest()
                        ->get();
        return view('orders', compact('orders'));
    }

    public function clearCart()
    {
        Cart::where('user_id', Auth::id())->delete();
        return redirect()->route('orders.index')->with('success', 'Pembayaran berhasil dan keranjang dibersihkan!');
    }
}
