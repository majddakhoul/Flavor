<?php

namespace App\Http\Controllers\Api\CartControllers;

use App\Models\Meal;
use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    private function getCartKey()
    {
        $userId = Auth::id();
        return 'cart_' . $userId;
    }

    public function index()
    {
        $cartKey = $this->getCartKey();
        $cart = Session::get($cartKey, [
            'meals' => [],
            'offers' => [],
        ]);

        $total = 0;

        foreach ($cart['meals'] as $mealId => $item) {
            $meal = Meal::find($mealId);
            if ($meal) {
                $cart['meals'][$mealId] = [
                    'quantity' => $item['quantity'],
                    'price' => $meal->price(),
                    'name' => $meal->name,
                    'notes' => $item['notes'] ?? null // Include notes
                ];
            }
        }

        foreach ($cart['offers'] as $offerId => $item) {
            $offer = Offer::find($offerId);
            if ($offer) {
                $cart['offers'][$offerId] = [
                    'quantity' => $item['quantity'],
                    'price' => $offer->final_price(),
                    'title' => $offer->title,
                    'notes' => $item['notes'] ?? null // Include notes
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart fetched successfully.',
            'status' => 200,
            'data' => [
                'cart' => $cart,
                'total' => $total,
            ],
        ], 200);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:meal,offer',
            'id' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1|max:100',
            'notes' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        $type = $request->input('type');
        $id = $request->input('id');
        $quantity = $request->input('quantity', 1);

        $cartKey = $this->getCartKey();
        $cart = Session::get($cartKey, [
            'meals' => [],
            'offers' => [],
        ]);

        if ($type === 'meal') {
            $item = Meal::find($id);
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Meal not found!',
                    'status' => 404,
                    'data' => null
                ], 404);
            }
            $cart['meals'][$id] = [
                'quantity' => ($cart['meals'][$id]['quantity'] ?? 0) + $quantity,
                'notes' => $request->notes // Store notes with meal
            ];
        } else {
            $item = Offer::find($id);
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Offer not found!',
                    'status' => 404,
                    'data' => null
                ], 404);
            }
            $cart['offers'][$id] = [
                'quantity' => ($cart['offers'][$id]['quantity'] ?? 0) + $quantity,
                'notes' => $request->notes
            ];
        }

        Session::put($cartKey, $cart);

        return response()->json([
            'success' => true,
            'message' => ucfirst($type) . ' added to cart!',
            'status' => 200,
            'data' => [
                'cart' => $cart,
            ],
        ], 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:meal,offer',
            'id' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1|max:100',
            'notes' => 'nullable|string|max:255' // Add notes
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        $type = $request->input('type');
        $id = $request->input('id');
        $quantity = $request->input('quantity');

        $cartKey = $this->getCartKey();
        $cart = Session::get($cartKey, [
            'meals' => [],
            'offers' => [],
        ]);

        if ($type === 'meal' && isset($cart['meals'][$id])) {
            $cart['meals'][$id] = [
                'quantity' => $quantity,
                'notes' => $request->notes // Update notes
            ];
        } elseif ($type === 'offer' && isset($cart['offers'][$id])) {
            $cart['offers'][$id] = [
                'quantity' => $quantity,
                'notes' => $request->notes // Update notes
            ];
        } else {
            return response()->json([
                'success' => false,
                'message' => ucfirst($type) . ' not found in cart!',
                'status' => 404,
                'data' => null
            ], 404);
        }

        Session::put($cartKey, $cart);

        return response()->json([
            'success' => true,
            'message' => ucfirst($type) . ' quantity updated!',
            'status' => 200,
            'data' => [
                'cart' => $cart,
            ],
        ], 200);
    }

    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:meal,offer',
            'id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        $type = $request->input('type');
        $id = $request->input('id');

        $cartKey = $this->getCartKey();
        $cart = Session::get($cartKey, [
            'meals' => [],
            'offers' => [],
        ]);

        if ($type === 'meal' && isset($cart['meals'][$id])) {
            unset($cart['meals'][$id]);
        } elseif ($type === 'offer' && isset($cart['offers'][$id])) {
            unset($cart['offers'][$id]);
        } else {
            return response()->json([
                'success' => false,
                'message' => ucfirst($type) . ' not found in cart!',
                'status' => 404,
                'data' => null
            ], 404);
        }

        Session::put($cartKey, $cart);

        return response()->json([
            'success' => true,
            'message' => ucfirst($type) . ' removed from cart!',
            'status' => 200,
            'data' => [
                'cart' => $cart,
            ],
        ], 200);
    }

public function checkout(Request $request)
{
    $user = Auth::user();
    $cartKey = $this->getCartKey();
    $cart = Session::get($cartKey, ['meals' => [], 'offers' => []]);

    if (empty($cart['meals']) && empty($cart['offers'])) {
        return response()->json([
            'success' => false,
            'message' => 'Cart is empty.',
            'status' => 400,
            'data' => null
        ], 400);
    }

    // تحقق من توفر الكميات في المخزن
    foreach ($cart['meals'] as $mealId => $item) {
        $meal = Meal::find($mealId);
        $quantity = $item['quantity'] ?? $item; // يدعم الشكلين

        if (!$meal || !$meal->is_active()) {
            return response()->json([
                'success' => false,
                'message' => "Meal with ID $mealId is not available or out of stock.",
                'status' => 400,
                'data' => null
            ], 400);
        }

        foreach ($meal->ingredients as $ingredient) {
            $required = $ingredient->pivot->quantity * $quantity;
            if ($ingredient->stock_quantity < $required) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for ingredient {$ingredient->name} in meal {$meal->name}.",
                    'status' => 400,
                    'data' => null
                ], 400);
            }
        }
    }

    foreach ($cart['offers'] as $offerId => $item) {
        $offer = Offer::find($offerId);
        $quantity = $item['quantity'] ?? $item;

        if (!$offer || !$offer->is_active()) {
            return response()->json([
                'success' => false,
                'message' => "Offer with ID $offerId is not available or out of stock.",
                'status' => 400,
                'data' => null
            ], 400);
        }

        foreach ($offer->meals as $meal) {
            foreach ($meal->ingredients as $ingredient) {
                $required = $ingredient->pivot->quantity * $meal->pivot->quantity * $quantity;
                if ($ingredient->stock_quantity < $required) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for ingredient {$ingredient->name} in offer {$offer->title}.",
                        'status' => 400,
                        'data' => null
                    ], 400);
                }
            }
        }
    }

    $orderNotes = $request->input('notes', '');

    // ملاحظات الوجبات
    foreach ($cart['meals'] as $mealId => $item) {
        $meal = Meal::find($mealId);
        if ($meal && !empty($item['notes'])) {
            $orderNotes .= "\n{$meal->name}: {$item['notes']}";
        }
    }

    // ملاحظات العروض
    foreach ($cart['offers'] as $offerId => $item) {
        $offer = Offer::find($offerId);
        if ($offer && !empty($item['notes'])) {
            $orderNotes .= "\n{$offer->title}: {$item['notes']}";
        }
    }

    $orderData = [
        'notes' => trim($orderNotes),
        'status' => 'Pending',
        'dated_at' => now(),
        'reservation_id' => $request->input('reservation_id'),
    ];

    // تحديد نوع المستخدم ونوع الطلب
    if ($user->user_type === 'Customer') {
        $orderData['order_type'] = 'Delivery';

        if (!$user->customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer record not found for this user.',
                'status' => 404,
                'data' => null
            ], 404);
        }

        $orderData['customer_id'] = $user->customer->id;

        if ($request->filled('location_id')) {
            $orderData['location_id'] = $request->input('location_id');
        } elseif ($user->location_id) {
            $orderData['location_id'] = $user->location_id;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Location is required for delivery.',
                'status' => 422,
                'data' => null
            ], 422);
        }
    } elseif (in_array($user->user_type, ['Employee', 'Manager'])) {
        if (!$user->employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee record not found for this user.',
                'status' => 404,
                'data' => null
            ], 404);
        }
        $orderData['employee_id'] = $user->employee->id;
        $orderData['order_type'] = $request->filled('reservation_id') ? 'Reservation' : 'Takeaway';
        $orderData['location_id'] = null;
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized user type.',
            'status' => 403,
            'data' => null
        ], 403);
    }

    $order = Order::create($orderData);

    // ربط الوجبات
    foreach ($cart['meals'] as $mealId => $item) {
        $quantity = $item['quantity'] ?? $item;
        $order->meals()->attach($mealId, ['quantity' => $quantity]);

        $meal = Meal::find($mealId);
        foreach ($meal->ingredients as $ingredient) {
            $ingredient->stock_quantity -= $ingredient->pivot->quantity * $quantity;
            $ingredient->save();
        }
    }

    // ربط العروض
    foreach ($cart['offers'] as $offerId => $item) {
        $quantity = $item['quantity'] ?? $item;
        $order->offers()->attach($offerId, ['quantity' => $quantity]);

        $offer = Offer::find($offerId);
        foreach ($offer->meals as $meal) {
            foreach ($meal->ingredients as $ingredient) {
                $totalQty = $ingredient->pivot->quantity * $meal->pivot->quantity * $quantity;
                $ingredient->stock_quantity -= $totalQty;
                $ingredient->save();
            }
        }
    }

    // مسح السلة
    Session::forget($cartKey);

    return response()->json([
        'success' => true,
        'message' => 'Order created successfully.',
        'status' => 201,
        'data' => [
            'order_id' => $order->id,
        ],
    ], 201);
}

    public function cancelOrder($orderId)
    {
        $user = Auth::user();

        $order = Order::with([
            'meals.ingredients' => function ($query) {
                $query->withPivot('quantity'); // 👈 THIS is required!
            },
            'offers.meals.ingredients' => function ($query) {
                $query->withPivot('quantity'); // already working
            }
        ])->find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.', 'status' => 404], 404);
        }

        if ($user->user_type === 'Customer' && $order->customer_id !== optional($user->customer)->id) {

            return response()->json(['success' => false, 'message' => 'Unauthorized.', 'status' => 403], 403);
        }

        // السماح بالإلغاء فقط إذا كانت الحالة Pending
        if ($order->status !== 'Pending') {
            return response()->json(['success' => false, 'message' => 'Order cannot be cancelled unless it is Pending.', 'status' => 400], 400);
        }

        if ($order->trashed()) {
            return response()->json(['success' => false, 'message' => 'Already cancelled.', 'status' => 400], 400);
        }

        // زيادة الكميات في المخزون بنفس منطق checkout لكن عكسياً

        foreach ($order->offers as $offer) {
            $offerQty = $offer->pivot->quantity;
            $offer->loadMissing('meals.pivot');
            foreach ($offer->meals as $meal) {
                $mealQtyInOffer = $meal->pivot->quantity ?? 1;
                foreach ($meal->ingredients as $ingredient) {
                    $required = $ingredient->pivot->quantity * $mealQtyInOffer * $offerQty;
                    $ingredient->stock_quantity += $required;
                    $ingredient->save();
                }
            }
        }
        foreach ($order->meals as $meal) {
            $orderQty = $meal->pivot->quantity;
            foreach ($meal->ingredients as $ingredient) {
                $required = $ingredient->pivot->quantity * $orderQty;
                $ingredient->stock_quantity += $required;
                $ingredient->save();
            }
        }

        $order->delete();

        return response()->json(['success' => true, 'message' => 'Order cancelled successfully.', 'status' => 200], 200);
    }

    public function revertOrderToCart(Request $request)
    {
        $user = Auth::user();
        $orderId = $request->order_id;

        $order = Order::with([
            'meals' => function ($q) {
                $q->with([
                    'ingredients' => function ($q2) {
                        $q2->withPivot('quantity');
                    }
                ])->withPivot('quantity');
            },
            'offers' => function ($q) {
                $q->with([
                    'meals' => function ($q2) {
                        $q2->with([
                            'ingredients' => function ($q3) {
                                $q3->withPivot('quantity');
                            }
                        ])->withPivot('quantity');
                    }
                ])->withPivot('quantity');
            }
        ])->find($orderId);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.', 'status' => 404], 404);
        }

        if ($user->user_type === 'Customer' && $order->customer_id !== $user->customer->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.', 'status' => 403], 403);
        }

        // السماح بالتحويل للسلة فقط إذا كانت الحالة Pending
        if ($order->status !== 'Pending') {
            return response()->json(['success' => false, 'message' => 'Order can only be reverted to cart if status is Pending.', 'status' => 400], 400);
        }

        // زيادة المخزون أولاً بنفس طريقة cancelOrder

        foreach ($order->meals as $meal) {
            $orderQty = $meal->pivot->quantity;
            foreach ($meal->ingredients as $ingredient) {
                $required = $ingredient->pivot->quantity * $orderQty;
                $ingredient->stock_quantity += $required;
                $ingredient->save();
            }
        }

        foreach ($order->offers as $offer) {
            $offerQty = $offer->pivot->quantity;
            $offer->loadMissing('meals.pivot');
            foreach ($offer->meals as $meal) {
                $mealQtyInOffer = $meal->pivot->quantity ?? 1;
                foreach ($meal->ingredients as $ingredient) {
                    $required = $ingredient->pivot->quantity * $mealQtyInOffer * $offerQty;
                    $ingredient->stock_quantity += $required;
                    $ingredient->save();
                }
            }
        }

        // بناء السلة من الطلب
        $cartKey = $this->getCartKey();
        $cart = ['meals' => [], 'offers' => []];

        foreach ($order->meals as $meal) {
            $cart['meals'][$meal->id] = [
                'quantity' => $meal->pivot->quantity,
                'notes' => $meal->pivot->notes // Preserve notes
            ];
        }

        foreach ($order->offers as $offer) {
            $cart['offers'][$offer->id] = [
                'quantity' => $offer->pivot->quantity,
                'notes' => $offer->pivot->notes // Preserve notes
            ];
        }
        Session::put($cartKey, $cart);

        $order->forceDelete();

        return response()->json(['success' => true, 'message' => 'Order reverted to cart successfully.', 'status' => 200, 'data' => ['cart' => $cart]], 200);
    }

    public function restoreOrder($orderId)
    {
        $user = Auth::user();
        $order = Order::onlyTrashed()->with(['meals.ingredients', 'offers.meals.ingredients'])->find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or not deleted.',
                'status' => 404,
                'data' => null
            ], 404);
        }

        if ($user->user_type === 'Customer' && $order->customer_id !== $user->customer->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
                'status' => 403,
                'data' => null
            ], 403);
        }

        // ممنوع الاستعادة إذا كانت الحالة Cancelled أو Confirmed
        if (in_array($order->status, ['Cancelled', 'Confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be restored if it is Cancelled or Confirmed.',
                'status' => 400,
                'data' => null
            ], 400);
        }

        foreach ($order->meals as $meal) {
            foreach ($meal->ingredients as $ing) {
                $need = $ing->pivot->quantity * $meal->pivot->quantity;
                if ($ing->stock_quantity < $need) {
                    return response()->json([
                        'success' => false,
                        'message' => "Not enough stock for {$ing->name}.",
                        'status' => 400,
                        'data' => null
                    ], 400);
                }
            }
        }

        foreach ($order->offers as $offer) {
            foreach ($offer->meals as $meal) {
                foreach ($meal->ingredients as $ing) {
                    $need = $ing->pivot->quantity * $meal->pivot->quantity * $offer->pivot->quantity;
                    if ($ing->stock_quantity < $need) {
                        return response()->json([
                            'success' => false,
                            'message' => "Not enough stock for {$ing->name}.",
                            'status' => 400,
                            'data' => null
                        ], 400);
                    }
                }
            }
        }

        foreach ($order->meals as $meal) {
            foreach ($meal->ingredients as $ing) {
                $ing->stock_quantity -= ($ing->pivot->quantity * $meal->pivot->quantity);
                $ing->save();
            }
        }

        foreach ($order->offers as $offer) {
            foreach ($offer->meals as $meal) {
                foreach ($meal->ingredients as $ing) {
                    $ing->stock_quantity -= ($ing->pivot->quantity * $meal->pivot->quantity * $offer->pivot->quantity);
                    $ing->save();
                }
            }
        }

        $order->restore();

        return response()->json([
            'success' => true,
            'message' => 'Order restored successfully.',
            'status' => 200,
            'data' => null
        ], 200);
    }
}
