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
      public function __construct()
      {
          // $this->middleware(['auth']);
      }  


     //
     public function index()
     {
       $viewData = $this->loadViewData();
   
       // Get the access token from the cache
       $tokenCache = new TokenCache();
       $accessToken = $tokenCache->getAccessToken();
       //$accessToken = $tokenCache->getAccessTokenForApplication();

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
     

     public function selectSearch(Request $request)
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
            '$select' => 'id,displayName,mail,userPrincipalName',
            //'$filter'  =>  "startswith(displayName,'". $request->q . "')",
            //'$search'  =>  '"displayName:' . $request->q . '"',
            '$orderby' => 'displayName'
          );
      
        if ($request->q) {
            $queryParams['$search'] = '"displayName:' . $request->q . '"';
        }
   
         // test User  API https://graph.microsoft.com/v1.0/users
        $getUsersUrl = '/users?'.http_build_query($queryParams);
        //$getUsersUrl = '/users?$search="displayName:de"';
        $users = $graph->createRequest('GET', $getUsersUrl)
               ->addHeaders(['ConsistencyLevel'=> 'eventual'])
                ->setReturnType(Model\User::class)
                ->execute();

         $newarray= [];
         foreach ($users as $user)
         {
            $newarray[] = array('id' => $user->getUserPrincipalName(), 'name' => $user->getDisplayName() ) ;    
         }

         /* if($request->has('q')){
             $search = $request->q;
             $movies =Movie::select("id", "name")
                   ->where('name', 'LIKE', "%$search%")
                   ->get();
         }
         */
 
         // $movies = [
         //     [ 'id' => 31, 'name' => 'Abc'],
         //     [ 'id' => 32,  'name' =>'Abc12' ],
         //     [ 'id' => 33, 'name' => 'Abc123'] ,
         //     [ 'id' => 34, 'name' => 'Abc12122'],
         // ];
 
         // return "[{'id':31,'name':'Abc'}, {'id':32,'name':'Abc12'}, {'id':33,'name':'Abc123'},{'id':34,'name':'Abc'}]";
 
         return response()->json($newarray);
     }

}
