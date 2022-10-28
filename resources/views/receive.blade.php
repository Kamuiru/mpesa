@extends('layout')
@section('content')
@php
$response = json_decode(file_get_contents("php://input"))
echo $response
@endphp
@endsection