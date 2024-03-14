@extends($activeTemplate . 'user.layouts.app')

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
                                    <th scope="col">@lang('Start POINT')</th>
                                    <th scope="col">@lang('Distribute')</th>
                                    <th scope="col">@lang('Post POINT')</th>
                                    <th scope="col">@lang('Detail')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions  as $trx)
                                    <tr class="">
                                        <td data-label="@lang('SL')">{{ $transactions->firstItem() + $loop->index }}
                                        </td>
                                        <td data-label="@lang('Date')">{{ showDateTime($trx->created_at) }}</td>
                                        <td data-label="@lang('Start')">{{ $trx->start_point }}</td>
                                        <td data-label="@lang('PIN')"
                                            class="{{ $trx->type == '-' ? 'text-danger' : 'text-success' }}">
                                            {{ $trx->type . $trx->point }}
                                        </td>
                                        <td data-label="@lang('Post ')">{{ $trx->end_point }}</td>
                                        <td data-label="@lang('Detail')">{!! __($trx->desc) !!}</td>
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
    <form action="" method="GET" class="form-inline float-sm-right bg--white">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="@lang('Search by TRX')"
                value="{{ $search ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
@endpush
