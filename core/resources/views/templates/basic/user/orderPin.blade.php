@extends($activeTemplate . 'user.layouts.app')

@push('style')
    <style>
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
    </style>
@endpush
@section('panel')
    <div class="row ">
        <div class="col-md-5 mb-30 text-center">
            <div class="card"
                style="background-color: #ECBC13; height: 110px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;">
                <div class="card-body d-flex align-items-center justify-content-start gap-5">
                    <div class=" ml-4 icon-rp-total">
                        <i class="fas fa-key text-dark" style="font-size: 30px;"></i>
                    </div>
                    <div class="ml-3" style="text-align: start">
                        <span class="text-dark" style="font-size: 20px">Available </span>
                        <h6 class="text-dark font-weight-bold" style="font-size: 30px; margin-top: -10px;">
                            {{ nb(auth()->user()->pin) }} PIN
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 col-lg-5 ">
            <div class="card" style="min-height: 15rem; border-radius: 15px">

                <div class="card-body">
                    <form action="{{ route('user.pins.order.post') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <h3 class="text-dark font-weight-bold">1 PIN = Rp 500.000</h3>
                            <input type="number" name="pin" id="" placeholder="Masukan Nilai PIN"
                                class="form-control form-control-lg mt-3" style="border-radius: 10px">

                        </div>
                        <div class="card-body">
                            <button type="submit"
                                style="width: 100%; background: #008C4F; color: #fff; height: 45px; border-radius: 10px "
                                class="btn" onmouseover="this.style.background='#000'"
                                onmouseout="this.style.background='#008C4F'">Order PIN</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <iframe id="sgoplus-iframe" sandbox="allow-same-origin allow-scripts allow-top-navigation"
                        src="" scrolling="no" frameborder="0"></iframe>
                    <script type="text/javascript" src="https://sandbox-kit.espay.id/public/signature/js"></script>
                    <script type="text/javascript">
                        window.onload = function() {
                            var data = {
                                    key: "45c19b8cd6d132fa1c3801d21707a0dd",
                                    paymentId: "{{ generateTrxCode() }}",
                                    backUrl: "{{ url('user.dashboard') }}",
                                    display: 'tab'
                                },
                                sgoPlusIframe = document.getElementById("sgoplus-iframe");
                            if (sgoPlusIframe !== null) sgoPlusIframe.src = SGOSignature.getIframeURL(data);
                            SGOSignature.receiveForm();
                        };
                    </script>
                </div>
            </div>
        </div>
    </div>
@endsection
{{-- @push('script')
    <script>
        'use strict';
        (function($) {
            checkValid();
            $('#username').on('keyup', delay(function(e) {

                var uname = $(this).val();
                $.ajax({
                    url: "{{ url('find-uname') }}" + "/" + uname,
                    cache: false,
                    success: function(res) {
                        if (res.status == 404) {
                            $('.pin').attr('disabled', true);
                            $('.uname').removeClass('is-valid').addClass('is-invalid');
                            $('.txt-uname').addClass('text-danger').removeClass('text-success')
                                .html(res.msg)
                            $('#user_id').val('');

                            checkValid();

                        }
                        if (res.status == 200) {
                            $('.pin').attr('disabled', false).attr('placeholder',
                                "Input PIN Qty").focus();
                            $('.uname').removeClass('is-invalid').addClass('is-valid');
                            $('.txt-uname').addClass('text-success').removeClass('text-danger')
                                .html(res.msg)
                            $('#user_id').val(res.data.id);
                            checkValid();

                        }
                    }
                });
            }, 500))

            $('#pin').on('keyup', delay(function(e) {
                $('.txt-pin').html('');
                const uPin = parseInt("{{ auth()->user()->pin }}"); //60
                let pin = parseInt($(this).val()); //100
                if (uPin >= pin) {
                    $('.pin').removeClass('is-invalid').addClass('is-valid');
                    $('.txt-pin').addClass('text-success').removeClass('text-danger').html(
                        'Qty Match');

                    checkValid();

                } else {
                    $('.pin').removeClass('is-valid').addClass('is-invalid');
                    $('.txt-pin').addClass('text-danger').removeClass('text-success').html(
                        'You Not Have Enough PIN to Send');
                    checkValid();

                }
                if (isNaN(pin)) {
                    $('.pin').removeClass('is-invalid is-valid');
                    $('.txt-pin').removeClass('text-danger text-success').addClass('text-secondary').html(
                        'Type PIN Qty');
                    checkValid();

                }

            }, 500))

            function delay(callback, ms) {
                var timer = 0;
                return function() {
                    var context = this,
                        args = arguments;
                    clearTimeout(timer);
                    timer = setTimeout(function() {
                        callback.apply(context, args);
                    }, ms || 0);
                };
            }

            function checkValid() {
                let uname = $('.uname').hasClass('is-valid');
                let pin = $('.pin').hasClass('is-valid');
                if (uname && pin) {
                    $('.btnSend').attr('disabled', false);
                } else {
                    $('.btnSend').attr('disabled', true);
                }
                let userID = $('#user_id').val();
                const url = "{{ url('user/send-pin') }}" + '/' + userID;
                $('#formSubBalance').attr('action', url)

            }


        })(jQuery)
    </script>
@endpush --}}
