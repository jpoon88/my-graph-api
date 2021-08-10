<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TimeZones\TimeZones;
use Illuminate\Http\Request;
use App\TokenStore\TokenCache;

class ConversationController extends Controller
{

    //
    public function index()
    {
        $viewData = $this->loadViewData();

        // Get user's timezone
        $timezone = TimeZones::getTzFromWindows($viewData['userTimeZone']);
        $now = Carbon::now($timezone);
        $now->day = $now->day;
        $now->hour = $now->hour;

        $array = [
            'eventDate' => $now->format('Y-m-d'),
            'eventTime' => $now->format('H:i'),
        ];

        return view('conversation', $array);
    }

    public function store(Request $request)
    {

  
        // Validate required fields
        $request->validate([
            //'eventSubject' => 'required|string',
            'eventAttendees' => 'required', // 'nullable',
            'eventDate' => 'required',
            'eventTime' => 'required',
            'eventDuration' => 'required',
            //'eventBody' => 'nullable|string'
        ]);

        $viewData = $this->loadViewData();

        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();
        //$accessToken = $tokenCache->getAccessTokenForApplication();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        /*
        $body = <<<TEXT
        {
        "attendees": [
        {
        "type": "required",
        "emailAddress": {
        "name": "Diego Siciliani",
        "address": "DiegoS@laravelphp.onmicrosoft.com"
        }
        }
        ],
        "locationConstraint": {
        "isRequired": false,
        "suggestLocation": false,
        "locations": [
        {
        "resolveAvailability": false,
        "displayName": "Conf room Hood"
        }
        ]
        },
        "timeConstraint": {
        "activityDomain":"work",
        "timeSlots": [
        {
        "start": {
        "dateTime": "2021-07-16T09:00:00",
        "timeZone": "Pacific Standard Time"
        },
        "end": {
        "dateTime": "2021-07-18T17:00:00",
        "timeZone": "Pacific Standard Time"
        }
        }
        ]
        },
        "isOrganizerOptional": "false",
        "meetingDuration": "PT1H",
        "returnSuggestionReasons": "true",
        "minimumAttendeePercentage": "100"
        }
        TEXT;
         */
        if (($request->eventDate) && ($request->eventTime)) {
            $today = Carbon::createFromFormat('Y-m-d H:i', $request->eventDate . ' ' . $request->eventTime);
        }

        if ($request->eventAttendees) {
            $attendeeAddresses = $request->eventAttendees;
        } else {
            $attendeeAddresses = [];
        }

        $attendees = [];
        foreach ($attendeeAddresses as $attendeeAddress) {
            array_push($attendees, [
                // Add the email address in the emailAddress property
                'emailAddress' => [
                    'address' => $attendeeAddress,
                ],
                // Set the attendee type to required
                'type' => 'required',
            ]);
        }

        // Build the event
        $newEvent = [
            'attendees' => $attendees,
            'locationConstraint' => [
                "isRequired" => false,
                "suggestLocation" => false,
                'locations' => array([
                    "resolveAvailability" => false,
                    "displayName" => "Conf room Hood",
                ]),
            ],
            'timeConstraint' => [
                "activityDomain" => "work",
                "timeSlots" => array([
                    "start" => [
                        "dateTime" => $today->format('Y-m-d\TH:i:s'), // "2021-07-16T09:00:00",
                        "timeZone" => "Pacific Standard Time",
                    ],
                    "end" => [
                        "dateTime" => $today->addDay(7)->format('Y-m-d\TH:i:s'), //"2021-07-18T17:00:00",
                        "timeZone" => "Pacific Standard Time",
                    ],
                ]),
            ],
            "isOrganizerOptional" => false,
            "meetingDuration" => $request->eventDuration,  // "PT1H",
            "returnSuggestionReasons" => "true",
            "minimumAttendeePercentage" => "100",
            "maxCandidates" => 20,
        ];

        //$json = json_encode($newEvent);
        //$json = json_decode($body);
        //dd( $json);

        $findMeetingEventsUrl = '/me/findMeetingTimes';
        $results = $graph->createRequest('POST', $findMeetingEventsUrl)
            ->addHeaders(['Prefer' => 'outlook.timezone="Pacific Standard Time"'])
            ->attachBody($newEvent)
            ->setReturnType(Model\MeetingTimeSuggestionsResult::class)
            ->execute();

        //dd($events["emptySuggestionsReason"]);


        $viewData['emptySuggestionsReason'] = $results->getEmptySuggestionsReason();
        $viewData['suggestions'] = $results->getMeetingTimeSuggestions();

        $viewData['eventDate'] = $request->eventDate;
        $viewData['eventTime'] = $request->eventTime;

        if ($request->eventAttendees) {
          $viewData['eventAttendees'] = $this->userList($request->eventAttendees);
        }  

        // $suggestion = $viewData['suggestions'][0];
        // $locations = $suggestion->getLocations();
        // $attendees = $suggestion->getAttendeeAvailability();
        // $timeSlot = $suggestion->getMeetingTimeSlot();

        //$viewData['eventAttendees'] = [ {'id' => 123, 'name' => 'test' } ];
        //dd ( $attendees  );

        if($request->ajax()) {

            $suggestionsByDate = [];
            foreach( $viewData['suggestions'] as $suggestion) {

                $start = \Carbon\Carbon::parse($suggestion->getMeetingTimeSlot()->getStart()->getDateTime());
                $suggestionsByDate[$start->format('Y-m-d')][] = $suggestion;
            }

            $viewData['suggestionsByDate'] = $suggestionsByDate;

            return view('conversation-partial', $viewData);
        }
        return view('conversation', $viewData);

    }


    public function findmeetingtime(Request $request) 
    {

        if($request->ajax()){
            return "AJAX";
        }
        return "HTTP";

        // Validate required fields
        $request->validate([
            //'eventSubject' => 'required|string',
            'eventAttendees' => 'required', // 'nullable',
            'eventDate' => 'required',
            'eventTime' => 'required',
            'eventDuration' => 'required',
            //'eventBody' => 'nullable|string'
        ]);

        $viewData = $this->loadViewData();
        return view('conversation-partial', $viewData);
    }

    // common function
    protected function userList(array $userPrincipalNames)
    {

        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $newarray = [];
        foreach ($userPrincipalNames as $userPrincipalName) {

            //  User - API https://graph.microsoft.com/v1.0/users/{id | userPrincipalName}
            $getUsersUrl = '/users/' . $userPrincipalName;
            $user = $graph->createRequest('GET', $getUsersUrl)
            //->addHeaders(['ConsistencyLevel'=> 'eventual'])
                ->setReturnType(Model\User::class)
                ->execute();

            if ($user) {
                $newarray[] = $user;
                  //array('id' => $user->getUserPrincipalName(), 'name' => $user->getDisplayName());
            }

        }

        return $newarray;

    }


}
