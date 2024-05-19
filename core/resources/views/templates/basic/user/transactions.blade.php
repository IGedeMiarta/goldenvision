@extends($activeTemplate . 'user.layouts.app')

@section('panel')
    {{-- <div class="row mb-5">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header text-center">
                    Convert Your Balance To PIN
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.convert.saldo') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="qty" class="col-sm-2 col-form-label">Convert ALL</label>

                            <div class="input-group col-sm-10">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <input type="checkbox" id="check"
                                            aria-label="Checkbox for following text input">
                                    </div>
                                </div>
                                <input type="text" class="form-control" id="value"
                                    aria-label="Text input with checkbox" name="saldo"
                                    value="{{ getAmount(auth()->user()->balance) }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="qty" class="col-sm-2 col-form-label">QTY <span
                                    class="text-danger">*</span></label>
                            <div class="input-group col-sm-10">
                                <input type="text" class="form-control col-md-2" id="qty" name="qty"
                                    placeholder="qty" value="">
                                <button type="button">=</button>
                                <input type="text" class="form-control" id="idr" name="idr" placeholder="IDR"
                                    value="" readonly>
                                <button type="button">//</button></button>
                                <input type="text" class="form-control" id="sisa" name="sisa"
                                    placeholder="Remais" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label"></label>
                            <div class="input-group col-sm-10">
                                <button type="submit" class="btn btn-success">Convert</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
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
                                    <th scope="col">@lang('TRX')</th>
                                    <th scope="col">@lang('Amount')</th>
                                    <th scope="col">@lang('Charge')</th>
                                    <th scope="col">@lang('Post Balance')</th>
                                    <th scope="col">@lang('Detail')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions  as $trx)
                                    <tr>
                                        <td data-label="@lang('SL')">{{ $transactions->firstItem() + $loop->index }}
                                        </td>
                                        <td data-label="@lang('Date')">{{ showDateTime($trx->created_at) }}</td>
                                        <td data-label="@lang('TRX')" class="font-weight-bold">{{ $trx->trx }}
                                        </td>
                                        <td data-label="@lang('Amount')" class="budget">
                                            <strong
                                                @if ($trx->trx_type == '+') class="text-success"
                                                @else class="text-danger" @endif>
                                                {{ $trx->trx_type == '+' ? '+' : '-' }} {{ nb(getAmount($trx->amount)) }}
                                                {{ $general->cur_text }}</strong>
                                        </td>
                                        <td data-label="@lang('Charge')" class="budget">{{ $general->cur_sym }}
                                            {{ nb(getAmount($trx->charge)) }} </td>
                                        <td data-label="@lang('Post Balance')">{{ nb($trx->post_balance + 0) }}
                                            {{ $general->cur_text }}</td>
                                        <td data-label="@lang('Detail')">{!! __($trx->details) !!}</td>
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

@push('script')
    <script>
        $("#check").on('change', function() {
            let A = $('#value').val();
            let B = 350000;
            if (A < B) {
                alert('not have balance')
                $(this).prop('checked', false); // Unchecks it
            }
            let hasil = Math.floor(A / B);
            let sisa = A % B;
            if (this.checked) {
                $('#qty').val(hasil);
                $('#idr').val(hasil * B);
                $('#sisa').val(sisa);
            }
        });
        $('#qty').on('keyup', delay(function(e) {
            let A = $('#value').val(); //5.100
            let B = 350000; //2.500
            let C = $(this).val(); //2
            let D = (B * C); // 2.500 * 2 = 5.000
            let F = A - D; //5.100 - 5.000 = 100
            if (D > A) {
                alert('not have balance')
                $(this).val(''); // Unchecks it
            }
            let hasil = Math.floor(D / B); // 5.000/2.500 = 2
            let sisa = D % B; //5000 / 2500 = 0 + 
            $('#idr').val(hasil * B);
            $('#sisa').val(sisa + F);
        }, 500));

        function delay(callback, ms) {
            var timer = 0;
            return function() {
                var context = this,
                    args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function() {
                    callback.apply(context, args);
                }, ms || 0);
            };
        }
    </script>
@endpush


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
