@extends($activeTemplate . 'user.layouts.app')

@section('panel')

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th scope="col">@lang('Transaction ID')</th>
                                <th scope="col">@lang('Amount')</th>
                                <th scope="col">@lang('Order')</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Time')</th>
                                <th scope="col"> @lang('Details')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($logs) >0)
                                @foreach($logs as $k=>$data)
                                    <tr>
                                        <td data-label="#@lang('Transaction ID')">{{$data->trx}}</td>
                                        <td data-label="@lang('Amount')">
                                            <strong>{{nb(getAmount($data->amount))}} {{$general->cur_text}}</strong>
                                        </td>
                                        <td data-label="@lang('Order')">
                                            <strong>{{nb(getAmount($data->amount) / $plan->price )}} PIN</strong>
                                        </td>
                                        <td data-label="@lang('Status')">
                                            @if($data->status == 1)
                                                <span class="badge badge--success">@lang('Complete')</span>
                                            @elseif($data->status == 2)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($data->status == 3)
                                                <span class="badge badge--danger">@lang('Cancel')</span>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Time')">
                                            <i class="las la-calendar"></i> {{showDateTime($data->created_at)}}
                                        </td>
                                        <td data-label="@lang('Details')">
                                            <button class="btn--info btn-rounded  badge detailBtn"
                                            data-admin_feedback="{{$data->admin_feedback}}" data-detail="{{ asset($data->detail) }}" data-sender="{{ $data->btc_amo }}"><i
                                            class="fa fa-eye"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="100%" class="text-center"> @lang('No results found')!</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{$logs->appends($_GET)->links()}}
                </div>
            </div>
        </div>
    </div>


    {{-- Detail MODAL --}}
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
                    <p>Nama Pengirim : <b id="Sender"></b></p>
                    <img src="" id="imgDetail" alt="">
                    <div class="withdraw-detail"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger" data-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        "use strict";
        (function ($) {
        
            $('.detailBtn').on('click', function () {
                var modal = $('#detailModal');
                var feedback = $(this).data('admin_feedback');
                var sender = $(this).data('sender');
                var img = $(this).data('detail')
                // modal.find('.withdraw-detail').html(`<p> ${feedback} </p>`);
                modal.find('#Sender').text(sender);
                modal.find('#imgDetail').attr("src",img);
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush

@push('breadcrumb-plugins')
    <form action="" method="GET" class="form-inline float-sm-right bg--white">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="@lang('Search by TRX')" value="{{ @$search }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
@endpush


