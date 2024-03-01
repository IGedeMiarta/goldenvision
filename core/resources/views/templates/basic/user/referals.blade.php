@extends($activeTemplate . 'user.layouts.app')

@section('panel')
    <div class="row">

        <div class="col-lg-12">
            <div class="card b-radius--10 ">

                <div class="tab-content" id="myTabContent">
                    @foreach ($referrals as $key => $referral)
                        <div class="tab-pane fade @if ($key == 1) show active @endif"
                            id="tab-{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
                            @if (0 < count($referral))
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <tr>
                                            <th>No</th>
                                            {{-- <th>MP No</th> --}}
                                            <th>Username</th>
                                            <th>Phone</th>
                                            <th>Position <br> (By Upline | By Referral)</th>
                                            <th>Join date</th>
                                        </tr>
                                        <tbody>
                                            @php
                                                $no = 1;
                                            @endphp
                                            @foreach ($referral as $t)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    {{-- <td>
                                                        {{ $t->no_bro }}
                                                    </td> --}}
                                                    <td>
                                                        <p class="user-name mb-0">
                                                            {{ strtolower($t->username) }}
                                                        </p>

                                                    </td>
                                                    <td>
                                                        {{ '(+62) ' . strtolower($t->mobile) }}

                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <span class="col-md-4" style="text-align: right">
                                                                {!! $t->positionUpline() !!}
                                                            </span>
                                                            <span class="col-md-4">
                                                                |
                                                            </span>
                                                            <span class="col-md-4" style="text-align: left">
                                                                {!! $t->position() !!}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {!! date('M d Y', strtotime($t->created_at)) !!}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="table-responsive nowrap">
                                    <table class="table mb-0">
                                        <tr>
                                            <th>No</th>
                                            <th>MP No</th>
                                            <th>Username</th>
                                            <th>Phone</th>
                                            <th>Join date</th>
                                        </tr>
                                        <tbody>
                                            <tr>
                                                <th colspan="5" class="text-center">No
                                                    Record</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                        </div>
                    @endforeach

                    <div class="tab-pane fade" id="tab-2" role="tabpanel" aria-labelledby="profile-tab">tab2 Lorem ipsum
                        dolor sit amet.</div>
                    <div class="tab-pane fade " id="tab-3" role="tabpanel" aria-labelledby="contact-tab">tab3
                        Lorem,
                        ipsum.</div>
                </div>
            </div>
        </div>
    </div>
@endsection
