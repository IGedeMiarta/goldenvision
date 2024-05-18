@extends('templates/basic/layouts/landing', [
    'title' => 'Sign In',
    'bodyClass' => 'login-bg',
])

@section('css')
    <style>

    </style>
@endsection

@section('content')
    <div class="section">
        <div class="section-1400 padding-top-bottom-120">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-7 col-xl-5">

                        <div
                            class="section py-4 py-md-5 px-3 px-sm-4 px-lg-5 over-hide border-4 section-shadow-blue bg-white section-background-24 background-img-top form">
                            <form class="section" method="post" action="{{ route('user.login') }}" class="form">
                                @csrf

                                <h4 class="mb-4 text-sm-center">
                                    <img src="{{ asset('assets/nav-logo.png') }}" alt=""
                                        style="max-width: 250px;-webkit-filter: drop-shadow(5px 5px 5px #222);
  filter: drop-shadow(5px 5px 5px #222);">
                                    <br>
                                    <br>
                                    Sign in
                                </h4>

                                <div class="form-group">
                                    <input type="text"
                                        class="form-control form-style-with-icon {{ $errors->has('username') ? 'is-invalid' : '' }}"
                                        placeholder="@lang('Username/E-mail')" autocomplete="off" name="username"
                                        value="{{ app('request')->input('username') ?? old('username') }}">
                                    <i class="input-icon uil uil-user-circle"></i>
                                    @error('username')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- <div class="form-group mt-3 ">

                                    <input type="password"
                                        class="form-style form-style-with-icon {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                        placeholder="@lang('Password')" autocomplete="off" name="password">
                                    <i class="input-icon uil uil-lock-alt"></i>

                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div> --}}
                                <div class=" input-group mt-3">
                                    <input type="password"
                                        class="form-control form-style-with-icon {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                        placeholder="@lang('Password')" autocomplete="off" name="password" id="password"
                                        value="{{ app('request')->input('password') ?? '' }}">
                                    <i class="input-icon uil uil-lock-alt"></i>

                                    <div class="input-group-append">
                                        <button class="input-group-text" id="btnPass" type="button"><i
                                                class="uil uil-eye"></i></button>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 text-sm-center">
                                        <button type="submit" class="btn btn-dark-primary">Sign in<i
                                                class="uil uil-arrow-right size-22 ml-3"></i></button>
                                    </div>
                                </div>

                                @if (!$general->disable_regist)
                                    <p class="mt-4 mb-0 text-sm-center size-16">
                                        Belum Punya Akun?
                                        <a href="{{ route('user.register') }}"
                                            class="link link-dark-primary-2 link-normal animsition-link">
                                            Buat Akun
                                        </a>
                                    </p>
                                    <center>-- atau --</center>
                                @endif


                                <p class="mb-0 text-sm-center size-16 mt-3">
                                    Lupa Password?
                                    <a href="{{ route('user.password.request') }}"
                                        class="link link-dark-primary-2 link-normal animsition-link">
                                        Reset Password
                                    </a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAlert" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;background-color: #ECBC13;">
                <form action="{{ route('user.plan.purchase.ro') }}" method="POST">
                    @csrf
                    <div class="modal-body"
                        style="background-color: #ECBC13;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
                        <button type="button" class="close" style="color: white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="color: black">&times;</span>
                        </button>

                        <div class="text-center">
                            <h3 style="color: black"><i class="fas fa-exclamation-triangle mr-3"></i> Golden Vision, CEO
                                Message</h3>
                        </div>
                        <hr>
                        <p style="color: black" class="h6">
                            Dear Member <br>
                            Terimakasih atas kepercayaan Anda sudah bergabung di Golden Vision. <br><br>

                            Untuk teman² yang sudah Lock Posisi di Golden Vision, dipersilahkan untuk segera melakukan
                            Klaim/Reedem Produk paling lambat tanggal 18 Mei 2024. Keterlambatan Klaim Produk dari batas
                            waktu yang telah ditentukan tidak dapat diganggu gugat dan sudah final. <br><br>

                            Saldo Redemption Point akan di Convert Menjadi Cashback ke Cash Wallet dengan Nilai 1 Redemption
                            Point = Rp 50.000,-
                        </p>
                    </div>
                    {{-- <img src="{{ asset('assets/ro.gif') }}" alt=""> --}}
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#modalAlert').modal('show');
        });
        $('#btnPass').on('click', function() {
            var type = $('#password').attr('type');
            if (type == "password") {
                $("#password").attr({
                    type: "text"
                });
            } else {
                $("#password").attr({
                    type: "password"
                });
            }
        })
    </script>
@endpush
