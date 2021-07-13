<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TokenStore\TokenCache;


class EventController extends Controller
{
    
    //
    public function list(Request $request)
    {
        $viewData = $this->loadViewData();

// Get the access token from the cache
        $tokenCache = new TokenCache();
        if ( env('OAUTH_APP_PERMISSION') ) {
            $accessToken = $tokenCache->getAccessTokenForApplication();
        } else  {
            $accessToken = $tokenCache->getAccessToken();
        }


// Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $queryParams = array(
            '$select' => 'subject,organizer,start,end,recurrence',
            '$orderby' => 'createdDateTime DESC',
        );

// test Me API
        // $getEventsUrl = '/me/';
        // $result = $graph->createRequest('GET', $getEventsUrl)
        //   ->execute();

// Append query parameters to the '/me/events' url
        $getEventsUrl = '/me/events?' . http_build_query($queryParams);

// jpoon test scenario
        // 2.  other person
        $getEventsUrl = '/users/' . $request->uid . '/events?' . http_build_query($queryParams);
//$getEventsUrl = '/me/calendars/'.$request->cid.'/events?'.http_build_query($queryParams);
        //  getEvenetUrl --> "/me/events?%24select=subject%2Corganizer%2Cstart%2Cend&%24orderby=createdDateTime+DESC"

        $events = $graph->createRequest('GET', $getEventsUrl)
            ->setReturnType(Model\Event::class)
            ->execute();

//

        $viewData['events'] = $events;
        return view('event', $viewData);
    }


    // calendar_events
    public function calendar_events(Request $request)
    {
        $viewData = $this->loadViewData();

// Get the access token from the cache
        $tokenCache = new TokenCache();
        if ( env('OAUTH_APP_PERMISSION') ) {
            $accessToken = $tokenCache->getAccessTokenForApplication();
        } else {
            $accessToken = $tokenCache->getAccessToken();
        }        

// Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $queryParams = array(
            '$select' => 'subject,organizer,start,end,recurrence',
            '$orderby' => 'createdDateTime DESC',
        );

// test Me API
        // $getEventsUrl = '/me/';
        // $result = $graph->createRequest('GET', $getEventsUrl)
        //   ->execute();

// Append query parameters to the '/me/events' url
        //$getEventsUrl = '/me/events?' . http_build_query($queryParams);

// jpoon test scenario
        // 2.  other person
        // GET /users/{id | userPrincipalName}/calendars/{id}/events
        $getEventsUrl = '/users/' . $request->uid . '/calendars/' . $request->cid . '/events?' . http_build_query($queryParams);
//$getEventsUrl = '/me/calendars/'.$request->cid.'/events?'.http_build_query($queryParams);
        //  getEvenetUrl --> "/me/events?%24select=subject%2Corganizer%2Cstart%2Cend&%24orderby=createdDateTime+DESC"

        $events = $graph->createRequest('GET', $getEventsUrl)
            ->setReturnType(Model\Event::class)
            ->execute();

        $viewData['events'] = $events;
        return view('event', $viewData);
    }

}
