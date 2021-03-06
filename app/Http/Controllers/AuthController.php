<?php

namespace App\Http\Controllers;

use App\Models\User;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Illuminate\Http\Request;
use App\TokenStore\TokenCache;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Exception\ClientException;

class AuthController extends Controller
{
    //
    public function signin()
    {
      // Initialize the OAuth client
      $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId'                => env('OAUTH_APP_ID'),
        'clientSecret'            => env('OAUTH_APP_PASSWORD'),
        'redirectUri'             => env('OAUTH_REDIRECT_URI'),
        'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
        'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
        'urlResourceOwnerDetails' => '',
        'scopes'                  => env('OAUTH_SCOPES')
      ]);
  
      $authUrl = $oauthClient->getAuthorizationUrl();

      // Save client state so we can validate in callback
      session(['oauthState' => $oauthClient->getState()]);

      // Redirect to AAD signin page
      return redirect()->away($authUrl);
    }
  
    public function callback(Request $request)
    {

      // Validate state
      $expectedState = session('oauthState');
      $request->session()->forget('oauthState');
      $providedState = $request->query('state');
  


      if (!isset($expectedState)) {
        // If there is no expected state in the session,
        // do nothing and redirect to the home page.
        return redirect('/');
      }
  
      if (!isset($providedState) || $expectedState != $providedState) {
        return redirect('/')
          ->with('error', 'Invalid auth state')
          ->with('errorDetail', 'The provided auth state did not match the expected value');
      }
  
      // Authorization code should be in the "code" query param
      $authCode = $request->query('code');
      if (isset($authCode)) {
        // Initialize the OAuth client
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
          'clientId'                => env('OAUTH_APP_ID'),
          'clientSecret'            => env('OAUTH_APP_PASSWORD'),
          'redirectUri'             => env('OAUTH_REDIRECT_URI'),
          'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
          'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
          'urlResourceOwnerDetails' => '',
          'scopes'                  => env('OAUTH_SCOPES')
        ]);
  
        // <StoreTokensSnippet>
        try {
          // Make the token request
          $accessToken = $oauthClient->getAccessToken('authorization_code', [
            'code' => $authCode
          ]);
  
          $graph = new Graph();
          $graph->setAccessToken($accessToken->getToken());
  
          $user = $graph->createRequest('GET', '/me')
            ->setReturnType(Model\User::class)
            ->execute();

          //dd( $user );

          // get me/photo/$value

          try {
            $stream = $graph->createRequest('GET', '/me/photos/48x48/$value')
            ->setReturnType(\Psr\Http\Message\StreamInterface::class)
            ->execute();
          // } catch(ClientException $e) {
          //   if $e->getResponse()->getStatusCode() == '404'
          // }
           } catch(ClientException $e) {
              if ($e->getResponse()->getStatusCode() == '404') {
                // No action 
              } else {
                  dd('throw error if not 404');
              // throw ExceptionWrapper::wrapGuzzleBadResponseException($e);
              }
           }
          
          $profilePhoto = "";
          if (isset($stream)) {
            $profilePhoto = $stream->getContents();
          }
  
          $tokenCache = new TokenCache();
          $tokenCache->storeTokens($accessToken, $user, $profilePhoto);

          // JP -- Local Data 
          /*
          // $auth_user = User::updateOrCreate(
          //   ['email' => $user->getMail() ],  // check the nmail 
          //   ['name' => $user->getDisplayName(),   'password' => '' ]
          // ); 
          // Auth::login($auth_user);         
          // $request->session()->regenerate();
          */  

        // James added started
        // User::create([
        //     'name' => $userrequest->name,
        //     'username' => $request->username,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]);

        // auth()->attempt($request->only('email', 'password'));
        // James added started

          return redirect('/');
        }
        // </StoreTokensSnippet>
        catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
          return redirect('/')
            ->with('error', 'Error requesting access token')
            ->with('errorDetail', $e->getMessage());
        }
      }

      return redirect('/')
        ->with('error', $request->query('error'))
        ->with('errorDetail', $request->query('error_description'));
    }
  
    // <SignOutSnippet>
    public function signout(Request $request)
    {
      $tokenCache = new TokenCache();
      $tokenCache->clearTokens();

      // JP added
      // Auth::logout();
      // $request->session()->invalidate();
      // $request->session()->regenerateToken(); 

      return redirect("https://login.microsoftonline.com/common/oauth2/v2.0/logout?post_logout_redirect_uri=http%3A%2F%2Flocalhost%3A8080%2F");

      

      // return redirect('/');
    }
    // </SignOutSnippet>
}
