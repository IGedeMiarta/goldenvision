@extends($activeTemplate . 'user.layouts.app')

@section('panel')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h3>Invoice</h3>

        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th style="text-align: start">Product</th>
                        <th>Total</th>
                        <th>Status</th>

                    </tr>
                </thead>

                <tbody>
                    @foreach ($inv as $item)
                        <tr>
                            <td>{{ $item->inv }}</td>

                            <td>
                                <ul class="list-group">
                                    @foreach ($item->detail as $i)
                                        <li class="list-group-item" style="text-align: start">
                                            <div class="row">

                                                <div class="col-md-2">
                                                    <img src="{{ asset($i->product->image) }}" alt="product_image"
                                                        style="max-width: 50px">
                                                </div>
                                                <div class="col-md-10">
                                                    <b>Qty:</b> {{ $i->qty }}<br>
                                                    <b>Name:</b><i>{{ $i->product->name }}</i>
                                                    <br>
                                                    <b>Price:</b> {{ $i->product->price }} POINT
                                                </div>
                                            </div>

                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $item->total_order }} POINT</td>
                            <td>
                                <span class="badge badge-warning">Created, Wait for deliver</span>
                                <br>
                                {{ $item->admin_feedback }}
                            </td>

                        </tr>
                    @endforeach
                </tbody>

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
