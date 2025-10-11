@extends('layouts.app')
@section('content')
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="shop-checkout container">
            <h2 class="page-title">Detalles de Envío</h2>
            <div class="checkout-steps">
                <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">01</span>
                    <span class="checkout-steps__item-title">
                        <span>Carrito de Compras</span>
                        <em>Gestionar tu lista de artículos</em>
                    </span>
                </a>
                <a href="javascript:void(0)" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">02</span>
                    <span class="checkout-steps__item-title">
                        <span>Envío y Pago</span>
                        <em>Revisa y Completa tu Pedido</em>
                    </span>
                </a>
                <a href="javascript:void(0)" class="checkout-steps__item">
                    <span class="checkout-steps__item-number">03</span>
                    <span class="checkout-steps__item-title">
                        <span>Confirmación</span>
                        <em>Revisa y Envía tu Pedido</em>
                    </span>
                </a>
            </div>
            <form name="checkout-form" action="{{ route('cart.place.an.order') }}" method="POST">
                @csrf
                <div class="checkout-form">
                    <div class="billing-info__wrapper">
                        <div class="row">
                            <div class="col-6">
                                <h4>DETALLES DE ENVÍO</h4>
                            </div>
                            <div class="col-6">
                            </div>
                        </div>
                        @if($address)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="my-account__address-list">
                                        <div class="my-account__address-list-item">
                                            <div class="my-account__adress-item__detail">
                                                <p>{{ $address->name }}</p>
                                                <p>{{ $address->address }}</p>
                                                <p>{{ $address->landmark }}</p>
                                                <p>{{ $address->city }},{{ $address->state }}, {{ $address->country }}</p>
                                                <p>{{ $address->zip }}</p>
                                                </br>
                                                <p>{{ $address->phone }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row mt-5">
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="name" required=""
                                            value="{{ old('name') }}">
                                        <label for="name">Nombre Completo *</label>
                                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="phone" required=""
                                            value="{{ old('phone') }}">
                                        <label for="phone">Número de Teléfono *</label>
                                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="zip" required="" value="{{ old('zip') }}">
                                        <label for="zip">Código Postal *</label>
                                        @error('zip') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mt-3 mb-3">
                                        <input type="text" class="form-control" name="state" required=""
                                            value="{{ old('state') }}">
                                        <label for="state">Estado *</label>
                                        @error('state') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="city" required=""
                                            value="{{ old('city') }}">
                                        <label for="city">Ciudad *</label>
                                        @error('city') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="address" required=""
                                            value="{{ old('address') }}">
                                        <label for="address">Número de Casa, Nombre del Edificio *</label>
                                        @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="locality" required=""
                                            value="{{ old('locality') }}">
                                        <label for="locality">Nombre de la Calle, Área, Colonia *</label>
                                        @error('locality') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="landmark" required=""
                                            value="{{ old('landmark') }}">
                                        <label for="landmark">Punto de Referencia *</label>
                                        @error('landmark') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="checkout__totals-wrapper">
                        <div class="sticky-content">
                            <div class="checkout__totals">
                                <h3>Tu Pedido</h3>
                                <table class="checkout-cart-items">
                                    <thead>
                                        <tr>
                                            <th>PRODUCTO</th>
                                            <th align="right">SUBTOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach (Cart::instance('cart') as $item)
                                            <tr>
                                                <td>
                                                    {{ $item->name }} x {{ $item->qty }}
                                                </td>
                                                <td align="right">
                                                    ${{ $item->subtotal }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <table class="checkout-totals">
                                    @if(Session::has('discounts'))
                                        <tbody>
                                            <tr>
                                                <th>Subtotal</th>
                                                <td class="text-right">${{ Cart::instance('cart')->subtotal() }}</td>
                                            </tr>
                                            <tr>
                                                <th>Descuento {{ Session::get('coupon')['code'] }}</th>
                                                <td class="text-right">${{ Session::get('discounts')['discount']  }}</td>
                                            </tr>
                                            <tr>
                                                <th>Subtotal Después del Descuento</th>
                                                <td class="text-right">${{ Session::get('discounts')['subtotal'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Envío</th>
                                                <td class="text-right">Gratis</td>
                                            </tr>
                                            <tr>
                                                <th>IVA</th>
                                                <td class="text-right">${{ Session::get('discounts')['tax'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total</th>
                                                <td class="text-right">{{ Session::get('discounts')['total'] }}</td>
                                            </tr>
                                        </tbody>
                                    @else

                                        <tbody>
                                            <tr>
                                                <th>SUBTOTAL</th>
                                                <td class="text-right">${{ Cart::instance('cart')->subtotal() }}</td>
                                            </tr>
                                            <tr>
                                                <th>ENVÍO</th>
                                                <td class="text-right">Envío gratis</td>
                                            </tr>
                                            <tr>
                                                <th>IVA</th>
                                                <td class="text-right">${{ Cart::instance('cart')->tax() }}</td>
                                            </tr>
                                            <tr>
                                                <th>TOTAL</th>
                                                <td class="text-right">${{ Cart::instance('cart')->total() }}</td>
                                            </tr>
                                        </tbody>
                                    @endif
                                </table>
                            </div>
                            <div class="checkout__payment-methods">
                                <div class="form-check">
                                    <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                                        id="mode1" value="card">
                                    <label class="form-check-label" for="mode1">
                                        Tarjeta de Débito o Crédito

                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                                        id="mode2" value="paypal">
                                    <label class="form-check-label" for="mode2">
                                        Paypal
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                                        id="mode3" value="cod">
                                    <label class="form-check-label" for="mode3">
                                        Contra reembolso
                                    </label>
                                </div>
                                <div class="policy-text">
                                    Sus datos personales se utilizarán para procesar su pedido, apoyar su experiencia
                                    en este
                                    sitio web y para otros fines descritos en nuestra <a href="terms.html"
                                        target="_blank">política de
                                        privacidad</a>.
                                </div>
                            </div>
                            <button class="btn btn-primary btn-checkout" type="submit">REALIZAR PEDIDO</button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </main>
@endsection
