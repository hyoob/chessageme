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
            {!!Form::open(['action'=> ['TestsController@update', '1' ],'method'=> 'POST'])!!}
              {{Form::label('title', 'Fetch lichess for player status')}}
              {{Form::hidden('_method', 'PUT')}}
              <div>
              <br> 
                {{Form::submit('Test', ['class'=> 'btn btn-primary'])}}
              </div>
            {!!Form::close()!!}
        </div>
    </div>
</div>
@endsection
