@push('style')
    <style>
        .icon-rp {
            width: 50px;
            /* Adjust width and height as needed */
            height: 50px;
            border-radius: 50%;
            /* Makes the element round */
            background-color: white;
            /* Circle color */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            /* Adjust font size as needed */
            color: #28C76F;
            font-weight: bold;
            /* Transparent text color */
            border: 2px solid white;
            /* Adjust border width and color as needed */
        }

        .icon-rp-total {
            width: 50px;
            /* Adjust width and height as needed */
            height: 50px;
            border-radius: 50%;
            /* Makes the element round */
            background-color: white;
            /* Circle color */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            /* Adjust font size as needed */
            color: #ECBC13;
            font-weight: bold;
            /* Transparent text color */
            border: 2px solid white;
            /* Adjust border width and color as needed */
        }

        .icon-key {
            width: 50px;
            /* Adjust width and height as needed */
            height: 50px;
            border-radius: 50%;
            /* Makes the element round */
            background-color: white;
            /* Circle color */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            /* Adjust font size as needed */
            color: #28C76F;
            font-weight: bold;
            /* Transparent text color */
            border: 2px solid white;
            /* Adjust border width and color as needed */
        }

        .icon-users {
            width: 30px;
            /* Adjust width and height as needed */
            height: 30px;
            border-radius: 50%;
            /* Makes the element round */
            background-color: white;
            /* Circle color */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 15px;
            /* Adjust font size as needed */
            color: #008C4F;
            font-weight: bold;
            /* Transparent text color */
            border: 2px solid white;
            /* Adjust border width and color as needed */
        }

        .icon-user {
            width: 50px;
            /* Adjust width and height as needed */
            height: 50px;
            border-radius: 50%;
            /* Makes the element round */
            background-color: white;
            /* Circle color */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            /* Adjust font size as needed */
            color: #053461;
            font-weight: bold;
            /* Transparent text color */
            border: 2px solid white;
            /* Adjust border width and color as needed */
        }

        .bg-btn-success {
            background-color: #008C4F;
            color: white;
        }

        .bg-btn-primary {
            background: #053461;
            color: white;
        }

        .bg-btn-dark {
            background-color: #0A0A0A;
            color: white;
        }

        .text-small {
            font-size: 9px;
            color: white;
            font-style: italic;
        }

        .card-footer {
            position: absolute;
            bottom: 10px;
            /* Distance from the bottom of the card */
            left: 0;
            width: 100%;
        }

        .badges {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;

            /* Blue color */
            color: #fff;
            /* White text color */
            font-size: 12px;
            font-weight: bold;
        }

        .badges-success {
            background-color: #00A878;
        }

        .badges-danger {
            background-color: red;
        }

        .icon-check-mini {
            width: 15px;
            /* Adjust width and height as needed */
            height: 15px;
            border-radius: 50%;
            /* Makes the element round */
            background-color: white;
            /* Circle color */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 10px;
            /* Adjust font size as needed */

            font-weight: bold;
            /* Transparent text color */
            border: 2px solid white;
        }

        .fa-check {
            color: #28C76F;
        }

        .fa-clock {
            color: red;
        }
    </style>
@endpush
<div style="display: flex; align-items: center;">
    <p style="font-size: 14px; margin-right: 5px;">PIN</p>
    <hr style="flex: 1; margin: 0;">
</div>
<div class="row">

    <div class="col-xl-4 col-md-4 col-sm-4 mb-30 text-center">
        <div class="card bg--success"
            style="height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-white" style="font-size: 12px">Print PIN Total</span>
                    <h6 class="text-white" style="font-size: 25px;font-weight: bolder">
                        {{ $data['pin'] }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-key">
                        <i class="fas fa-key"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-sm-4 mb-30 text-center">
        <div class="card bg--success"
            style="height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-white" style="font-size: 12px">PIN Active Member</span>
                    <h6 class="text-white" style="font-size: 25px;font-weight: bolder">
                        {{ $data['member_pin'] }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-key">
                        <i class="fas fa-key"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-sm-4 mb-30 text-center">
        <div class="card bg--success"
            style="height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-white" style="font-size: 12px">PIN Used</span>
                    <h6 class="text-white" style="font-size: 25px;font-weight: bolder">
                        {{ $data['used_pin'] }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-key">
                        <i class="fas fa-key"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display: flex; align-items: center;">
    <p style="font-size: 14px; margin-right: 5px;">User</p>
    <hr style="flex: 1; margin: 0;">
</div>
<div class="row">

    <div class="col-xl-4 col-md-4 col-sm-4 mb-30 text-center">
        <div class="card"
            style="background-color: #045199; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-white" style="font-size: 12px">Total Free User </span>
                    <h6 class="text-white" style="font-size: 25px;font-weight: bolder">
                        {{ $data['free_user'] }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-user">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
            {{-- <div class="card-body" style="position: relative;">
                <a href="{{ route('user.report.transactions') }}"
                    class="btn btn-sm btn-block text--small bg-btn-primary box--shadow3 mt-3">@lang('View Network')</a>
            </div> --}}
        </div>
    </div>

    <div class="col-xl-4 col-md-4 col-sm-4 mb-30 text-center">
        <div class="card"
            style="background-color: #045199; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-white" style="font-size: 12px">Total Active User </span>
                    <h6 class="text-white"style="font-size: 25px;font-weight: bolder">{{ $data['total_active_user'] }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-user">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
            {{-- <div class="card-body" style="position: relative;">
                <a href="{{ route('user.report.transactions') }}"
                    class="btn btn-sm btn-block text--small bg-btn-primary box--shadow3 mt-3">@lang('View Network')</a>
            </div> --}}
        </div>
    </div>

    <div class="col-xl-4 col-md-4 col-sm-4 mb-30 text-center">
        <div class="card"
            style="background-color: #045199; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-white" style="font-size: 12px">Free User Today</span>
                    <h6 class="text-white"style="font-size: 25px;font-weight: bolder">{{ $data['free_user_today'] }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-user">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
            {{-- <div class="card-body" style="position: relative;">
                <a href="{{ route('user.report.transactions') }}"
                    class="btn btn-sm btn-block text--small bg-btn-primary box--shadow3 mt-3">@lang('View Network')</a>
            </div> --}}
        </div>
    </div>


    <div class="col-xl-4 col-md-4 col-sm-4 mb-30 text-center">
        <div class="card"
            style="background-color: #045199; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-white" style="font-size: 12px">+ User This Month </span>
                    <h6 class="text-white"style="font-size: 25px;font-weight: bolder">{{ $data['user_month'] }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-user">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
            {{-- <div class="card-body" style="position: relative;">
                <a href="{{ route('user.report.transactions') }}"
                    class="btn btn-sm btn-block text--small bg-btn-primary box--shadow3 mt-3">@lang('View Network')</a>
            </div> --}}
        </div>
    </div>

    <div class="col-xl-4 col-md-4 col-sm-4 mb-30 text-center">
        <div class="card"
            style="background-color: #045199; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-white" style="font-size: 12px">+ User This Week </span>
                    <h6 class="text-white"style="font-size: 25px;font-weight: bolder">{{ $data['user_week'] }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-user">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
            {{-- <div class="card-body" style="position: relative;">
                <a href="{{ route('user.report.transactions') }}"
                    class="btn btn-sm btn-block text--small bg-btn-primary box--shadow3 mt-3">@lang('View Network')</a>
            </div> --}}
        </div>
    </div>

    <div class="col-xl-4 col-md-4 col-sm-4 mb-30 text-center">
        <div class="card"
            style="background-color: #045199; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-white" style="font-size: 12px">+ User Today </span>
                    <h6 class="text-white"style="font-size: 25px;font-weight: bolder">{{ $data['active_user_today'] }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-user">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
            {{-- <div class="card-body" style="position: relative;">
                <a href="{{ route('user.report.transactions') }}"
                    class="btn btn-sm btn-block text--small bg-btn-primary box--shadow3 mt-3">@lang('View Network')</a>
            </div> --}}
        </div>
    </div>
</div>

<div style="display: flex; align-items: center;">
    <p style="font-size: 14px; margin-right: 5px;">Total</p>
    <hr style="flex: 1; margin: 0;">
</div>
<div class="row mt-1">
    <div class="col-xl-4 col-md-4 col-sm-6 mb-30 text-center">
        <div class="card"
            style="background-color: #ECBC13; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-dark" style="font-size: 12px">Total Payout </span>
                    <h6 class="text-dark"style="font-size: 25px;font-weight: bolder">{{ nb($data['total_payout']) }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-rp-total">
                        Rp
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-sm-6 mb-30 text-center">
        <div class="card"
            style="background-color: #ECBC13; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-dark" style="font-size: 12px">Payout This Month </span>
                    <h6 class="text-dark"style="font-size: 25px;font-weight: bolder">
                        {{ nb($data['payout_this_month']) }}</h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-rp-total">
                        Rp
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-sm-6 mb-30 text-center">
        <div class="card"
            style="background-color: #ECBC13; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-dark" style="font-size: 12px">Payout This Week </span>
                    <h6 class="text-dark"style="font-size: 25px;font-weight: bolder">
                        {{ nb($data['payout_this_week']) }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-rp-total">
                        Rp
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-sm-6 mb-30 text-center">
        <div class="card"
            style="background-color: #ECBC13; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-dark" style="font-size: 12px">Payout Today </span>
                    <h6 class="text-dark"style="font-size: 25px;font-weight: bolder">
                        {{ nb($data['payout_today']) }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-rp-total">
                        Rp
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-sm-6 mb-30 text-center">
        <div class="card"
            style="background-color: #ECBC13; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-dark" style="font-size: 12px">Total Omset </span>
                    <h6 class="text-dark"style="font-size: 25px;font-weight: bolder">
                        {{ nb($data['total_omset']) }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-rp-total">
                        Rp
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-sm-6 mb-30 text-center">
        <div class="card"
            style="background-color: #ECBC13; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-dark" style="font-size: 12px">Omset This Month</span>
                    <h6 class="text-dark"style="font-size: 25px;font-weight: bolder">
                        {{ nb($data['omset_this_month']) }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-rp-total">
                        Rp
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-sm-6 mb-30 text-center">
        <div class="card"
            style="background-color: #ECBC13; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-dark" style="font-size: 12px">Omset This Week</span>
                    <h6 class="text-dark"style="font-size: 25px;font-weight: bolder">
                        {{ nb($data['omset_this_week']) }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-rp-total">
                        Rp
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-sm-6 mb-30 text-center">
        <div class="card"
            style="background-color: #ECBC13; height: 150px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body" style="display: flex; justify-content: space-between">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-dark" style="font-size: 12px">Omset Today</span>
                    <h6 class="text-dark"style="font-size: 25px;font-weight: bolder">
                        {{ nb($data['omset_today']) }}
                    </h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-rp-total">
                        Rp
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
