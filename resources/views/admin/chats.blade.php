@extends('adminlte::page')

@section('title', 'Chats')

@section('content')
    @livewire('chat')
@stop
@section('css') <link rel="stylesheet" href="{{ asset('css/style.css') }}"> @stop

@push('scripts')
<script src="{{ mix('js/app.js') }}"></script>
@endpush
