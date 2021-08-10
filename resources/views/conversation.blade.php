@extends('layouts.app')


@section('javascript')
@endsection
@section('stylesheet')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
@endsection
  

@section('content')

<div class="container mt-5">
    <h2>Demo - MS Graph </h2>

    <!-- Modal -->
    <div class="modal fade" id="empModal" role="dialog">
        <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Suggest Meeting Time</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div><!-- modal-header -->
            <div class="modal-body">
    
            </div><!-- modal-body -->
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div><!-- modal-footer -->
        </div><!--modal-content -->
        </div><!-- modal-dialog -->
    </div><!-- modal -->

  <!-- start -->
  <div class="alert alert-default-danger error-date-alert" style="display:none">
    <span class="h5"><i class="icon fas fa-exclamation-circle"></i>
    <span class="error-date">
      Conversations must be scheduled every four months, at minimum.
    </span>
  </div>
  <form id="conversation_form" action="{{ route('conversation.store') }}" method="POST">
      @csrf
      <div class="row ">
          <div class="col-6 col-md-6">
              <label>Topic </label>
                 <select class="form-control" name="conversation_topic_id" required="">
                    <option value="1">Performance Check-In</option>
                    <option value="2">Goal Setting</option>
                    <option value="3">Career Conversation</option>
                    <option value="4">Performance Improvement</option>
            </select>
           </div>

            <div class="col-6 col-md-6">
                <label> Participants</label>
            <select class="livesearch form-control form-select-lg " id='eventAttendees' name="eventAttendees[]" multiple='multiple'>

                @foreach(  ($eventAttendees ?? []) as $p)
                    <option value="{{ $p->getUserPrincipalName() }}" selected>{{ $p->getDisplayName() }}</option>
                @endforeach

            </select>
              
                 <small class="text-danger error-participant_id">{{ $message ?? '' }}</small>
              
            </div>

        </div>

        <div class="row mt-3">        
                 <div class="col-4 col-md-4 ">
                  <label> Date</label>
                  <input class="error-date" type="date" id='eventDate' name="eventDate" value="{{  old('eventDate') ?? $eventDate  }}" />
                  <!-- :min="Carbon\Carbon::now()->toDateString()" required -->
                  @error('eventDate')
                  <small class="text-danger error-time">{{ $message }}</small>
                  @enderror
 
                 </div>

        
              <div class="col-8 col-md-8 mt-1">
                <div class="row">
                    <div class='col-4'>
                        <label> Time</label>
                        <input  class="error-date" type="time" id='eventTime' name="eventTime" value="{{ old('eventTime') ?? $eventTime  }}" />
                        <!-- step="900" -->                   
                        @error('eventTime')                 
                            <small class="text-danger error-time">{{ $message }}</small>
                        @enderror              
                    </div>

                    <div class="col-4">
                        <label> Duration</label>
                        <select class="" name="eventDuration" id='eventDuration'> 
                            <option>Select Item</option> 
                            @foreach ( ['PT30M'=>'00:30', 'PT1H' => '01:00', 
                                        'PT1H30M'=>'01:30', 'PT2H' => '02:00'] as $key => $value) 
                                <option value="{{ $key }}" {{ ( $key == ( old('eventDuration') ?? 'PT1H') ) ? 'selected' : '' }}>  
                                    {{ $value }}  
                                </option> 
                            @endforeach     
                        </select> 
                                  <!-- step="900" -->                   
                          @error('eventTime')                 
                          <small class="text-danger error-time">{{ $message }}</small>
                        @enderror              
  
                    </div>

                    <div class="col-4 text-left">
                        <a data-id="value-in-data-id"  class="btn btn-secondary  userinfo">Suggest Meeting Time</a> 
                    </div>


                </div>
    

              </div>     
        
        </div>
        

        <div class="row mt-3">                  
          <div class="col-6 col-md-6">
             
              <div class="card p-3">
                  <span>Supporting Material</span>
                  <a href="https://www2.gov.bc.ca/gov/content/careers-myhr/all-employees/career-development/myperformance/myperformance-guides" target="_blank">This is a placeholder for a link to relevant contacts and support documentation for this process. Content to follow.</a>
              </div>
          </div>
          <div class="col-12 text-left pb-5 mt-3">
              <button type="submit" class="btn-md btn-submit"> Create Conversation Time</button>
          </div>
  
      </div>


      @isset( $emptySuggestionsReason )
        <span> {{  $emptySuggestionsReason }}</span>        
      @endisset

      @isset( $suggestions )

        <table class="table">
            <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">First</th>
                  <th scope="col">Last</th>
                  <th scope="col">Handle</th>
                </tr>
             </thead>
             <tbody> 
        @foreach($suggestions as $suggestion)
             <tr scope="col">
                 <th scope="row"></th>
                 <td> {{ $suggestion->getSuggestionReason()  }}
                </td>
                <td> 
                        @foreach($suggestion->getAttendeeAvailability() as $attendee) 
                            <span> {{ $attendee->getAttendee()->getEmailAddress()->getAddress() }}</span>
                        @endforeach                    
                </td>
                <td> {{  \Carbon\Carbon::parse($suggestion->getMeetingTimeSlot()->getStart()->getDateTime())->format('n/j/y g:i A')  }}
                </td>
                <td> {{  \Carbon\Carbon::parse($suggestion->getMeetingTimeSlot()->getEnd()->getDateTime())->format('n/j/y g:i A')  }}
                </td>
            </tr>   
        @endforeach
            </tbody> 
        </table>

      @endisset

  </form>

  <!-- end -->


</div>


<script type="text/javascript">
$('.livesearch').select2({
    placeholder: 'Select people',
    ajax: {
        url: '/ajax-autocomplete-people',
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.name,
                        id: item.id
                    }
                })
            };
        },
        cache: true
    }
});

$(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.userinfo').click(function(){
        
                var userid = $(this).data('id');
                // clear up error message if exists
                $('.error-participant_id').text('');
                // AJAX request
                $.ajax({
                    url: "{{ route('conversation.store') }}",
                    type: 'post',
                    data: {
                        eventAttendees: $('#eventAttendees').val(),
                        eventDate : $('#eventDate').val(),
                        eventTime: $('#eventTime').val(),
                        eventDuration: $('#eventDuration').val()  
                    },
                    success: function(response) { 
                        // Add response in Modal body
                        $('.modal-body').html(response);

                        // Display Modal
                        $('#empModal').modal('show'); 
                    },
                    error: function(data ) {// this are default for ajax errors 
                        if ( data.status === 422 ) {
                            var json =  $.parseJSON(data.responseText);        
                            if ( json.errors.hasOwnProperty('eventAttendees') ) {
                                $('.error-participant_id').text(  json.errors['eventAttendees'] );
                            }
                        }
                    }
                });
            });

            // $('.student_name').click(function(){

            //     var student_name = $(this).data('id');

            //  
            // });

           

        });

        function updateDateTime(el) {

                var tDate = $(el).attr("data-date");
                var tTime = $(el).attr("data-time");
                
                $('#eventDate').val( tDate );
                $('#eventTime').val( tTime );

                // Close  Modal
                $('#empModal').modal('hide');

                // Animate fields were updated
                $("#eventDate").fadeOut(500).fadeIn(500);
                $("#eventTime").fadeOut(500).fadeIn(500);
                //$('#eventDate').animate({borderColor:'red'}, 400).delay(400).animate({borderColor:'black'}, 1000);

            }


</script>    

@endsection    