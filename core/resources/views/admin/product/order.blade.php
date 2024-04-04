@extends('admin.layouts.app')
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/assets/dropify/css/dropify.min.css') }}">
@endpush
@section('panel')
    {{-- @include('invoice.style') --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table">
                            <thead style="background-color: #E8BC00">
                                <tr>
                                    <th>No</th>
                                    <th>Invoice No</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Agent</th>
                                    <th>Receipt Number</th>
                                    <th>Tracking</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody style="background-color: white">
                                @forelse ($tables as $item)
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
                                            <a href="{{ url('admin/user/detail', $item->user->id) }}">
                                                {{ $item->user->username }}</a>
                                        </td>
                                        <td>
                                            @if ($item->status == 1)
                                                <span class="badge badge-warning">Waiting Approve</span>
                                            @elseif($item->status == 2)
                                                <span class="badge badge-primary">On Delivery</span>
                                            @elseif($item->status == 3)
                                                <span class="badge badge-primary">Completed</span>
                                            @else
                                                <span class="badge badge-danger">Rejected</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ $item->agent->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $item->resi ?? '-' }}
                                        </td>
                                        <td style="white-space:nowrap;">
                                            @if ($item->status != 4 && ($item->agen != null && $item->agen != 1 && $item->resi != null))
                                                <a href="{{ $item->agent->check_resi_url ?? '#' }}" target="_blank"
                                                    style="color: blue;text-decoration: underline;">Check
                                                    Agent</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->status == 1)
                                                @php
                                                    $rs = '<tr>';
                                                    foreach ($item->detail as $value) {
                                                        $rs .= '<td>' . $value->product->name . '</td>';
                                                        $rs .= '<td>' . $value->qty . '</td>';
                                                        $rs .= '<td>' . $value->total . '</td>';
                                                    }
                                                    $rs .= '</tr>';

                                                    if ($item->status == 1) {
                                                        $sts =
                                                            '<span class="badge badge-warning">Waiting Approve</span>';
                                                    } elseif ($item->status == 2) {
                                                        $sts = '<span class="badge badge-warning">On Delivery</span>';
                                                    } elseif ($item->status == 3) {
                                                        $sts = '<span class="badge badge-success">Completed</span>';
                                                    } else {
                                                        $sts = '<span class="badge badge-danger">Rejected</span>';
                                                    }
                                                @endphp
                                                <button data-id="{{ $item->id }}",
                                                    data-total_order="{{ $item->total_order }}"
                                                    data-inv="{{ $item->inv }}" data-resi="{{ $item->resi }}"
                                                    data-agen="{{ $item->agen }}"
                                                    data-expect_ongkir="{{ $item->expect_ongkir }}"
                                                    data-ongkir="{{ $item->ongkir }}" data-status="{{ $item->status }}"
                                                    data-admin_feedback="{{ $item->admin_feedback }}"
                                                    data-details="{{ $rs }}"
                                                    data-name="{{ $item->user->firstname . ' ' . $item->user->lastname }}"
                                                    data-address="{{ $item->user->address->address }}"
                                                    data-zip="{{ $item->user->address->zip }}"
                                                    data-sts="{{ $sts }}" class="btn btn-sm btn--warning edit"><i
                                                        class="las la-edit"></i></button>
                                            @else
                                                -
                                            @endif
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
                </div>
                <div class="card-footer py-4">
                    {{ $tables->links('admin.partials.paginate') }}
                </div>
            </div>
        </div>
    </div>
    <div id="edit-product" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Detail Order')</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <form action="{{ route('admin.product.order.up') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="header">
                            <h4 class="text-center" id="inv"></h4>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="name">
                                    <b>Name:</b> <span id="name"></span>
                                </div>
                                <div class="address">
                                    <b>Alamat:</b> <span id="address"></span>
                                </div>
                                <div class="zip">
                                    <b>Kode Pos:</b> <span id="zip"></span>
                                </div>
                            </div>
                            <div class="col-md-6" style="display: flex;justify-content: end">
                                <div id="StatusInfo"></div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span>#Product Details</span>
                            <table class="table table-bordered">
                                <thead class="bg-primary">
                                    <tr>
                                        <th>Product</th>
                                        <th>QTY</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="detailsProduct">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="text-align: end;">Total order</td>
                                        <td id="total_order"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <input class="form-control" id="id" type="hidden" name="id">
                        <div class="form-group mt-4">
                            <label for="agent">Agent<span class="text-danger">*</span></label>
                            <select name="agent" id="agen" class="form-control" required>
                                <option disabled>Pilih</option>
                                @foreach ($agent as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == 1) selected @endif>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                            {{-- <span class="text-success">please select one</span> --}}
                        </div>
                        <div class="form-group mt-1">
                            <label for="ongkir">Ongkir</label>
                            <input class="form-control" id="ongkir" type="number" name="ongkir" placeholder="00,000">
                        </div>
                        <div class="form-group mt-1">
                            <label for="resi">No Resi</label>
                            <input class="form-control" id="resi" type="text" name="resi" placeholder="0000">
                        </div>
                        <div class="form-group mt-1">
                            <label for="admin_feedback">Admin Notes</label>
                            <textarea name="admin_feedback" id="admin_feedback" class="form-control" cols="30" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-body" style="display: flex;justify-content: space-between">
                        <input type="submit" name="action" value="Reject" class="btn btn--danger"
                            style="width: 100%; margin-right: 20px;" />
                        <input type="submit" name="action" value="Approve" class="btn btn--success"
                            style="width: 100%" />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script src="{{ asset('assets/assets/dropify/js/dropify.min.js') }}"></script>
    <script>
        "use strict";
        (function($) {
            $('.dropify').dropify();

            $('.edit').on('click', function() {
                var modal = $('#edit-product');
                modal.modal('show');

                var id = $(this).data('id');
                var inv = $(this).data('inv');
                var total_order = $(this).data('total_order');
                var resi = $(this).data('resi');
                var agen = $(this).data('agen');
                var expect_ongkir = $(this).data('expect_ongkir');
                var ongkir = $(this).data('ongkir');
                var status = $(this).data('status');
                var admin_feedback = $(this).data('admin_feedback');
                var details = $(this).data('details');
                var name = $(this).data('name');
                var address = $(this).data('address');
                var zip = $(this).data('zip');
                var sts = $(this).data('sts');

                $('#id').val(id);
                $('#inv').html(inv);
                $('#total_order').html(total_order)
                $('#detailsProduct').html(details);
                $('#name').html(name);
                $('#address').html(address);
                $('#zip').html(zip);
                $('#expect_ongkir').val(expect_ongkir);
                $('#agen').val(agen)
                $('#StatusInfo').html(sts)
            });
        })(jQuery);
    </script>
    <script>
        var loadFile = function(event) {
            var output = document.getElementById('output');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src)
            }
        };
    </script>
@endpush
