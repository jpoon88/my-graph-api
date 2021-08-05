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

<!-- start -->
  <div class="alert alert-default-danger error-date-alert" style="display:none">
    <span class="h5"><i class="icon fas fa-exclamation-circle"></i>
    <span class="error-date">
      Conversations must be scheduled every four months, at minimum.
    </span>
  </div>
  <form id="conversation_form" action="{{ route('conversation.store') }}" method="POST">
      @csrf
      <div class="row">
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
            <select class="livesearch form-control form-select-lg " name="eventAttendees[]" multiple='multiple'>

                @foreach(  ($eventAttendees ?? []) as $p)
                    <option value="{{ $p->getUserPrincipalName() }}" selected>{{ $p->getDisplayName() }}</option>
                @endforeach

            </select>
              <small class="text-danger error-participant_id"></small>
              </div>

                 <div class="col-6 col-md-6 mt-1">
                  <label> Date</label>
                  <input class="error-date" type="date" name="eventDate" value="{{  old('eventDate') ?? $eventDate  }}" />
                  <!-- :min="Carbon\Carbon::now()->toDateString()" required -->
                  @error('eventDate')
                  <small class="text-danger error-time">{{ $message }}</small>
                  @enderror
 
                 </div>
              <div class="col-6 col-md-6 mt-1">
                    <label> Time</label>
                  <input  class="error-date" type="time" name="eventTime" value="{{ old('eventTime') ?? $eventTime  }}" />
                  <!-- step="900" -->                   
                  @error('eventTime')                 
                <small class="text-danger error-time">{{ $message }}</small>
                @enderror              
              </div>     
             
          <div class="col-6 col-md-6">
             <label> Supporting Material</label>
              <div class="card p-3">
                  <span>Supporting Material</span>
                  <a href="https://www2.gov.bc.ca/gov/content/careers-myhr/all-employees/career-development/myperformance/myperformance-guides" target="_blank">This is a placeholder for a link to relevant contacts and support documentation for this process. Content to follow.</a>
              </div>
          </div>
          <div class="col-12 text-left pb-5 mt-3">
              <button type="submit" class="btn-md btn-submit"> Find Meeting Time</button>
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
</script>    

@endsection    