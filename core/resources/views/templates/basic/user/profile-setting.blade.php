@extends($activeTemplate . 'user.layouts.app')

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">

    <style>
        :root {
            --primary-color: rgb(11, 78, 179)
        }

        .bg-grand-gold {
            background: linear-gradient(90deg, rgba(194, 102, 31, 1) 14%, rgba(255, 192, 0, 1) 100%);
        }

        .txt-grand-gold {
            background: -webkit-linear-gradient(rgba(194, 102, 31, 1) 14%, rgba(255, 192, 0, 1) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        label {
            display: block;
            margin-bottom: 0.5rem
        }

        input {
            display: block;
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 0.25rem;
            height: 50px
        }

        .width-50 {
            width: 50%
        }

        .ml-auto {
            margin-left: auto
        }

        .text-center {
            text-align: center
        }

        .progressbar {
            position: relative;
            display: flex;
            justify-content: space-between;
            counter-reset: step;
            margin: 2rem 2rem 4rem
        }

        .progressbar::before,
        .progress {
            content: "";
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            height: 4px;
            width: 100%;
            background-color: #dcdcdc;
            z-index: 1
        }

        .progress {
            background-color: rgb(0 128 0);
            width: 0%;
            transition: 0.3s
        }

        .progress-step {
            width: 2.1875rem;
            height: 2.1875rem;
            background-color: #dcdcdc;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1
        }

        .progress-step::before {
            counter-increment: step;
            content: counter(step)
                /* font-family: FontAwesome; */
                /* content: "\f023"; */

        }

        .progress-step::after {
            content: attr(data-title);
            position: absolute;
            top: calc(100% + 0.5rem);
            font-size: 0.85rem;
            color: #666;
            width: 80px;
        }

        .progress-step-active {
            background-color: var(--primary-color);
            color: #f3f3f3
        }

        .form {
            width: clamp(320px, 30%, 430px);
            margin: 0 auto;
            border: none;
            border-radius: 10px !important;
            overflow: hidden;
            padding: 1.5rem;
            background-color: #fff;
            padding: 20px 30px
        }

        .step-forms {
            display: none;
            transform-origin: top;
            animation: animate 1s
        }

        .step-forms-active {
            display: block
        }

        .group-inputs {
            margin: 1rem 0
        }

        .animated-progress {
            width: 600px;
            height: 30px;
            border-radius: 5px;
            margin: 20px 10px;
            border: 1px solid rgb(8, 37, 201);
            overflow: hidden;
            position: relative;
        }

        .animated-progress span {
            height: 100%;
            display: block;
            width: 0;
            color: rgb(255, 251, 251);
            line-height: 30px;
            position: absolute;
            text-align: end;
            padding-right: 5px;
        }

        .progress-blue span {
            background-color: blue;
        }

        .progress-green span {
            background-color: green;
        }

        .progress-purple span {
            background-color: indigo;
        }

        .progress-red span {
            background-color: red;
        }

        @keyframes animate {
            from {
                transform: scale(1, 0);
                opacity: 0
            }

            to {
                transform: scale(1, 1);
                opacity: 1
            }
        }

        .btns-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem
        }

        .progress-step-check {
            position: relative;
            background-color: green !important;
            transition: all 0.8s
        }

        .progress-step-check::before {
            position: absolute;
            content: '\2713';
            width: 100%;
            height: 100%;
            top: 8px;
            left: 13px;
            font-size: 12px
        }

        .group-inputs {
            position: relative
        }

        .group-inputs label {
            font-size: 13px;
            position: absolute;
            height: 19px;
            padding: 4px 7px;
            top: -14px;
            left: 10px;
            color: #a2a2a2;
            background-color: white
        }

        .welcome {
            height: 450px;
            width: 350px;
            background-color: #fff;
            border-radius: 6px;
            display: flex;
            justify-content: center;
            align-items: center
        }

        .welcome .content {
            display: flex;
            align-items: center;
            flex-direction: column
        }

        .checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke-miterlimit: 10;
            stroke: #7ac142;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards
        }

        .checkmark {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: block;
            stroke-width: 2;
            stroke: #fff;
            stroke-miterlimit: 10;
            margin: 10% auto;
            box-shadow: inset 0px 0px 0px #7ac142;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both
        }

        .checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards
        }

        .cards-wrapper {
            display: flex;
        }

        .cardSlide {
            margin: 0 0.5em;
            width: calc(100%/3);
        }

        /* userInfo */

        .profile {
            width: 330px;
            height: 100px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 5px;
            background-color: #fafafa;
            box-shadow: 0 0 2rem #babbbc;
            animation: show-profile 0.5s forwards ease-in-out;
        }

        /* @keyframes show-profile {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        0% {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            width: 0;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    } */

        .profile .photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid #fafafa;
            margin-left: -50px;
            box-shadow: 0 0 0.5rem #babbbc;
            animation: rotate-photo 0.5s forwards ease-in-out;
        }

        /* @keyframes rotate-photo {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            0% {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                transform: rotate(0deg);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            100% {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                transform: rotate(-360deg);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } */

        .profile .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile .content {
            padding: 10px;
            overflow: hidden;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }

        .profile .content .text {
            margin-top: 20px;
            margin-left: 50px;
        }

        .profile .content .text h3,
        .profile .content .text h6 {
            font-weight: normal;
            margin: 3px 0;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .profile .content .btn {
            background-color: #70a1ff;
            width: 50px;
            height: 50px;
            position: absolute;
            right: 25px;
            top: 25px;
            border-radius: 50%;
            z-index: 1;
            cursor: pointer;
            transition-duration: 0.3s;
            animation: pop-btn 0.3s both ease-in-out 0.5s;
        }

        .shing {
            &::before {
                animation: shine #{$anim-duration}s ease-in-out infinite;
            }
        }

        @media (max-width: 768px) {
            .carousel-inner .carousel-item>div {
                display: none;
            }

            .carousel-inner .carousel-item>div:first-child {
                display: block;
            }
        }

        .carousel-inner .carousel-item.active,
        .carousel-inner .carousel-item-next,
        .carousel-inner .carousel-item-prev {
            display: flex;
        }

        .imgUser {
            height: 10rem;
            width: 10rem;
            background-color: #fff;
        }

        .cardImages {
            height: 15rem;
            width: 16rem;
            background-color: #141214;
        }

        .cardImages.masterGold {
            background-image: url("{{ asset('assets/assets/badges/title-mg.jpeg') }}");
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }

        .cardImages.grandMaster {
            background-image: url("{{ asset('assets/assets/badges/title-gmg.jpeg') }}");
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }

        /* display 3 */
        @media (min-width: 768px) {

            .carousel-inner .carousel-item-right.active,
            .carousel-inner .carousel-item-next {
                transform: translateX(33.333%);
            }

            .carousel-inner .carousel-item-left.active,
            .carousel-inner .carousel-item-prev {
                transform: translateX(-33.333%);
            }
        }

        .carousel-inner .carousel-item-right,
        .carousel-inner .carousel-item-left {
            transform: translateX(0);
        }


        @keyframes shine {
            0% {
                left: -100%;
                transition-property: left;
            }

            #{($anim-speed / ($anim-duration + $anim-speed) * 100%)},
            100% {
                left: 100%;
                transition-property: left;
            }
        }


        @keyframes stroke {
            100% {
                stroke-dashoffset: 0
            }
        }

        @keyframes scale {

            0%,
            0% {
                transform: none
            }

            50% {
                transform: scale3d(1.1, 1.1, 1)
            }
        }

        @keyframes fill {
            100% {
                box-shadow: inset 0px 0px 0px 30px #7ac142
            }
        }
    </style>
    <style>
        .custom-btn {
            width: 130px;
            height: 40px;
            color: #fff;
            border-radius: 5px;
            padding: 10px 25px;
            font-family: 'Lato', sans-serif;
            font-weight: 500;
            background: transparent;
            /* cursor: pointer; */
            transition: all 0.3s ease;
            position: relative;
            display: inline-block;
            box-shadow: inset 2px 2px 2px 0px rgba(255, 255, 255, .5),
                7px 7px 20px 0px rgba(0, 0, 0, .1),
                4px 4px 5px 0px rgba(0, 0, 0, .1);
            outline: none;
        }

        /* 11 */
        .btn-11 {
            display: inline-block;
            outline: none;
            font-family: inherit;
            font-size: 1em;
            box-sizing: border-box;
            border: none;
            border-radius: .3em;
            height: 2.75em;
            line-height: 2.5em;
            text-transform: uppercase;
            padding: 0 1em;
            box-shadow: 0 3px 6px rgba(0, 0, 0, .16), 0 3px 6px rgba(110, 80, 20, .4),
                inset 0 -2px 5px 1px rgba(139, 66, 8, 1),
                inset 0 -1px 1px 3px rgba(250, 227, 133, 1);
            background-image: linear-gradient(160deg, #a54e07, #b47e11, #fef1a2, #bc881b, #a54e07);
            border: 1px solid #a55d07;
            color: rgb(120, 50, 5);
            text-shadow: 0 2px 2px rgba(250, 227, 133, 1);
            /* cursor: pointer; */
            transition: all .2s ease-in-out;
            background-size: 100% 100%;
            background-position: center;
            overflow: hidden;
        }

        /* .btn-11:hover {
                                                                                                                                                                                                                                                                                                                                                                                                                                        text-decoration: none;
                                                                                                                                                                                                                                                                                                                                                                                                                                        color: #fff;
                                                                                                                                                                                                                                                                                                                                                                                                                                    } */
        .btn-11:before {
            position: absolute;
            content: '';
            display: inline-block;
            top: -180px;
            left: 0;
            width: 30px;
            height: 100%;
            background-color: #fff;
            animation: shiny-btn1 3s ease-in-out infinite;
        }

        /* .btn-11:hover{
                                                                                                                                                                                                                                                                                                                                                                                                                                      opacity: .7;
                                                                                                                                                                                                                                                                                                                                                                                                                                    } */
        /* .btn-11:active{
                                                                                                                                                                                                                                                                                                                                                                                                                                      box-shadow:  4px 4px 6px 0 rgba(255,255,255,.3),
                                                                                                                                                                                                                                                                                                                                                                                                                                                  -4px -4px 6px 0 rgba(116, 125, 136, .2),
                                                                                                                                                                                                                                                                                                                                                                                                                                        inset -4px -4px 6px 0 rgba(255,255,255,.2),
                                                                                                                                                                                                                                                                                                                                                                                                                                        inset 4px 4px 6px 0 rgba(0, 0, 0, .2);
                                                                                                                                                                                                                                                                                                                                                                                                                                    } */


        @-webkit-keyframes shiny-btn1 {
            0% {
                -webkit-transform: scale(0) rotate(45deg);
                opacity: 0;
            }

            80% {
                -webkit-transform: scale(0) rotate(45deg);
                opacity: 0.5;
            }

            81% {
                -webkit-transform: scale(4) rotate(45deg);
                opacity: 1;
            }

            100% {
                -webkit-transform: scale(50) rotate(45deg);
                opacity: 0;
            }
        }
    </style>
@endpush

@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-5 col-md-5">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body p-0">
                    <div class="p-3 bg--white">
                        <img id="output"
                            src="{{ getImage('assets/images/user/profile/' . auth()->user()->image, null, true) }}"
                            alt="@lang('profile-image')" class="b-radius--10 w-100">


                        <ul class="list-group mt-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Name')</span> {{ auth()->user()->fullname }}
                            </li>
                            <li class="list-group-item rounded-0 d-flex justify-content-between">
                                <span>@lang('Username')</span> {{ auth()->user()->username }}
                            </li>

                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Joined at')</span>
                                {{ date(
                                    'd M, Y h:i
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            A',
                                    strtotime(auth()->user()->created_at),
                                ) }}
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Account Status</span>
                                @switch(auth()->user()->is_kyc)
                                    @case(0)
                                        <span class="badge badge-pill bg--danger">Unverified</span>
                                    @break

                                    @case(1)
                                        <span class="badge badge-pill bg--warning">On Process Verification</span>
                                    @break

                                    @case(2)
                                        <span class="badge badge-pill bg--success">Verified</span>
                                    @break

                                    @case(3)
                                        <span class="badge badge-pill bg--danger">Rejected</span>
                                    @break
                                @endswitch
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-7 col-md-7 mb-30">

            <div class="card card-header-actions mb-3">
                <div class="card-header" style="font-weight: 600;">
                    Account Verification
                </div>
                <div class="card-body text-center">
                    <div class="progressbar">
                        <div class="progress" id="progress"></div>
                        <div class="progress-step progress-step-active" data-title="Unverified"></div>
                        <div class="progress-step progress-step-active" data-title="Verification"></div>
                        <div class="progress-step progress-step-active bg-success" data-title="Verified"></div>
                    </div>
                    <p>Your data has been successfully verified.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-50 border-bottom pb-2">@lang('User Information')</h5>

                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div style="display: flex; align-items: center; margin-top: -10px; margin-bottom: 5px;">
                            <p style="font-size: 14px; margin-right: 5px; color: rgb(72, 71, 71)">Account Section</p>
                            <hr style="flex: 1; margin: 2; color: black">
                        </div>
                        <div class="row ml-3">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('First Name') <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-lg" type="text" name="firstname"
                                        value="{{ auth()->user()->firstname }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Last Name') <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-lg" type="text" name="lastname"
                                        value="{{ auth()->user()->lastname }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row ml-3">
                            @if (auth()->user()->plan_id != 0)
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="form-control-label font-weight-bold">@lang('Referrals')<span
                                                class="text-danger">*</span></label>
                                        <input class="form-control form-control-lg" type="text"
                                            value="{{ auth()->user()->username }}" readonly>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Email')<span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-lg" type="email"
                                        value="{{ auth()->user()->email }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Mobile Number')<span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-lg" type="text"
                                        value="{{ auth()->user()->mobile }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Avatar')</label>
                                    <input class="form-control form-control-lg" type="file" accept="image/*"
                                        onchange="loadFile(event)" name="image">
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; margin-top: 15px; margin-bottom: 5px;">
                            <p style="font-size: 14px; margin-right: 5px; color: rgb(72, 71, 71)">Address Section<span
                                    class="text-danger">*</span></p>
                            <hr style="flex: 1; margin: 2; color: black">
                        </div>
                        <div class="row ml-3">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Provinsi')<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="provinsi" id="provinsi" required>
                                        <option>--Pilih Provinsi </option>
                                        @foreach ($provinsi as $item)
                                            <option value="{{ $item->id ?? '' }}"
                                                {{ isset(auth()->user()->address->prov_code) && auth()->user()->address->prov_code == $item->id ? 'selected' : '' }}>
                                                {{ $item->name ?? '' }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Kabupaten / Kota')<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="kota" id="kota" required>
                                        @if (isset(auth()->user()->address->kota))
                                            <option>{{ auth()->user()->address->kota }}</option>
                                        @else
                                            <option>--Pilih Kabupaten/Kota</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Kecamatan')<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="kecamatan" id="kecamatan" required>
                                        @if (isset(auth()->user()->address->kec))
                                            <option>{{ auth()->user()->address->kec }}</option>
                                        @else
                                            <option>--Pilih Kecamatan</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Desa')<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="desa" id="desa" required>
                                        @if (isset(auth()->user()->address->desa))
                                            <option>{{ auth()->user()->address->desa }}</option>
                                        @else
                                            <option>--Pilih Desa</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Kode Pos')</label>
                                    <input class="form-control " type="text" name="pos"
                                        value="{{ auth()->user()->address->zip }}">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Negara')</label>
                                    <select name="negara" class="form-control ">
                                        @include('partials.country') </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Alamat') </label>
                                    <textarea class="form-control form-control-lg" type="text" name="alamat">{{ auth()->user()->address->address }}</textarea>
                                    <small class="form-text text-muted"><i
                                            class="las la-info-circle"></i>@lang(' Alamat Lengkap Rumah')
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; margin-top: 15px; margin-bottom: 5px;">
                            <p style="font-size: 14px; margin-right: 5px; color: rgb(72, 71, 71)">Dinaran Section<span
                                    class="text-danger">*</span></p>
                            <hr style="flex: 1; margin: 2; color: black">
                        </div>
                        <div class="row ml-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Dinaran Email')<i
                                            class="text-danger">*</i> </label>
                                    <input class="form-control form-control-lg" type="email" name="email_dinaran"
                                        value="{{ auth()->user()->email_dinaran }}">
                                    <small class="form-text text-muted"><i
                                            class="las la-info-circle"></i>@lang('Pastikan Email Dinaran anda sudah benar sebelum melakukan penarikan ')
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit"
                                        class="btn btn--primary btn-block btn-lg">@lang('Save
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            Changes')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- <div class="col-xl-9 col-lg-7 col-md-7 mb-30">
            <div class="card">
                <div class="card-header">
                    @lang('Bank Account Information')
                </div>
                <div class="card-body">

                    @if (!auth()->user()->bank_up)
                        <div class="alert alert-warning alert-dismissible fade show p-3" role="alert">
                            <strong>Hey {{ Auth::user()->fullname }}!</strong>Kamu tidak dapat lagi merubah account
                            bank. jika ada perubahan harap laporkan ke admin untuk penyesuaian.
                        </div>
                    @endif
                    @if ($bank_user)
                        <form action="{{ route('user.edit_rekening') }}" method="POST" enctype="multipart/form-data"
                            id="edit">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="form-control-label font-weight-bold">@lang('Bank Name') <span
                                                class="text-danger">*</span></label>
                                        <select name="bank_name" id="bank_name" class="form-control form-control-lg">
                                            @foreach ($bank as $item)
                                                <option value="{{ $item->nama_bank }}"
                                                    {{ auth()->user()->userBank->nama_bank == $item->nama_bank ? 'selected' : '' }}>
                                                    {{ $item->nama_bank }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Bank Branch City')
                                            <small>(Optional)</small></label>
                                        <input class="form-control form-control-lg" type="text" name="kota_cabang"
                                            value="{{ auth()->user()->userBank->kota_cabang }}"
                                            placeholder="Bank KCP Jakarta">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Bank Account Name') <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control form-control-lg" type="text" name="acc_name"
                                            value="{{ auth()->user()->userBank->nama_akun }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Bank Account Number') <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control form-control-lg" type="text" name="acc_number"
                                            value="{{ auth()->user()->userBank->no_rek }}" required>
                                    </div>
                                </div>
                            </div>

                            @if (auth()->user()->bank_up)
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit"
                                                class="btn btn--primary btn-block btn-lg">@lang('Update
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        Changes')</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </form>
                    @else
                        <form action="{{ route('user.add_rekening') }}" method="POST" enctype="multipart/form-data"
                            id="add">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="form-control-label font-weight-bold">@lang('Bank Name') <span
                                                class="text-danger">*</span></label>
                                        <select name="bank_name" id="bank_name" class="form-control form-control-lg"
                                            required>
                                            <option value="" hidden selected>-- Pilih Bank --</option>
                                            @foreach ($bank as $item)
                                                <option value="{{ $item->nama_bank }}">{{ $item->nama_bank }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Bank Branch City')
                                            <small>(Optional)</small></label>
                                        <input class="form-control form-control-lg" type="text" name="kota_cabang"
                                            value="" placeholder="Bank KCP Jakarta">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Account Name') <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control form-control-lg" type="text" name="acc_name"
                                            value="" required placeholder="Account Name">
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Account Number') <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control form-control-lg" type="text"
                                            placeholder="Account Number" name="acc_number" value="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit"
                                            class="btn btn--primary btn-block btn-lg">@lang('Save
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            Changes')</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif


                </div>
            </div>
        </div> --}}


        {{-- <div class="col-xl-9 col-lg-7 col-md-7 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                            <h5 class="card-title mb-50 border-bottom pb-2">{{ auth()->user()->fullname }}
                                @lang('Shipping
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            Address Information')</h5>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn--primary add-address">Add New Address</button>
                        </div>
                    </div>
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th scope="col">@lang('#')</th>
                                    <th scope="col">@lang('Recipient`s name')</th>
                                    <th scope="col">@lang('Recipient`s phone number')</th>
                                    <th scope="col">@lang('Full Address')</th>
                                    <th scope="col">@lang('Postal Code')</th>
                                    <th scope="col">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 0; ?>
                                @foreach ($alamat as $item)
                                    <?php $no++; ?>
                                    <tr>
                                        <td>{{ $no }}</td>
                                        <td>{{ $item->nama_penerima }}</td>
                                        <td>{{ $item->no_telp }}</td>
                                        <td>{{ $item->alamat }}</td>
                                        <td>{{ $item->kode_pos }}</td>
                                        <td data-label="@lang('Action')">
                                            <button type="button" class="icon-btn editaddress" data-toggle="tooltip"
                                                data-id="{{ $item->id }}" data-alamat="{{ $item->alamat }}"
                                                data-nama_penerima="{{ $item->nama_penerima }}"
                                                data-no_telp="{{ $item->no_telp }}"
                                                data-kode_pos="{{ $item->kode_pos }}" data-original-title="Edit">
                                                <i class="la la-pencil"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table><!-- table end -->
                    </div>


                </div>
            </div>
        </div> --}}
    </div>


    <div id="add-address" class="modal  fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add New Address')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ route('user.add_address') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="form-group">
                            <label class="font-weight-bold"> @lang('Recipient`s name')</label>
                            {{-- <input class="form-control form-control-lg" type="file" accept="image/*"
                                onchange="loadFile(event)" name="images" required> --}}
                            <input type="text" class="form-control" name="nama_penerima" placeholder="Nama Penerima"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold"> @lang('Recipient`s phone number')</label>
                            {{-- <input class="form-control form-control-lg" type="file" accept="image/*"
                                onchange="loadFile(event)" name="images" required> --}}
                            <input type="text" class="form-control" name="no_telp"
                                placeholder="Nomor Telepon Penerima" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold"> @lang('Full Address')</label>
                            {{-- <input class="form-control form-control-lg" type="file" accept="image/*"
                            onchange="loadFile(event)" name="images" required> --}}
                            <Textarea id="alamat" name="alamat" rows="4" placeholder="Jalan, No Rumah, RT, RW , Kec/Kota, Kab"
                                required></Textarea>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold"> @lang('Postal Code')</label>
                            {{-- <input class="form-control form-control-lg" type="file" accept="image/*"
                                onchange="loadFile(event)" name="images" required> --}}
                            <input type="text" class="form-control" name="kode_pos" placeholder="Kode Pos Penerima"
                                required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn-block btn btn--primary">@lang('Submit')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div id="edit-address" class="modal  fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Edit Address')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ route('user.edit_address') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" class="id" name="id" id="id">
                        {{-- <div class="form-row">
                        <div class="form-group col">
                            <label class="font-weight-bold"> @lang('Address')</label>
                            <Textarea class="alamat" id="alamat" name="alamat" rows="4"
                                placeholder="Jalan, No Rumah, RT, RW , Kec/Kota, Kab, No Pos"></Textarea>
                        </div>
                    </div> --}}
                        <div class="form-group">
                            <label class="font-weight-bold"> @lang('Recipient`s name')</label>
                            {{-- <input class="form-control form-control-lg" type="file" accept="image/*"
                            onchange="loadFile(event)" name="images" required> --}}
                            <input type="text" class="form-control nama_penerima" id="nama_penerima"
                                name="nama_penerima" placeholder="Nama Penerima" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold"> @lang('Recipient`s phone number')</label>
                            {{-- <input class="form-control form-control-lg" type="file" accept="image/*"
                            onchange="loadFile(event)" name="images" required> --}}
                            <input type="text" class="form-control no_telp" id="no_telp" name="no_telp"
                                placeholder="Nomor Telepon Penerima" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold"> @lang('Full Address')</label>
                            {{-- <input class="form-control form-control-lg" type="file" accept="image/*"
                        onchange="loadFile(event)" name="images" required> --}}
                            <Textarea class="alamat" id="alamat" name="alamat" rows="4"
                                placeholder="Jalan, No Rumah, RT, RW , Kec/Kota, Kab" required></Textarea>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold"> @lang('Postal Code')</label>
                            {{-- <input class="form-control form-control-lg" type="file" accept="image/*"
                            onchange="loadFile(event)" name="images" required> --}}
                            <input type="text" class="form-control kode_pos" id="kode_pos" name="kode_pos"
                                placeholder="Kode Pos Penerima" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn-block btn btn--primary">@lang('Submit')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('user.change-password') }}" class="btn btn--success btn--shadow"><i
            class="fa fa-key"></i>@lang('Change Password')</a>
@endpush



@push('script')
    <script>
        function onChangeSelect(url, id, name) {
            // send ajax request to get the cities of the selected province and append to the select tag
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    id: id
                },
                success: function(data) {
                    $('#' + name).empty();
                    $('#' + name).append('<option>-Pilih</option>');

                    $.each(data, function(key, value) {
                        $('#' + name).append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        }
        $(function() {
            $('#provinsi').on('change', function() {
                onChangeSelect('{{ route('cities') }}', $(this).val(), 'kota');
            });
            $('#kota').on('change', function() {
                onChangeSelect('{{ route('districts') }}', $(this).val(), 'kecamatan');
            })
            $('#kecamatan').on('change', function() {
                onChangeSelect('{{ route('villages') }}', $(this).val(), 'desa');
            })
        });
    </script>

    <script>
        'use strict';
        (function($) {
            $("select[name=country]").val("{{ auth()->user()->address->country }}");
        })(jQuery)

        var loadFile = function(event) {
            var output = document.getElementById('output');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src)
            }
        };

        $('.add-address').on('click', function() {
            var modal = $('#add-address');
            modal.modal('show');
        });
        $('.editaddress').on('click', function() {
            // console.log($(this).data('alamat'));
            var modal = $('#edit-address');
            modal.find('.id').val($(this).data('id'));
            modal.find('.alamat').val($(this).data('alamat'));
            modal.find('.nama_penerima').val($(this).data('nama_penerima'));
            modal.find('.no_telp').val($(this).data('no_telp'));
            modal.find('.kode_pos').val($(this).data('kode_pos'));
            modal.modal('show');
        });
    </script>
@endpush
