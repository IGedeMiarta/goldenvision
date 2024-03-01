<!-- navbar-wrapper start -->
<nav class="navbar-wrapper">
    <form class="navbar-search" onsubmit="return false;">
        <button type="submit" class="navbar-search__btn">
            <i class="las la-search"></i>
        </button>
        <input type="search" name="navbar-search__field" id="navbar-search__field" placeholder="@lang('Search...')">
        <button type="button" class="navbar-search__close"><i class="las la-times"></i></button>

        <div id="navbar_search_result_area">
            <ul class="navbar_search_result"></ul>
        </div>
    </form>

    <div class="navbar__left">
        <button class="res-sidebar-open-btn"><i class="las la-bars"></i></button>
        <button type="button" class="fullscreen-btn">
            <i class="fullscreen-open las la-compress" onclick="openFullscreen();"></i>
            <i class="fullscreen-close las la-compress-arrows-alt" onclick="closeFullscreen();"></i>
        </button>
    </div>

    <div class="navbar__right">
        <ul class="navbar__action-list">
            <li>
                <button type="button" class="navbar-search__btn-open">
                    <i class="las la-search"></i>
                </button>
            </li>
            <li class="dropdown">
                <button type="button" class="primary--layer" data-toggle="dropdown" data-display="static"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="las la-shopping-cart text--primary"></i>
                    @if (checkCart())
                        <span class="pulse--primary"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu--md p-0 border-0 box--shadow1 dropdown-menu-right">
                    <div class="dropdown-menu__header text-center">
                        <span class="caption">@lang('Product in Cart')</span>
                        @if (checkCart())
                            <p>@lang('You have') {{ '2' }} @lang('product in cart')</p>
                        @else
                            <p>@lang('No items on cart')</p>
                        @endif
                    </div>
                    <div class="dropdown-menu__body">

                        @foreach (LoopCart() as $cart)
                            <a href="#" class="dropdown-menu__item">
                                <div class="navbar-notifi">
                                    <div class="navbar-notifi__left bg--white"><img
                                            src="{{ asset($cart->product->image) }}" alt="@lang('Product Image')"></div>
                                    <div class="navbar-notifi__right">
                                        <h6 class="notifi__title">{{ __($cart->product->name) }}</h6>
                                        <span class="time">@
                                            {{ $cart->qty }}</span>
                                    </div>
                                </div><!-- navbar-notifi end -->
                            </a>
                        @endforeach
                    </div>
                    <div class="dropdown-menu__footer">
                        <a href="#" class="view-all-message">@lang('View all Cart')</a>
                    </div>
                </div>
            </li>

            <li class="dropdown">
                <button type="button" class="" data-toggle="dropdown" data-display="static" aria-haspopup="true"
                    aria-expanded="false">

                    <span class="navbar-user">
                        <span class="navbar-user__thumb"><img
                                src="{{ getImage('assets/images/user/profile/' . auth()->user()->image, null, true) }}"
                                alt="@lang('image')"></span>

                        <span class="navbar-user__info">
                            <span class="navbar-user__name">{{ auth()->user()->username }}</span>
                        </span>
                        <span class="icon"><i class="las la-chevron-circle-down"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    <a href="{{ route('user.profile-setting') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-user-circle"></i>
                        <span class="dropdown-menu__caption">@lang('Profile')</span>
                    </a>
                    <a href="{{ route('user.change-password') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-key"></i>
                        <span class="dropdown-menu__caption">@lang('Password')</span>
                    </a>
                    <a href="{{ route('user.logout') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                        <span class="dropdown-menu__caption">@lang('Logout')</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- navbar-wrapper end -->
