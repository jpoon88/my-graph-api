<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TokenStore\TokenCache;


class CalendarController extends Controller
{
    //
    public function __contruct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
    $viewData = $this->loadViewData();

    // Get the access token from the cache
    $tokenCache = new TokenCache();
    $accessToken = $tokenCache->getAccessToken();
    //$accessToken = $tokenCache->getAccessTokenForApplication();

    // Create a Graph client
    $graph = new Graph();
    $graph->setAccessToken($accessToken);

    // Get user's timezone
    //$timezone = TimeZones::getTzFormatWindows($viewData['userTimeZone']);

    $queryParams = array(
      '$select' => 'subject,organizer,start,end,recurrence',
      '$orderby' => 'createdDateTime DESC'
    );

    // test Me API
    // $getEventsUrl = '/me/';
    // $result = $graph->createRequest('GET', $getEventsUrl)
    //   ->execute();

    // Append query parameters to the '/me/events' url
    $getEventsUrl = '/me/events?'.http_build_query($queryParams);

    // jpoon test scenario
    // 2.  other person
    //$getEventsUrl = '/users/'.$request->uid .'/events?'.http_build_query($queryParams);
    //$getEventsUrl = '/me/calendars/events?'.http_build_query($queryParams);
    // $getEvenetUrl =  "/me/events?%24select=subject%2Corganizer%2Cstart%2Cend&%24orderby=createdDateTime+DESC"


    $events = $graph->createRequest('GET', $getEventsUrl)
      ->setReturnType(Model\Event::class)
      ->execute();

    // 
    //dd($events);

    $viewData['events'] = $events;
    return view('event', $viewData);
  }


 
  // List all the calendars for the input users 
  public function calendars(Request $request) 
  {   

    $viewData = $this->loadViewData();

    // Get the access token from the cache
    $tokenCache = new TokenCache();
    $accessToken = $tokenCache->getAccessToken();
    //$accessToken = $tokenCache->getAccessTokenForApplication();

    // Create a Graph client
    $graph = new Graph();
    $graph->setAccessToken($accessToken);


      // test User  API https://graph.microsoft.com/v1.0/users
      // $getUsersUrl = '/users/' . $request->uid;
      // $results = $graph->createRequest('GET', $getUsersUrl)
      // ->setReturnType(Model\Calendar::class)
      //       ->execute();

      
      // GET https://graph.microsoft.com/v1.0/users/{Alex-userId | Alex-userPrincipalName}/calendar

      // $getUsersUrl ="https://graph.microsoft.com/v1.0/me/calendarview?startdatetime=2021-06-30T23:34:25.575Z&enddatetime=2021-07-31T23:34:25.575Z";
      $getUsersUrl ="https://graph.microsoft.com/v1.0/users/$request->uid/calendars";
      //$getUsersUrl ="https://graph.microsoft.com/v1.0/me/calendars";
      //$getUsersUrl ="https://graph.microsoft.com/v1.0/users/$request->uid/calendar/calendarPermissions";

      $results = $graph->createRequest('GET', $getUsersUrl)
      ->setReturnType(Model\Calendar::class)
            ->execute();

      $viewData['uid'] = $request->uid;
      $viewData['calendars'] = $results;
      return view('calendar', $viewData);
  }

  public function test(Request $request) 
  {

    $viewData = $this->loadViewData();

    // Get the access token from the cache
    $tokenCache = new TokenCache();
    //$accessToken = $tokenCache->getAccessToken();
    $accessToken = $tokenCache->getAccessTokenForApplication();

    // Create a Graph client
    $graph = new Graph();
    $graph->setAccessToken($accessToken);


      // test User  API https://graph.microsoft.com/v1.0/users
      // $getUsersUrl = '/users/' . $request->uid;
      // $results = $graph->createRequest('GET', $getUsersUrl)
      // ->setReturnType(Model\Calendar::class)
      //       ->execute();

      
      // GET https://graph.microsoft.com/v1.0/users/{Alex-userId | Alex-userPrincipalName}/calendar

      // $getUsersUrl ="https://graph.microsoft.com/v1.0/me/calendarview?startdatetime=2021-06-30T23:34:25.575Z&enddatetime=2021-07-31T23:34:25.575Z";
      //$getUsersUrl ="https://graph.microsoft.com/v1.0/users/$request->uid/calendar";
      //$getUsersUrl ="https://graph.microsoft.com/v1.0/me/calendars";
      $getUsersUrl ="https://graph.microsoft.com/v1.0/users/$request->uid/calendar/calendarPermissions";

      $results = $graph->createRequest('GET', $getUsersUrl)
      ->setReturnType(Model\Calendar::class)
            ->execute();
      
      dd($results);      
      
  }


}
