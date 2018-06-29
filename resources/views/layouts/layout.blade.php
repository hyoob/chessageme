<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="{{asset('css/app.css')}}">

        <title>{{config('app.name', 'Chessageme')}}</title>
    </head>
    <body>
        @include('inc.navbar')
        <div class="container">
        @include('inc.messages')
        @yield('body')
        </div>
    </body>
</html>
