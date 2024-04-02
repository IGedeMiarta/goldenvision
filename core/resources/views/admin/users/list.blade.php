@extends('admin.layouts.app')
@section('panel')
    @if (request()->routeIs('admin.users.balance'))
        <div class="row mb-none-30">
            <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
                <div class="dashboard-w1 bg--gradi-10 b-radius--10 box-shadow">
                    <div class="icon">
                        <i class="fa fa-wallet"></i>
                    </div>
                    <div class="details">
                        <div class="numbers">
                            <span class="currency-sign">{{ $general->cur_sym }}</span>

                            <span class="amount">{{ nb($b_balance) }}</span>
                        </div>
                        <div class="desciption">
                            <span class="text--small">@lang('Total B Wallet')</span>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
                <div class="dashboard-w1 bg--gradi-9 b-radius--10 box-shadow">
                    <div class="icon">
                        <i class="fa fa-wallet"></i>
                    </div>
                    <div class="details">
                        <div class="numbers">
                            <span class="currency-sign">{{ $general->cur_sym }}</span>

                            <span class="amount">{{ nb($balance) }}</span>
                        </div>
                        <div class="desciption">
                            <span class="text--small">@lang('Total Cash Wallet')</span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
                <div class="dashboard-w1 bg--gradi-12 b-radius--10 box-shadow">
                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="details">
                        <div class="numbers">
                            {{-- <span class="currency-sign">{{ $general->cur_sym }}</span> --}}
                            <span class="amount">{{ nb($pin) }}</span>
                        </div>
                        <div class="desciption">
                            <span class="text--small">@lang('Total PIN')</span>
                        </div>

                    </div>
                </div>
            </div>


            <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
                <div class="dashboard-w1 bg--gradi-13 b-radius--10 box-shadow">
                    <div class="icon">
                        <i class="fa fa-wallet"></i>
                    </div>
                    <div class="details">
                        <div class="numbers">
                            {{-- <span class="currency-sign">{{ $general->cur_sym }}</span> --}}

                            <span class="amount">{{ nb($point) }}</span>
                        </div>
                        <div class="desciption">
                            <span class="text--small">@lang('Total POINT')</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th scope="col">@lang('User')</th>
                                    @if (request()->routeIs('admin.users.balance'))
                                        <th scope="col">@lang('Balance')</th>
                                    @endif
                                    <th scope="col">@lang('Username')</th>
                                    <th scope="col">@lang('Email')</th>
                                    <th scope="col">@lang('Phone')</th>
                                    <th scope="col">@lang('Joined At')</th>
                                    <th scope="col">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>

                                        <td data-label="@lang('User')">
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ getImage(imagePath()['profile']['user']['path'] . '/' . $user->image, imagePath()['profile']['user']['size']) }}"
                                                        alt="@lang('image')">
                                                </div>
                                                <span class="name">{{ $user->fullname }}</span>
                                            </div>
                                        </td>
                                        {{-- <td data-label="@lang('User')" style="text-align: start">
                                            <span style="font-size: 12px">B Balance:</span> <a
                                                href="{{ route('admin.users.detail', $withdraw->user_id) }}">{{ @$withdraw->user->username }}</a>
                                            <br>
                                            <span style="font-size: 12px">Fullname:</span>
                                            <b>{{ $withdraw->user->fullname }}</b> <br>
                                            <span style="font-size: 12px">Username:</span>
                                            <b>{{ $withdraw->user->mobile }}</b>
                                        </td> --}}
                                        @if (request()->routeIs('admin.users.balance'))
                                            <td style="text-align: start">
                                                <span style="font-size: 12px">B Balance <b>Rp
                                                        {{ nb($user->b_balance) }}</b></span> <br>
                                                <span style="font-size: 12px">Cash Balance <b> Rp
                                                        {{ nb($user->balance) }}</b></span> <br>
                                                <span style="font-size: 12px">PIN <b>{{ nb($user->pin) }}</b></span> <br>
                                                <span style="font-size: 12px">POINT <b>{{ nb($user->point) }}</b></span>
                                            </td>
                                        @endif
                                        <td data-label="@lang('Username')"><a
                                                href="{{ route('admin.users.detail', $user->id) }}">{{ $user->username }}</a>
                                        </td>
                                        <td data-label="@lang('Email')">{{ $user->email }}</td>
                                        <td data-label="@lang('Phone')">{{ $user->mobile }}</td>
                                        <td data-label="@lang('Joined At')">{{ showDateTime($user->created_at) }}</td>
                                        <td data-label="@lang('Action')">
                                            <a href="{{ route('admin.users.detail', $user->id) }}" class="icon-btn"
                                                data-toggle="tooltip" data-original-title="@lang('Details')">
                                                <i class="las la-desktop text--shadow"></i>
                                            </a>
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
                    {{ paginateLinks($users) }}
                </div>
            </div><!-- card end -->
        </div>


    </div>
@endsection



@push('breadcrumb-plugins')
    <div class="row">
        <div class="col-md-10 col-9">
            <form
                action="{{ route('admin.users.dateSearch', $scope ?? str_replace('admin.users.', '', request()->route()->getName())) }}"
                method="GET" class="form-inline float-sm-right bg--white ml-3 mr-3">
                <div class="input-group has_append ">
                    <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - "
                        data-language="en" class="datepicker-here form-control bg-white text--black"
                        data-position='bottom right' placeholder="@lang('Min Date - Max date')" autocomplete="off" readonly
                        value="{{ @$dateSearch }}">
                    <input type="hidden" name="search" value="{{ @$search }}">
                    <div class="input-group-append">
                        <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
            <form
                action="{{ route('admin.users.search', $scope ?? str_replace('admin.users.', '', request()->route()->getName())) }}"
                method="GET" class="form-inline float-sm-right bg--white">
                <div class="input-group has_append">
                    <input type="text" name="search" class="form-control" placeholder="@lang('Username or email')"
                        value="{{ $search ?? '' }}">
                    <div class="input-group-append">
                        <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-2 col-3">
            <form action="{{ route('admin.users.export.all') }}" method="GET" class="form-inline float-sm-right">
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
