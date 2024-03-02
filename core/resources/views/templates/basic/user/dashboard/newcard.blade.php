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

        .bg-btn-success {
            background-color: #008C4F;
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
    </style>
@endpush
<div class="row">
    <div class="col-md-4 mb-30 text-center">
        <div class="card bg--success"
            style="height: 200px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body row">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-white">Cash - Wallet</span>
                    <h6 class="text-white">5.000.000 IDR</h6>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-rp">Rp</div>
                </div>
            </div>
            <div class="card-body" style="position: relative;">
                <a href="{{ route('user.report.transactions') }}"
                    class="btn btn-sm btn-block text--small bg-btn-success box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-30 text-center">
        <div class="card bg--success"
            style="height: 200px; border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
            <div class="card-body row">
                <div class="col-md-6 col-sm-6" style="text-align: start;">
                    <span class="text-white">B - Wallet</span>
                    <h6 class="text-white">1.000.000 IDR</h6>
                    <span class="text-small mt-n5">Available Limit 2.000.000</span>
                </div>
                <div class="col-md-6 col-sm-6" style="display: flex; justify-content: end">
                    <div class="icon-rp">Rp</div>
                </div>
            </div>
            <div class="card-body mt-n4" style="position: relative; ">
                <div class="" style="display: flex; justify-content: space-between">
                    <a href="{{ route('user.report.transactions') }}"
                        class="btn btn-sm btn-block text--small bg-btn-success box--shadow3 mt-3 mr-2">@lang('Repeat Order')</a>
                    <a href="{{ route('user.report.transactions') }}"
                        class="btn btn-sm btn-block text--small bg-btn-dark box--shadow3 mt-3">@lang('Convert') <svg
                            width="18" height="15" viewBox="0 0 18 15" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.0492 1.15085C14.7848 0.886429 14.7848 0.458862 15.0492 0.197259C15.3136 -0.0643446 15.7412 -0.0671575 16.0028 0.197259L17.8031 1.99754C17.9297 2.12412 18 2.29571 18 2.47574C18 2.65577 17.9297 2.82736 17.8031 2.95394L16.0028 4.75422C15.7384 5.01864 15.3108 5.01864 15.0492 4.75422C14.7876 4.4898 14.7848 4.06224 15.0492 3.80063L15.6962 3.15366L10.8017 3.14803C10.4276 3.14803 10.1266 2.84705 10.1266 2.47293C10.1266 2.09881 10.4276 1.79782 10.8017 1.79782H15.699L15.0492 1.15085ZM2.95359 10.6023L2.30661 11.2493H7.20112C7.57525 11.2493 7.87623 11.5503 7.87623 11.9244C7.87623 12.2985 7.57525 12.5995 7.20112 12.5995H2.3038L2.95077 13.2465C3.21519 13.5109 3.21519 13.9385 2.95077 14.2001C2.68636 14.4617 2.25879 14.4645 1.99719 14.2001L0.196906 12.4026C0.0703235 12.276 0 12.1044 0 11.9244C0 11.7444 0.0703235 11.5728 0.196906 11.4462L1.99719 9.64592C2.2616 9.38151 2.68917 9.38151 2.95077 9.64592C3.21238 9.91034 3.21519 10.3379 2.95077 10.5995L2.95359 10.6023ZM2.70042 1.79782H9.50492C9.40084 2.00035 9.33896 2.2282 9.33896 2.47293C9.33896 3.28024 9.99437 3.93566 10.8017 3.93566H14.1041C13.9916 4.41386 14.121 4.93425 14.4923 5.30837C15.0633 5.8794 15.9887 5.8794 16.5598 5.30837L17.1027 4.76547V10.7992C17.1027 11.7922 16.2954 12.5995 15.3024 12.5995H8.49789C8.60197 12.397 8.66385 12.1691 8.66385 11.9244C8.66385 11.1171 8.00844 10.4617 7.20112 10.4617H3.89873C4.01125 9.98347 3.88186 9.46308 3.51055 9.08896C2.93952 8.51793 2.01406 8.51793 1.44304 9.08896L0.900141 9.63186V3.5981C0.900141 2.60514 1.70745 1.79782 2.70042 1.79782ZM4.5007 3.5981H2.70042V5.39838C3.69339 5.39838 4.5007 4.59107 4.5007 3.5981ZM15.3024 8.99895C14.3094 8.99895 13.5021 9.80626 13.5021 10.7992H15.3024V8.99895ZM9.00141 9.89909C9.7176 9.89909 10.4045 9.61458 10.9109 9.10815C11.4173 8.60172 11.7018 7.91486 11.7018 7.19867C11.7018 6.48247 11.4173 5.79561 10.9109 5.28918C10.4045 4.78275 9.7176 4.49824 9.00141 4.49824C8.28521 4.49824 7.59835 4.78275 7.09192 5.28918C6.58549 5.79561 6.30098 6.48247 6.30098 7.19867C6.30098 7.91486 6.58549 8.60172 7.09192 9.10815C7.59835 9.61458 8.28521 9.89909 9.00141 9.89909Z"
                                fill="white" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>