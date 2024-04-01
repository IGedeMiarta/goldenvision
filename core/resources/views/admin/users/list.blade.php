@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th scope="col">@lang('User')</th>
                                    <th scope="col">@lang('Username')</th>
                                    {{-- <th scope="col">@lang('MP')</th> --}}
                                    <th scope="col">@lang('Email')</th>
                                    <th scope="col">@lang('Phone')</th>
                                    <th scope="col">@lang('Joined At')</th>
                                    <th scope="col">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td data-label="@lang('User')">
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ getImage(imagePath()['profile']['user']['path'] . '/' . $user->image, imagePath()['profile']['user']['size']) }}"
                                                        alt="@lang('image')">
                                                </div>
                                                <span class="name">{{ $user->fullname }}</span>
                                            </div>
                                        </td>
                                        <td data-label="@lang('Username')"><a
                                                href="{{ route('admin.users.detail', $user->id) }}">{{ $user->username }}</a>
                                        </td>
                                        {{-- <td data-label="@lang('MP')">
                                            @if ($user->no_bro == 0)
                                                <small>Not subscribed yet</small>
                                            @else
                                                {{ $user->no_bro }}
                                            @endif
                                        </td> --}}
                                        <td data-label="@lang('Email')">{{ $user->email }}</td>
                                        <td data-label="@lang('Phone')">{{ $user->mobile }}</td>
                                        <td data-label="@lang('Joined At')">{{ showDateTime($user->created_at) }}</td>
                                        <td data-label="@lang('Action')">
                                            <a href="{{ route('admin.users.detail', $user->id) }}" class="icon-btn"
                                                data-toggle="tooltip" data-original-title="@lang('Details')">
                                                <i class="las la-desktop text--shadow"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($empty_message) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ paginateLinks($users) }}
                </div>
            </div><!-- card end -->
        </div>


    </div>
@endsection



@push('breadcrumb-plugins')
    <div class="row">
        <div class="col-md-10 col-9">
            <form
                action="{{ route('admin.users.dateSearch', $scope ?? str_replace('admin.users.', '', request()->route()->getName())) }}"
                method="GET" class="form-inline float-sm-right bg--white ml-3 mr-3">
                <div class="input-group has_append ">
                    <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - "
                        data-language="en" class="datepicker-here form-control bg-white text--black"
                        data-position='bottom right' placeholder="@lang('Min Date - Max date')" autocomplete="off" readonly
                        value="{{ @$dateSearch }}">
                    <input type="hidden" name="search" value="{{ @$search }}">
                    <div class="input-group-append">
                        <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
            <form
                action="{{ route('admin.users.search', $scope ?? str_replace('admin.users.', '', request()->route()->getName())) }}"
                method="GET" class="form-inline float-sm-right bg--white">
                <div class="input-group has_append">
                    <input type="text" name="search" class="form-control" placeholder="@lang('Username or email')"
                        value="{{ $search ?? '' }}">
                    <div class="input-group-append">
                        <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-2 col-3">
            <form action="{{ route('admin.users.export.all') }}" method="GET" class="form-inline float-sm-right">
                <input hidden type="text" name="search" class="form-control" placeholder="@lang('Username or email')"
                    value="{{ $search ?? null }}">
                <input hidden type="text" name="date" class="form-control" placeholder="@lang('Username or email')"
                    value="{{ $dateSearch ?? null }}">
                <input hidden type="text" name="page" class="form-control" placeholder="@lang('Username or email')"
                    value="{{ $page_title ?? '' }}">
                <button class="btn btn-success" type="submit"><i class="fas fa-file-excel"></i> Export</button>
            </form>
        </div>
    </div>
@endpush


@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush


@push('script')
    <script>
        'use strict';
        (function($) {
            if (!$('.datepicker-here').val()) {
                $('.datepicker-here').datepicker();
            }
        })(jQuery)
    </script>
@endpush
