{{-- <div class="sidebar {{ sidebarVariation()['selector'] }} {{ sidebarVariation()['sidebar'] }} {{ @sidebarVariation()['overlay'] }} {{ @sidebarVariation()['opacity'] }}"
     data-background="{{getImage('assets/admin/images/sidebar/2.jpg','400x800')}}"> --}}

<div class="sidebar capsule--rounded bg_img overlay" {{-- data-background="{{asset('assets/admin/images/sidebar/2.jpg')}}" --}} style="background-color: #141414 !important;">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('admin.dashboard') }}" class="sidebar__main-logo"><img
                    src="{{ asset('assets/nav-logo.png') }}" alt="@lang('image')" style="max-width: 150px"></a>
            <a href="{{ route('admin.dashboard') }}" class="sidebar__logo-shape"><img
                    src="{{ asset('assets/logo.png') }}" alt="@lang('image')" style="max-width: 150px"></a>
            <button type="button" class="navbar__expand"></button>
        </div>

        @if (auth()->guard('admin')->user()->role == 'su')
            <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
                <ul class="sidebar__menu">
                    <li class="sidebar-menu-item {{ menuActive('admin.dashboard') }}">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link ">
                            <i class="menu-icon las la-home"></i>
                            <span class="menu-title">@lang('Dashboard')</span>
                        </a>
                    </li>
                    {{-- <li class="sidebar-menu-item">
                        <a href="{{ route('admin.setting.emergency') }}" class="nav-link ">
                            <i class="menu-icon las la-cog"></i>
                            <span class="menu-title">@lang('Emergency Button')</span>
                        </a>
                    </li> --}}

                    {{-- <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{menuActive('admin.survey*',3)}}">
                        <i class="menu-icon las la-tags"></i>
                        <span class="menu-title">@lang('Survey')</span>
                    </a>
                    <div class="sidebar-submenu {{menuActive('admin.survey*',2)}} ">
                        <ul>
                            <li class="sidebar-menu-item {{menuActive('admin.survey.category*')}}">
                                <a href="{{route('admin.survey.category.all')}}" class="nav-link ">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Category')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('admin.survey.all')}} ">
                                <a href="{{route('admin.survey.all')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All Survey')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('admin.survey.report')}} ">
                                <a href="{{route('admin.survey.report')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Survey Report')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li> --}}

                    <li class="sidebar-menu-item {{ menuActive('admin.plan*') }}">
                        <a href="{{ route('admin.plan') }}" class="nav-link ">
                            <i class="menu-icon las la-paper-plane"></i>
                            <span class="menu-title">@lang('Plans')</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.product*', 3) }}">
                            <i class="menu-icon las la-archive"></i>
                            <span class="menu-title">@lang('Product & Order')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.product*', 2) }} ">
                            <ul>
                                <li class="sidebar-menu-item {{ menuActive('admin.product.index') }} ">
                                    <a href="{{ route('admin.product.index') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('All Product')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.product.order') }} ">
                                    <a href="{{ route('admin.product.order') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Order Product')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    {{-- <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.reward*', 3) }}">
                            <i class="menu-icon las la-coins"></i>
                            <span class="menu-title">@lang('Bonus Reward')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.reward*', 2) }} ">
                            <ul>
                                <li class="sidebar-menu-item {{ menuActive('admin.reward.userBonus') }} ">
                                    <a href="{{ route('admin.reward.userBonus') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('User Reward')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.reward.allReward') }} ">
                                    <a href="{{ route('admin.reward.allReward') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Bonus Reward')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.reward.checkTree') }} ">
                                    <a href="{{ route('admin.reward.checkTree') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Check Reward')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.reward.provitSharing') }} ">
                                    <a href="{{ route('admin.reward.provitSharing') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Sharing Provit')</span>
                                    </a>
                                </li>


                            </ul>
                        </div>
                    </li> --}}

                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.users*', 3) }}">
                            <i class="menu-icon las la-users"></i>
                            <span class="menu-title">@lang('Users')</span>

                            @if ($banned_users_count > 0 || $email_unverified_users_count > 0 || $sms_unverified_users_count > 0)
                                <span class="menu-badge pill bg--primary ml-auto">
                                    <i class="fa fa-exclamation"></i>
                                </span>
                            @endif
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.users*', 2) }} ">
                            <ul>
                                <li class="sidebar-menu-item {{ menuActive('admin.users.all') }} ">
                                    <a href="{{ route('admin.users.all') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('All Users')</span>
                                    </a>
                                </li>

                                <li class="sidebar-menu-item {{ menuActive('admin.users.active') }} ">
                                    <a href="{{ route('admin.users.active') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Active Users')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.users.banned') }} ">
                                    <a href="{{ route('admin.users.banned') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Banned Users')</span>
                                        @if ($banned_users_count)
                                            <span
                                                class="menu-badge pill bg--primary ml-auto">{{ $banned_users_count }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.users.balance') }} ">
                                    <a href="{{ route('admin.users.balance') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Balance Users')</span>
                                    </a>
                                </li>
                                {{-- <li class="sidebar-menu-item  {{ menuActive('admin.users.emailUnverified') }}">
                                    <a href="{{ route('admin.users.emailUnverified') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Email Unverified')</span>

                                        @if ($email_unverified_users_count)
                                            <span
                                                class="menu-badge pill bg--primary ml-auto">{{ $email_unverified_users_count }}</span>
                                        @endif
                                    </a>
                                </li> --}}

                                {{-- <li class="sidebar-menu-item {{menuActive('admin.users.smsUnverified')}}">
                                <a href="{{route('admin.users.smsUnverified')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('SMS Unverified')</span>
                                    @if ($sms_unverified_users_count)
                                        <span
                                            class="menu-badge pill bg--primary ml-auto">{{$sms_unverified_users_count}}</span>
                                    @endif
                                </a>
                            </li> --}}


                                {{-- <li class="sidebar-menu-item {{menuActive('admin.users.email.all')}}">
                                <a href="{{route('admin.users.email.all')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Send Email')</span>
                                </a>
                            </li> --}}

                                {{-- <li class="sidebar-menu-item {{ menuActive('admin.users.dataverification') }} ">
                                    <a href="{{ route('admin.users.dataverification') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Data Users')</span>
                                        @if ($need_action_users_count)
                                            <span
                                                class="menu-badge pill bg--primary ml-auto">{{ $need_action_users_count }}</span>
                                        @endif
                                    </a>
                                </li> --}}
                                {{-- <li class="sidebar-menu-item {{ menuActive('admin.users.dataverified') }} ">
                                    <a href="{{ route('admin.users.dataverified') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Verified Data Users')</span>
                                        @if ($reject_users_count)
                                            <span
                                                class="menu-badge pill bg--primary ml-auto">{{ $reject_users_count }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.users.datareject') }} ">
                                    <a href="{{ route('admin.users.datareject') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Rejected Data Users')</span>
                                        @if ($reject_users_count)
                                            <span
                                                class="menu-badge pill bg--primary ml-auto">{{ $reject_users_count }}</span>
                                        @endif
                                    </a>
                                </li> --}}

                            </ul>
                        </div>
                    </li>



                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.deposit*', 3) }}">
                            <i class="menu-icon las la-credit-card"></i>
                            <span class="menu-title">@lang('Deposits')</span>
                            @if (0 < $pending_deposits_count)
                                <span class="menu-badge pill bg--primary ml-auto">
                                    <i class="fa fa-exclamation"></i>
                                </span>
                            @endif
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.deposit*', 2) }} ">
                            <ul>

                                <li class="sidebar-menu-item {{ menuActive('admin.deposit.pending') }} ">
                                    <a href="{{ route('admin.deposit.pending') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Pending Deposits')</span>
                                        @if ($pending_deposits_count)
                                            <span
                                                class="menu-badge pill bg--primary ml-auto">{{ $pending_deposits_count }}</span>
                                        @endif
                                    </a>
                                </li>

                                {{-- <li class="sidebar-menu-item {{menuActive('admin.deposit.approved')}} ">
                                <a href="{{route('admin.deposit.approved')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Approved Deposits')</span>
                                </a>
                            </li> --}}

                                <li class="sidebar-menu-item {{ menuActive('admin.deposit.successful') }} ">
                                    <a href="{{ route('admin.deposit.successful') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Successful Deposits')</span>
                                    </a>
                                </li>


                                {{-- <li class="sidebar-menu-item {{menuActive('admin.deposit.rejected')}} ">
                                <a href="{{route('admin.deposit.rejected')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Rejected Deposits')</span>
                                </a>
                            </li> --}}

                                <li class="sidebar-menu-item {{ menuActive('admin.deposit.list') }} ">
                                    <a href="{{ route('admin.deposit.list') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('All Deposits')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.pin*', 3) }}">
                            <i class="menu-icon la la-product-hunt"></i>
                            <span class="menu-title">@lang('PIN') </span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.pin*', 2) }} ">
                            <ul>
                                <li class="sidebar-menu-item {{ menuActive('admin.pin.delivered') }} ">
                                    <a href="{{ route('admin.pin.delivered') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('PIN By Admin')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.pin.all') }} ">
                                    <a href="{{ route('admin.pin.all') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('All PIN')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.point*', 3) }}">
                            <i class="menu-icon la la-product-hunt"></i>
                            <span class="menu-title">@lang('Redemption Point') </span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.point*', 2) }} ">
                            <ul>
                                <li class="sidebar-menu-item {{ menuActive('admin.point.delivered') }} ">
                                    <a href="{{ route('admin.point.delivered') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('POINT By System')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.point.all') }} ">
                                    <a href="{{ route('admin.point.all') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('All POINT')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.withdraw*', 3) }}">
                            <i class="menu-icon la la-bank"></i>
                            <span class="menu-title">@lang('Withdrawals') </span>
                            @if (0 < $pending_withdraw_count)
                                <span class="menu-badge pill bg--primary ml-auto">
                                    <i class="fa fa-exclamation"></i>
                                </span>
                            @endif
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.withdraw*', 2) }} ">
                            <ul>

                                <li class="sidebar-menu-item {{ menuActive('admin.withdraw.method.index') }}">
                                    <a href="{{ route('admin.withdraw.method.index') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Withdraw Methods')</span>
                                    </a>
                                </li>

                                <li class="sidebar-menu-item {{ menuActive('admin.withdraw.pending') }} ">
                                    <a href="{{ route('admin.withdraw.pending') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Pending Log')</span>

                                        @if ($pending_withdraw_count)
                                            <span
                                                class="menu-badge pill bg--primary ml-auto">{{ $pending_withdraw_count }}</span>
                                        @endif
                                    </a>
                                </li>

                                <li class="sidebar-menu-item {{ menuActive('admin.withdraw.approved') }} ">
                                    <a href="{{ route('admin.withdraw.approved') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Approved Log')</span>
                                    </a>
                                </li>

                                <li class="sidebar-menu-item {{ menuActive('admin.withdraw.rejected') }} ">
                                    <a href="{{ route('admin.withdraw.rejected') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Rejected Log')</span>
                                    </a>
                                </li>

                                <li class="sidebar-menu-item {{ menuActive('admin.withdraw.log') }} ">
                                    <a href="{{ route('admin.withdraw.log') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Withdrawals Log')</span>
                                    </a>
                                </li>


                            </ul>
                        </div>
                    </li>
                    {{-- <li class="sidebar-menu-item {{ menuActive('admin.adminReward*') }}">
                        <a href="{{ route('admin.adminReward') }}" class="nav-link ">
                            <i class="menu-icon las la-archive"></i>
                            <span class="menu-title">@lang('Admin Reward')</span>
                        </a>
                    </li> --}}
                    {{-- <li class="sidebar-menu-item {{ menuActive('admin.invest.gdetail*') }}">
                        <a href="{{ route('admin.invest.gdetail') }}" class="nav-link ">
                            <i class="menu-icon las la-coins"></i>
                            <span class="menu-title">@lang('User Golds')</span>
                        </a>
                    </li> --}}
                    {{-- <li class="sidebar-menu-item {{ menuActive('admin.users.reward.gold*') }}">
                        <a href="{{ route('admin.users.reward.gold') }}" class="nav-link ">
                            <i class="menu-icon las la-gem"></i>
                            <span class="menu-title">@lang('User Gold Reward')</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ menuActive('admin.custom.order*') }}">
                        <a href="{{ route('admin.custom.order') }}" class="nav-link ">
                            <i class="menu-icon las la-shopping-cart"></i>
                            <span class="menu-title">@lang('Custom Order')</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ menuActive('admin.exchange*') }}">
                        <a href="{{ route('admin.exchange') }}" class="nav-link ">
                            <i class="menu-icon las la-sync"></i>
                            <span class="menu-title">@lang('Exchange')</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ menuActive('admin.delivery*') }}">
                        <a href="{{ route('admin.delivery') }}" class="nav-link ">
                            <i class="menu-icon las la-truck"></i>
                            <span class="menu-title">@lang('Delivery')</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ menuActive('admin.BroDelivery*') }}">
                        <a href="{{ route('admin.BroDelivery') }}" class="nav-link ">
                            <i class="menu-icon las la-truck"></i>
                            <span class="menu-title">@lang('MP Pack Delivery')</span>
                        </a>
                    </li> --}}
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.report*', 3) }}">
                            <i class="menu-icon la la-list"></i>
                            <span class="menu-title">@lang('Payout Commission') </span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.report*', 2) }} ">
                            <ul>
                                <li class="sidebar-menu-item {{ menuActive(['admin.report.allPayout']) }}">
                                    <a href="{{ route('admin.report.allPayout') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('All Payout')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive(['admin.report.refCom']) }}">
                                    <a href="{{ route('admin.report.refCom') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Referral Commission')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive(['admin.report.binaryCom']) }}">
                                    <a href="{{ route('admin.report.binaryCom') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Binary Commission')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive(['admin.report.leadership']) }}">
                                    <a href="{{ route('admin.report.leadership') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Leader Commission')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive(['admin.report.founder']) }}">
                                    <a href="{{ route('admin.report.founder') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Founder Commission')</span>
                                    </a>
                                </li>

                                {{-- <li
                                    class="sidebar-menu-item {{ menuActive(['admin.report.transaction', 'admin.report.transaction.search']) }}">
                                    <a href="{{ route('admin.report.transaction') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('All Trasaction')</span>
                                    </a>
                                </li> --}}

                                {{-- <li
                                    class="sidebar-menu-item {{ menuActive(['admin.report.login.history', 'admin.report.login.ipHistory']) }}">
                                    <a href="{{ route('admin.report.login.history') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Login History')</span>
                                    </a>
                                </li> --}}

                            </ul>
                        </div>
                    </li>
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.log*', 3) }}">
                            <i class="menu-icon la la-list"></i>
                            <span class="menu-title">@lang('Logs') </span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.log*', 2) }} ">
                            <ul>
                                <li class="sidebar-menu-item {{ menuActive(['admin.log.invest']) }}">
                                    <a href="{{ route('admin.log.invest') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Invest Log')</span>
                                    </a>
                                </li>
                                <li
                                    class="sidebar-menu-item {{ menuActive(['admin.log.transaction', 'admin.log.transaction.search']) }}">
                                    <a href="{{ route('admin.log.transaction') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Trasaction Log')</span>
                                    </a>
                                </li>
                                {{-- <li
                                    class="sidebar-menu-item {{ menuActive(['admin.log.login.history', 'admin.log.login.ipHistory']) }}">
                                    <a href="{{ route('admin.log.login.history') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Login Log')</span>
                                    </a>
                                </li> --}}

                            </ul>
                        </div>
                    </li>
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.ticket*', 3) }}">
                            <i class="menu-icon la la-ticket"></i>
                            <span class="menu-title">@lang('Support Ticket') </span>
                            @if (0 < $pending_ticket_count)
                                <span class="menu-badge pill bg--primary ml-auto">
                                    <i class="fa fa-exclamation"></i>
                                </span>
                            @endif
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.ticket*', 2) }} ">
                            <ul>

                                <li class="sidebar-menu-item {{ menuActive('admin.ticket') }} ">
                                    <a href="{{ route('admin.ticket') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('All Ticket')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.ticket.pending') }} ">
                                    <a href="{{ route('admin.ticket.pending') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Pending Ticket')</span>
                                        @if ($pending_ticket_count)
                                            <span
                                                class="menu-badge pill bg--primary ml-auto">{{ $pending_ticket_count }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.ticket.closed') }} ">
                                    <a href="{{ route('admin.ticket.closed') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Closed Ticket')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.ticket.answered') }} ">
                                    <a href="{{ route('admin.ticket.answered') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Answered Ticket')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>




                    <li class="sidebar-menu-item  {{ menuActive('admin.subscriber.index') }}">
                        <a href="{{ route('admin.subscriber.index') }}" class="nav-link"
                            data-default-url="{{ route('admin.subscriber.index') }}">
                            <i class="menu-icon las la-thumbs-up"></i>
                            <span class="menu-title">@lang('Subscribers') </span>
                        </a>
                    </li>


                    <li class="sidebar__menu-header">@lang('Settings')</li>

                    <li class="sidebar-menu-item {{ menuActive('admin.setting.index') }}">
                        <a href="{{ route('admin.setting.index') }}" class="nav-link">
                            <i class="menu-icon las la-life-ring"></i>
                            <span class="menu-title">@lang('General Setting')</span>
                        </a>
                    </li>

                    <li class="sidebar-menu-item {{ menuActive('admin.setting.logo_icon') }}">
                        <a href="{{ route('admin.setting.logo_icon') }}" class="nav-link">
                            <i class="menu-icon las la-images"></i>
                            <span class="menu-title">@lang('Logo Icon Setting')</span>
                        </a>
                    </li>

                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.gateway*', 3) }}">
                            <i class="menu-icon la la-paypal"></i>
                            <span class="menu-title">@lang('Gateways')</span>

                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.gateway*', 2) }} ">
                            <ul>

                                <li class="sidebar-menu-item {{ menuActive('admin.gateway.automatic.index') }} ">
                                    <a href="{{ route('admin.gateway.automatic.index') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Automatic Gateways')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.gateway.manual.index') }} ">
                                    <a href="{{ route('admin.gateway.manual.index') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Manual Gateways')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="sidebar-menu-item {{ menuActive('admin.extensions.index') }}">
                        <a href="{{ route('admin.extensions.index') }}" class="nav-link">
                            <i class="menu-icon las la-cogs"></i>
                            <span class="menu-title">@lang('Extensions')</span>
                        </a>
                    </li>

                    <li class="sidebar-menu-item  {{ menuActive(['admin.language.manage', 'admin.language.key']) }}">
                        <a href="{{ route('admin.language.manage') }}" class="nav-link"
                            data-default-url="{{ route('admin.language.manage') }}">
                            <i class="menu-icon las la-language"></i>
                            <span class="menu-title">@lang('Language') </span>
                        </a>
                    </li>

                    <li class="sidebar-menu-item {{ menuActive('admin.seo') }}">
                        <a href="{{ route('admin.seo') }}" class="nav-link">
                            <i class="menu-icon las la-globe"></i>
                            <span class="menu-title">@lang('SEO Manager')</span>
                        </a>
                    </li>

                    {{-- <li class="sidebar-menu-item {{menuActive('admin.setting.notice')}}">
                    <a href="{{route('admin.setting.notice')}}" class="nav-link">
                        <i class="menu-icon las la-exclamation-triangle"></i>
                        <span class="menu-title">@lang('Notice')</span>
                    </a>
                </li> --}}


                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.email.template*', 3) }}">
                            <i class="menu-icon la la-envelope-o"></i>
                            <span class="menu-title">@lang('Email Manager')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.email.template*', 2) }} ">
                            <ul>

                                <li class="sidebar-menu-item {{ menuActive('admin.email.template.global') }} ">
                                    <a href="{{ route('admin.email.template.global') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Global Template')</span>
                                    </a>
                                </li>
                                <li
                                    class="sidebar-menu-item {{ menuActive(['admin.email.template.index', 'admin.email.template.edit']) }} ">
                                    <a href="{{ route('admin.email.template.index') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Email Templates')</span>
                                    </a>
                                </li>

                                <li class="sidebar-menu-item {{ menuActive('admin.email.template.setting') }} ">
                                    <a href="{{ route('admin.email.template.setting') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Email Configure')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.sms.template*', 3) }}">
                            <i class="menu-icon la la-mobile"></i>
                            <span class="menu-title">@lang('SMS Manager')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.sms.template*', 2) }} ">
                            <ul>
                                <li class="sidebar-menu-item {{ menuActive('admin.sms.template.global') }} ">
                                    <a href="{{ route('admin.sms.template.global') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('API Setting')</span>
                                    </a>
                                </li>
                                <li
                                    class="sidebar-menu-item {{ menuActive(['admin.sms.template.index', 'admin.sms.template.edit']) }} ">
                                    <a href="{{ route('admin.sms.template.index') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('SMS Templates')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>


                    {{-- <li class="sidebar__menu-header">@lang('TEMPLATES')</li> --}}



                    {{-- <li class="sidebar__menu-header">@lang('Frontend Manager')</li>

                <li class="sidebar-menu-item {{menuActive('admin.frontend.templates')}}">
                    <a href="{{route('admin.frontend.templates')}}" class="nav-link ">
                        <i class="menu-icon la la-html5"></i>
                        <span class="menu-title">@lang('Manage Templates')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{menuActive('admin.frontend.manage.pages')}}">
                    <a href="{{route('admin.frontend.manage.pages')}}" class="nav-link ">
                        <i class="menu-icon la la-list"></i>
                        <span class="menu-title">@lang('Manage Pages')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{menuActive('admin.frontend.sections*',3)}}">
                        <i class="menu-icon la la-html5"></i>
                        <span class="menu-title">@lang('Manage Section')</span>
                    </a>
                    <div class="sidebar-submenu {{menuActive('admin.frontend.sections*',2)}} ">
                        <ul>
                            @php
                               $lastSegment =  collect(request()->segments())->last();
                            @endphp
                            @foreach (getPageSections(true) as $k => $secs)
                                @if ($secs['builder'])
                                    <li class="sidebar-menu-item  @if ($lastSegment == $k) active @endif ">
                                        <a href="{{ route('admin.frontend.sections',$k) }}" class="nav-link">
                                            <i class="menu-icon las la-dot-circle"></i>
                                            <span class="menu-title">{{__($secs['name'])}}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach


                        </ul>
                    </div>
                </li> --}}
                </ul>
                {{-- <div class="text-center mb-3 text-uppercase">
                <span class="text--primary">{{systemDetails()['name']}}</span>
                <span class="text--success">@lang('V'){{systemDetails()['version']}} </span>
            </div> --}}
            </div>
        @else
            <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
                <ul class="sidebar__menu">
                    <li class="sidebar-menu-item {{ menuActive('admin.dashboard') }}">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link ">
                            <i class="menu-icon las la-home"></i>
                            <span class="menu-title">@lang('Dashboard')</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.reward*', 3) }}">
                            <i class="menu-icon las la-coins"></i>
                            <span class="menu-title">@lang('Bonus Reward')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.reward*', 2) }} ">
                            <ul>
                                <li class="sidebar-menu-item {{ menuActive('admin.reward.userBonus') }} ">
                                    <a href="{{ route('admin.reward.userBonus') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('All User Reward')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.reward.allReward') }} ">
                                    <a href="{{ route('admin.reward.allReward') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle"></i>
                                        <span class="menu-title">@lang('Bonus Reward')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="sidebar-menu-item {{ menuActive('admin.phoneReward') }}">
                        <a href="{{ route('admin.phoneReward') }}" class="nav-link">
                            <i class="menu-icon las la-mobile"></i>
                            <span class="menu-title">@lang('Reward HP')</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ menuActive('admin.thaiReward') }}">
                        <a href="{{ route('admin.thaiReward') }}" class="nav-link">
                            <i class="menu-icon las la-map-marked"></i>
                            <span class="menu-title">@lang('Trip Thai')</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ menuActive('admin.turkieReward') }}">
                        <a href="{{ route('admin.turkieReward') }}" class="nav-link">
                            <i class="menu-icon las la-map-marked"></i>
                            <span class="menu-title">@lang('Trip Turkie')</span>
                        </a>
                    </li>
                </ul>
            </div>

        @endif

    </div>
</div>
<!-- sidebar end -->
