@extends('layouts.app')

@section('content')

<h1>User</h1>

<table class="table">
  <thead>
    <tr>
      <th scope="col">Display Name</th>
      <th scope="col">Business Phones</th>
      <th scope="col">Office Location</th>
      <th scope="col">Last Password Change</th>
      <th scope="col">Principal Name</th>
      <th scope="col">ID</th>
    </tr>
  </thead>
  <tbody>
    @isset($members)
      @foreach($members as $member)
        <tr>
          <td>{{ $member->getDisplayName() }}</td>
          <td>{{ implode("|", $member->getBusinessPhones() ) }}</td>
          <td>{{ $member->getOfficeLocation() }}</td>
          <td>{{ \Carbon\Carbon::parse($member->getLastPasswordChangeDateTime())->format('n/j/y g:i A') }}&nbsp;</td>          
          <td>{{  $member->getUserPrincipalName() }}</td>
          <td>{{  $member->getId() }}</td>
          <td>
            <form action="{{ route('calendars') }}" method="post" class="p-3 inline">
              @csrf
              <input id="uid" name="uid" type="hidden" value="{{ $member->getId() }}">
              <button type="submit">Calendars</button>
          </form>
          </td>
          <td>
            <form action="{{ route('events') }}" method="post" class="p-3 inline">
              @csrf
              <input id="uid" name="uid" type="hidden" value="{{ $member->getId() }}">
              <button type="submit">Events</button>
          </form>
          </td>
          <td>
          <form action="{{ route('test') }}" method="post" class="p-3 inline">
              @csrf
              <input id="uid" name="uid" type="hidden" value="{{ $member->getId() }}">
              <button type="submit">Test</button>
          </form>
          <td>
        </tr>
      @endforeach
    @endif
  </tbody>
</table>

@endsection