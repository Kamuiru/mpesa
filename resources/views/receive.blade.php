@extends('layout')
@section('content')
@php
$response = file_get_contents("php://input");
$response = json_decode($response, true);
error_log(print_r($response, true, 0));
@endphp
@endsection