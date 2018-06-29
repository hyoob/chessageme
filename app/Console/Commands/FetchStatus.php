<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Player;
use App\Subscription;
use DB;
use Illuminate\Support\Facades\Mail;

class FetchStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch lichess statuses for subscribed players';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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
                Log::info("cURL Error: " . curl_error($ch));
            }

            Log::info("Batch ".($bsent+1).": sent ".$countToFetch." players to Lichess ");

            //Get json from curl output
            $json = json_decode($output, true);

            if ($httpcode != "200") {
                Log::info("Lichess returned status: ".$httpcode." URL was ".$url);
            } else {
                $countReturned = count($json);
                Log::info("Lichess returned: ".$countReturned." players");

                foreach ($json as $playerStatus) {

                    if ($playerStatus ['playing'] === true) {

                        $playerPlaying = $playerStatus ['id'];

                        $PlayerLastPlaying = DB::table('players')
                        ->where('userId', '=', $playerStatus ['id'])
                        ->value('lastPlaying');

                        $minSinceLastPlayed = now()->diffInMinutes($PlayerLastPlaying);

                        if (is_null($PlayerLastPlaying) OR $minSinceLastPlayed > 60) {

                            $subscribedUsers = DB::table('users')
                            ->join('subscriptions', 'subscriptions.userId','=','users.id')
                            ->join('players', 'players.id','=','subscriptions.playerId')
                            ->where('players.userId', '=', $playerStatus ['id'])
                            ->select('users.email')
                            ->get();

                            $subscribedUsersArray = json_decode($subscribedUsers, true);
                             
                            foreach ($subscribedUsersArray as $key2 => $value2) { 
                                Mail::to($value2['email'])
                                ->queue(new \App\Mail\PlayingEmail($playerPlaying));
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

    }
}
