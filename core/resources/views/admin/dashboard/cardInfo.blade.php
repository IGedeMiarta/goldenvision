<div class="row mb-none-30">
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--gradi-10 b-radius--10 box-shadow">
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{ $widget['total_bro_joined'] }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Nomor MP')</span>
                </div>
                <a href="{{ route('admin.users.all') }}"
                    class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{ $widget['total_users'] }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Users')</span>
                </div>
                <a href="{{ route('admin.users.all') }}"
                    class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--1 b-radius--10 box-shadow">
            <div class="icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{ $general->cur_sym }}</span>
                    <span class="amount">{{ nb(getAmount($widget['users_balance'])) }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Saldo User')</span>
                </div>
                <a href="{{ route('admin.users.active') }}"
                    class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
            </div>
        </div>
    </div>


    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--success b-radius--10 box-shadow">
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{ $widget['verified_users'] }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total User Terverifikasi')</span>
                </div>
                <a href="{{ route('admin.users.active') }}"
                    class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--gradi-13 b-radius--10 box-shadow ">
            <div class="icon">
                <i class="la la-product-hunt"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{ allUserPin() }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('PIN In Wallet User')</span>
                </div>
                {{-- <a href="{{ route('admin.users.emailVerified') }}"
                    class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a> --}}
            </div>
        </div>
    </div>


    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--red b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-ban"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ $widget['banned_users'] }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Banned Users')</span>
                    </div>
                    <a href="{{ route('admin.users.banned') }}"
                        class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
                </div>
            </div>
        </div> --}}


    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--indigo b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="la la-envelope"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ $widget['email_verified_users'] }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Total Email Verified')</span>
                    </div>
                    <a href="{{ route('admin.users.emailVerified') }}"
                        class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
                </div>
            </div>
        </div> --}}


    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--danger b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-envelope"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ $widget['emailUnverified'] }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Total Email Unverified')</span>
                    </div>
                    <a href="{{ route('admin.users.emailUnverified') }}"
                        class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
                </div>
            </div>
        </div> --}}

    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--primary b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-phone"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ $widget['sms_verified_users'] }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Total SMS Verified')</span>
                    </div>
                    <a href="{{ route('admin.users.smsVerified') }}"
                        class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
                </div>
            </div>
        </div> --}}

    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-window-close"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ $widget['smsUnverified'] }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Total SMS Unverified')</span>
                    </div>
                    <a href="{{ route('admin.users.smsUnverified') }}"
                        class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
                </div>
            </div>
        </div> --}}


    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--cyan b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-money-bill-wave-alt"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="currency-sign">{{ $general->cur_sym }}</span>
                        <span class="amount">{{ nb(getAmount($widget['users_invest'])) }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Total Invest')</span>
                    </div>
                    <a href="{{ route('admin.report.invest') }}"
                        class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
                </div>
            </div>
        </div> --}}

    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--info b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-money-bill-wave-alt"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="currency-sign">{{ $general->cur_sym }}</span>
                        <span class="amount">{{ nb(getAmount($widget['last7days_invest'])) }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Last 7 Days Invest')</span>
                    </div>
                    <a href="#0"
                        class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
                </div>
            </div>
        </div> --}}


    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--3 b-radius--10 box-shadow">
            <div class="icon">
                <i class="las la-hand-holding-usd"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{ $general->cur_sym }}</span>
                    <span class="amount">{{ nb(sharingProfit()) }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Sharing Profit')</span>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--17 b-radius--10 box-shadow">
            <div class="icon">
                <i class="las la-tree"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{ $general->cur_sym }}</span>
                    <span class="amount">{{ nb(getAmount($widget['total_binary_com'])) }}</span>

                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Komisi Binary')</span>
                </div>
                {{-- <a href="{{ route('admin.report.binaryCom') }}"
                    class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a> --}}
            </div>
        </div>
    </div>
    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--gradi-51 b-radius--10 box-shadow">
            <div class="icon">
                <i class="las la-coins"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{ $general->cur_sym }}</span>
                    <span class="amount">{{ nb(getAmount($widget['totalWdGold'])) }}</span>

                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Gold Harian & Mingguan')</span>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--17 b-radius--10 box-shadow">
            <div class="icon">
                <i class="las fa-money-bill-wave-alt"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{ $general->cur_sym }}</span>
                    <span class="amount">{{ nb(tarik_emas()) }}</span>

                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Mini Gold')</span>
                </div>
               
            </div>
        </div>
    </div> --}}
    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--17 b-radius--10 box-shadow">
            <div class="icon">
                <i class="las fa-money-bill-wave-alt"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="h5 text-light">
                        {{ nb(minigold('count')) }}</span> <span class="badge badge-light">user</span><br>

                    <span class="h5 text-light">{{ minigold('emas') . ' gr' }} <span class="badge badge-light">Gold
                        </span></span>

                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Mini Gold')</span>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--pink b-radius--10 box-shadow">
            <div class="icon">
                <i class="las fa-money-bill-wave-alt"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{ $general->cur_sym }}</span>
                    <span class="amount">{{ nb(getAmount($widget['totalColagenProd'])) }}</span>

                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Produksi Colagen')</span>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--danger b-radius--10 box-shadow">
            <div class="icon">
                <i class="las fa-money-bill-wave-alt"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{ $general->cur_sym }}</span>
                    <span class="amount">{{ nb(getAmount($widget['totalReferalsCommision'])) }}</span>

                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Komisi Refeerals')</span>
                </div>
                {{-- <a href="#"
                        class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a> --}}
            </div>
        </div>
    </div>

    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--success b-radius--10 box-shadow">
            <div class="icon">
                <i class="las fa-money-bill-wave-alt"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{ $general->cur_sym }}</span>
                    <span class="amount">{{ nb(getAmount($widget['totalPurchasedPlan'])) }}</span>

                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Pembelian Plan')</span>
                </div>
            </div>
        </div>
    </div> --}}



    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--gradi-5 b-radius--10 box-shadow">
            <div class="details">
                <div class="numbers ">
                    {{-- <span class="amount">User Badges</span> --}}
                </div>
            </div>
            <div class="icon">
                <i class="las fa-info"></i>
            </div>
            <div class="details mr-3">
                <div class="desciption">
                    <span class="text--small">{{ 'QLF' }}</span>
                </div>
                <div class="numbers">
                    <span class="amount">{{ $widget['gold_silver']['gold'] }}</span>
                </div>
            </div>
            <div class="details mr-3">
                <div class="desciption">
                    <span class="text--small">{{ 'NOT' }}</span>
                </div>
                <div class="numbers">
                    <span class="amount">{{ $widget['gold_silver']['silver'] }}</span>
                </div>
            </div>

        </div>
    </div>

    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--3 b-radius--10 box-shadow">
            <div class="icon">
                <i class="las fa-money-bill-wave-alt"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{ $general->cur_sym }}</span>
                    <span class="amount">{{ nb(getAmount($widget['omset_this_month'])) }}</span>

                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Omset Bulan ini')</span>
                </div>
                {{-- <a href="#"
                        class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a> --}}
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--3 b-radius--10 box-shadow">
            <div class="icon">
                <i class="las fa-money-bill-wave-alt"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{ $general->cur_sym }}</span>
                    <span class="amount">{{ nb(getAmount($widget['omset_last_month'])) }}</span>

                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Omset Bulan Lalu')</span>
                </div>
                {{-- <a href="#"
                        class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a> --}}
            </div>
        </div>
    </div>
    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--success b-radius--10 box-shadow">
            <div class="icon">
                <i class="las fa-money-bill-wave-alt"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{ $general->cur_sym }}</span>
                    <span class="amount">{{ nb(SellingOmset()) }}</span>

                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Omset Penjualan')</span>
                </div>

            </div>
        </div>
    </div> --}}
    {{-- <div class="col-md-6">
            <div class="card ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr class="text-center">
                                    <th colspan="3">Leader Not Distributed PIN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leader as $user)
                                    <tr>
                                        <td data-label="@lang('User')">
                                            <div class="user">
                                                <div class="thumb"><img
                                                        src="{{ getImage('assets/images/user/profile/' . $user->image, null, true) }}"
                                                        alt="@lang('image')"></div>
                                                <span class="name">{{ $user->fullname }}</span>
                                            </div>
                                        </td>
                                        <td data-label="@lang('Username')"><a
                                                href="{{ route('admin.users.detail', $user->id) }}">{{ $user->username }}</a>
                                        </td>
                                        <td data-label="@lang('Email')" class="text-left">{{ $user->pin }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('User Not Found')</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
            </div>
        </div> --}}
    {{-- <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--deep-purple b-radius--10 box-shadow">
                <div class="icon">
                    <i class="las la-cut"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{getAmount($bv['totalBvCut'])}}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Users Total Bv Cut')</span>
                    </div>
                    <a href="{{route('admin.report.bvLog')}}?type=cutBV"
                       class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--15 b-radius--10 box-shadow">
                <div class="icon">
                    <i class="las la-cart-arrow-down"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{getAmount($bv['bvLeft'] + $bv['bvRight'])}}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Users Total BV')</span>
                    </div>
                    <a href="{{route('admin.report.bvLog')}}?type=paidBV"
                       class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--10 b-radius--10 box-shadow">
                <div class="icon">
                    <i class="las la-arrow-alt-circle-left"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{getAmount($bv['bvLeft'])}}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Users Left BV')</span>
                    </div>
                    <a href="{{route('admin.report.bvLog')}}?type=leftBV"
                       class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--3 b-radius--10 box-shadow">
                <div class="icon">
                    <i class="las la-arrow-alt-circle-right"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{getAmount($bv['bvRight'])}}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Right BV')</span>
                    </div>
                    <a href="{{route('admin.report.bvLog')}}?type=rightBV" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('Lihat Semua')</a>
                </div>
            </div>
        </div> --}}
</div>
