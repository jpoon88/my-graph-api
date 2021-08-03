@extends('layouts.app')

@section('content')

@auth
<h2>Is sign in </h2>
@endauth
@guest
<h2>Is a gues (not signin) </h2>    
@endguest



    <div class="flex justify-center">
        <div class="w-8/12 bg-white p-6 rounded-lg">
            Home
        </div>


    </div>

    <div class="flex justify-center">
        <div class="w-8/12 bg-white p-6 rounded-lg">
            <h1>{{  session()->get('userEmail') }}</h1>        
        @if( session('userName') )
            <h4>Welcome {{ Auth()->User()->name }}!</h4>
        @endif
    </div> 

@endsection