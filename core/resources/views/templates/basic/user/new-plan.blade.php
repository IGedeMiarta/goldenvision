@extends($activeTemplate . 'user.layouts.app')
@push('style')
    <style>
        .text-small {
            font-size: 14px;
        }

        .secondary {
            color: rgba(255, 255, 255, 0.509);
        }

        .icon-check {
            width: 20px;
            /* Adjust width and height as needed */
            height: 20px;
            border-radius: 50%;
            /* Makes the element round */
            background-color: #28C76F;
            /* Circle color */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 12px;
            /* Adjust font size as needed */
            color: white;
            font-weight: bold;
            /* Transparent text color */
            border: 2px solid #28C76F;
            /* Adjust border width and color as needed */
        }

        .custom-preview-info {
            background-color: #1F1F1F;
            border-radius: 10px;
            height: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 280px;
            margin-left: 1rem;
            margin-right: 1rem;
        }

        .custom-col-sm-3,
        .custom-col-md-3,
        .custom-col-xs-3 {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .custom-icon-check {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #28C76F;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 12px;
            color: white;
            font-weight: bold;
            border: 2px solid #28C76F;
        }

        .custom-col-sm-9,
        .custom-col-md-9,
        .custom-col-xs-9 {
            padding-left: 0.5rem;
        }

        .custom-text-white {
            margin-bottom: 0;
            color: white;
        }
    </style>
@endpush


@section('panel')
    <div class="row mb-30">
        @foreach ($plans as $data)
            <div class="col-sm-4 col-md-4">
                <div class="card "
                    style="background-color: #000000; border-radius: 15px; max-width: 400px; 
                    background-image: url('{{ asset('assets/figma/nav-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
                    ">
                    <div class="card-body  ">
                        <div class=" ml-1 text" style="display: flex; justify-content: space-between;">
                            <h5 class="text-warning" style="font-size: 18px">
                                {{ $data->name }}
                            </h5>
                            <i class="fas fa-star text-warning mr-1"></i>
                        </div>
                        <div class=" ml-1 text-price d-flex">
                            <h3 class="text-white" style="font-size: 24px">Rp {{ nb($data->price) }} <span
                                    class="text-small text-white">/
                                    PIN</span>
                            </h3>
                        </div>
                        <div class="ml-1 text-price-info d-flex mt-n4">
                            <h2 class="text-white"><span class="text-small secondary" style="font-size: 12px">Use Your
                                    {{ auth()->user()->pin }}
                                    PIN to
                                    Buy</span>
                            </h2>
                        </div>
                        <div class="custom-preview-info row ml-1 mr-1 mt-3"
                            style="background-color: #1F1F1F; border-radius: 10px; height: 80px; display: flex; justify-content: center; align-items: center; max-width: 380px;">
                            <div
                                class="custom-col-sm-3 custom-col-md-3 custom-col-xs-3 d-flex justify-content-end align-items-center">
                                <div class="custom-icon-check mr-2">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <div class="custom-col-sm-9 custom-col-md-9 custom-col-xs-9">
                                <p class="text-warning " style="font-size: 14px">Refferal Commission</p>
                                <h4 class="mt-n1 custom-text-white" style="font-weight: bolder">Rp {{ nb($data->ref_com) }}
                                </h4>
                            </div>
                        </div>

                        @if (Auth::user()->plan_id != $data->id)
                            <a href="#confBuyModal{{ $data->id }}" data-toggle="modal"
                                style="background-color: #008C4F;"
                                class="btn w-100 btn-sm  text-light  mt-20 py-2 @if (auth()->user()->pin < 1) disabled @endif">@lang('Subscribe')</a>
                        @else
                            <a data-toggle="modal" class="btn w-100 btn-sm mt-20 py-2 text-white"
                                style="background-color: #008C4F">@lang('Already Subscribe')</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal fade" id="confBuyModal{{ $data->id }}" class="modalPlan" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="modal" data-backdrop="static"
                data-keyboard="false">
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
                                        <select name="package" id="gold" class="package form-control form--control-2">
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
                                    <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;"
                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
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
        @endforeach
    </div>
@endsection
@push('script')
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
