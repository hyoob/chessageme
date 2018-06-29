@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div>
                @if (session('status'))
                  <div class="alert alert-success">
                    {{ session('status') }}
                  </div>
                @endif
            </div>

            {!!Form::open(['action'=> ['StatusController@update', '1' ],'method'=> 'POST'])!!}
              {{Form::label('title', 'Fetch lichess for player status')}}
              {{Form::hidden('_method', 'PUT')}}
              <div>
              {{Form::submit('Fetch', ['class'=> 'btn btn-primary'])}}
              </div>
            {!!Form::close()!!}

            <br>
            <br>

            <div class="flex-center position-ref full-height">
              <form action="{{route('sendmail')}}" method="post">
                <p><b>Send an email</b></p>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="playerPlaying" value="HB">
                <input type="email" class="form-control" name="mail" placeholder="mail address">
                <br>
                <button type="submit" class="btn btn-primary">Send</button>
              </form>
            </div>

        </div>
    </div>
</div>
@endsection
