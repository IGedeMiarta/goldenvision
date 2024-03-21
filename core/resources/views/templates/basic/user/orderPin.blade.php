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
    <link rel="stylesheet" href="{{ asset('assets/assets/dropify/css/dropify.min.css') }}">

@endpush
@section('panel')


    <div class="row ">
        <div class="col-md-4 mb-30 text-center">
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
        <div class="col-md-4 mb-30 text-center">
            <div class="card"
                style="background-color: #008C4F; height: 110px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;">
                <div class="card-body d-flex align-items-center justify-content-start gap-5">
                    <div class=" ml-4 icon-rp-total">
                        <i class="fas fa-key text-dark" style="font-size: 30px;"></i>
                    </div>
                    <div class="ml-3" style="text-align: start">
                        <span class="text-dark" style="font-size: 20px">Pending </span>
                        <h6 class="text-dark font-weight-bold" style="font-size: 30px; margin-top: -10px;">
                            @php
                                $pendi = $pending ?? 0;
                                if ($pendi == 0) {
                                   $rs = 0;
                                }else{
                                    $rs = $pending / 500000;
                                }
                            @endphp
                            {{ $rs }} PIN
                        </h6>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-4 mb-30 text-center">
            <div class="card"
                style="background-color:#EE4266; height: 110px;border-radius: 20px; background-image: url('{{ asset('assets/figma/card-bg.png') }}') !important;  
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;">
                <div class="card-body d-flex align-items-center justify-content-start gap-5">
                    <div class=" ml-4 icon-rp-total">
                        <i class="fas fa-key text-dark" style="font-size: 30px;"></i>
                    </div>
                    <div class="ml-3" style="text-align: start">
                        <span class="text-dark" style="font-size: 20px">Rejected </span>
                        <h6 class="text-dark font-weight-bold" style="font-size: 30px; margin-top: -10px;">
                            {{ ($order->amount?? 500000) / 500000 }} PIN
                        </h6>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
    <div class="row">
        @if (!isset($order) || (isset($order) && $order?->status == 2))
            <div class="col-md-4 col-lg-4">
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
           
        @elseif(isset($order) || ($order?->detail == null && $order?->status != 2))
        <div class="col-md-4 col-lg-4">
            <div class="card" style="min-height: 15rem; border-radius: 15px">
                <div class="card-body">
                    <div class="text-center">
                        <h5 class=""> Please Complate Your Order <b>{{ ($order?->amount?? 500000) /500000 }} PIN.</b> <br>
                    </div>
                    <ul class="list-group mt-3">
                        <li class="list-group-item">
                           <div class="row">
                            <div class="col-md-3">Bank</div>
                            <div class="col-md-9"><img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" style="width: 100px;"></div>
                           </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-3">Rek</div>
                                <div class="col-md-9">
                                    <a href="#" id="copy" data-rek="5250444828"> <b>5250444828</b> <i class="fas fa-copy"></i></a>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-3">A/N</div>
                                <div class="col-md-9">
                                    <b>PT. MEMAYU BARATA ADIGUNA</b>
                                </div>
                            </div>
                            </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-3">Amount</div>
                                <div class="col-md-9">
                                    <b>Rp {{ nb($order?->amount??0) }}</b>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <form action="{{ url('user/user-order',$order?->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <label for="">Uplad Bukti Trasfer</label>
                                <div class="input-group mb-3">
                                    <input type="file" name="images" id="" class="dropify" data-default-file="{{ $order->detail != null ? asset($order->detail):'' }}">
                                </div>
                                <label for="inpName">Nama Rekening Pengirim</label>
                                <input type="text" name="name" id="inpName" class="form-control" placeholder="ex: Haryanto" value="{{ $order->btc_amo??'' }}">
                                <br>
                                <button class="btn btn-primary" type="submit" > <i class="fas fa-save"></i> {{ $order->status == 0 ?'Submit':'Update' }}</button>
                            </form>
                        </li>
                    </ul>
                     
                </div>
            </div>
        </div>
        @endif
      
    </div>
@endsection
@push('script')
    <script src="{{ asset('assets/assets/dropify/js/dropify.min.js') }}"></script>
    <script>
      function copyToClipboard(text) {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const copyLink = document.getElementById('copy');

            copyLink.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the default action of the link

                const rekValue = copyLink.getAttribute('data-rek');
                copyToClipboard(rekValue);
                alert('Copied to clipboard Rek: ' + rekValue);
            });
        });
    </script>
@endpush
@push('script')
    <script>
        'use strict';
        (function($) {
            $('.dropify').dropify();

        })(jQuery)
    </script>
@endpush
