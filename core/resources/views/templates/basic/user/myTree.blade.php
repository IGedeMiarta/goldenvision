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
    <div class="row d-flex justify-content-center">

        <div class="mr-4 col-md-3 card mb-3">

            <div class="card-body">
                <h4 class="text-center">Last Level Left</h4>
            </div>

            <div class="card-body">
                <div class="row text-center justify-content-center">
                    <!-- <div class="col"> -->
                    <div class="w-1 ">
                        @php echo showLastUserLeft(); @endphp
                    </div>
                </div>
            </div>
            {{-- <div class="card-footer justify-center text-center">
                <button class="btn btn-primary btn-block btnCopy"
                    data-url="{{ url('user/plan') . '?sponsor=' . auth()->user()->username . '&pos=1' }}">COPY URL</button>
            </div> --}}
        </div>
        <div class="mr-4 col-md-3 card mb-3">
            <div class="card-body">
                <h4 class="text-center">Last Level Right</h4>
            </div>
            <div class="card-body">
                <div class="row text-center justify-content-center">
                    <!-- <div class="col"> -->
                    <div class="w-1 ">
                        @php echo showLastUserRight(); @endphp
                    </div>
                </div>
            </div>
            {{-- <div class="card-footer justify-center text-center">
                <button class="btn btn-warning btn-block btnCopy"
                    data-url="{{ url('user/plan') . '?sponsor=' . auth()->user()->username . '&pos=2' }}">COPY URL</button>
            </div> --}}
        </div>
        <div class="col-md-12 card card-tree">
            <div class="card-body">

            </div>
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
                    @php echo showSingleUserNoLine($tree['e']); @endphp
                </div>
                <!-- <div class="col"> -->
                <div class="w-4  ">
                    @php echo showSingleUserNoLine($tree['f']); @endphp
                </div>
                <!-- <div class="col"> -->
                <div class="w-4  ">
                    @php echo showSingleUserNoLine($tree['g']); @endphp
                </div>
                <!-- <div class="col"> -->

            </div>
            {{-- <div class="row text-center justify-content-center llll">
            <!-- <div class="col"> -->
            <div class="w-8 ">
                @php echo showSingleUserinTree($tree['h']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8 ">
                @php echo showSingleUserinTree($tree['i']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8 ">
                @php echo showSingleUserinTree($tree['j']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8 ">
                @php echo showSingleUserinTree($tree['k']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8 ">
                @php echo showSingleUserinTree($tree['l']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8 ">
                @php echo showSingleUserinTree($tree['m']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8 ">
                @php echo showSingleUserinTree($tree['n']); @endphp
            </div>
            <!-- <div class="col"> -->
            <div class="w-8 ">
                @php echo showSingleUserinTree($tree['o']); @endphp
            </div>
        </div> --}}

        </div>
    </div>
    {{-- <div class="card mb-4">
        <div class="card-header">
            Share Referals
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="kiri" class="col-sm-2 col-form-label">Kiri</label>
                <div class="col-sm-10 input-group">
                    <input type="text" class="form-control" id="kiri"
                        value="{{ url('user/plan?') . 'sponsor=' . auth()->user()->no_bro . '&position=1' }}">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text" id="btnKiri"> <i class="fas fa-copy mr-2"></i>
                            Salin</button>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="kiri" class="col-sm-2 col-form-label">Kanan</label>
                <div class="col-sm-10 input-group">
                    <input type="text" class="form-control" id="kanan"
                        value="{{ url('user/plan?') . 'sponsor=' . auth()->user()->no_bro . '&position=2' }}">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text" id="btnKanan"> <i class="fas fa-copy mr-2"></i>
                            Salin</button>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}



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
                const postUrl = "{{ route('user.sponsor.set') }}";

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
@push('breadcrumb-plugins')
    <form action="{{ route('user.other.tree.search') }}" method="GET" class="form-inline float-right bg--white">
        <div class="input-group has_append">
            <input type="text" name="username" class="form-control" placeholder="@lang('Search by username')">
            <div class="input-group-append">
                <button class="btn btn--success" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
@endpush
