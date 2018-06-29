@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div>
              <!--  <div class="panel-heading">Dashboard</div> //-->

                <div>
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                        <h4>Enter a player to follow</h4>
                    {!! Form::open(['action' => 'PlayersController@store', 'method' => 'POST']) !!}
                        <div class="form-group">
                          {{Form::text('player', '', ['class' => 'form-control', 'placeholder' => 'player'])}}
                        </div>
                        {{Form::submit('Submit',['class'=>'btn btn-primary'])}}
                    {!! Form::close() !!}

                </div>
                <br>
                <div>
                  <h4>You are currently following</h4>
                  @if(count($subscribed) > 0)
                    @foreach($subscribed as $suby)
                      <div class="form-group">
                        <div class="list-group-item">
                        {!!Form::open(['action'=> ['PlayersController@destroy', $suby->id], 'method'=> 'POST', 'class'=> ''])!!}
                          {{Form::label('title',$suby->username)}}
                          {{Form::hidden('_method', 'DELETE')}}
                          {{Form::submit('Unfollow', ['class'=> 'btn btn-danger btn-xs pull-right'])}}
                        {!!Form::close()!!}
                        </div>
                      </div>
                    @endforeach
                    {{$subscribed->links()}}
                  @else
                    <p>... no one. Go to lichess and find someone interesting to follow!</p>
                  @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
