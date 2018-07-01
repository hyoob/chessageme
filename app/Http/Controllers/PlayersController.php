<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Player;
use App\Subscription;
use DB;

class PlayersController extends Controller
{

      /**
       * Create a new controller instance.
       *
       * @return void
       */
      public function __construct()
      {
          $this->middleware('auth');
      }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // For referrence, listing different ways of fetching the data
          // $subscribed = Player::all();
          // $subscribed = Player::orderBy('username','asc')->get();
          // $subscribed = Player::orderBy('username','asc')->take(1)->get();
          // $subscribed = Player::where('username','hbull')->get();
          // $subscribed = DB::select(SELECT * FROM players);

        $subscribed = DB::table('players')
        ->select(DB::raw("*"))
        ->join(
          'subscriptions',
          'subscriptions.playerId','=','players.id'
        )
        ->where(['subscriptions.userId' => auth()->user()->id])
        ->orderby('subscriptions.created_at','desc')
        ->paginate(10);

        //return $subscribed;
        return view('pages.home')->with('subscribed',$subscribed);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.home');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'player' => 'required'
      ]);

      $player = trim($request->input('player'));

      $count = Player::where('userId', '=' ,$player)->count();

      if($count == 1){
        $countSubscription = Subscription::where('playerId', '=' ,Player::find($player)->id)->where('userId', '=' ,auth()->user()->id)->count();

        if ($countSubscription == 1) {
          return redirect('/')->with('success', 'You are already following player <b>'.$player.'</b>!');
        } else {

          $totalSubscriptions = Subscription::where('userId', '=' ,auth()->user()->id)->count();
          log::info("User id: ".auth()->user()->id.". Total subscriptions:".$totalSubscriptions);

          if ($totalSubscriptions >= 15) {
            return redirect('/')->with('error', 'You are already following <strong>15 players</strong>. This is the maximum we can allow at the moment.');
          } else {

              $subscription = new Subscription;

              $subscription->userId = auth()->user()->id;
              $subscription->playerId = Player::find($player)->id;
              $subscription->onlineMail = True;
              $subscription->playingMail = True;
              $subscription->streamingMail = True;
              $subscription->onlinePush = True;
              $subscription->playingPush = True;
              $subscription->streamingPush = True;

              $subscription->save();

              return redirect('/')->with('success', 'You are now following <b>'.$player.'</b>!');
              }
            }
      } else{ 
        //Set error handling
        error_reporting('E_All');
        ini_set('display_errors',1);
        //Initialize cURL
        $url = 'https://lichess.org/api/user/'.$player;
        $ch = curl_init($url);
        //Set cURL options
        //Whether to include the header in the output. False here.
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //Return instead of outputting directly
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        //Execute the request and fetch the response.
        $output = curl_exec($ch);
        //Get the http response status
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //Close and free up the curl handle
        curl_close($ch);
        //Check for errors
        if($output === FALSE) {
        echo "cURL Error:" . curl_error($ch);
        }
        //Get json from curl output
        $json = json_decode($output, true);

        if ($httpcode != "200") {
          return redirect('/')->with('error', "Oops... Lichess returns <b>".$httpcode."</b>. Are you sure player <b>".$player."</b> exists?");
        } else {
          $newPlayer = new Player;

          $newPlayer->userId = $json ['id'];
          $newPlayer->username = $json ['username'];
          $newPlayer->title = $json ['title'];
          $newPlayer->online = $json ['online'];
          $newPlayer->playing = $json ['playing'];
          $newPlayer->streaming = $json ['streaming'];
          $newPlayer->createdAt = $json ['createdAt'];
          $newPlayer->seenAt = $json ['seenAt'];
          $newPlayer->patron = $json ['patron'];
          $newPlayer->disabled = $json ['disabled'];
          $newPlayer->engine = $json ['engine'];
          $newPlayer->playTime = $json ['playTime']['total'];
          $newPlayer->countAll = $json ['count']['all'];

          $newPlayer->save();

          $subscription = new Subscription;

          $subscription->userId = auth()->user()->id;
          $subscription->playerId = Player::find($player)->id;
          $subscription->onlineMail = True;
          $subscription->playingMail = True;
          $subscription->streamingMail = True;
          $subscription->onlinePush = True;
          $subscription->playingPush = True;
          $subscription->streamingPush = True;

          $subscription->save();

          return redirect('/')->with('success', "You are now following <b>".$newPlayer->username."</b>!");
        }
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $playerRemoved = DB::table('players')
        ->select(DB::raw("players.username"))
        ->join(
          'subscriptions',
          'subscriptions.playerId','=','players.id'
        )
        ->where('subscriptions.id', '=', $id)
        ->value("players.username");

        $subscription = Subscription::find($id);
        $subscription->delete();
        return redirect('/')->with('success', "Player <strong>".$playerRemoved. "</strong> removed!");

    }
}
