@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<x-page-wrapper
    title="Dashboard"
    alert="You are logged in as {{ strtoupper(auth()->user()->role) }}."
    alertType="success" />
@endsection