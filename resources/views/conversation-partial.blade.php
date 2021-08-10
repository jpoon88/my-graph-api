<div class='h5 text-secondary'>
    Here are the suggest meeting time, please select your choice: 
</div>

@isset( $emptySuggestionsReason )
<span> {{  $emptySuggestionsReason }}</span>        
@endisset

@isset( $suggestionsByDate )

    @foreach($suggestionsByDate as $key => $suggestions ) 
    <div class='mt-3'>
        <span class='text-success'>{{  \Carbon\Carbon::parse($key)->format('F jS, Y (l)')  }}</span>
    </div>
    <ul class='nav'>
        @foreach( $suggestions as $suggestion ) 
        <li class='nav-item'>
            <a class="nav-link" 
                    data-date='{{ \Carbon\Carbon::parse($suggestion->getMeetingTimeSlot()->getStart()->getDateTime())->format('Y-m-d') }}' 
                    data-time='{{ \Carbon\Carbon::parse($suggestion->getMeetingTimeSlot()->getStart()->getDateTime())->format('H:i') }}' 
            href="#" onclick="updateDateTime(this);return false">
            {{  \Carbon\Carbon::parse($suggestion->getMeetingTimeSlot()->getStart()->getDateTime())->format('g:i A')  }} 
            </a>
            </li>

        @endforeach
    </ul>
    @endforeach

@endisset
