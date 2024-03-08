<div class="sidebar capsule--rounded bg_img overlay" data-background="{{ asset('assets/figma/nav-bg.png') }}"
    style="background-color: #141414 !important;">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('user.home') }}" class="sidebar__main-logo"><img src="{{ asset('assets/nav-logo.png') }}"
                    alt="@lang('image')" style="max-width: 150px"></a>
            <a href="{{ route('user.home') }}" class="sidebar__logo-shape"><img src="{{ asset('assets/logo.png') }}"
                    alt="@lang('image')" style="max-width: 150px"></a>
            <button type="button" class="navbar__expand"></button>
        </div>
        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{ menuActive('user.home') }}">
                    <a href="{{ route('user.home') }}" class="nav-link ">
                        <i class="menu-icon las la-home {{ routeActive('user.home') }}"></i>
                        <span class="menu-title {{ routeActive('user.home') }}">@lang('Dashboard')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('user.plan.index') }}">
                    <a href="{{ route('user.plan.index') }}" class="nav-link ">
                        <i class="menu-icon las la-archive {{ routeActive('user.plan.index') }}"></i>
                        <span class="menu-title {{ routeActive('user.plan.index') }}">@lang('Order Plan')</span>
                    </a>
                </li>

                <li
                    class="sidebar-menu-item sidebar-dropdown {{ menuActive('user.product*', 2) }} {{ auth()->user()->plan_id == 0 ? 'd-none' : '' }}">
                    <a href="javascript:void(0)" class=" my-2">
                        <i class="menu-icon  las la-tag {{ menuActive('user.product*', 2) }}"></i>
                        <span class="menu-title {{ menuActive('user.product*', 2) }}">@lang('Product')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('user.product*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('user.product.index') }}">
                                <a href="{{ route('user.product.index') }}" class="nav-link ">
                                    <i class="menu-icon las la-dot-circle {{ routeActive('user.product.index') }}"></i>
                                    <span
                                        class="menu-title {{ routeActive('user.product.index') }}">@lang('Reedem Product')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item  {{ menuActive('user.product.inv') }}">
                                <a href="{{ route('user.product.inv') }}" class="nav-link ">
                                    <i class="menu-icon las la-dot-circle {{ routeActive('user.product.inv') }}"></i>
                                    <span
                                        class="menu-title {{ routeActive('user.product.inv') }}">@lang('Invoice')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item  ">
                                <a href="{{ route('user.product.tracking') }}" class="nav-link ">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Tracking Product')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @if (auth()->user()->plan_id == 1)
                    <li class="sidebar-menu-item sidebar-dropdown">
                        <a href="javascript:void(0)" class="{{ menuActive('user.my*', 2) }} my-2">
                            <i class="menu-icon las la-code-branch {{ menuActive('user.my*', 2) }}"
                                style="-webkit-transform: rotate(180deg);-moz-transform: rotate(180deg);-ms-transform: rotate(180deg);-o-transform: rotate(180deg);transform: rotate(180deg);"></i>
                            <span class="menu-title {{ menuActive('user.my*', 2) }}">@lang('My Network')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('user.my*', 2) }} ">
                            <ul>
                                <li class="sidebar-menu-item {{ menuActive('user.my.tree') }} ">
                                    <a href="{{ route('user.my.tree') }}" class="nav-link">
                                        <i class="menu-icon las la-dot-circle {{ routeActive('user.my.tree') }}"></i>
                                        <span
                                            class="menu-title {{ routeActive('user.my.tree') }}">@lang('Geneology Tree')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('user.my.referral') }} ">
                                    <a href="{{ route('user.my.referral') }}" class="nav-link">
                                        <i
                                            class="menu-icon las la-dot-circle {{ routeActive('user.my.referral') }}"></i>
                                        <span
                                            class="menu-title {{ routeActive('user.my.referral') }}">@lang('Referals Tree')</span>
                                    </a>
                                </li>


                            </ul>
                        </div>
                    </li>
                @endif


                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('user.pins*', 2) }} my-2">
                        <i class="menu-icon la la-product-hunt"></i>
                        <span class="menu-title">@lang('User PINs')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('user.pins*', 2) }} ">
                        <ul>

                            <li class="sidebar-menu-item {{ menuActive('user.pins.view') }} ">
                                <a href="{{ route('user.pins.view') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Request PIN')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.pins.view') }} ">
                                <a href="{{ route('user.pins.view') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Send PIN')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.pins.PinDeliveriyLog') }} ">
                                <a href="{{ route('user.pins.PinDeliveriyLog') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Log Delivery')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                @if (auth()->user()->is_stockiest == 1)
                    <li class="sidebar-menu-item {{ menuActive('user.deposit') }}">
                        <a href="{{ route('user.deposit') }}" class="nav-link">
                            <i class=" menu-icon las la-credit-card"></i>
                            <span class="menu-title">@lang('Deposit Now')</span>
                        </a>
                    </li>
                @endif
                <li class="sidebar-menu-item {{ menuActive('user.withdraw') }} disabled">
                    <a href="{{ route('user.withdraw') }}" class="nav-link">
                        <i class="menu-icon las la-wallet"></i>
                        <span class="menu-title">@lang('Withdraw Now')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('user.report*', 3) }} my-2">
                        <i class="menu-icon las la-clipboard"></i>
                        <span class="menu-title">@lang('Reports') / @lang('Logs')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('user.report*', 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('user.report.transactions') }} ">
                                <a href="{{ route('user.report.transactions') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Transactions Log')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.report.withdraw') }}">
                                <a href="{{ route('user.report.withdraw') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Withdraw Log')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('user.report.refCom') }}">
                                <a href="{{ route('user.report.refCom') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Referral Commission')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.report.binaryCom') }}">
                                <a href="{{ route('user.report.binaryCom') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Binary Commission')</span>
                                </a>
                            </li>
                            {{-- <li class="sidebar-menu-item {{ menuActive('user.report.leadersComm') }}">
                                <a href="{{ route('user.report.leadersComm') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Leadership Commission')</span>
                                </a>
                            </li> --}}

                        </ul>
                    </div>
                </li>
                <li class="sidebar-menu-item {{ menuActive('ticket*') }}">
                    <a href="{{ route('ticket') }}" class="nav-link">
                        <i class="menu-icon las la-ticket-alt"></i>
                        <span class="menu-title">@lang('Support')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('user.profile-setting') }}">
                    <a href="{{ route('user.profile-setting') }}" class="nav-link">
                        <i class="menu-icon las la-user"></i>
                        <span class="menu-title">@lang('Profile')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item logout-menu-item">
                    <a href="{{ route('user.logout') }}" class="nav-link">
                        <i class="menu-icon las la-sign-out-alt"></i>
                        <span class="menu-title">@lang('Logout')</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
