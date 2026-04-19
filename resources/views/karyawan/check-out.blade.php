@extends('layouts.dashboard', [
    'role' => 'karyawan',
    'userName' => 'Dina Andini',
    'pageTitle' => 'Check-Out',
])

@section('content')
    @include('partials.presensi.location-attendance', [
        'prefix' => 'checkout',
        'modeTitle' => 'Check Out',
        'buttonLabel' => 'Check Out Sekarang',
        'checkInInfo' => '08:32',
    ])
@endsection
