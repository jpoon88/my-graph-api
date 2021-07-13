<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TokenStore\TokenCache;

class PeopleController extends Controller
{
    //
     //
     public function index()
     {
       $viewData = $this->loadViewData();
   
       // Get the access token from the cache
       $tokenCache = new TokenCache();
       //$accessToken = $tokenCache->getAccessToken();
       $accessToken = $tokenCache->getAccessTokenForApplication();


   
       // Create a Graph client
       $graph = new Graph();
       $graph->setAccessToken($accessToken);
   
    //    $queryParams = array(
    //      '$select' => 'subject,organizer,start,end',
    //      '$orderby' => 'createdDateTime DESC'
    //    );
   
       // test Me API
       // $getEventsUrl = '/me/';
       // $result = $graph->createRequest('GET', $getEventsUrl)
       //   ->execute();
   
       // dd($result);
   
       // test User  API https://graph.microsoft.com/v1.0/users
       $getUsersUrl = '/users';
       $users = $graph->createRequest('GET', $getUsersUrl)
       ->setReturnType(Model\User::class)
       ->execute();
   
       $viewData['members'] = $users;
   
    //    // Append query parameters to the '/me/events' url
    //    $getEventsUrl = '/me/events?'.http_build_query($queryParams);
   
    //    $events = $graph->createRequest('GET', $getEventsUrl)
    //      ->setReturnType(Model\Event::class)
    //      ->execute();
   
    //    $viewData['events'] = $events;
       return view('people', $viewData);
     }
     
}
