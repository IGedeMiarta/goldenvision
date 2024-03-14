@extends($activeTemplate . 'user.layouts.app')

@push('style')
    <style>
        .table {
            border-radius: 15px;
            overflow: hidden;
            /* Ensures that content doesn't overflow rounded corners */
        }

        .badge {
            display: inline-block;
            padding: 8px 15px;
            /* Adjust padding to make the badges wider */
            background-color: #007bff;
            border-radius: 10px;
            /* Rounded corners with a radius of 15px */
            font-size: 14px;
            width: 100%
        }

        /* Additional styles for different badge colors */
        .badge-primary {
            background-color: #007bff;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .badge-warning {
            background-color: #FFDD4A;
        }

        .badge-info {
            background-color: #17a2b8;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            /* background-color: #007bff; */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            border-radius: 10px;
        }

        /* Additional styles for different button sizes */
        .btn-small {
            padding: 5px 10px;
            font-size: 14px;
        }

        .btn-large {
            padding: 15px 30px;
            font-size: 18px;
        }

        /* Additional styles for different button colors */
        .btn-primary {
            background-color: #007bff;
        }

        .btn-success {
            background-color: #00A878;
        }

        .btn-danger {
            background-color: #DD1C1A;
        }

        .btn-warning {
            background-color: #FFDD4A;
        }

        .btn-info {
            background-color: #17a2b8;
        }
    </style>
@endpush
@section('panel')
    <div class="mb-3">
        <button class=" btn btn-warning mr-2 mt-2">On Delivery: {{ $deliver }}</button>
        <button class=" btn btn-success  mr-2 mt-2">Completed : {{ $accept }}</button>
        <button class=" btn btn-danger mt-2">Rejected : {{ $reject }}</button>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead style="background-color: #E8BC00">
                <tr>
                    <th>No</th>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Receipt Number</th>
                    <th>Agent</th>
                    <th>Tracking</th>
                </tr>
            </thead>
            <tbody style="background-color: white">
                @forelse ($inv as $item)
                    <tr>
                        <td>
                            {{ $loop->iteration }}
                        </td>
                        <td style="white-space:nowrap;">
                            {{ $item->inv }}
                        </td>
                        <td style="white-space:nowrap;">
                            {{ date('M d, Y', strtotime($item->created_at)) }}
                        </td>
                        <td>
                            @if ($item->status == 1)
                                <span class="badge badge-warning">Waiting Approve</span>
                            @elseif($item->status == 2)
                                <span class="badge badge-warning">On Delivery</span>
                            @elseif($item->status == 3)
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            {{ $item->resi }}
                        </td>
                        <td>
                            {{ $item->agen }}
                        </td>
                        <td style="white-space:nowrap;">
                            <a href="#" style="color: #8C8C8C;text-decoration: underline;">Check Agent</a>
                        </td>


                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">empty tracking data...</td>
                    </tr>
                @endforelse

            </tbody>

        </table>
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
