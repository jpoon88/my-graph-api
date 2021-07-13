@extends('layouts.app')

@section('content')
    <div class="flex justify-center">
        <div class="w-8/12 bg-white p-6 rounded-lg">
            Dashboard

            
        </div>

       
    </div>
    <div class="flex justify-center">
        <div class="w-8/12 bg-white p-6 rounded-lg">
        
        @if( session('userName') )
            <h4>Welcome {{ session('userName') }}!</h4>
        @endif
        </div> 
     </div>


@endsection