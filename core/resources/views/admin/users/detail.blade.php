@extends('admin.layouts.app')

@push('style')
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

        <div class="col-xl-3 col-lg-5 col-md-5 mb-30">

            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body p-0">
                    <div class="p-3 bg--white">
                        <div class="">
                            <img src="{{ getImage('assets/images/user/profile/' . $user->image, null, true) }}"
                                alt="@lang('profile-image')" class="b-radius--10 w-100">
                        </div>
                        <div class="mt-15">
                            <h4 class="">{{ $user->fullname }}</h4>
                            <span
                                class="text--small">@lang('Joined At ')<strong>{{ showDateTime(
                                    $user->created_at,
                                    'd M, Y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                h:i A',
                                ) }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $imageRank = $user->rank != 0 ? $user->ranks->logo : null;
            @endphp
            @if ($imageRank != null)
                <div class="card b-radius--10 overflow-hidden mt-30 box--shadow1">
                    <div class="card-body text-center">
                        <img src="{{ getImage($imageRank, null, true) }}" alt="UserRank" style="max-width: 200px">
                        <form action="{{ route('admin.users.detail.rank', $user->id) }}">
                            @csrf
                            <div class="input-group mb-3">
                                <select name="rank" class="form-control" id="">
                                    @foreach ($rank as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($user->rank == $item->id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-success" type="button"
                                        id="button-addon2"><i class="fas fa-check"></i> Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div class="card b-radius--10 overflow-hidden mt-30 box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('User information')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Username')
                            <span class="font-weight-bold">{{ $user->username }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Ref By')
                            <span class="font-weight-bold"> {{ $ref_id->no_bro ?? 'N/A' }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('MP Left User')
                            <span class="font-weight-bold">{{ $user->userExtra->left }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('MP Right User')
                            <span class="font-weight-bold">{{ $user->userExtra->right }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @switch($user->status)
                                @case(1)
                                    <span class="badge badge-pill bg--success">@lang('Active')</span>
                                @break

                                @case(2)
                                    <span class="badge badge-pill bg--danger">@lang('Banned')</span>
                                @break
                            @endswitch
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            KYC
                            @switch($user->is_kyc)
                                @case(0)
                                    <span class="badge badge-pill bg--danger">Unverified</span>
                                @break

                                @case(1)
                                    <span class="badge badge-pill bg--warning">Need Verification Admin</span>
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
            <div class="card b-radius--10 overflow-hidden mt-30 box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('User action')</h5>
                    <a href="{{ route('admin.users.login', $user->id) }}" target="_blank"
                        class="btn btn--dark btn--shadow btn-block btn-lg">
                        <i class="las la-sign-in-alt"></i>@lang('Login as User')
                    </a>
                    <a data-toggle="modal" href="#addSubPointModal" class="btn btn--info btn--shadow btn-block btn-lg">
                        @lang('Add/Subtract POINT')
                    </a>

                    <a data-toggle="modal" href="#addSubModal" class="btn btn--success btn--shadow btn-block btn-lg">
                        @lang('Add/Subtract PIN')
                    </a>
                    <a data-toggle="modal" href="#addSubBalance" class="btn btn--warning btn--shadow btn-block btn-lg">
                        @lang('Add/Subtract Balance')
                    </a>
                    <a data-toggle="modal" href="#addSubBBalance" class="btn btn--primary btn--shadow btn-block btn-lg">
                        @lang('Add/Subtract B Balance')
                    </a>
                    <a href="{{ route('admin.users.login.history.single', $user->id) }}"
                        class="btn btn--primary btn--shadow btn-block btn-lg">
                        @lang('Login Logs')
                    </a>
                    <a href="{{ route('admin.users.email.single', $user->id) }}"
                        class="btn btn--danger btn--shadow btn-block btn-lg">
                        @lang('Send Email')
                    </a>
                    <a href="{{ route('admin.users.single.tree', $user->username) }}"
                        class="btn btn--primary btn--shadow btn-block btn-lg">
                        @lang('User Tree')
                    </a>
                    <a href="{{ route('admin.users.ref', $user->id) }}" class="btn btn--info btn--shadow btn-block btn-lg">
                        @lang('User Referrals')
                    </a>
                    {{-- <a data-toggle="modal" href="#userPlacement" class="btn btn--success btn--shadow btn-block btn-lg">
                    @lang('Set User Placement')
                </a> --}}
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-7 col-md-7 mb-30">
            <div class="row mb-none-30">

                <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                    <div class="dashboard-w1 bg--dark b-radius--10 box-shadow has--link">
                        <a href="#" class="item--link"></a>
                        <div class="icon">
                            <i class="la la-exchange-alt"></i>
                        </div>
                        <div class="details">
                            <div class="numbers">
                                <span class="amount">{{ $user->pin }}</span>
                                {{-- <span class="amount">{{$user->bro_qty + 1}}</span> --}}
                                <span class="currency-sign">PIN</span>
                            </div>
                            <div class="desciption">
                                <span>@lang('Available Pin')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                    <div class="dashboard-w1 bg--gradi-18 b-radius--10 box-shadow has--link">
                        <a href="#" class="item--link"></a>
                        <div class="icon">
                            <i class="fa fa-coins"></i>
                        </div>
                        <div class="details">
                            <div class="numbers">
                                <span class="amount">{{ $user->point }}</span>
                                <span class="currency-sign">POINT</span>
                            </div>
                            <div class="desciption">
                                <span>@lang('Total POINT')</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                    <div class="dashboard-w1 bg--success b-radius--10 box-shadow has--link">
                        <a href="{{ route('admin.users.transactions', $user->id) }}" class="item--link"></a>
                        <div class="icon">
                            <i class="la la-money"></i>
                        </div>
                        <div class="details">
                            <div class="numbers">
                                <span class="amount">{{ nb(getAmount($user->balance)) }} </span>
                                {{-- <span class="amount">{{$user->bro_qty + 1}}</span> --}}
                                <span class="currency-sign">{{ $general->cur_text }}</span>
                            </div>
                            <div class="desciption">
                                <span>@lang('Available Balance')</span>
                            </div>
                        </div>
                        <br>
                        {{-- <a href="{{ route('admin.users.transactions', $user->id) }}"
                            class="btn btn-sm btn-block text--small bg--white text--black box--shadow3 mt-3">@lang('View Log')</a> --}}
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                    <div class="dashboard-w1 bg--primary b-radius--10 box-shadow has--link">
                        <a href="{{ route('admin.users.deposits', $user->id) }}" class="item--link"></a>
                        <div class="icon">
                            <i class="fa fa-credit-card"></i>
                        </div>
                        <div class="details">
                            <div class="numbers">
                                <span class="amount">{{ getAmount($totalDeposit) }}</span>
                                <span class="currency-sign">{{ $general->cur_text }}</span>
                            </div>
                            <div class="desciption">
                                <span>@lang('Total Deposit')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                    <div class="dashboard-w1 bg--red b-radius--10 box-shadow has--link">
                        <a href="{{ route('admin.users.withdrawals', $user->id) }}" class="item--link"></a>
                        <div class="icon">
                            <i class="fa fa-wallet"></i>
                        </div>
                        <div class="details">
                            <div class="numbers">
                                <span class="amount">{{ nb(getAmount($totalWithdraw)) }}</span>
                                <span class="currency-sign">{{ $general->cur_text }}</span>
                            </div>
                            <div class="desciption">
                                <span>@lang('Total Withdraw')</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- dashboard-w1 end -->

                <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                    <div class="dashboard-w1 bg--dark b-radius--10 box-shadow has--link">
                        <a href="{{ route('admin.users.transactions', $user->id) }}" class="item--link"></a>
                        <div class="icon">
                            <i class="la la-exchange-alt"></i>
                        </div>
                        <div class="details">
                            <div class="numbers">
                                <span class="amount">{{ $totalTransaction }}</span>
                            </div>
                            <div class="desciption">
                                <span>@lang('Total Transaction')</span>
                            </div>
                        </div>
                    </div>
                </div><!-- dashboard-w1 end -->


                <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                    <div class="dashboard-w1 bg--info b-radius--10 box-shadow has--link">
                        <a href="{{ route('admin.report.invest') }}?user={{ $user->id }}" class="item--link"></a>
                        <div class="icon">
                            <i class="la la-money"></i>
                        </div>
                        <div class="details">
                            <div class="numbers">
                                <span class="amount">{{ nbk(getAmount($user->total_invest)) }}</span>
                                <span class="currency-sign">{{ $general->cur_text }}</span>
                            </div>
                            <div class="desciption">
                                <span>@lang('Total Invest')</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                <div class="dashboard-w1 bg--indigo b-radius--10 box-shadow has--link">
                    <a href="{{route('admin.report.refCom')}}?userID={{$user->id}}" class="item--link"></a>
                    <div class="icon">
                        <i class="la la-user"></i>
                    </div>
                    <div class="details">
                        <div class="numbers">
                            <span class="amount">{{getAmount($user->total_ref_com)}}</span>
                            <span class="currency-sign">{{$general->cur_text}}</span>
                        </div>
                        <div class="desciption">
                            <span>@lang('Total Referral Commission')</span>
                        </div>
                    </div>
                </div>
            </div> --}}


                <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                    <div class="dashboard-w1 bg--10 b-radius--10 box-shadow has--link">
                        <a href="{{ route('admin.report.binaryCom') }}?userID={{ $user->id }}"
                            class="item--link"></a>
                        <div class="icon">
                            <i class="la la-tree"></i>
                        </div>
                        <div class="details">
                            <div class="numbers">
                                <span class="amount">{{ nb(getAmount($user->total_binary_com)) }}</span>
                                <span class="currency-sign">{{ $general->cur_text }}</span>
                            </div>
                            <div class="desciption">
                                <span>@lang('Total Binary Commission')</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                    <div class="dashboard-w1 bg--21 b-radius--10 box-shadow has--link">
                        <a href="#" class="item--link"></a>
                        <div class="icon">
                            <i class="la la-gem"></i>
                        </div>
                        <div class="details">
                            <div class="numbers">
                                <span class="amount">{{ nbk($user->total_golds) }}</span>
                                <span class="currency-sign">Gram</span>
                            </div>
                            <div class="desciption">
                                <span class="text--small">{{ nbk($user->total_daily_golds) }} Daily Gold</span> <br />
                                <span class="text--small">{{ nbk($user->total_weekly_golds) }} Weekly Gold</span>
                            </div>
                        </div>
                    </div>
                </div> --}}

                {{-- <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                <div class="dashboard-w1 bg--19 b-radius--10 box-shadow has--link">
                    <a href="{{route('admin.report.single.bvLog', $user->id)}}?type=cutBV" class="item--link"></a>
                    <div class="icon">
                        <i class="la la-cut"></i>
                    </div>
                    <div class="details">
                        <div class="numbers">
                            <span class="amount">{{getAmount($totalBvCut)}}</span>
                        </div>
                        <div class="desciption">
                            <span>@lang('Total Cut BV')</span>
                        </div>
                    </div>
                </div>
            </div> --}}
                {{-- <div class="col-xl-3 col-lg-6 col-sm-6 mb-30">
                <div class="dashboard-w1 bg--15 b-radius--10 box-shadow has--link">
                    <a href="{{route('admin.report.single.bvLog', $user->id)}}?type=leftBV" class="item--link"></a>
                    <div class="icon">
                        <i class="las la-arrow-alt-circle-left"></i>
                    </div>
                    <div class="details">
                        <div class="numbers">
                            <span class="amount">{{getAmount($user->userExtra->bv_left)}}</span>
                        </div>
                        <div class="desciption">
                            <span>@lang('Left BV')</span>
                        </div>
                    </div>
                </div>
            </div> --}}
                {{-- <div class="col-xl-3 col-lg-6 col-sm-6 mb-30">
                <div class="dashboard-w1 bg--12 b-radius--10 box-shadow has--link">
                    <a href="{{route('admin.report.single.bvLog', $user->id)}}?type=rightBV" class="item--link"></a>
                    <div class="icon">
                        <i class="las la-arrow-alt-circle-right"></i>
                    </div>
                    <div class="details">
                        <div class="numbers">
                            <span class="amount">{{getAmount($user->userExtra->bv_right)}}</span>
                        </div>
                        <div class="desciption">
                            <span>@lang('Right BV')</span>
                        </div>
                    </div>
                </div>
            </div> --}}
                {{-- <div class="col-xl-3 col-lg-6 col-sm-6 mb-30">
                <div class="dashboard-w1 bg--19 b-radius--10 box-shadow has--link">
                    <a href="{{ route('admin.users.survey', $user->id) }}" class="item--link"></a>
                    <div class="icon">
                        <i class="las la-question-circle"></i>
                    </div>
                    <div class="details">
                        <div class="numbers">
                            <span class="amount">{{$user->completed_survey}}</span>
                        </div>
                        <div class="desciption">
                            <span>@lang('Completed Survey')</span>
                        </div>
                    </div>
                </div>
            </div> --}}

            </div>

            {{-- user form update --}}
            <div class="card mt-50">
                <div class="card-body">
                    <h5 class="card-title mb-50 border-bottom pb-2">@lang('Information')</h5>

                    <form action="{{ route('admin.users.update', [$user->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div style="display: flex; align-items: center; margin-top: 15px; margin-bottom: 5px;">
                            <p style="font-size: 14px; margin-right: 5px; color: rgb(72, 71, 71)">User Section<span
                                    class="text-danger">*</span></p>
                            <hr style="flex: 1; margin: 2; color: black">
                        </div>

                        <div class="row ml-3">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('First Name')<span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="firstname"
                                        value="{{ $user->firstname }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Last Name') <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="lastname"
                                        value="{{ $user->lastname }}">
                                </div>
                            </div>
                        </div>

                        <div class="row ml-3">
                            @if ($user->plan_id != 0)
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="form-control-label font-weight-bold">@lang('Refferals') <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="no_bro"
                                            value="{{ $user->username }}" readonly>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Email') <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="email" name="email"
                                        value="{{ $user->email }}">
                                </div>
                            </div>


                        </div>
                        <div class="row ml-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Mobile Number') <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="mobile"
                                        value="{{ $user->mobile }}">
                                </div>
                            </div>
                        </div>


                        {{-- <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Address') </label>
                                    <input class="form-control" type="text" name="address"
                                        value="{{ $user->address->address }}">
                                    <small class="form-text text-muted"><i class="las la-info-circle"></i>
                                        @lang('House
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    number, street address')
                                    </small>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('City') </label>
                                    <input class="form-control" type="text" name="city"
                                        value="{{ $user->address->city }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('State') </label>
                                    <input class="form-control" type="text" name="state"
                                        value="{{ $user->address->state }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Zip/Postal') </label>
                                    <input class="form-control" type="text" name="zip"
                                        value="{{ $user->address->zip }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Country') </label>
                                    <select name="country" class="form-control"> @include('partials.country') </select>
                                </div>
                            </div>
                        </div> --}}
                        <div class="row ml-3">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Provinsi')<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="provinsi" id="provinsi" required>
                                        <option>--Pilih Provinsi </option>
                                        @foreach ($provinsi as $item)
                                            <option value="{{ $item->id ?? '' }}"
                                                {{ isset($user->address->prov_code) && $user->address->prov_code == $item->id ? 'selected' : '' }}>
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
                                        @if (isset($user->address->kota))
                                            <option>{{ $user->address->kota }}</option>
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
                                        @if (isset($user->address->kec))
                                            <option>{{ $user->address->kec }}</option>
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
                                        @if (isset($user->address->desa))
                                            <option>{{ $user->address->desa }}</option>
                                        @else
                                            <option>--Pilih Desa</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Alamat') </label>
                                    <textarea name="alamat" id="alamat" cols="30" rows="5" class="form-control">{{ $user->address->address }}</textarea>

                                    <small class="form-text text-muted"><i
                                            class="las la-info-circle"></i>@lang(' Alamat Lengkap Rumah')
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Kode Pos')</label>
                                    <input class="form-control " type="text" name="pos"
                                        value="{{ $user->address->zip }}">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Negara')</label>
                                    <select name="negara" class="form-control ">
                                        @include('partials.country') </select>
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
                                        value="{{ $user->email_dinaran }}">
                                    <small class="form-text text-muted"><i
                                            class="las la-info-circle"></i>@lang('Pastikan Email Dinaran sudah benar untuk melakukan penarikan ')
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; margin-top: 15px; margin-bottom: 5px;">
                            <p style="font-size: 14px; margin-right: 5px; color: rgb(72, 71, 71)">Checklist Section<span
                                    class="text-danger">*</span></p>
                            <hr style="flex: 1; margin: 2; color: black">
                        </div>
                        <div class="row ml-3">
                            <div class="form-group col-xl-4 col-md-6  col-sm-3 col-12">
                                <label class="form-control-label font-weight-bold">@lang('Status') </label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                    data-toggle="toggle" data-on="Active" data-off="Banned" data-width="100%"
                                    name="status" @if ($user->status) checked @endif>
                            </div>

                            <div class="form-group  col-xl-4 col-md-6  col-sm-3 col-12">
                                <label class="form-control-label font-weight-bold">@lang('Email Verification') </label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                    data-toggle="toggle" data-on="Verified" data-off="Unverified" name="ev"
                                    @if ($user->ev) checked @endif>

                            </div>

                            <div class="form-group  col-xl-4 col-md-6  col-sm-3 col-12">
                                <label class="form-control-label font-weight-bold">@lang('SMS Verification') </label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                    data-toggle="toggle" data-on="Verified" data-off="Unverified" name="sv"
                                    @if ($user->sv) checked @endif>

                            </div>
                            {{-- <div class="form-group  col-xl-4 col-md-6  col-sm-3 col-12">
                                <label class="form-control-label font-weight-bold">@lang('Leader Status') </label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                    data-toggle="toggle" data-on="Active" data-off="Deactive" name="is_leader"
                                    @if ($user->is_leader) checked @endif>
                            </div> --}}
                            {{-- <div class="form-group  col-xl-4 col-md-6  col-sm-3 col-12">
                                <label class="form-control-label font-weight-bold">@lang('Stockiest Status') </label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                    data-toggle="toggle" data-on="Active" data-off="Deactive" name="is_stockiest"
                                    @if ($user->is_stockiest) checked @endif>
                            </div> --}}
                            <div class="form-group  col-xl-4 col-md-6  col-sm-3 col-12">
                                <label class="form-control-label font-weight-bold">@lang('2FA Status') </label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                    data-toggle="toggle" data-on="Active" data-off="Deactive" name="ts"
                                    @if ($user->ts) checked @endif>
                            </div>

                            <div class="form-group  col-xl-4 col-md-6  col-sm-3 col-12">
                                <label class="form-control-label font-weight-bold">@lang('2FA Verification') </label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                    data-toggle="toggle" data-on="Verified" data-off="Unverified" name="tv"
                                    @if ($user->tv) checked @endif>
                            </div>

                        </div>


                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Save Changes')
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            {{-- user bank form --}}
            {{-- <div class="card mt-50">
                <div class="card-body">
                    <h5 class="card-title mb-50 border-bottom pb-2">@lang('Bank Account Information')</h5>
                    @if ($user->userBank)
                        <form action="{{ route('admin.users.rek', [$user->id]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="form-control-label font-weight-bold">@lang('Bank name')<span
                                                class="text-danger">*</span></label>
                                        <select name="bank_name" id="bank_name" class="form-control" required>
                                            @foreach ($bank as $item)
                                                <option value="{{ $item->nama_bank }}"
                                                    {{ $user->userBank->nama_bank == $item->nama_bank ? 'selected' : '' }}>
                                                    {{ $item->nama_bank }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Bank Branch City')
                                            <small>(Optional)</small></label>
                                        <input class="form-control bankCabang" type="text" name="kota_cabang"
                                            id="bankCabang" value="{{ $user->userBank->kota_cabang }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Account Name') <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="acc_name"
                                            value="{{ $user->userBank->nama_akun }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Account Number') <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="acc_number"
                                            value="{{ $user->userBank->no_rek }}" required>
                                    </div>
                                </div>
                            </div>


                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit"
                                            class="btn btn--primary btn-block btn-lg">@lang('Save Changes')
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    @else
                        <form action="{{ route('admin.users.rek', [$user->id]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="form-control-label font-weight-bold">@lang('Bank name')<span
                                                class="text-danger">*</span></label>
                                        <select name="bank_name" id="bank_name" class="form-control" required>
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
                                        <input class="form-control" type="text" name="kota_cabang">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Account Name') <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="acc_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Account Number') <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="acc_number" required>
                                    </div>
                                </div>
                            </div>


                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit"
                                            class="btn btn--primary btn-block btn-lg">@lang('Save Changes')
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    @endif

                </div>
            </div> --}}

            {{-- <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-50 border-bottom pb-2">@lang('Data Verification')</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label class="form-control-label font-weight-bold">National ID/Passport ID</label>
                                <input class="form-control" type="nik" name="nik" value="{{ $user->nik }}">
                            </div>
                        </div>
                        <div class="row col-md-12 mb-50">
                            <div class="col-md-6">
                                <label class="form-control-label font-weight-bold">National ID/Passport ID Photo</label>
                                <div class="">
                                    <img src="{{ getImage('assets/images/user/kyc/' . $user->foto_ktp, null, true) }}"
                                        alt="National ID/Passport ID Photo" class="b-radius--10 w-100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-control-label font-weight-bold">Selfie With National ID/Passport ID
                                    Photo</label>
                                <div class="">
                                    <img src="{{ getImage('assets/images/user/kyc/' . $user->foto_selfie, null, true) }}"
                                        alt="Selfie Photo" class="b-radius--10 w-100">
                                </div>
                            </div>
                        </div>

                        @if ($user->is_kyc == 2)
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--success btn-block">User Verified</button>
                            </div>
                        @elseif ($user->is_kyc == 3)
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--danger btn-block">User Rejected</button>
                            </div>
                        @else
                            <div class="col-md-6">
                                <form action="{{ route('admin.users.verify', [$user->id]) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <button type="submit" class="btn btn--success btn-block">Verify</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('admin.users.reject', [$user->id]) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <button class="btn btn--danger btn-block">Reject</button>
                                </form>
                            </div>
                        @endif

                    </div>
                </div>
            </div> --}}
        </div>
    </div>



    {{-- Add Sub POINT MODAL --}}
    <div id="addSubPointModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add / Subtract POINT')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.users.addSubPoint', $user->id) }}" class="formDisable" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <input type="checkbox" data-width="100%" data-height="44px" data-onstyle="-success"
                                    data-offstyle="-danger" data-toggle="toggle" data-on="Add POINT"
                                    data-off="Subtract POINT" name="act">
                            </div>
                            <div class="form-group col-md-12">
                                <label>@lang('POINT')<span class="text-danger">*</span></label>
                                <div class="input-group has_append">
                                    <input type="text" id="point" name="point"
                                        class="form-control number-separator" placeholder="POINT">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--success submitModal">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Add Sub PIN MODAL --}}
    <div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add / Subtract PIN')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.users.addSubPin', $user->id) }}" class="formDisable" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <input type="checkbox" data-width="100%" data-height="44px" data-onstyle="-success"
                                    data-offstyle="-danger" data-toggle="toggle" data-on="Add PIN"
                                    data-off="Subtract PIN" name="act" checked>
                            </div>
                            <div class="form-group col-md-12">
                                <label>@lang('Titik')<span class="text-danger">*</span></label>
                                <div class="input-group has_append">
                                    <input type="text" id="titik" name="pin"
                                        class="form-control number-separator" placeholder="Pin">

                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label>@lang('Amount')<span class="text-danger">*</span></label>
                                <div class="input-group has_append">
                                    <input type="text" name="amount" id="amountId"
                                        class="form-control number-separator"
                                        placeholder="Please provide positive amount">
                                    <div class="input-group-append">
                                        <div class="input-group-text">{{ $general->cur_text }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--success submitModal">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Add Sub Balance MODAL --}}
    <div id="addSubBalance" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add / Subtract Balance')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.users.addSubBalance', $user->id) }}" class="formDisable" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <input type="checkbox" data-width="100%" data-height="44px" data-onstyle="-success"
                                    data-offstyle="-danger" data-toggle="toggle" data-on="Add Balance"
                                    data-off="Subtract Balance" name="act" checked>
                            </div>

                            <div class="form-group col-md-12">
                                <label>@lang('Balance')<span class="text-danger">*</span></label>
                                <div class="input-group has_append">
                                    <input type="text" name="amount" id="amountId"
                                        class="form-control number-separator"
                                        placeholder="Please provide positive amount">
                                    <div class="input-group-append">
                                        <div class="input-group-text">{{ $general->cur_text }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>@lang('Details')<span class="text-danger">*</span></label>
                                <div class="input-group has_append">
                                    <textarea name="details" id="" class="form-control" placeholder="Details"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--success submitModal">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="addSubBBalance" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add / Subtract B Balance')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.users.addSubBBalance', $user->id) }}" class="formDisable" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <input type="checkbox" data-width="100%" data-height="44px" data-onstyle="-success"
                                    data-offstyle="-danger" data-toggle="toggle" data-on="Add Balance"
                                    data-off="Subtract Balance" name="act" checked>
                            </div>

                            <div class="form-group col-md-12">
                                <label>@lang('Balance')<span class="text-danger">*</span></label>
                                <div class="input-group has_append">
                                    <input type="text" name="amount" id="amountId"
                                        class="form-control number-separator"
                                        placeholder="Please provide positive amount">
                                    <div class="input-group-append">
                                        <div class="input-group-text">{{ $general->cur_text }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>@lang('Details')<span class="text-danger">*</span></label>
                                <div class="input-group has_append">
                                    <textarea name="details" id="" class="form-control" placeholder="Details"></textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>@lang('Remark')</label>
                                <div class="input-group has_append">
                                    <input name="remark" id="" class="form-control"
                                        placeholder="remark"></input>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--success submitModal">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="userPlacement" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Set User Placement')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.users.setUserPlacement', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">

                            <div class="form-group col-md-12">
                                <label>@lang('MP Number')<span class="text-danger">*</span></label>
                                <div class="input-group has_append">
                                    <input type="text" name="no_bro" class="form-control"
                                        placeholder="MP Number as parent" required>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="ref_name" class="form--label-2">@lang('Select Position')</label>
                                <select name="position" class="position form-control form--control-2" id="position"
                                    required>
                                    <option value="">@lang('Select position')*</option>
                                    @foreach (mlmPositions() as $k => $v)
                                        <option value="{{ $k }}">@lang($v)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--success submitModal">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('breadcrumb-plugins')
    <div class="row">

        <div class="col-md-10 col-12">
            <form action="{{ route('admin.users.detail.find') }}" method="POST"
                class="form-inline float-sm-right bg--white">
                @csrf
                <div class="input-group has_append">
                    <input type="text" name="search" class="form-control" placeholder="@lang('Username')">
                    <div class="input-group-append">
                        <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
        $('.formDisable').submit(function() {
            $('.submitModal').prop('disabled', true);
        });
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
            $("select[name=country]").val("{{ @$user->address->country }}");
            $('#titik').on('keyup', function() {
                const num = $(this).val();
                const angka = numberWithCommas(num * 500000);
                $("#amountId").val(angka);
            })

            function numberWithCommas(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        })(jQuery)
    </script>
@endpush
