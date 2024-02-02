@extends($activeTemplate . 'user.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-12 col-lg-12 col-md-12 col-12 mb-30 ">
            <div class="card">
                <div class="card-body ">

                    <br>
                    <div class="img row text-center">
                        <div class="col-md-6">
                            <img src="{{ asset('assets/images/favicon-new.png') }}" alt=""
                                style="max-width: 300px;-webkit-filter: drop-shadow(5px 5px 5px #222);
  filter: drop-shadow(5px 5px 5px #222);">
                            <h1 style="text-transform: uppercase" style="display-1"><b>masterplan</b></h1>
                        </div>

                        <div class="col-md-6 mt-4">
                            <img src="{{ asset('assets/dinaran.png') }}" alt="" style="max-width: 300px;">
                        </div>


                    </div>
                    <div class="card-body d-flex justify-content-center">
                        <img src="{{ asset('assets/mainten.jpg') }}" style="max-width: 500px" alt="maintenence">
                    </div>
                    <h1 class="text-center"><b>UNDER CONSTRUCTION</b></h1>
                </div>
            </div>
        </div>


        <div class="col-xl-12 col-lg-12 col-md-12 col-12 mb-30 d-none">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-50 border-bottom pb-2">{{ auth()->user()->fullname }} @lang('Information')</h5>

                    <form action="{{ route('user.submitVerification') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('First Name') <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-lg" type="text" name="firstname"
                                        value="{{ auth()->user()->firstname }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Last Name') <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-lg" type="text" name="lastname"
                                        value="{{ auth()->user()->lastname }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @if (auth()->user()->plan_id != 0)
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="form-control-label font-weight-bold">@lang('No MP')<span
                                                class="text-danger">*</span></label>
                                        <input class="form-control form-control-lg" type="email"
                                            value="{{ auth()->user()->no_bro }}" readonly>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Email')<span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-lg" type="email"
                                        value="{{ auth()->user()->email }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Mobile Number')<span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-lg" type="text"
                                        value="{{ auth()->user()->mobile }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label  font-weight-bold">@lang('Avatar')</label>
                                    <input class="form-control form-control-lg" type="file" accept="image/*"
                                        onchange="loadFile(event)" name="image">
                                </div>
                            </div>
                        </div>

                        {{-- alamat --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Alamat') </label>
                                    <input class="form-control form-control-lg" type="text" name="alamat"
                                        value="{{ auth()->user()->address->address }}">
                                    <small class="form-text text-muted"><i class="las la-info-circle"></i>@lang(' Alamat Lengkap Rumah')
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Provinsi')<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="provinsi" id="provinsi" required>
                                        <option>--Pilih Provinsi </option>
                                        @foreach ($provinsi as $item)
                                            <option value="{{ $item->id ?? '' }}"
                                                {{ isset(auth()->user()->address->prov_code) && auth()->user()->address->prov_code == $item->id ? 'selected' : '' }}>
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
                                        @if (isset(auth()->user()->address->kota))
                                            <option>{{ auth()->user()->address->kota }}</option>
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
                                        @if (isset(auth()->user()->address->kec))
                                            <option>{{ auth()->user()->address->kec }}</option>
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
                                        @if (isset(auth()->user()->address->desa))
                                            <option>{{ auth()->user()->address->desa }}</option>
                                        @else
                                            <option>--Pilih Desa</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">@lang('Kode Pos')</label>
                                    <input class="form-control " type="text" name="pos"
                                        value="{{ auth()->user()->address->zip }}">
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

                        <div class="row mt-4">
                            <div class="col-xl-6 col-md-6 col-12">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">Nomor National ID (KTP)<span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-lg" value="{{ auth()->user()->nik }}"
                                        type="text" name="nik" required>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-3 col-12">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">Foto KTP<span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-lg" type="file" accept="image/*"
                                        onchange="loadFile(event)" name="ktp" required>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-3 col-12">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold">Foto Selfie Dengan KTP
                                        ID<span class="text-danger">*</span></label>
                                    <input class="form-control form-control-lg" type="file" accept="image/*"
                                        onchange="loadFile(event)" name="selfie" required>
                                </div>
                            </div>
                        </div>

                        <hr>
                        @if ($bank_user)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="form-control-label font-weight-bold">@lang('Bank Name') <span
                                                class="text-danger">*</span></label>
                                        {{-- <input class="form-control form-control-lg" type="text" name="firstname"
                                    value="{{auth()->user()->firstname}}" required> --}}
                                        <select name="bank_name" id="bank_name" class="form-control form-control-lg">
                                            @foreach ($bank as $item)
                                                <option value="{{ $item->nama_bank }}"
                                                    {{ auth()->user()->userBank->nama_bank == $item->nama_bank ? 'selected' : '' }}>
                                                    {{ $item->nama_bank }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Bank Branch City')
                                            <small>(Optional)</small></label>
                                        <input class="form-control form-control-lg" type="text" name="kota_cabang"
                                            value="{{ auth()->user()->userBank->kota_cabang }}"
                                            placeholder="Bank KCP Jakarta">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Bank Account Name') <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control form-control-lg" type="text" name="acc_name"
                                            value="{{ auth()->user()->userBank->nama_akun }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Bank Account Number') <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control form-control-lg" type="text" name="acc_number"
                                            value="{{ auth()->user()->userBank->no_rek }}" required>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="form-control-label font-weight-bold">@lang('Bank Name') <span
                                                class="text-danger">*</span></label>
                                        {{-- <input class="form-control form-control-lg" type="text" name="firstname"
                                    value="{{auth()->user()->firstname}}" required> --}}
                                        <select name="bank_name" id="bank_name" class="form-control form-control-lg"
                                            required>
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
                                        <input class="form-control form-control-lg" type="text" name="kota_cabang"
                                            value="" placeholder="Bank KCP Jakarta">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Account Name') <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control form-control-lg" type="text" name="acc_name"
                                            value="" required placeholder="Account Name">
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Account Number') <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control form-control-lg" type="text"
                                            placeholder="Account Number" name="acc_number" value="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit"
                                            class="btn btn--primary btn-block btn-lg">@lang('Save
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    Changes')</button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit"
                                        class="btn btn--primary btn-block btn-lg">@lang('Save Changes')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('user.change-password') }}" class="btn btn--success btn--shadow"><i
            class="fa fa-key"></i>@lang('Change Password')</a>
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
            $("select[name=country]").val("{{ auth()->user()->address->country }}");
        })(jQuery)

        var loadFile = function(event) {
            var output = document.getElementById('output');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src)
            }
        };
    </script>
@endpush
