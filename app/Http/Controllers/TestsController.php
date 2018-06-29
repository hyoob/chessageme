<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Player;
use App\Subscription;
use DB;
use Illuminate\Support\Facades\Mail;

class TestsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.test');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $playersToFetch = DB::table('players')
        ->select(DB::raw("players.userId"))
        ->join(
          'subscriptions',
          'subscriptions.playerId','=','players.id'
        )
        ->groupBy('players.userId')
        ->get();

        // Create a comma separated list of players
        $playersArray = json_decode($playersToFetch, true);

        $playerCount = count($playersArray);
        $batches = ceil($playerCount/50);
        $bsent = 0;


        $splitArray = array_chunk($playersArray, 50);

        foreach ($splitArray as $key => $value) { 
            $list = array();
    
            foreach ($value as $k => $v) {
                $list[] = $v['userId'];
            }

            $countToFetch = count($list);
            $listToFetch = implode(',',$list);

            error_reporting('E_All');
            ini_set('display_errors',1);
            //Initialize cURL
            $url = 'https://lichess.org/api/users/status?ids='.$listToFetch;
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

            Log::info("Batch ".($bsent+1).": sent ".$countToFetch." players to Lichess ");

            //Get json from curl output
            $json = json_decode($output, true);

            if ($httpcode != "200") {
                return redirect('/status')->with('error', "Oops... Lichess returns <b>".$httpcode);
            } else {
                $countReturned = count($json);
                Log::info("Lichess returned: ".$countReturned." players");

                foreach ($json as $playerStatus) {

                    if ($playerStatus ['playing'] === true) {

                        $PlayerLastPlaying = DB::table('players')
                        ->where('userId', '=', $playerStatus ['id'])
                        ->value('lastPlaying');

                        $minSinceLastPlayed = now()->diffInMinutes($PlayerLastPlaying);

                        if (is_null($PlayerLastPlaying) OR $minSinceLastPlayed > 0) {

                            $subscribedUsers = DB::table('users')
                            ->join('subscriptions', 'subscriptions.userId','=','users.id')
                            ->join('players', 'players.id','=','subscriptions.playerId')
                            ->where('players.userId', '=', $playerStatus ['id'])
                            ->select('users.email')
                            ->get();

                            $subscribedUsersArray = json_decode($subscribedUsers, true);
                             
                            foreach ($subscribedUsersArray as $key2 => $value2) { 
                                Mail::to($value2['email'])
                                ->queue(new \App\Mail\PlayingEmail());
                                Log::info("Email sent to: ".$value2['email']." to notify that ".$playerStatus ['id']." is playing.");
                            }

                        }

                    }
                    
                    $updatedPlayer = Player::find($playerStatus ['id']);

                    $updatedPlayer->online = $playerStatus ['online'];
                    $updatedPlayer->playing = $playerStatus ['playing'];
                    $updatedPlayer->streaming = $playerStatus ['streaming'];
                    
                    if ($playerStatus ['playing'] === true) {
                        $updatedPlayer->lastPlaying = now();
                    }
                    
                    $updatedPlayer->save();
                    }

                $bsent++;

                if ($bsent < $batches) {
                    sleep(7);
                }
            }
        }
    
    Log::info($playerCount." player statuses were fetched & updated");

    return redirect('/test')->with('success', $playerCount.' player statuses were updated!');
          
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
