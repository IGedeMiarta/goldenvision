@extends($activeTemplate . 'user.layouts.app')

@push('style')
    <style>
        .progress {
            height: 30px;
        }

        a.disabled {
            cursor: default;
            pointer-events: none
        }

        .alert {
            position: relative;
            top: 10;
            left: 0;
            width: auto;
            height: auto;
            padding: 10px;
            margin: 10px;
            line-height: 1.8;
            border-radius: 5px;
            cursor: hand;
            cursor: pointer;
            font-family: sans-serif;
            font-weight: 400;
        }

        .alertCheckbox {
            display: none;
        }

        :checked+.alert {
            display: none;
        }

        .alertText {
            display: table;
            margin: 0 auto;
            text-align: center;
            font-size: 16px;
        }

        .alertClose {
            float: right;
            padding-top: 5px;
            font-size: 10px;
        }

        .clear {
            clear: both;
        }

        .info {
            background-color: #EEE;
            border: 1px solid #DDD;
            color: #999;
        }

        .success {
            background-color: #EFE;
            border: 1px solid #DED;
            color: #9A9;
        }

        .notice {
            background-color: #EFF;
            border: 1px solid #DEE;
            color: #9AA;
        }

        .warning {
            background-color: #FDF7DF;
            border: 1px solid #FEEC6F;
            color: #C9971C;
        }

        .error {
            background-color: #FEE;
            border: 1px solid #EDD;
            color: #A66;
        }
    </style>
@endpush

@section('panel')
    <div class="row">
        @foreach ($plans as $data)
            <div class="card col-md-3" style="background-color: #000000; border-radius: 15px">
                <div class="card-body  ">
                    <div class="text" style="display: flex; justify-content: space-between;">
                        <h5 class="text-warning">
                            {{ $data->name }}
                        </h5>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <div class="text-price d-flex">
                        <h2 class="text-white">Rp {{ nb($data->price) }} <span class="text-small text-white">/
                                PIN</span>
                        </h2>
                    </div>
                    <div class="text-price-info d-flex mt-n4">
                        <h2 class="text-white"><span class="text-small secondary">Use Your {{ auth()->user()->pin }}
                                PIN to
                                Buy</span>
                        </h2>
                    </div>
                    <div class="preview-info">

                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-30">

                <div class="card" style="border-radius: 15px">

                    <div class="card-body pt-5 pb-5 ">
                        <div class="pricing-table text-center mb-4">
                            <h2 class="package-name mb-20 text-"><strong>@lang($data->name)</strong></h2>
                            <span
                                class="price text--dark font-weight-bold d-block">{{ $general->cur_sym }}{{ nb(getAmount($data->price)) }}</span>
                            <p>/ PIN</p>
                            <hr>
                            Use Your {{ auth()->user()->pin }} PIN to buy
                            <hr>
                            <ul class="package-features-list mt-30">
                                <li>
                                    <i
                                        class="fas @if (getAmount($data->tree_com) != 0) fa-check bg--success @else fa-times bg--danger @endif "></i>
                                    <span>@lang('Referal Commission'): {{ $general->cur_sym }} {{ nb(getAmount($data->ref_com)) }}
                                    </span>
                                </li>

                            </ul>
                        </div>
                        @if (Auth::user()->plan_id != $data->id)
                            <a href="#confBuyModal{{ $data->id }}" data-toggle="modal"
                                class="btn w-100  btn--primary text-light btn-outline--primary  mt-20 py-2 box--shadow1  @if (auth()->user()->pin < 1) disabled @endif">@lang('Subscribe')</a>
                        @else
                            <a data-toggle="modal"
                                class="btn w-100 btn-outline--primary  mt-20 py-2 box--shadow1">@lang('Already Subscribe')</a>
                        @endif
                    </div>


                    <div class="modal fade" id="confBuyModal{{ $data->id }}" class="modalPlan" tabindex="-1"
                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="modal"
                        data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" id="modalHeader">
                                    <h4 class="modal-title" id="myModalLabel"> @lang('Confirm Purchase ' . $data->name)?</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">×</span></button>
                                </div>

                                <div id="formModal" class="">
                                    <form method="post" action="{{ route('user.plan.purchase') }}">
                                        @csrf
                                        <div class="modal-body row">
                                            <h2 class="text-center col-12">
                                                <span class="text-primary h5"> Use Your <b>{{ auth()->user()->pin }}
                                                        PIN</b> to
                                                    buy</span>
                                            </h2>
                                            <input type="hidden" name="prices" value="{{ getAmount($data->price) }}">
                                            <input type="hidden" name="plan_id" value="{{ $data->id }}">
                                            <div class="form-group col-6">
                                                <label for="ref_name" class="form--label-2">@lang('Package')</label>
                                                <select name="package" id="gold"
                                                    class="package form-control form--control-2">
                                                    <option>{{ auth()->user()->pin < 1 ? 'You Have No Pin' : 'Select' }}
                                                    </option>
                                                    <option value="1" {{ auth()->user()->pin < 1 ? 'disabled' : '' }}
                                                        {{ old('pin') == 1 ? 'selected' : '' }}>1 ID</option>
                                                    <option value="3"
                                                        {{ auth()->user()->pin < 3 ? 'disabled' : '' }}{{ old('pin') == 1 ? 'selected' : '' }}>
                                                        3 ID
                                                    </option>
                                                    <option value="5"
                                                        {{ auth()->user()->pin < 5 ? 'disabled' : '' }}{{ old('pin') == 1 ? 'selected' : '' }}>
                                                        5 ID
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="form-group col-6">
                                                <label for="">total</label>
                                                <input class="form-control" type="number" name="total"
                                                    value="{{ getAmount($data->price) }}" placeholder="total" disabled>
                                            </div>
                                            <div class="form-group col-6 d-none">
                                                <label for="">QTY</label>
                                                <input class="form-control" type="number" name="qty" id="qty"
                                                    min="1" value="" placeholder="MP qty" readonly>
                                            </div>

                                            {{-- <div class="col-12 d-none">
                                                <label for="ref_name" class="form--label-2 ">@lang('Sponsor Username')</label>
                                                <input type="text" name="sponsor"
                                                    class="referral form-control form--control-2 sponsorName"
                                                    value="{{ $sponsor?->username ?? old('sponsor') }}" id="ref_name"
                                                    placeholder="@lang('Enter Sponsor Username')*" required>
                                                <span class="text-success d-none textSponsorInfo"></span>
                                            </div> --}}
                                            <div class="form-group col-12 mt-3 positionInpt d-none">
                                                <label for="">Position</label>
                                                <select name="pos" class="form-control position">
                                                    <option>-Select</option>
                                                    <option value="1" {{ old('position') == 1 ? 'selected' : '' }}>
                                                        Kiri
                                                    </option>
                                                    <option value="2" {{ old('position') == 2 ? 'selected' : '' }}>
                                                        Kanan
                                                    </option>


                                                </select>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn--danger" data-dismiss="modal"><i
                                                    class="fa fa-times"></i>
                                                @lang('Close')</button>

                                            <button type="submit" class="btn btn-primary btnSubmit"><i
                                                    class="lab la-telegram-plane"></i>
                                                @lang('Subscribe')</button>
                                        </div>
                                    </form>
                                </div>
                                <div id="barModal" class="d-none">
                                    <div class="modal-body">
                                        <div class="">
                                            <img src="{{ asset('assets/spin.gif') }}" alt="loading.."
                                                style=" display: block;
                                                margin-left: auto;
                                                margin-right: auto;
                                                width: 50%;">
                                        </div>
                                        <hr>
                                        <div class="progress d-none">
                                            <div id="progressBar" class="progress-bar" role="progressbar"
                                                style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                                aria-valuemax="100">0%</div>
                                        </div>
                                        <ul class="package-features-list mt-30 borderless">
                                            <div id="bar">
                                                <li><i class="fas fa-times bg--secondary"></i>Validation Input</li>
                                                <li><i class="fas fa-times bg--secondary"></i>Subscribed Plan</li>
                                                <li><i class="fas fa-times bg--secondary"></i>Register New User</li>
                                                <li><i class="fas fa-times bg--secondary"></i>Publish Data</li>
                                            </div>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="modal fade" id="bvInfoModal">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">@lang('Business Volume (BV) info')</h5>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-label="@lang('Close')">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <h5 class="text-danger">@lang('When someone from your below tree subscribe this plan, You will get this Business Volume which will be used for matching bonus').
                                    </h5>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn--dark"
                                        data-dismiss="modal">@lang('Close')</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="confBuyMP{{ $data->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel1"> @lang('Confirm Purchase MP')?</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">×</span></button>
                                </div>
                                <form method="post" action="{{ route('user.plan.mppurchase') }}">

                                    @csrf
                                    <div class="modal-body row">
                                        <h5 class="text-center col-12"> {{ getAmount($data->price) }}
                                            {{ $general->cur_text }} / MP
                                            <br>
                                            or
                                            <br>
                                            <span class="text-success">Use Your {{ auth()->user()->pin }} PIN to
                                                buy</span>
                                        </h5>
                                        <input type="hidden" name="pricess" value="{{ getAmount($data->price) }}">
                                        <input type="hidden" name="plan_id" value="{{ $data->id }}">
                                        <div class="form-group col-6">
                                            <label for="">QTY</label>
                                            <input class="form-control" type="number" name="qtyy" id="qty"
                                                min="1" value="" placeholder="MP qty" required>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="">total</label>
                                            <input class="form-control" type="number" name="totall" value=""
                                                placeholder="total" disabled>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn--danger" data-dismiss="modal"><i
                                                class="fa fa-times"></i>
                                            @lang('Close')</button>

                                        <button type="submit" class="btn btn--success"><i
                                                class="lab la-telegram-plane"></i> @lang('Buy')</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
        @endforeach
    </div>
    @if ($sponsor && $pos)
        <div class="col-md-3">
            <div class="card success" style="width: 500px; border-radius: 15px">
                <div class="card-body">
                    <h4>Sponsored By: <b>{{ $sponsor->username }}</b></h4>
                    <hr>
                    Success Menerapkan Link Undangan.
                </div>
            </div>
        </div>
    @endif
    <div class="modal fade" id="refComInfoModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Referral Commission info')</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5><span class=" text-danger">@lang('When your referred user subscribe in') <b> @lang('ANY PLAN')</b>,
                            @lang('you will get this amount').</span>
                        <br>
                        <br>
                        <span class="text-success"> @lang('This is the reason you should choose a plan with bigger referral commission').</span>
                    </h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="treeComInfoModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Commission to tree info')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class=" text-danger">@lang('When someone from your below tree subscribe this plan, You will get this amount as tree commission'). </h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

    <script>
        $('input[name=qty]').on('keyup change', function() {
            // alert('okl');
            $('input[name=total]').val($('input[name=qty]').val() * $('input[name=prices]').val());
        });
    </script>
    <script>
        $('input[name=qtyy]').on('keyup change', function() {
            // alert('okl');
            $('input[name=totall]').val($('input[name=qtyy]').val() * $('input[name=pricess]').val());
        });
    </script>
    {{-- @dd($sponsor) --}}
    @if ($sponsor)
        <script type="text/javascript">
            $(window).on('load', function() {
                $('#confBuyModal1').modal('show');
            });
        </script>
    @endif
    <script>
        (function($) {
            "use strict";

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

            $('.sponsorName').on('keyup', delay(function() {
                $('.sponsorName').addClass('is-invalid');
                var username = this.value;
                var usernameMe = "{{ auth()->user()->username }}";

                var token = "{{ csrf_token() }}";
                if (username == usernameMe) {
                    $('.textSponsorInfo').removeClass('d-none').removeClass(
                        'text-success').addClass('text-danger').html(
                        "Invalid Sponsor, you cant not reffer yourself");

                    $('.positionInpt').addClass('d-none');
                } else {
                    $.ajax({
                        type: "POST",

                        url: "{{ route('user.search.user') }}",
                        data: {
                            'username': username,
                            '_token': token
                        },
                        success: function(data) {
                            console.log(data);
                            if (data.success) {
                                $('.sponsorName').removeClass('is-invalid').addClass(
                                    'is-valid');

                                $('.btnSubmit').prop('disabled', false);
                                $('.textSponsorInfo').removeClass(
                                    'text-danger').addClass('text-success').removeClass(
                                    'd-none').html(
                                    "Referred By Sponsor: <b><i>`" +
                                username +
                                "`</i></b> Success!");
                                if (data.data) {
                                    $('.positionInpt').removeClass('d-none');
                                } else {
                                    $('.positionInpt').addClass('d-none');
                                }
                            } else {
                                $('.textSponsorInfo').removeClass('d-none').removeClass(
                                    'text-success').addClass('text-danger').html(
                                    "Referred By Sponsor: <b><i>`" +
                                username +
                                "`</i></b>. Not Found!");

                                $('.positionInpt').addClass('d-none');
                            }

                        },
                        error: function(data) {
                            // $("#position-test").html(data.msg);
                        }
                    });
                }

            }, 500));


            var bar =
                `<li><i class="fas fa-check bg--success me-3"></i>Validation Input</li>
                    <li><i class="fas fa-times bg--secondary"></i>Subscribed Plan</li>
                    <li><i class="fas fa-times bg--secondary"></i>Register New User</li>
                    <li><i class="fas fa-times bg--secondary"></i>Publish Data</li>`;

            var progress = 0;
            var progressBar = $('#progressBar');

            function updateProgress(percentage) {
                progress += percentage;
                progressBar.css('width', progress + '%').attr('aria-valuenow', progress).text(progress + '%');
            }

            function simulateProgress() {
                // Simulate validating data
                setTimeout(function() {
                    updateProgress(2); // 0.5 seconds
                }, 500);

                // Simulate subscribing plan
                setTimeout(function() {
                    updateProgress(3); // 0.5 seconds
                }, 1000);

                // Simulate creating user
                var userCount = 1;
                var createUserInterval = setInterval(function() {
                    updateProgress(1); // 0.3 seconds
                    if (userCount >= 5) {
                        clearInterval(createUserInterval);

                    }
                    userCount++;
                }, 200);
            }

            $('button[type="submit"]').on('click', function() {
                setTimeout(function() {
                    $('#bar').html(bar);
                }, 2000);
                var formModal = $('#formModal');
                var barModal = $('#barModal');
                $('#modalHeader').addClass('d-none');
                formModal.addClass('d-none');
                barModal.removeClass('d-none');

                var intervalId = window.setInterval(function() {
                    simulateProgress();

                    var ariaValueNow = $('#progressBar').attr('aria-valuenow');
                    if (ariaValueNow == 10) {
                        bar =
                            `<li><i class="fas fa-check bg--success me-3"></i>Validation Input</li>
                <li><i class="fas fa-check bg--success"></i>Subscribed Plan</li>
                <li><i class="fas fa-times bg--secondary"></i>Register New User</li>
                <li><i class="fas fa-times bg--secondary"></i>Publish Data</li>`;
                        $('#bar').html(bar);

                    }
                    if (ariaValueNow == 20) {
                        bar =
                            `<li><i class="fas fa-check bg--success me-3"></i>Validation Input</li>
                <li><i class="fas fa-check bg--success"></i>Subscribed Plan</li>
                <li><i class="fas fa-check bg--success"></i>Register New User</li>
                <li><i class="fas fa-times bg--secondary"></i>Publish Data</li>`;
                        $('#bar').html(bar);

                    }
                    if (ariaValueNow == 80) {
                        bar =
                            `<li><i class="fas fa-check bg--success me-3"></i>Validation Input</li>
                <li><i class="fas fa-check bg--success"></i>Subscribed Plan</li>
                <li><i class="fas fa-check bg--success"></i>Register New User</li>
                <li><i class="fas fa-check bg--success"></i>Publish Data</li>`;
                        $('#bar').html(bar);

                    }
                    if (ariaValueNow == 90) {
                        clearInterval(intervalId);
                    }

                }, 5000);
            });

            // Listen to submit button click


            // var oldPosition = '{{ old('position') }}';

            // if (oldPosition) {
            //     $('select[name=position]').removeAttr('disabled');
            //     $('#position').val(oldPosition);
            // }
            $('.package').on('change', function() {
                const pack = $(this).val();
                $('input[name=qty]').val(pack);
                $('input[name=total]').val(pack * $('input[name=prices]').val());
                // console.log(pack * harga);
            });
            var not_select_msg = $('#position-test').html();

            $(document).on('blur', '#ref_name', function() {
                var ref_id = $('#ref_name').val();
                var token = "{{ csrf_token() }}";
                $.ajax({
                    type: "POST",
                    url: "{{ route('check.referralbro') }}",
                    data: {
                        'ref_id': ref_id,
                        '_token': token
                    },
                    success: function(data) {
                        if (data.success) {
                            $('select[name=position]').removeAttr('disabled');
                            $('#position-test').text('');
                        } else {
                            $('select[name=position]').attr('disabled', true);
                            $('#position-test').html(not_select_msg);
                        }
                        $("#ref").html(data.msg);
                    }
                });
            });

            $(document).on('change', '#position', function() {
                updateHand();
            });

            function updateHand() {
                var pos = $('#position').val();
                var referrer_id = $('#referrer_id').val();
                var token = "{{ csrf_token() }}";
                $.ajax({
                    type: "POST",
                    url: "{{ route('get.user.position') }}",
                    data: {
                        'referrer': referrer_id,
                        'position': pos,
                        '_token': token
                    },
                    error: function(data) {
                        $("#position-test").html(data.msg);
                    }
                });
            }

            @if (@$country_code)
                $(`option[data-code={{ $country_code }}]`).attr('selected', '');
            @endif
            $('select[name=country_code]').change(function() {
                $('input[name=country]').val($('select[name=country_code] :selected').data('country'));
            }).change();

            function submitUserForm() {
                var response = grecaptcha.getResponse();
                if (response.length == 0) {
                    document.getElementById('g-recaptcha-error').innerHTML =
                        '<span style="color:red;">@lang('Captcha field is required.')</span>';
                    return false;
                }
                return true;
            }

            function verifyCaptcha() {
                document.getElementById('g-recaptcha-error').innerHTML = '';
            }

            @if ($general->secure_password)
                $('input[name=password]').on('input', function() {
                    var password = $(this).val();
                    var capital = /[ABCDEFGHIJKLMNOPQRSTUVWXYZ]/;
                    var capital = capital.test(password);
                    if (!capital) {
                        $('.capital').removeClass('text--success');
                    } else {
                        $('.capital').addClass('text--success');
                    }
                    var number = /[123456790]/;
                    var number = number.test(password);
                    if (!number) {
                        $('.number').removeClass('text--success');
                    } else {
                        $('.number').addClass('text--success');
                    }
                    var special = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
                    var special = special.test(password);
                    if (!special) {
                        $('.special').removeClass('text--success');
                    } else {
                        $('.special').addClass('text--success');
                    }

                });
            @endif


        })(jQuery);
    </script>
@endpush
