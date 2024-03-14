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
                                    <th>Status</th>
                                    <th>Receipt Number</th>
                                    <th>Agent</th>
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
                                            <a href="#" style="color: #8C8C8C;text-decoration: underline;">Check
                                                Agent</a>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn--warning edit"><i
                                                    class="las la-edit"></i></button>
                                        </td>
                                    </tr>
                                    <div id="editOrder" class="modal fade" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">@lang('Edit Product')</h5>

                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>

                                                </div>
                                                <form method="post" action="{{ route('admin.products.update') }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div>

                                                        </div>
                                                        <input class="form-control plan_id" type="hidden" name="id">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit"
                                                            class="btn btn-block btn--primary">@lang('Update')</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
@endsection


@push('script')
    <script src="{{ asset('assets/assets/dropify/js/dropify.min.js') }}"></script>
    <script>
        "use strict";
        (function($) {
            $('.dropify').dropify();

            $('.edit').on('click', function() {
                var modal = $('#edit-product');
                modal.find('.name').val($(this).data('name'));
                modal.find('.price').val($(this).data('price'));
                modal.find('.stok').val($(this).data('stok'));
                modal.find('.weight').val($(this).data('weight'));
                modal.find('.details').val($(this).data('details'));
                var input = modal.find('.image');
                var image = $(this).data('image');
                $('#editImage').attr('data-default-file', image).dropify();
                if ($(this).data('status')) {
                    modal.find('.toggle').removeClass('btn--danger off').addClass('btn--success');
                    modal.find('input[name="status"]').prop('checked', true);

                } else {
                    modal.find('.toggle').addClass('btn--danger off').removeClass('btn--success');
                    modal.find('input[name="status"]').prop('checked', false);
                }

                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });

            $('.add-product').on('click', function() {
                var modal = $('#add-product');
                modal.modal('show');
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
