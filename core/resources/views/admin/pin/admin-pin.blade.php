@extends('admin.layouts.app')

@section('panel')
    <div class="row ">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th scope="col">@lang('SL')</th>
                                    <th scope="col">@lang('Date')</th>
                                    <th scope="col">@lang('PIN By')</th>
                                    <th scope="col">@lang('PIN To')</th>
                                    <th scope="col">@lang('Start PIN')</th>
                                    <th scope="col">@lang('Distribute')</th>
                                    <th scope="col">@lang('Post PIN')</th>
                                    <th scope="col">@lang('Detail')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions  as $trx)
                                    <tr class="">
                                        <td data-label="@lang('SL')">{{ $transactions->firstItem() + $loop->index }}
                                        </td>
                                        <td data-label="@lang('Date')">{{ showDateTime($trx->created_at) }}</td>
                                        <td data-label="@lang('TRX')" class="font-weight-bold">
                                            {{ $trx->pin_username ?? 'System' }}
                                        </td>
                                        <td data-label="@lang('TRX')" class="font-weight-bold">
                                            {{ $trx->user_username ?? '-' }}
                                        </td>
                                        <td data-label="@lang('Start')">{{ $trx->start_pin }}</td>
                                        <td data-label="@lang('PIN')"
                                            class="{{ $trx->type == '-' ? 'text-danger' : 'text-success' }}">
                                            {{ $trx->type . $trx->pin }}
                                        </td>
                                        <td data-label="@lang('Post ')">{{ $trx->end_pin }}</td>
                                        <td data-label="@lang('Detail')">{{ __($trx->ket) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($empty_message) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ $transactions->appends($_GET)->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection




@push('breadcrumb-plugins')
    {{-- @if (!request()->routeIs('admin.users.withdrawals') && !request()->routeIs('admin.users.withdrawals.method'))
        <div class="row">
            <div class="col-md-10">
                <form
                    action="{{ route('admin.withdraw.dateSearch', $scope ?? str_replace('admin.withdraw.', '', request()->route()->getName())) }}"
                    method="GET" class="form-inline float-sm-right bg--white mr-0 mr-xl-2 ml-lg-2">
                    <div class="input-group has_append">
                        <input name="date" type="date" data-range="true" data-multiple-dates-separator=" - "
                            data-language="en" class=" bg--white text--secondary form-control" data-position='bottom right'
                            placeholder="@lang('date')" autocomplete="off" value="{{ @$dateSearch }}">

                        <input type="hidden" name="method" value="{{ @$method->id }}">
                        <div class="input-group-append">
                            <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
                <form
                    action="{{ route('admin.withdraw.search', $scope ?? str_replace('admin.withdraw.', '', request()->route()->getName())) }}"
                    method="GET" class="form-inline float-sm-right bg--white">
                    <div class="input-group has_append">
                        <input type="text" name="search" class="form-control" placeholder="@lang('Withdrawal code/Username')"
                            value="{{ $search ?? '' }}">
                        <div class="input-group-append">
                            <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>

            </div>
            <div class="col-md-2 col-3">
                <form action="{{ route('admin.withdraw.export') }}" method="GET" class="form-inline float-sm-right">
                    <input hidden type="text" name="search" class="form-control" placeholder="@lang('Username or email')"
                        value="{{ $search ?? null }}">
                    <input hidden type="text" name="date" class="form-control" placeholder="@lang('Username or email')"
                        value="{{ $dateSearch ?? null }}">
                    <input hidden type="text" name="page" class="form-control" placeholder="@lang('Username or email')"
                        value="{{ $page_title ?? '' }}">
                    <button class="btn btn-success" type="submit"><i class="fas fa-file-excel"></i> Export</button>
                </form>
            </div>
        </div>
    @endif --}}
@endpush
@push('script')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js"
        integrity="sha512-RCgrAvvoLpP7KVgTkTctrUdv7C6t7Un3p1iaoPr1++3pybCyCsCZZN7QEHMZTcJTmcJ7jzexTO+eFpHk4OCFAg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    {{-- <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script> --}}
    <script>
        'use strict';
        (function($) {
            // if (!$('.datepicker-here').val()) {
            //     $('.datepicker-here').datepicker();
            // }
            $('.datepicker').datepicker();
        })(jQuery)
    </script>
@endpush
