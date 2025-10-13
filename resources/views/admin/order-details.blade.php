@extends('layouts.admin')
@section('content')
    <style>
        .table-transaction>tbody>tr:nth-of-type(odd) {
            --bs-table-accent-bg: #fff !important;
        }
    </style>
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Order Details</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Order Details</div>
                    </li>
                </ul>
            </div>
            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <h5>Ordered Details</h5>
                    </div>
                    <a class="tf-button style-1 w208" href="{{ route('admin.orders') }}">Volver</a>
                </div>
                <div class="table-responsive">
                    @if(Session::has('status'))
                        <p class="alert alert-success">
                            {{ Session::get('status') }}
                        </p>
                    @endif
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Order No</th>
                                <td>{{ $order->id }}</td>
                                <th>Teléfono</th>
                                <td>{{ $order->phone }}</td>
                                <th>Código Postal</th>
                                <td>{{ $order->zip }}</td>
                            </tr>
                            <tr>
                                <th>Fecha de Pedido</th>
                                <td>{{ $order->created_at }}</td>
                                <th>Fecha de Entrega</th>
                                <td>{{ $order->delivered_at }}</td>
                                <th>Fecha de Cancelación</th>
                                <td>{{ $order->canceled_date }}</td>
                            </tr>
                            <tr>
                                <th>Estado del Pedido</th>
                                <td colspan="5">
                                    @if($order->status == 'delivered')
                                        <span class="badge bg-success">Entregado</span>
                                    @elseif($order->status == 'canceled')
                                        <span class="badge bg-danger">Cancelado</span>
                                    @else
                                        <span class="badge bg-warning">Pedido Realizado</span>
                                    @endif
                                </td>
                            </tr>
                        </thead>

                    </table>
                </div>



            </div>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Artículos Pedidos</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th class="text-center">Precio</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">SKU</th>
                            <th class="text-center">Categoría</th>
                            <th class="text-center">Marca</th>
                            <th class="text-center">Opciones</th>
                            <th class="text-center">Estado de Devolución</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderItems as $item)
                            <tr>
                                <td class="pname">
                                    <div class="image">
                                        <img src="{{ asset('uploads/products/thumbnails/') }}/{{ $item->product->image }}"
                                            alt="{{ $item->product->name }}" class="image">
                                    </div>
                                    <div class="name">
                                        <a href="{{ route('shop.product.details', ['product_slug' => $item->product->slug]) }}"
                                            target="_blank" class="body-title-2">{{ $item->product->name }}</a>
                                    </div>
                                </td>
                                <td class="text-center">${{ $item->price }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-center">{{ $item->product->sku }}</td>
                                <td class="text-center">{{ $item->product->category->name }}</td>
                                <td class="text-center">{{ $item->product->brand->name }}</td>
                                <td class="text-center">{{ $item->options }}</td>
                                <td class="text-center">{{ $item->rstatus == 0 ? 'NO' : 'YES' }}</td>
                                <td class="text-center">
                                    <div class="list-icon-function view-icon">
                                        <div class="item eye">
                                            <i class="icon-eye"></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $orderItems->links('pagination::bootstrap-5') }}
            </div>
        </div>

        <div class="wg-box mt-5">
            <h5>Dirección de Envío</h5>
            <div class="my-account__address-item col-md-6">
                <div class="my-account__address-item__detail">
                    <p>{{ $order->name }}</p>
                    <p>{{ $order->address }}</p>
                    <p>{{ $order->locality }}</p>
                    <p>{{ $order->city }}, {{ $order->country }} </p>
                    <p>{{ $order->landmark }}</p>
                    <p>{{ $order->zip }}</p>
                    <br>
                    <p>Móvil : {{ $order->phone }}</p>
                </div>
            </div>
        </div>

        <div class="wg-box mt-5">
            <h5>Transacciones</h5>
            <table class="table table-striped table-bordered table-transaction">
                <tbody>
                    <tr>
                        <th>Subtotal</th>
                        <td>${{ $order->subtotal }}</td>
                        <th>IVA</th>
                        <td>${{ $order->tax }}</td>
                        <th>Descuento</th>
                        <td>${{ $order->discount }}</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>${{ $order->total }}</td>
                        <th>Método de Pago</th>
                        <td>{{ $transaction->mode }}</td>
                        <th>Status</th>
                        <td>
                            @if($transaction->status == 'approved')
                                <span class="badge bg-success">Aprobado</span>
                            @elseif($transaction->status == 'declined')
                                <span class="badge bg-warning">Rechazado</span>
                            @elseif($transaction->status == 'refunded')
                                <span class="badge bg-secondary">Reembolsado</span>
                            @else
                                <span class="badge bg-danger">Pendiente</span>
                            @endif
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="wg-box mt-5">
            <h5>Actualizar Estado del Pedido</h5>
            <form action="{{ route('admin.order.status.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="row">
                    <div class="col-mb-3">
                        {{-- <label for="status" class="col-sm-2 col-form-label">Order Status</label> --}}
                        <div class="select">
                            <select name="order_status" id="order_status" required>
                            <option value="ordered" {{ $order->status == 'ordered' ? 'selected' : '' }}>Ordenado</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Entregado</option>
                            <option value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        </div>
                    </div>
                    <div class="col-mb-3">
                        <button type="submit" class="btn btn-primary tf-button style-1 w208">Actualizar Estado</button>
                    </div>

                </div>
        </div>

    </div>
    </div>
@endsection
