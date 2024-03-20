@extends($activeTemplate . 'user.layouts.app')

@section('panel')
    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-md-12 col-lg-12 col-xl-10 col-xxl-7">
                <div class="card card-deposit text-center">
                    <div class="card-body card-body-deposit card-body">
                        <div class="">
                            {{-- <div class="deposit-thumb">
                                <img class="" src="{{ $data->gateway_currency()->methodImage() }}" />
                            </div> --}}
                            <div class="deposit-content">
                                <ul class="mb-3">
                                    <li>
                                        @lang('Amount'):
                                        <b class="fw-bolder"><span class="text--success">{{ getAmount($data->amount) }}
                                            </span> {{ $general->cur_text }}</b>
                                    </li>
                                    <li>
                                        @lang('Charge'):
                                        <b class="fw-bolder"><span
                                                class="text--danger">{{ getAmount($data->charge) }}</span>
                                            {{ $general->cur_text }}</b>
                                    </li>
                                    <li>
                                        @lang('Payable'):
                                        <b class="fw-bolder"><span class="text--warning">
                                                {{ getAmount($data->amount + $data->charge) }}</span>
                                            {{ $general->cur_text }}</b>
                                    </li>
                                    <li>
                                        @lang('Conversion Rate'):
                                        <b class="fw-bolder"><span class="text--info">1 {{ $general->cur_text }} =
                                                {{ getAmount($data->rate) }} {{ $data->baseCurrency() }}</span></b>
                                    </li>
                                    <li>
                                        @lang('In') {{ $data->baseCurrency() }}:
                                        <b class="fw-bolder"><span
                                                class="text--primary"text--primary>{{ getAmount($data->final_amo) }}</span></b>
                                    </li>
                                    @if ($data->gateway->crypto == 1)
                                        <li>
                                            @lang('Conversion with')
                                            <b> {{ $data->method_currency }}</b> @lang('and final value will show on next step')
                                        </li>
                                    @endif
                                </ul>
                                {{-- @dd($data->method_code) --}}
                                @if (1000 > $data->method_code)
                                    <a href="{{ route('user.deposit.confirm') }}"
                                        class="btn btn--success btn-block py-3 font-weight-bold">@lang('Confirm Deposit')</a>
                                @else
                                    <a href="{{ route('user.deposit.manual.confirm') }}"
                                        class="btn btn--success btn-block py-3 font-weight-bold">@lang('Confirm Deposit')</a>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@stop
