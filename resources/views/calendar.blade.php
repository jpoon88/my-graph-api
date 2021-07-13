@extends('layouts.app')

@section('content')

    <h1>Calendar</h1>
    <table class="border-separate border border-gray-800 ">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">isDefaultCalendar</th>
          <th scope="col">canEdit</th>
          <th scope="col">canShare</th>
          <th scope="col">isRemovable</th>
          <th scope="col">Owner</th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody>
        @isset($calendars)
          @foreach($calendars as $calendar)
            <tr>
              <td>{{ $calendar->getName() }}</td>
              <td>{{ $calendar->getIsDefaultCalendar() }}</td>
              <td>{{ $calendar->getCanEdit() }}</td>
              <td>{{ $calendar->getCanShare() }}</td>
              <td>{{ $calendar->getIsRemovable() }}</td>
              <td>{{ $calendar->getOwner()->getName()  }}</td>
              <td>
              <form action="{{ route('calendar_events') }}" method="post" class="p-3 inline">
                @csrf
                
                <input id="uid" name="uid" type="hidden" value="{{ $uid }}">
                <input id="cid" name="cid" type="hidden" value="{{ $calendar->getId() }}">

                <button type="submit">Event</button>
            </form>
          </td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
    

@endsection    