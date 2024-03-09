@extends($activeTemplate . 'user.layouts.app')

@push('style')
    <link href="{{ asset('assets/admin/css/tree.css') }}" rel="stylesheet">
    <style>
        .progress {
            height: 30px;
        }
    </style>
@endpush

@section('panel')
    <form action="{{ route('user.sponsorRegist.post.up') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-header">
                <h3>Upliners</h3>
            </div>
            <div class="card-footer">
                <div class="form-group row">
                    <label for="sponsor" class="col-sm-2 col-form-label">Sponsor</label>
                    <div class="input-group col-sm-10">
                        <input type="text" class="form-control col-md-12" id="sponsor" name="sponsor"
                            placeholder="Sponsor" value="{{ $user->username }}" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="sponsor" class="col-sm-2 col-form-label">Upline</label>
                    <div class="input-group col-sm-10">
                        <input type="text" class="form-control col-md-12" id="upline" name="upline"
                            placeholder="upline" value="{{ session()->get('SponsorSet')['upline'] ?? '' }}" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="sponsor" class="col-sm-2 col-form-label">Placement</label>
                    <div class="col-sm-10">
                        <input type="hidden" name="position" id=""
                            value="{{ session()->get('SponsorSet')['position'] }}">
                        <select name="" id="position" class="form-control" disabled>
                            <option value="1" {{ session()->get('SponsorSet')['position'] == 1 ? 'selected' : '' }}>
                                Kiri</option>
                            <option value="2"{{ session()->get('SponsorSet')['position'] == 2 ? 'selected' : '' }}>
                                Kanan</option>
                        </select>
                    </div>
                </div>
                <div class="for-group row">
                    <label for="phone" class="col-sm-2 col-form-label">Registered User
                        <br>
                        <span class="text-sm  {{ auth()->user()->pin < 1 ? 'text-danger' : 'text-primary' }}">
                            {{ auth()->user()->pin < 1 ? 'You Have No PIN' : 'You Have ' . auth()->user()->pin . ' PIN' }}
                        </span>
                    </label>
                    <div class="col-sm-10">
                        <select name="pin" id=""
                            class="form-control {{ $errors->has('pin') ? 'is-invalid' : '' }}">
                            <option>{{ auth()->user()->pin < 1 ? 'You Have No Pin' : 'Select' }}</option>
                            <option value="1" {{ auth()->user()->pin < 1 ? 'disabled' : '' }}
                                {{ old('pin') == 1 ? 'selected' : '' }} {{ old('pin') == 1 ? 'selected' : '' }}>1 ID
                            </option>
                            <option value="3"
                                {{ auth()->user()->pin < 3 ? 'disabled' : '' }}{{ old('pin') == 3 ? 'selected' : '' }}>
                                3 ID
                            </option>
                            <option value="5"
                                {{ auth()->user()->pin < 5 ? 'disabled' : '' }}{{ old('pin') == 5 ? 'selected' : '' }}>
                                5
                                ID
                            </option>
                        </select>
                        @error('pin')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <hr>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header">
                <h3>Registered New Users</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">


                    <div class="row justify-content-center">

                        <div class="form-group col-md-5 mr-2">
                            <label>@lang('First Name')</label>
                            <input type="text" class="form-control {{ $errors->has('firstname') ? 'is-invalid' : '' }}"
                                placeholder="@lang('First Name')" name="firstname" autocomplete="off"
                                value="{{ old('firstname') }}">
                            @error('firstname')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-5">
                            <label>@lang('Last Name')</label>
                            <input type="text" class="form-control {{ $errors->has('lastname') ? 'is-invalid' : '' }}"
                                placeholder="@lang('Last Name')" name="lastname" autocomplete="off"
                                value="{{ old('lastname') }}">
                            @error('lastname')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-10 mt-3">
                            <label>@lang('Email')</label>
                            <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                placeholder="Email" name="email" value="{{ old('email') }}" autocomplete="off">
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group col-md-10 mt-3">
                            <label>@lang('Email Dinaran')</label>
                            <input type="email"
                                class="form-control {{ $errors->has('email_dinaran') ? 'is-invalid' : '' }}"
                                placeholder="Email" name="email_dinaran" value="{{ old('email_dinaran') }}"
                                autocomplete="off">
                            @error('email_dinaran')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mt-3 col-md-10">
                            <label>@lang('Username')</label>
                            <input type="text" class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}"
                                placeholder="@lang('Username')" autocomplete="off" value="{{ old('username') }}"
                                name="username">
                            @error('username')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mt-3 col-md-10">
                            <label>@lang('Phone Number')</label>
                            <div class="row m-0">
                                <div class="col-3 m-0 p-0">
                                    <select class="form-control" name="country_code">

                                        @include('partials.country_code')
                                    </select>
                                </div>
                                <div class="col-9 m-0 p-0">
                                    <input type="text"
                                        class="form-control {{ $errors->has('mobile') ? 'is-invalid' : '' }}"
                                        placeholder="@lang('Phone Number')" autocomplete="off" name="mobile"
                                        value="{{ old('mobile') }}">
                                    @error('mobile')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3 col-md-5 mr-2">
                            <label>@lang('Password')</label>
                            <input type="password"
                                class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                placeholder="@lang('Password')" autocomplete="off" name="password">
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mt-3 col-md-5">
                            <label>@lang('Confirm Password')</label>
                            <input type="password"
                                class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                                placeholder="@lang('Confirm Password')" autocomplete="off" name="password_confirmation">
                            @error('password_confirmation')
                                <small class="text-danger">{{ $message }}</small>
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
                    </div> {{-- end div row --}}

                </div>
            </div>
            <div class="card-footer text-center">
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success btn-lg btn-block"
                            {{ auth()->user()->pin < 1 ? 'disabled' : '' }}><i class="fa fa-save"></i>
                            Submit</button>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ session()->get('SponsorSet')['url'] ?? '' }}"
                            class="btn btn-warning btn-lg btn-block"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade" id="confBuyModal" class="modalPlan" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="modal" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="modalHeader">

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
                                <li><i class="fas fa-times bg--secondary"></i>Valiadate Input</li>
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
@endsection
@push('script')
    <script>
        (function($) {
            "use strict";
            var bar =
                `<li><i class="fas fa-check bg--success me-3"></i>Valiadate Input</li>
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
                $('#confBuyModal').modal('show');
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
                            `<li><i class="fas fa-check bg--success me-3"></i>Valiadate Input</li>
                    <li><i class="fas fa-check bg--success"></i>Subscribed Plan</li>
                    <li><i class="fas fa-times bg--secondary"></i>Register New User</li>
                    <li><i class="fas fa-times bg--secondary"></i>Publish Data</li>`;
                        $('#bar').html(bar);

                    }
                    if (ariaValueNow == 20) {
                        bar =
                            `<li><i class="fas fa-check bg--success me-3"></i>Valiadate Input</li>
                    <li><i class="fas fa-check bg--success"></i>Subscribed Plan</li>
                    <li><i class="fas fa-check bg--success"></i>Register New User</li>
                    <li><i class="fas fa-times bg--secondary"></i>Publish Data</li>`;
                        $('#bar').html(bar);

                    }
                    if (ariaValueNow == 80) {
                        bar =
                            `<li><i class="fas fa-check bg--success me-3"></i>Valiadate Input</li>
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

        })(jQuery);
    </script>
@endpush
