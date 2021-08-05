@extends('layouts.app')

@section('content')
  
    <div class="flex justify-center">
        <div class="w-8/12 bg-white p-6 rounded-lg">
        
        </div> 
     </div>


     <div class="container">

        <div class="row justify-content-center">

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>
    
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
    
                            <div>
                                Dashboard
                            </div>
                        <div>    
                            @if( session('userName') )
                                <h4>Welcome {{ session('userName') }}!</h4>
                            @endif
                        </div>
                


                    </div>
                </div>
            </div>
        </div>
    </div>     


@endsection