@extends($activeTemplate . 'user.layouts.app')

@section('panel')
    <div class="row d-flex justify-content-end">
        <div class="col-md-3 col-lg-3 mb-3">
            <div class="card b-radius--10 " style="min-height: 15rem;">
                <div class="card-body text-center" style="display: table; min-height: 15rem; overflow: hidden;">
                    <div style="display: table-cell; vertical-align: middle;">
                        <h3>Available Point</h3>
                        <h1 class="display-1 font-weight-bold">{{ auth()->user()->point }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @foreach ($product as $data)
            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-6 mb-30">
                <div class="card h-100">
                    <div class="card-body pt-5">
                        <div class="pricing-table text-center mb-4">
                            <img class="img-fluid" src="{{ asset($data->image) }}" alt="">
                            <h4 class="package-name mb-20 text-"><strong>@lang($data->name)</strong></h4>
                            <p>Price: <b>{{ nb($data->price) }} POINT</b></p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <form action="{{ route('user.product.cart') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $data->id }}">
                            <button type="submit" class="btn btn--sm btn--primary btn-block buy"><i
                                    class="las la-shopping-cart"></i> Add To
                                Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="card @if ($cart->count() <= 0) d-none @endif">
        <div class="card-header d-flex justify-content-between">
            <h3>User Cart</h3>

        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th class="text-end">Qty</th>
                        <th>Total</th>
                        <th class="text-center">Option</th>
                    </tr>
                </thead>
                <form action="{{ route('user.product.purchase') }}" method="POST">

                    @csrf
                    <tbody>
                        @php
                            $total = 0;
                        @endphp
                        @foreach ($cart as $item)
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <img src="{{ asset($item->product->image) }}" alt="@lang('Product Image')"
                                                style="max-width: 100px">
                                        </div>
                                        <div class="col-md-10">
                                            {{ $item->product->name }}

                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $item->product->price }} POINT
                                </td>

                                <td class="d-flex justify-content-center">
                                    <input type="number" class="form-control qtyinp" name="qty"
                                        id="qtyinp{{ $item->id }}" data-id="{{ $item->id }}"
                                        value="{{ $item->qty }}" style="width: 100px">
                                    <button type="button" class="btn btn-success btn-save ml-2 d-none btnSave"
                                        id="btnSave{{ $item->id }}" data-id="{{ $item->id }}"
                                        data-product="{{ $item->product_id }}" data-qty="{{ $item->qty }}">
                                        <i class="fas fa-check"></i></button>
                                </td>
                                <input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
                                <input type="hidden" name="process_qty[]" value="{{ $item->qty }}">
                                <td>
                                    {{ $item->product->price * $item->qty }} POINT
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @php
                                $total += $item->product->price * $item->qty;
                            @endphp
                        @endforeach
                    </tbody>
                    <input type="hidden" name="total" value="{{ $total }}">

                    <tfoot>
                        <tr class="bg-secondary">
                            <th colspan="3" class="text-center">Total</th>
                            <th>{{ $total }} POINT</th>
                            <th class="text-center">
                                <button type="submit" class="btn btn-outline-primary bg-white text-primary "
                                    id="savePurchase">
                                    <i class="fas fa-save"></i>Transaction</button>
                            </th>
                        </tr>
                    </tfoot>
                </form>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {

            $('.qtyinp').on('change', function() {
                const val = $(this).val();
                const id = $(this).data('id');

                $(`#btnSave${id}`).removeClass('d-none');

            });

            $('.qtyinp').on('keyup', function() {
                const val = $(this).val();
                const id = $(this).data('id');

                console.log(val, id);

                $(`#btnSave${id}`).removeClass('d-none');

            });

            $('.btnSave').on('click', function() {
                var id = $(this).data('id');
                var product = $(this).data('product');
                var qty = $(`#qtyinp${id}`).val();

                var formData = {
                    product_id: product,
                    qty: qty,
                };
                var url = "{{ url('user/Product-cart') }}" + '/' + id;
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    success: function(msg) {
                        location.reload();
                    }
                });
            });

            // $('#savePurchase').on('click', function) {

            // }
        })(jQuery);
    </script>
@endpush
