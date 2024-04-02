@extends('admin.layouts.app')

@section('panel')
    @if (request()->routeIs('admin.report.allPayout'))
        <div class="row mb-none-30">
            <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
                <div class="dashboard-w1 bg--gradi-10 b-radius--10 box-shadow">
                    <div class="icon">
                        <i class="fa fa-wallet"></i>
                    </div>
                    <div class="details">
                        <div class="numbers">
                            <span class="currency-sign">{{ $general->cur_sym }}</span>

                            <span class="amount">{{ nb($ref) }}</span>
                        </div>
                        <div class="desciption">
                            <span class="text--small">@lang('Referrals Commission')</span>
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

                            <span class="amount">{{ nb($binnary) }}</span>
                        </div>
                        <div class="desciption">
                            <span class="text--small">@lang('Binnary Commission')</span>
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
                            <span class="currency-sign">{{ $general->cur_sym }}</span>
                            <span class="amount">{{ nb($leader) }}</span>
                        </div>
                        <div class="desciption">
                            <span class="text--small">@lang('Leader Commission')</span>
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
                            <span class="currency-sign">{{ $general->cur_sym }}</span>

                            <span class="amount">{{ nb($founder) }}</span>
                        </div>
                        <div class="desciption">
                            <span class="text--small">@lang('Founder Commission')</span>
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
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th scope="col">@lang('Date')</th>
                                    <th scope="col">@lang('TRX')</th>
                                    <th scope="col">@lang('Username')</th>
                                    <th scope="col">@lang('Amount')</th>
                                    <th scope="col">@lang('Charge')</th>
                                    <th scope="col">@lang('Post Balance')</th>
                                    <th scope="col">@lang('Detail')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td data-label="@lang('Date')">{{ showDateTime($trx->created_at) }}</td>
                                        <td data-label="@lang('TRX')" class="font-weight-bold">{{ $trx->trx }}
                                        </td>
                                        <td data-label="@lang('Username')"><a
                                                href="{{ route('admin.users.detail', $trx->user_id) }}">{{ @$trx->user->username }}</a>
                                        </td>
                                        <td data-label="@lang('Amount')" class="budget">
                                            <strong
                                                @if ($trx->trx_type == '+') class="text-success" @else class="text-danger" @endif>
                                                {{ $trx->trx_type == '+' ? '+' : '-' }} {{ nb($trx->amount) }}
                                                {{ $general->cur_text }}</strong>
                                        </td>
                                        <td data-label="@lang('Charge')" class="budget">{{ $general->cur_sym }}
                                            {{ nb($trx->charge) }} </td>
                                        <td data-label="@lang('Post Balance')">{{ nb($trx->post_balance + 0) }}
                                            {{ $general->cur_text }}</td>
                                        <td data-label="@lang('Detail')">{{ __($trx->details) }}</td>
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
                    {{ $transactions->links('admin.partials.paginate') }}
                </div>
            </div><!-- card end -->
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    {{-- @if (request()->routeIs('admin.users.transactions'))
        <form action="" method="GET" class="form-inline float-sm-right bg--white">
            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('TRX / Username')" value="{{ $search ?? '' }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
    @else
        <form action="{{ route('admin.report.transaction.search') }}" method="GET" class="form-inline float-sm-right bg--white">
            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('TRX / Username')" value="{{ $search ?? '' }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
    @endif --}}
    <div class="row">

        <div class="col-md-10 col-9">

            <form action="{{ route('admin.report.transaction.search') }}" method="GET"
                class="form-inline float-sm-right bg--white">
                <div class="input-group has_append">
                    <input type="text" name="search" class="form-control" placeholder="@lang('TRX / Username')"
                        value="{{ $search ?? '' }}">
                    <div class="input-group-append">
                        <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-2 col-3">

            <form action="{{ route('admin.report.export') }}" method="GET" class="form-inline float-sm-right">
                <input hidden type="text" name="search" class="form-control" placeholder="@lang('Username or email')"
                    value="{{ $search ?? '' }}">
                <input hidden type="text" name="page" class="form-control" placeholder="@lang('Username or email')"
                    value="{{ $page_title ?? '' }}">
                <button class="btn btn--primary" type="submit">Export</button>
            </form>
        </div>
    </div>
@endpush
