@extends('templates/basic/layouts/landing', [
    'title' => 'Register',
    'bodyClass' => 'register-bg',
])

@section('css')
    <style>
        .register-bg {
            /* background: url('/assets/landing/img/register-bg.jpg') no-repeat center fixed; */
            background-size: cover;
            object-fit: contain;
        }

        .is-valid {
            border-color: #28a745 !important;
            /* Green border color */
        }

        .is-invalid {
            border-color: #dc3545 !important;
            /* Red border color */
        }
    </style>
@endsection

@section('content')
    <div class="section">
        <div class="section-1400 padding-top-bottom-120">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-8">
                        <div
                            class="section py-4 py-md-5 px-3 px-sm-4 px-lg-5 over-hide border-4 section-shadow-blue bg-white section-background-24 background-img-top form">
                            <form class="section" method="post" action="{{ route('user.register') }}">
                                @csrf

                                <h4 class="mb-4 text-sm-center">
                                    <img src="{{ asset('assets/nav-logo.png') }}" alt=""
                                        style="max-width: 300px;-webkit-filter: drop-shadow(5px 5px 5px #222);
  filter: drop-shadow(5px 5px 5px #222);">
                                    <br>
                                    <br>
                                    Register now.
                                </h4>
                                <div class="row justify-content-center">
                                    <div class="form-group col-md-5 mr-2">
                                        <label>@lang('First Name')</label>
                                        <input type="text"
                                            class="form-style {{ $errors->has('firstname') ? 'is-invalid' : '' }}"
                                            placeholder="@lang('First Name')" name="firstname" autocomplete="off"
                                            value="{{ old('firstname') }}">
                                        @error('firstname')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>@lang('Last Name')</label>
                                        <input type="text"
                                            class="form-style {{ $errors->has('lastname') ? 'is-invalid' : '' }}"
                                            placeholder="@lang('Last Name')" name="lastname" autocomplete="off"
                                            value="{{ old('lastname') }}">
                                        @error('lastname')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-10 mt-3">
                                        <label>@lang('Email')</label>
                                        <input type="text"
                                            class="form-style {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                            placeholder="Email" name="email" value="{{ old('email') }}"
                                            autocomplete="off">
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-10 mt-3">
                                        <label>@lang('Email Dinaran')</label>
                                        <input type="text"
                                            class="form-style {{ $errors->has('email_dinaran') ? 'is-invalid' : '' }}"
                                            placeholder="Email Dinaran" name="email_dinaran"
                                            value="{{ old('email_dinaran') }}" autocomplete="off">
                                        @error('email_dinaran')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-3 col-md-10">
                                        <label>@lang('Username')</label>
                                        <input type="text"
                                            class="form-style {{ $errors->has('username') ? 'is-invalid' : '' }}"
                                            placeholder="@lang('Username')" autocomplete="off"
                                            value="{{ old('username') }}" name="username">
                                        @error('username')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-3 col-md-10">
                                        <label>@lang('Phone Number')</label>
                                        <div class="row m-0">
                                            <div class="col-3 m-0 p-0">
                                                <select class="form-style" name="country_code">

                                                    @include('partials.country_code')
                                                </select>
                                            </div>
                                            <div class="col-9 m-0 p-0">
                                                <input type="text"
                                                    class="form-style {{ $errors->has('mobile') ? 'is-invalid' : '' }}"
                                                    placeholder="@lang('Phone Number')" autocomplete="off" name="mobile"
                                                    value="{{ old('mobile') }}">
                                                @error('mobile')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3 col-md-10">
                                        <label>@lang('Country')</label>
                                        <input type="text"
                                            class="form-style {{ $errors->has('country') ? 'is-invalid' : '' }}"
                                            name="country" placeholder="@lang('Country')" readonly autocomplete="off"
                                            value="{{ old('country') }}">
                                        @error('country')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3 col-md-5 mr-2">
                                        <label>@lang('Password')</label>
                                        <input type="password"
                                            class="form-style {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                            placeholder="@lang('Password')" autocomplete="off" name="password">
                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3 col-md-5">
                                        <label>@lang('Confirm Password')</label>
                                        <input type="password"
                                            class="form-style {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                                            placeholder="@lang('Confirm Password')" autocomplete="off"
                                            name="password_confirmation">
                                        @error('password_confirmation')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3 col-md-10">
                                        <label>@lang('Referrals')</label>
                                        <input type="text"
                                            class="form-style {{ $errors->has('referrals') ? 'is-invalid' : '' }} {{ isset($_GET['ref']) ? 'is-valid' : '' }}"
                                            placeholder="@lang('Referrals username')" autocomplete="off" id="referrals"
                                            value="{{ $_GET['ref'] ?? old('referrals') }}" name="referrals"
                                            {{ isset($_GET['ref']) ? 'readonly' : '' }}>
                                        <small class="refInfo d-none"></small>
                                        <br>
                                        @error('referrals')
                                            <small id="refError" class="text-danger">{{ $message }}</small>
                                        @enderror

                                    </div>

                                    <div class="col-lg-12">
                                        @include($activeTemplate . 'partials.custom-captcha')
                                    </div>
                                    @if ($general->agree_policy)
                                        <div class="form-group mt-3 col-md-10">
                                            <input type="checkbox" id="checkbox-1" name="agree">
                                            <label class="checkbox mb-0 font-weight-500 size-15" for="checkbox-1">
                                                I accept the <a href="#" class="link link-dark-primary"
                                                    data-hover="Terms and Conditions">Terms and
                                                    Conditions</a> and <a href="#" class="link link-dark-primary"
                                                    data-hover="Privacy Policy">Privacy Policy</a>
                                            </label>
                                            <br />
                                            @error('agree')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    @endif

                                </div>
                                <div class="row mt-4">
                                    <div class="col-12 text-sm-center">
                                        <button type="submit" class="btn btn-dark-primary">Register<i
                                                class="uil uil-arrow-right size-22 ml-3"></i></button>
                                    </div>
                                </div>
                                <p class="mt-4 mb-0 text-sm-center size-16">
                                    Already have an account?
                                    <a href="{{ route('user.login') }}"
                                        class="link link-dark-primary-2 link-normal animsition-link">
                                        Sign In
                                    </a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    @if (isset($_GET['ref']))
        <script>
            $(document).ready(function() {
                getvalidation();

                function getvalidation() {
                    var username = "{{ $_GET['ref'] }}";
                    $.ajax({
                        url: "{{ url('/api/check-user') }}/" + username,
                        type: 'GET',
                        success: function(response) {
                            var info = $('.refInfo');
                            var input = $('#referrals');
                            if (response.status) {
                                var msg = 'Referrals Insert: `' + response.username + '`  | ' + response
                                    .fullname;
                                info.removeClass('d-none').addClass('text-success').text(msg);
                                input.addClass('is-valid')
                                $('.refError').addClass('d-none');
                            } else {
                                info.removeClass('d-none').addClass('text-danger').text(
                                    'User Not Found');
                                input.addClass('is-invalid')


                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            $('#userStatus').text('Error occurred while checking user.');
                        }
                    });
                }
            })
        </script>
    @endif
    <script>
        $('select[name=country_code]').change(function() {
            $('input[name=country]').val($('select[name=country_code] :selected').data('country'));
        }).change();

        @if ($errors->any())
            $('#modal-login-register').modal();

            @if ($form = session('form'))
                @if ($form == 'register')
                    $('#pills-login-tab').removeClass('active');
                    $('#pills-register-tab').addClass('active');

                    $('#pills-login').removeClass('active show');
                    $('#pills-register').addClass('active show');
                @elseif ($form == 'register')
                    $('#pills-login-tab').addClass('active');
                    $('#pills-register-tab').removeClass('active');

                    $('#pills-login').addClass('active show');
                    $('#pills-register').removeClass('active show');
                @endif
            @endif
        @endif


        $(document).ready(function() {
            var typingTimer;
            var doneTypingInterval = 1000; // 1 second

            $('#referrals').on('keyup', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(doneTyping, doneTypingInterval);
            });

            $('#referrals').on('keydown', function() {
                clearTimeout(typingTimer);
            });

            function doneTyping() {
                var value = $('#referrals').val();
                if (value.trim() !== '') {
                    $.ajax({
                        url: "{{ url('/api/check-user') }}/" + value,
                        type: 'GET',
                        success: function(response) {
                            var info = $('.refInfo');
                            var input = $('#referrals');
                            if (response.status) {
                                clearInput();
                                var msg = 'Akun found: `' + response.username + '`  | ' + response
                                    .fullname;
                                info.removeClass('d-none').addClass('text-success').text(msg);
                                input.addClass('is-valid')
                            } else {
                                clearInput();
                                info.removeClass('d-none').addClass('text-danger').text(
                                    'User Not Found');
                                input.addClass('is-invalid')

                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            $('#userStatus').text('Error occurred while checking user.');
                        }
                    });
                } else {
                    $('#userStatus').text('Please enter a username.');
                }
            }

            function clearInput() {
                var info = $('.refInfo');
                var input = $('#referrals');

                info.removeClass('text-success').removeClass('text-danger');
                input.removeClass('is-valid').removeClass('is-invalid');
            }
        });
    </script>
@endsection
