@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light">
                            <thead>
                                <tr>
                                    <th scope="col">@lang('SL')</th>
                                    <th scope="col">@lang('Time')</th>
                                    <th scope="col">@lang('Delivery ID')</th>
                                    <th scope="col">@lang('User')</th>
                                    <th scope="col">@lang('MP Package Qty')</th>
                                    <th scope="col">@lang('Address')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th scope="col">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order  as $ex)
                                    <tr>
                                        <td data-label="@lang('SL')">{{ $items->firstItem() + $loop->index }}</td>
                                        <td data-label="@lang('Date')">{{ showDateTime($ex->created_at) }}</td>
                                        <td data-label="@lang('Delivery ID')">{{ $ex->inv }}</td>
                                        <td data-label="@lang('User')">{{ $ex->user->username }}</td>
                                        <td data-label="@lang('MP Package Qty')">{{ $ex->bro_qty }}</td>
                                        <td data-label="@lang('Address')">{{ Str::limit($ex->alamat, 40) }}<button
                                                class="btn--info btn-rounded  badge detailAlm"
                                                data-alamat="{{ $ex->alamat }}"><i class="fa fa-info"></i></button></td>
                                        <td data-label="@lang('Status')">
                                            @if ($ex->status == 1)
                                                <span class="badge badge--success">@lang('Complete. On Delivery')</span>
                                            @elseif($ex->status == 2)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($ex->status == 3)
                                                <span class="badge badge--danger">@lang('Cancel')</span>
                                            @endif
                                            @if ($ex->no_resi != null)
                                                <button class="btn--info btn-rounded  badge detailBtn"
                                                    data-no_resi="{{ $ex->no_resi }}"><i class="fa fa-info"></i></button>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn--primary btn-sm ml-1 devBtn"
                                                data-id="{{ $ex->id }}" data-toggle="tooltip"
                                                data-original-title="@lang('Deliver')"><i class="fas fa-paper-plane"></i>
                                                @lang('Deliver')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($empty_message) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ paginateLinks($order) }}
                </div>
            </div><!-- card end -->
        </div>
    </div>


    {{-- <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light">
                            <thead>
                                <tr>
                                    <th scope="col">@lang('SL')</th>
                                    <th scope="col">@lang('Time')</th>
                                    <th scope="col">@lang('Delivery ID')</th>
                                    <th scope="col">@lang('User')</th>
                                    <th scope="col">@lang('MP Package Qty')</th>
                                    <th scope="col">@lang('Address')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th scope="col">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items  as $ex)
                                    <tr>
                                        <td data-label="@lang('SL')">{{ $items->firstItem() + $loop->index }}</td>
                                        <td data-label="@lang('Date')">{{ showDateTime($ex->created_at) }}</td>
                                        <td data-label="@lang('Delivery ID')">{{ $ex->trx }}</td>
                                        <td data-label="@lang('User')">{{ $ex->user->username }}</td>
                                        <td data-label="@lang('MP Package Qty')">{{ $ex->bro_qty }}</td>
                                        <td data-label="@lang('Address')">{{ Str::limit($ex->alamat, 40) }}<button
                                                class="btn--info btn-rounded  badge detailAlm"
                                                data-alamat="{{ $ex->alamat }}"><i class="fa fa-info"></i></button></td>
                                        <td data-label="@lang('Status')">
                                            @if ($ex->status == 1)
                                                <span class="badge badge--success">@lang('Complete. On Delivery')</span>
                                            @elseif($ex->status == 2)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($ex->status == 3)
                                                <span class="badge badge--danger">@lang('Cancel')</span>
                                            @endif
                                            @if ($ex->no_resi != null)
                                                <button class="btn--info btn-rounded  badge detailBtn"
                                                    data-no_resi="{{ $ex->no_resi }}"><i class="fa fa-info"></i></button>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn--primary btn-sm ml-1 devBtn"
                                                data-id="{{ $ex->id }}" data-toggle="tooltip"
                                                data-original-title="@lang('Deliver')"><i class="fas fa-paper-plane"></i>
                                                @lang('Deliver')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($empty_message) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ paginateLinks($items) }}
                </div>
            </div><!-- card end -->
        </div>
    </div> --}}


    <div class="modal fade" id="exchange_verif" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Verification Exchange Request ID : <span
                            id="ex_id"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn--danger reject">Reject</button>
                    <button type="button" class="btn btn--primary accept">Accept</button>
                </div>
            </div>
        </div>
    </div>

    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="@lang('Close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Nomor Resi : <span class="withdraw-detail"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger" data-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
    <div id="detailAlm" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="@lang('Close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Alamat : <span class="withdraw-detail"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger" data-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>


    <div id="deliverModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Approve Deposit Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.BroDeliver.deliver') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="font-weight-bold" for="">Resi Number</label>
                                <input class="form-control " type="text" name="no_resi" id="no_resi"
                                    placeholder="No Resi" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--success">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        "use strict";
        (function($) {
            $('.exchange_verif').on('click', function() {
                // console.log($(this).data('id'));
                var id = $(this).data('id');
                var modal = $('#exchange_verif');
                modal.find('#ex_id').html($(this).data('exid'));
                modal.find('.reject').on('click', function() {
                    document.getElementById("rform" + id).submit();
                    // console.log(id);
                });
                modal.find('.accept').on('click', function() {
                    document.getElementById("aform" + id).submit();
                    // console.log(id);
                });
                //         modal.find('#prices').val($(this).data('price'));
                //         modal.find('#price').html($(this).data('price'));
                //         modal.find('#weight').val($(this).data('weight'));
                //         modal.find('#tweight').html($(this).data('tweight'));
                //         modal.find('#product_id').val($(this).data('id'));
                //         modal.find('#product_name').val($(this).data('name'));

                //         modal.find('#qty').on('keyup change',function() {
                // // alert('okl');
                //             modal.find('#total').val(modal.find('#qty').val() * modal.find('#weight').val().replace(/(\.\d{2})\d+/g, '$1'));
                //             modal.find('#total_rp').val(modal.find('#qty').val() * modal.find('#prices').val());
                //             modal.find('#totals').val(modal.find('#qty').val() * modal.find('#weight').val().replace(/(\.\d{2})\d+/g, '$1'));
                //         });
                //         // modal.find('.weight').val($(this).data('weight'));
                //         // var input = modal.find('.image');
                //         // // input.setAttribute("value", "http://localhost/microgold/assets/images/avatar.png");

                //         // if($(this).data('status')){
                //         //     modal.find('.toggle').removeClass('btn--danger off').addClass('btn--success');
                //         //     modal.find('input[name="status"]').prop('checked',true);

                //         // }else{
                //         //     modal.find('.toggle').addClass('btn--danger off').removeClass('btn--success');
                //         //     modal.find('input[name="status"]').prop('checked',false);
                //         // }

                //         // modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });

            // $('.modal').on('hidden.bs.modal', function(){
            //     $(this).find('form')[0].reset();
            // });

            // $('.add-product').on('click', function () {
            //     var modal = $('#add-product');
            //     modal.modal('show');
            // });

            $('.devBtn').on('click', function() {
                var modal = $('#deliverModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });


        })(jQuery);
    </script>
    <script>
        $('.detailBtn').on('click', function() {
            var modal = $('#detailModal');
            var feedback = $(this).data('no_resi');
            modal.find('.withdraw-detail').html(feedback);
            modal.modal('show');
        });
    </script>
    <script>
        $('.detailAlm').on('click', function() {
            var modal = $('#detailAlm');
            var feedback = $(this).data('alamat');
            modal.find('.withdraw-detail').html(feedback);
            modal.modal('show');
        });
    </script>
@endpush
