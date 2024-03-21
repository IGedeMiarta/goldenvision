@extends($activeTemplate . 'user.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body">
                    Referrals Active User
                </div>
                <div class="card-body">
                    <div class="table-responsive nowrap">
                        <table class="table mb-0 table-bordered">
                            <tr>
                                <th>No</th>
                                <th>MP No</th>
                                <th>Username</th>
                                <th>Phone</th>
                                <th>Join date</th>
                            </tr>
                            <tbody>
                                @forelse ($referrals_active as $t)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
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
                                @empty
                                <tr>
                                    <th colspan="5" class="text-center">No
                                        Record</th>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mt-5">
            <div class="card b-radius--10 ">
                <div class="card-body">
                    Referrals Free User
                </div>
                <div class="card-body">
                    <div class="table-responsive nowrap">
                    <table class="table mb-0 table-bordered">
                        <tr>
                            <th>No</th>
                            <th>MP No</th>
                            <th>Username</th>
                            <th>Phone</th>
                            <th>Join date</th>
                        </tr>
                        <tbody>
                            @forelse ($referrals_free as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <p class="user-name mb-0">
                                        {{ strtolower($item->username) }}
                                    </p>

                                </td>
                                <td>
                                    {{ '(+62) ' . strtolower($item->mobile) }}

                                </td>
                                <td>
                                    <div class="row">
                                        <span class="col-md-4" style="text-align: right">
                                            {!! $item->positionUpline() !!}
                                        </span>
                                        <span class="col-md-4">
                                            |
                                        </span>
                                        <span class="col-md-4" style="text-align: left">
                                            {!! $item->position() !!}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    {!! date('M d Y', strtotime($item->created_at)) !!}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <th colspan="5" class="text-center">No
                                    Record</th>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
