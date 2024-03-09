@extends($activeTemplate . 'user.layouts.app')

@push('style')
    <style>
        .icon-rp-total {
            width: 50px;
            /* Adjust width and height as needed */
            height: 50px;
            border-radius: 50%;
            /* Makes the element round */
            background-color: white;
            /* Circle color */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            /* Adjust font size as needed */
            color: #ECBC13;
            font-weight: bold;
            /* Transparent text color */
            border: 2px solid white;
            /* Adjust border width and color as needed */
        }

        .image-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-top: calc(145 / 100 * 100%);
            /* Maintain aspect ratio 145:300 */
            overflow: hidden;
            border-radius: 10px
        }

        .image-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Custom CSS for input styling */
        input[type="number"] {
            padding: 10px 15px;
            /* Adjust padding as needed */
            border: 1px solid #ced4da;
            border-radius: 10px;
            /* Rounded corners */
            font-size: 16px;
            /* Adjust font size as needed */
            outline: none;
            width: 100%
                /* Remove outline on focus */
        }

        /* Optional: Add some margin for spacing */
        input[type="number"] {
            margin: 5px 0;
        }

        .rounded-button {
            display: block;
            width: 100%;
            padding: 10px;
            /* Adjust padding as needed */
            border: none;
            border-radius: 10px;
            /* Rounded corners */
            background-color: #008C4F;
            /* Green color */
            color: #fff;
            /* White text color */
            font-size: 16px;
            /* Adjust font size as needed */
            cursor: pointer;
            outline: none;
            /* Remove outline on focus */
        }
    </style>
@endpush

@section('panel')
    <div class="row mt-1">

        <div class="col-md-4 mb-30 text-center">
            <div class="card"
                style="background-color: #ECBC13; height: 200px;border-radius: 15px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
                <div class="card-body" style="display: flex; justify-content: space-between">
                    <div class="col-md-12 col-sm-12" style="text-align: start;">
                        <span class="text-dark" style="font-size: 14px;font-weight: bold">Redemption Point </span>
                        <h6 class="text-dark" style="font-size: 50px; font-weight: bolder">{{ auth()->user()->point }} POINT
                        </h6>
                    </div>

                </div>

            </div>
        </div>
        <div class="col-md-4 mb-30 text-center">
            <div class="card"
                style="background-color: #ECBC13; height: 200px;border-radius: 15px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
                <div class="card-body" style="display: flex; justify-content: space-between">
                    <div class="col-md-12 col-sm-12" style="text-align: start;">
                        <span class="text-dark" style="font-size: 14px;font-weight: bold">Recipient Address: </span>
                        <h6 class="text-dark" style="font-size: 12px">
                            {{ auth()->user()->address->address ?? '-' }} <br>
                            Provinsi: {{ auth()->user()->address->prov ?? '-' }}, Kota:
                            {{ auth()->user()->address->kota ?? '-' }},
                            Kecamatan:
                            {{ auth()->user()->address->kec ?? '-' }},
                            Desa: {{ auth()->user()->address->desa ?? '-' }} <br>
                            {{ auth()->user()->address->zip ?? '' }}
                        </h6>
                    </div>
                </div>
                <div class="card-body" style="position: relative;">
                    <a href="{{ route('user.profile-setting') }}" class="btn btn-sm btn-block text--small box--shadow3 mt-3"
                        style="background-color: #000;color:white">@lang('Edit Address')</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-30 text-center">
            <div class="card"
                style="background-color: #ECBC13; height: 200px;border-radius: 15px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
                <div class="card-body" style="display: flex; justify-content: space-between">
                    <div class="col-md-6 col-sm-6" style="text-align: start;">
                        <span class="text-dark" style="font-size: 14px;font-weight: bold">Basket: </span>
                        <h6 class="text-dark" style="font-size: 18px">{{ LoopCart()->count() }} </h6>
                        <h6 class="text-dark" style="font-size: 14px; margin-top: 5px;">Total Items </h6>
                    </div>
                    <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                        <div class="icon-rp-total">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M2.03705 1.06473L0 5.14286H8.35714V0H3.76473C3.03348 0 2.36652 0.413839 2.03705 1.06473ZM9.64286 5.14286H18L15.9629 1.06473C15.6335 0.413839 14.9665 0 14.2353 0H9.64286V5.14286ZM18 6.42857H0V15.4286C0 16.8469 1.15313 18 2.57143 18H15.4286C16.8469 18 18 16.8469 18 15.4286V6.42857Z"
                                    fill="#B2911F" />
                            </svg>

                        </div>
                    </div>
                </div>
                <div class="card-body" style="position: relative;">
                    <form action="{{ route('user.product.purchase') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-block text--small box--shadow3 mt-3"
                            style="background-color: #000;color:white">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach ($product as $data)
            <div class="col-md-4 mb-30">
                <div class="card" style="border-radius: 15px">
                    <div class="card-body">
                        <p style="font-size: 14px;color: #000">1 Item, Price {{ $data->price }} POINT</p>
                        <p style="font-size: 18px;color: #000;font-weight: bolder">{{ strtoupper($data->name) }}</p>
                    </div>
                    <div class="card-body pt-5" style="display: flex; justify-content: center;">
                        <div class="image-container">
                            <img src="{{ asset($data->image) }}" alt="Your Image" width="1066" height="1600">
                        </div>
                    </div>
                    <div class="card-body">
                        <p style="font-size: 14px;color: #000">Product Detail</p>
                        <div style="background-color: #EDEDED; padding: 15px; border-radius: 10px;margin-top: 5px;">
                            <p style="font-size: 12px">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Unde illum laudantium tempora,
                                assumenda eveniet debitis nesciunt voluptatem in
                            </p>
                        </div>
                        <form action="{{ route('user.product.cart') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $data->id }}">
                            <input type="number" class="mt-3" name="qty" placeholder="Masukan Nilai" id="">
                            <button type="submit" class="rounded-button mt-4">Add Product</button>
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
