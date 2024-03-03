@extends($activeTemplate . 'user.layouts.app')
@include($activeTemplate . 'user.dashboard.styleDashboard')

@push('style')
    <style>
        .imgProfit {
            width: 19ch;
            margin-top: 165px;
        }
    </style>
@endpush

@section('panel')
    @include($activeTemplate . 'user.dashboard.newcard')
    {{-- @include($activeTemplate . 'user.dashboard.cardInfo') --}}
@endsection
