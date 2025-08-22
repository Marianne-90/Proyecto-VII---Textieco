<?php
namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');
        return redirect()->back();
    }

    public function increase_cart_quantity($rowId)
    {
        $item = Cart::instance('cart')->get($rowId);
        Cart::instance('cart')->update($rowId, $item->qty + 1);
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId)
    {
        $item = Cart::instance('cart')->get($rowId);
        Cart::instance('cart')->update($rowId, $item->qty - 1);
        return redirect()->back();
    }


    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function clear_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request)
    {
        $coupon_code = $request->input('coupon_code');

        if (isset($coupon_code)) {
            $coupon = Coupon::where('code', $coupon_code)->where('expiry_date', '>', now())->first();
            if (!$coupon) {
                // Apply the coupon to the cart
                return redirect()->back()->with('error', 'Invalid coupon code.');
            } else {
                Session::put('coupon', [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value,
                ]);
                $this->calculateDiscount();
                return redirect()->back()->with('success', 'Coupon applied successfully!');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid coupon code.');
        }

    }

    public function calculateDiscount()
    {
        $discount = 0;
        if (Session::has('coupon')) {

            if (Session::get('coupon')['type'] === 'fixed') {
                $discount = Session::get('coupon')['value'];

            } else {
                $discount = (Cart::instance('cart')->subtotal() * Session::get('coupon')['value']) / 100;
            }
            $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

            Session::put('discounts', [
                'discount' => number_format(floatval($discount), 2, '.', ''),
                'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '.', ''),
                'tax' => number_format(floatval($taxAfterDiscount), 2, '.', ''),
                'total' => number_format(floatval($totalAfterDiscount), 2, '.', ''),
            ]);


        }

    }

    public function remove_coupon_code()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return redirect()->back()->with('success', 'Coupon removed successfully!');
    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $address = Address::where('user_id', Auth::id())->where('isdefault', 1)->first();

        return view('checkout', compact('address'));
        // Checkout logic here
    }

    public function place_an_order(Request $request)
    {
        $user_id = Auth::id();
        $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();

        if (!$address) {
            $request->validate([
                'name' => 'required|string|max:100',
                'phone' => 'required|numeric|digits:10',
                'zip' => 'required||numeric|digits:10',
                'state' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'address' => 'required|string|max:255',
                'locality' => 'required|string|max:255',
                'landmark' => 'required|string|max:255',
            ]);
            $address = new Address();
            $address->user_id = $user_id;
            $address->name = $request->input('name');
            $address->phone = $request->input('phone');
            $address->zip = $request->input('zip');
            $address->state = $request->input('state');
            $address->city = $request->input('city');
            $address->address = $request->input('address');
            $address->locality = $request->input('locality');
            $address->landmark = $request->input('landmark');
            $address->country = 'Spain';
            $address->isdefault = true;
            $address->save();
        }

        $this->setAmountforCheckout();

        $order = new Order();
        $order->user_id = $user_id;
        $order->subtotal = Session::get('checkout')['subtotal'];
        $order->discount = Session::get('checkout')['discount'];
        $order->tax = Session::get('checkout')['tax'];
        $order->total = Session::get('checkout')['total'];
        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->locality = $address->locality;
        $order->address = $address->address;
        $order->city = $address->city;
        $order->state = $address->state;
        $order->country = $address->country;
        $order->landmark = $address->landmark;
        $order->zip = $address->zip;
        $order->save();

        foreach (Cart::instance('cart')->content() as $item) {
            $orderItem = new OrderItem();
            $orderItem->product_id = $item->id;
            $orderItem->order_id = $order->id;
            $orderItem->price = $item->price;
            $orderItem->quantity = $item->qty;

            $orderItem->save();
        }

        if ($request->mode == "card") {
            //
        } elseif ($request->mode == "paypal") {
            //
        } elseif ($request->mode == "cod") {

            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->status = 'pending';
            $transaction->save();
        }

        // Clear cart
        Cart::instance('cart')->destroy();
        Session::forget('checkout');
        Session::forget('coupon');
        Session::forget('discounts');
        Session::put('order_id', $order->id);

        return redirect()->route('cart.order.confirmation');
    }

    public function setAmountforCheckout()
    {
        if (!Cart::instance('cart')->count() > 0) {
            Session::forget('checkout');
            return;
        }

        if (Session::has('coupon')) {
            $checkout = [
                'discount' => Session::get('discounts')['discount'],
                'subtotal' => Session::get('discounts')['subtotal'],
                'tax' => Session::get('discounts')['tax'],
                'total' => Session::get('discounts')['total'],
            ];
            Session::put('checkout', $checkout);
        } else {
            $checkout = [
                'discount' => 0,
                'subtotal' => Cart::instance('cart')->subtotal(),
                'tax' => Cart::instance('cart')->tax(),
                'total' => Cart::instance('cart')->total(),
            ];
            Session::put('checkout', $checkout);
        }
    }

    public function order_confirmation()
    {
        if (Session::has('order_id')) {
            $orderId = Session::get('order_id');
            $order = Order::find($orderId);
            if ($order) {
                return view('order-confirmation', compact('order'));
            }
        }
        return redirect()->route('cart.index');
    }

    private function toDecimal($value): float
    {
        if ($value === null || $value === '')
            return 0.0;
        if (is_numeric($value))
            return (float) $value;

        $v = trim((string) $value);
        // elimina espacios normales o no-break space
        $v = str_replace(["\xC2\xA0", ' '], '', $v);

        // Caso EU: 1.234,56 -> 1234.56
        if (preg_match('/^\d{1,3}(\.\d{3})+,\d+$/', $v)) {
            $v = str_replace('.', '', $v);
            $v = str_replace(',', '.', $v);
            return (float) $v;
        }

        // Caso US: 1,234.56 -> 1234.56 (quita comas de miles)
        $v = str_replace(',', '', $v);

        // Último recurso: si quedó algo tipo "1067.20"
        return (float) $v;
    }

}
