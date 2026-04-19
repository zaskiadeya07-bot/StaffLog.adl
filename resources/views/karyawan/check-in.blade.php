@extends('layouts.dashboard', [
    'role' => 'karyawan',
    'userName' => 'Dina Andini',
    'pageTitle' => 'Check-In',
])

@section('content')
    @include('partials.presensi.location-attendance', [
        'prefix' => 'checkin',
        'modeTitle' => 'Check In',
        'buttonLabel' => 'Check In Sekarang',
    ])
@endsection
