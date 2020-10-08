<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" value="{{ csrf_token() }}"/>

    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">

    <link href="{{ mix('css/app.css') }}" type="text/css" rel="stylesheet"/>
    <title>PDF => EPUB</title>
</head>
<body>
<div id="app"></div>
<script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
</html>