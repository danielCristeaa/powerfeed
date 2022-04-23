@extends('layouts.app')

@section('content')
    <feedslist-component
        :user="{{!empty(Auth::user()) ? Auth::user() : 'null'}}"
        :base-url="{{ json_encode(config('app.url')) }}">
    </feedslist-component>
@endsection
