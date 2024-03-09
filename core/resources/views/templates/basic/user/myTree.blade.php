@extends($activeTemplate . 'user.layouts.app')

@push('style')
    <link href="{{ asset('assets/admin/css/tree.css') }}" rel="stylesheet">
    <style>
        .alert {
            position: relative;
            top: 10;
            left: 0;
            width: auto;
            height: auto;
            padding: 10px;
            margin: 10px;
            line-height: 1.8;
            border-radius: 5px;
            cursor: hand;
            cursor: pointer;
            font-family: sans-serif;
            font-weight: 400;
        }

        .alertCheckbox {
            display: none;
        }

        :checked+.alert {
            display: none;
        }

        .alertText {
            display: table;
            margin: 0 auto;
            text-align: center;
            font-size: 16px;
        }

        .alertClose {
            float: right;
            padding-top: 5px;
            font-size: 10px;
        }

        .clear {
            clear: both;
        }

        .info {
            background-color: #EEE;
            border: 1px solid #DDD;
            color: #999;
        }

        .success {
            background-color: #EFE;
            border: 1px solid #DED;
            color: #9A9;
        }

        .notice {
            background-color: #EFF;
            border: 1px solid #DEE;
            color: #9AA;
        }

        .warning {
            background-color: #FDF7DF;
            border: 1px solid #FEEC6F;
            color: #C9971C;
        }

        .error {
            background-color: #FEE;
            border: 1px solid #EDD;
            color: #A66;
        }
    </style>
@endpush

@section('panel')
    <div class="row" style="display: flex; justify-content: space-between">
        <div class="col-md-6 mb-3">
            <div class="card"
                style="height: 100px; border-radius: 15px; background-color: #fff; 
                background-position: end;
                background-repeat: no-repeat;
                background-size: cover;">
                <div class="card-body" style="display: flex; justify-content: start;">
                    <div class="text-icon" style="display: flex; align-items: center;">
                        <img src="{{ asset(auth()->user()->ranks->logo) }}" alt="logo" style="max-width: 80px">
                    </div>
                    <div style="display: block; text-align: start;" class="mb-3">
                        <h4 style="color: #000; font-size: 18px;">Carry Forward Binary Point</h4>
                        <span style="color: #000;">Left: {{ auth()->user()->userExtra->paid_left }}</span>
                        <br>
                        <span style="color: #000;">Right: {{ auth()->user()->userExtra->paid_right }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row" style="max-height: 200px;">
                <div class="col-md-12 mb-3">
                    <div class="card"
                        style="height: 100px; border-radius: 20px; background-color: #0A0A0A; background-image: url('{{ asset('assets/figma/nav-bg2.png') }}') !important;  
                background-position: end;
                background-repeat: no-repeat;
                background-size: cover;">
                        <form action="{{ route('user.other.tree.search') }}" method="GET">
                            <div class="card-body "
                                style="display: flex; justify-content: center; width: 100%; align-content: center">
                                <div class="input-group has_append mt-2">
                                    <input type="text" name="username" class="form-control"
                                        placeholder="@lang('Search by username')" style="background-color: #fff">
                                    <div class="input-group-append">
                                        <button class="btn btn--dark text-light" type="submit"><i
                                                class="fa fa-search"></i>CARI</button>
                                    </div>
                                </div>
                            </div>
                    </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="row d-flex justify-content-center">
        <div class="col-md-12 card card-tree" style="border-radius: 15px;">
            <div class="card-header ">
                <form action="{{ route('user.plan.changedef') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">

                            <div class="input-group">
                                <select name="pos" id="default" class="form-control">
                                    <option value="1" @if (auth()->user()->default_pos == 1) selected @endif>Left</option>
                                    <option value="2" @if (auth()->user()->default_pos == 2) selected @endif>Right</option>
                                </select>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-primary" type="button"
                                        id="button-addon2"><i class="fas fa-save"></i> Update</button>
                                </div>
                            </div>
                            <label for="default" style="font-size: 14px">Referrals Default Position</label>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('user.sponsor.regist') }}" class="btn btn-outline-success w-100"
                                type="button" id="button-addon2"><i class="fas fa-user"></i>Register New User</a>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </form>

            </div>

            <div class="card-body">
                <div class="active-user-none" data-id="{{ auth()->user()->id }}"></div>
                <div class="row text-center justify-content-center llll">
                    <!-- <div class="col"> -->
                    <div class="w-1 ">
                        @php echo showSingleUserinTree($tree['a']); @endphp
                    </div>
                </div>
                <div class="row text-center justify-content-center llll">
                    <!-- <div class="col"> -->
                    <div class="w-2 ">
                        @php echo showSingleUserinTree($tree['b']); @endphp
                    </div>
                    <!-- <div class="col"> -->
                    <div class="w-2 ">
                        @php echo showSingleUserinTree($tree['c']); @endphp
                    </div>
                </div>
                <div class="row text-center justify-content-center llll">
                    <!-- <div class="col"> -->
                    <div class="w-4  ">
                        @php echo showSingleUserNoLine($tree['d']); @endphp
                    </div>
                    <!-- <div class="col"> -->
                    <div class="w-4  ">
                        @php echo showSingleUserNoLineInsideLeft($tree['e']); @endphp
                    </div>
                    <!-- <div class="col"> -->
                    <div class="w-4  ">
                        @php echo showSingleUserNoLineInsideLeft($tree['f']); @endphp
                    </div>
                    <div class="w-4  ">
                        @php echo showSingleUserNoLine($tree['g']); @endphp
                    </div>

                </div>
            </div>
        </div>
    </div>




    <div class="modal fade user-details-modal-area" id="exampleModalCenter" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">@lang('User Details')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="@lang('Close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="user-details-modal">
                        <div class="user-details-header ">
                            <div class="thumb">
                                <img src="#" alt="*" class="tree_image w-h-100-p">

                            </div>
                            <div class="content">
                                <a class="user-name tree_url tree_name" href=""></a>
                                <strong style="    font-size: 19px;
                                font-weight: bolder;"
                                    class="user-status tree_bro"></strong>
                                <br>
                                <span class="user-status tree_email"></span>
                                <br>
                                <span class="user-status tree_phone"></span>
                                <br>

                                <span class="user-status tree_status"></span>
                                <span class="user-status tree_plan mb-3"></span>


                            </div>

                        </div>
                        <div class="user-details-body text-center">

                            {{-- <h6 class="my-3">@lang('Referred By'): <span class="tree_ref"></span></h6> --}}


                            <table class="table table-bordered">
                                <tr>
                                    <th>
                                    </th>
                                    <th>@lang('LEFT')</th>
                                    <th>@lang('RIGHT')</th>
                                </tr>

                                {{-- <tr>
                                    <td>@lang('Current BV')</td>
                                    <td><span class="lbv"></span></td>
                                    <td><span class="rbv"></span></td>
                                </tr> --}}
                                {{-- <tr>
                                    <td>@lang('Free Member')</td>
                                    <td><span class="lfree"></span></td>
                                    <td><span class="rfree"></span></td>
                                </tr> --}}

                                <tr>
                                    <td>@lang('MP Member')</td>
                                    <td><span class="lpaid"></span></td>
                                    <td><span class="rpaid"></span></td>
                                </tr>
                            </table>
                            {{-- <hr> --}}
                            {{-- <span class="mt-4">
                                <div class="form-group d-none" id="is_true">
                                    <label class="form-control-label font-weight-bold">@lang('Stockiest Status')
                                    </label><br>
                                    <input type="checkbox" id="is_stockiest_true" data-width="100%"
                                        data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle"
                                        data-on="Active" data-off="Deactive" name="is_stockiest" checked>
                                </div>
                                <div class="form-group d-none" id="is_false">
                                    <label class="form-control-label font-weight-bold">@lang('Stockiest Status')
                                    </label><br>
                                    <input type="checkbox" id="is_stockiest_false" data-width="100%"
                                        data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle"
                                        data-on="Active" data-off="Deactive" name="is_stockiest">
                                </div>
                            </span> --}}
                            <hr>
                            {{-- <a href="#" class="mt-4 btn btn--warning btn-block btn-sm btnAddSubs">Send Pin</a>
                            <hr> --}}
                            <a href="" class=" btn btn--primary btn-block btn-sm tree_url">See Tree</a>
                            <hr>
                            <form action="{{ route('user.sponsor.set') }}" method="POST" id="formAddDownline"
                                class="d-none">
                                @csrf
                                <input type="hidden" name="back" value="{{ Request::url() }}">
                                <input type="hidden" name="upline" id="upline">
                                <select name="postion" id="position" class="form-select form-control">
                                    <option selected disabled>--Select Position</option>
                                    <option value="1" id="s_kiri">Kiri</option>
                                    <option value="2" id="s_kanan">Kanan</option>
                                </select>
                                <button type="submit" class=" btn btn--success btn-block btn-sm mt-2">Add
                                    Downline</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Send PIN')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{-- <form action="{{ route('admin.users.addSubBalance', $user->id) }}" method="POST"> --}}
                <form action="" method="POST" id="formSubBalance">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            {{-- <div class="form-group col-md-12">
                                <input type="checkbox" data-width="100%" data-height="44px" data-onstyle="-success"
                                    data-offstyle="-danger" data-toggle="toggle" data-on="Add Balance"
                                    data-off="Subtract Balance" name="act" checked>
                            </div> --}}


                            <div class="form-group col-md-12">
                                <label>@lang('PIN')<span class="text-danger">*</span> <span class="text-secondary">
                                        <br>
                                        Sisa Pin =
                                        {{ auth()->user()->pin }}</span></label>
                                <div class="input-group has_append">
                                    <input type="text" name="pin" class="form-control"
                                        placeholder="Please provide positive amount">
                                    <div class="input-group-append">
                                        <div class="input-group-text">PIN</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--success">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            // $('.btnCopy').on('click', function() {

            //     var copyText = $(this).data('url');



            //     navigator.clipboard.writeText(copyText);
            //     // $(this).text('URL DISALIN')
            // })

            const userID = $('.active-user-none').data('id');

            $('.btnSeeUser').on('click', function() {
                let username = $(this).data('username');
                let url = `{{ url('user/tree/${username}') }}`
                window.location.replace(url)
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.btnUser').on('click', function(e) {
                e.preventDefault();
                let upline = $(this).data('upline');
                let pos = $(this).data('pos');
                let backUrl = "{{ Request::url() }}";
                const postUrl = "{{ route('user.sponsor.set.update') }}";

                $.ajax({
                    type: 'POST',
                    url: postUrl,
                    data: {
                        back: backUrl,
                        upline: upline,
                        postion: pos
                    },
                    success: function(rs) {
                        console.log(rs);
                        if (rs.sts = 200) {
                            window.location.replace(rs.url);
                        }
                    }
                });

                console.log(upline);
                console.log(pos);
            })
            // $('.showDetails').on('click', function() {
            //     var modal = $('#exampleModalCenter');
            //     let id = $(this).data('id');
            //     $('#is_stockiest_true').attr('data-id');
            //     $('#is_stockiest_false').attr('data-id');
            //     $('#is_true').addClass('d-none');
            //     $('#is_false').addClass('d-none');
            //     if (id == userID) {
            //         $('.btnAddSubs').addClass('d-none');
            //     } else {
            //         $('.btnAddSubs').removeClass('d-none');
            //     }
            //     $('.tree_name').text($(this).data('name'));
            //     $('.tree_url')
            //         .attr({
            //             "href": $(this).data('treeurl')
            //         });
            //     $('.tree_status').text($(this).data('status'));
            //     $('.tree_plan').text($(this).data('plan'));
            //     $(
            //         '.tree_phone').text('+' + $(this).data('mobile'));
            //     $('.tree_email').text($(this).data(
            //         'email'));
            //     $('.tree_bro').text($(this).data('bro'));
            //     $('.tree_image').attr({
            //         "src": $(this).data('image')
            //     });
            //     $('.user-details-header').removeClass('Paid');
            //     $('.user-details-header').removeClass('Free');
            //     $('.user-details-header').addClass($(this).data('status'));
            //     $('.tree_ref').text($(this).data(
            //         'refby'));
            //     $('.lbv').text($(this).data('lbv'));
            //     $('.rbv').text($(this).data('rbv'));
            //     $('.lpaid')
            //         .text($(this).data('lpaid'));
            //     $('.rpaid').text($(this).data('rpaid'));
            //     $('.lfree').text($(this)
            //         .data('lfree'));
            //     $('.rfree').text($(this).data('rfree'));
            //     $('#exampleModalCenter').modal(
            //         'show');
            //     $('.btnAddSubs').attr('data-id', id);
            //     $('#is_stockiest_true').attr('data-id', id);
            //     $('#is_stockiest_false').attr('data-id', id);

            //     const is_stockiest = $(this).data('is_stockiest');
            //     if (is_stockiest) {
            //         $('#is_true').removeClass('d-none');
            //         $('#is_false').addClass('d-none');
            //     } else {
            //         $('#is_true').addClass('d-none');
            //         $('#is_false').removeClass('d-none');

            //     }
            //     const kiri = $(this).data('lpaid');
            //     const kanan = $(this).data('rpaid');
            //     if (kiri < 1 || kanan < 1) {
            //         $('#formAddDownline').removeClass('d-none');
            //         $('#upline').val($(this).data('bro'));
            //         if (kiri > 0) {
            //             $('#s_kiri').attr('disabled', 'disabled');
            //         }
            //         if (kanan > 0) {
            //             $('#s_kanan').attr('disabled', 'disabled');
            //         }
            //     } else {
            //         $('#formAddDownline').addClass('d-none');
            //     }

            // });
            $('.btnAddSubs').on('click', function() {
                let userID = $(this).data('id');
                const url = "{{ url('user/send-pin') }}" + '/' + userID;

                $('#exampleModalCenter').modal('hide');
                $('#addSubModal').modal('show');
                $('#formSubBalance').attr('action', url)
            })
            $('#is_stockiest_true').on('change', function() {
                const status = 0;
                const id = $(this).data('id');
                $.ajax({
                    url: "{{ url('user/update-stockiest') }}" + '/' + id,
                    cache: false,
                    success: function(rs) {
                        location.reload();
                    }
                });
            })
            $('#is_stockiest_false').on('change', function() {
                const status = 1;
                const id = $(this).data('id');
                $.ajax({
                    url: "{{ url('user/update-stockiest') }}" + '/' + id,
                    cache: false,
                    success: function(rs) {
                        if (rs.status == 200) {
                            location.reload();
                        } else {
                            alert(rs.msg);
                        }
                    }
                });
            });
            $('#btnKiri').on('click', function() {
                $("#btnKanan").removeClass('bg-success');
                $("#btnKanan").html('<i class="fas fa-copy mr-2"> Salin');
                var kiri = $('#kiri').val();
                navigator.clipboard.writeText(kiri);
                $(this).addClass('bg-success');
                $(this).html('<i class="fas fa-copy mr-2"> Data Disalin');
            });
            $('#btnKanan').on('click', function() {
                $("#btnKiri").removeClass('bg-success');
                $("#btnKiri").html('<i class="fas fa-copy mr-2"> Salin');
                var kanan = $('#kanan').val();
                navigator.clipboard.writeText(kanan);
                $(this).addClass('bg-success');
                $(this).html('<i class="fas fa-copy mr-2"> Data Disalin');
            })
        })(jQuery);
    </script>
@endpush
