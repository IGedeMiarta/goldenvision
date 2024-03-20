@extends('admin.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        @if (request()->routeIs('admin.deposit.list') ||
                request()->routeIs('admin.deposit.method') ||
                request()->routeIs('admin.users.deposits') ||
                request()->routeIs('admin.deposit.dateSearch') ||
                request()->routeIs('admin.users.deposits.method'))
            <div class="col-md-4 col-sm-6 mb-30">
                <div class="widget-two box--shadow2 b-radius--5 bg--success">
                    <div class="widget-two__content">
                        <h2 class="text-white">
                            {{ __($general->cur_sym) }} {{ nb($deposits->where('status', 1)->sum('amount')) }}</h2>
                        <p class="text-white">@lang('Successful Deposit')</p>
                    </div>
                </div><!-- widget-two end -->
            </div>
            <div class="col-md-4 col-sm-6 mb-30">
                <div class="widget-two box--shadow2 b-radius--5 bg--6">
                    <div class="widget-two__content">
                        <h2 class="text-white">
                            {{ __($general->cur_sym) }} {{ nb($deposits->where('status', 2)->sum('amount')) }}</h2>
                        <p class="text-white">@lang('Pending Deposit')</p>
                    </div>
                </div><!-- widget-two end -->
            </div>
            <div class="col-md-4 col-sm-6 mb-30">
                <div class="widget-two box--shadow2 b-radius--5 bg--pink">
                    <div class="widget-two__content">
                        <h2 class="text-white">
                            {{ __($general->cur_sym) }} {{ nb($deposits->where('status', 3)->sum('amount')) }}</h2>
                        <p class="text-white">@lang('Rejected Deposit')</p>
                    </div>
                </div><!-- widget-two end -->
            </div>
        @endif
        <div class="col-md-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th scope="col">@lang('Date')</th>
                                    <th scope="col">@lang('Trx Number')</th>
                                    @if (!request()->routeIs('admin.users.deposits'))
                                        <th scope="col">@lang('Username')</th>
                                        <th scope="col">@lang('Email')</th>
                                    @endif
                                    <th scope="col">@lang('Amount')</th>
                                    <th scope="col">@lang('Payable')</th>
                                    <th scope="col">@lang('Status')</th>
                                    {{-- <th scope="col">@lang('Action')</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    @php
                                        $details = $deposit->detail != null ? json_encode($deposit->detail) : null;
                                    @endphp
                                    <tr>
                                        <td data-label="@lang('Date')"> {{ showDateTime($deposit->created_at) }}</td>
                                        <td data-label="@lang('Trx Number')" class="font-weight-bold text-uppercase">
                                            {{ $deposit->trx }}</td>
                                        @if (!request()->routeIs('admin.users.deposits'))
                                            <td data-label="@lang('Username')"><a
                                                    href="{{ route('admin.users.detail', $deposit->user_id) }}">{{ $deposit->user->username }}</a>
                                            </td>
                                            <td data-label="@lang('Date')"> {{ $deposit->user->email }}</td>
                                        @endif

                                        <td data-label="@lang('Amount')" class="font-weight-bold">
                                            {{ nb(getAmount($deposit->amount)) }} {{ __($general->cur_text) }}</td>

                                        <td data-label="@lang('Payable')" class="font-weight-bold">
                                            {{ nb(getAmount($deposit->final_amo)) }} {{ __($deposit->method_currency) }}
                                        </td>
                                        <td data-label="@lang('Status')">
                                            @if ($deposit->status == 2)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($deposit->status == 1)
                                                <span class="badge badge--success">@lang('Approved')</span>
                                            @elseif($deposit->status == 3)
                                                <span class="badge badge--danger">@lang('Rejected')</span>
                                            @endif
                                        </td>
                                        {{-- <td data-label="@lang('Action')">
                                            <a href="{{ route('admin.deposit.details', $deposit->id) }}"
                                                class="icon-btn ml-1 " data-toggle="tooltip" title=""
                                                data-original-title="@lang('Detail')">
                                                <i class="la la-eye"></i>
                                            </a>
                                        </td> --}}
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
                    {{ paginateLinks($deposits) }}
                </div>
            </div><!-- card end -->
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    @if (!request()->routeIs('admin.users.deposits') && !request()->routeIs('admin.users.deposits.method'))
        <div class="row">
            <div class="col-md-10 col-9">
                <form
                    action="{{ route('admin.deposit.search', $scope ?? str_replace('admin.deposit.', '', request()->route()->getName())) }}"
                    method="GET" class="form-inline float-sm-right bg--white mb-2">
                    <div class="input-group has_append ">
                        <input type="text" name="search" class="form-control" placeholder="@lang('Deposit code/Username')"
                            value="{{ $search ?? '' }}">
                        <div class="input-group-append">
                            <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>

                <form
                    action="{{ route('admin.deposit.dateSearch', $scope ?? str_replace('admin.deposit.', '', request()->route()->getName())) }}"
                    method="GET" class="form-inline float-sm-right bg--white mr-0 mr-xl-2 mr-lg-0">
                    <div class="input-group has_append ">
                        <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - "
                            data-language="en" class="datepicker-here form-control bg-white text--black"
                            data-position='bottom right' placeholder="@lang('Min Date - Max date')" autocomplete="off" readonly
                            value="{{ @$dateSearch }}">
                        <input type="hidden" name="method" value="{{ @$methodAlias }}">
                        <div class="input-group-append">
                            <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-2 col-3">
                <form action="{{ route('admin.deposit.export') }}" method="GET" class="form-inline float-sm-right">
                    <input hidden type="text" name="search" class="form-control" placeholder="@lang('Username or email')"
                        value="{{ $search ?? '' }}">
                    <input hidden type="text" name="date" class="form-control" placeholder="@lang('Username or email')"
                        value="{{ $dateSearch ?? '' }}">
                    <input hidden type="text" name="page" class="form-control" placeholder="@lang('Username or email')"
                        value="{{ $scope ?? '' }}">
                    <input hidden type="text" name="paget" class="form-control" placeholder="@lang('Username or email')"
                        value="{{ $page_title ?? '' }}">
                    <button class="btn btn--primary" type="submit">Export</button>
                </form>
            </div>
        </div>
    @endif
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
        'use strict';
        (function($) {
            if (!$('.datepicker-here').val()) {
                $('.datepicker-here').datepicker();
            }
        })(jQuery)
    </script>
@endpush
