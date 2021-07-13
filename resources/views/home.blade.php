@extends('layouts.app')

@section('content')
    <div class="flex justify-center">
        <div class="w-8/12 bg-white p-6 rounded-lg">
            Home
        </div>


    </div>

    <div class="flex justify-center">
        <div class="w-8/12 bg-white p-6 rounded-lg">
            <h1>{{  session()->get('userEmail') }}</h1>        
        @if( session('userName') )
            <h4>Welcome {{ session('userName') }}!</h4>
        @endif
    </div> 

@endsection