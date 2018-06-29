@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div>
              <!--  <div class="panel-heading">Dashboard</div> //-->

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    {!! Form::open(['action' => 'PlayersController@store', 'method' => 'POST']) !!}
                        <div class="form-group">
                          {{Form::label('player', 'Player')}}
                          {{Form::text('player', '', ['class' => 'form-control', 'placeholder' => 'player'])}}
                        </div>
                        {{Form::submit('Submit',['class'=>'btn btn-primary'])}}
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
