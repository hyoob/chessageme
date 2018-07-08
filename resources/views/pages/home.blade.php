@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
           <p align="center"><img src="/images/chessageme_logo_full.png" width="376" height="64"></p>
            <div>
              <!--  <div class="panel-heading">Dashboard</div> //-->

                <div>
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                        <hr>
                        <div>
                    This website emails you when your favorite chess player(s) start(s) playing on <a href="https://lichess.org/" target="_blank">lichess.org</a>.
                    <br>
                    <br>
                    1- <a href="/register">Register</a> or <a href="/login">Log in</a>. 
                    <br>
                    2- Go to <a href="https://lichess.org/" target="_blank">lichess.org</a> and grab the username of a player you'd like to follow (e.g <a href="https://lichess.org/@/DrDrunkenstein" target="_blank">DrDrunkenstein</a> for reigning world champion <strong>Magnus Carlsen</strong>).
                    <br>
                    3- Enter the lichess username in your <a href="/dashboard" target="_blank">dashboard page</a>  and hit "Submit". Note that you can unfollow players anytime.
                    <br>
                    <br>
                    This service is free and based on the <a href="https://lichess.org/api" target="_blank">lichess API</a>. In order to keep it free and respect Lichess' servers, you can follow a maximum of 10 players.
                    <br>
                    <br>
                    Enjoy!
                </div>
                <br>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
