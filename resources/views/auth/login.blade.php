@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <p align="center"><img src="/images/chessageme_logo.png"></p>

            <div class="panel panel-default">

                <div class="panel-heading">Login</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div>
                    Welcome to Chessageme!
                    <br>
                    <br>
                    This site allows you to receive notifications when your favorite chess players start playing on <a href="https://lichess.org/" target="_blank">lichess.org</a>.
                    <br>
                    <br>
                    It is free and simple to use:
                    <br>
                    1- <strong>Register/log in</strong>. The adress you use will be the one we send notifications to when the Lichess players you follow start playing on <a href="https://lichess.org/" target="_blank">lichess.org</a>.
                    <br>
                    2- Grab the handle of the Lichess player you'd like to follow (e.g <strong>Magnus Carlsen</strong> aka <a href="https://lichess.org/@/DrDrunkenstein" target="_blank">DrDrunkenstein</a>) and hit "follow".
                    <br>
                    3- You'll receive an email whenever a player you follow starts playing on Lichess. Note that you can unfollow players anytime.
                    <br>
                    <br>
                    To provide this service our site makes use of the <a href="https://lichess.org/api" target="_blank">lichess API</a>. Please limit the number of players you follow to a maximum of ~10, in order to respect our and Lichess' servers.
                    <br>
                    <br>
                    Enjoy!
                </div>
                <br>

        </div>
    </div>
</div>
@endsection
