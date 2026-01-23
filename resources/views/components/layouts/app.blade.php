<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ !empty($title) ? $title . ' | ' . config('app.name') : config('app.name') }}</title>
    @if (!empty($description))
        <meta name="description" content="{{ $description }}">
    @endif
    <meta name="robots" content="index, follow">

    <meta name="color-scheme" content="light">
    @vite('resources/css/app.css')

    <meta property="og:title" content="{{ !empty($title) ? $title . ' | ' . config('app.name') : config('app.name') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if (!empty($ogImage))
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @elseif(config('testa.open_graph.fallback_image'))
        <meta property="og:image" content="{{ config('testa.open_graph.fallback_image') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif
    @if (!empty($description))
        <meta property="og:description" content="{{ $description }}">
    @endif
    @if (isset($head))
        {{ $head }}
    @endif
</head>
<body class="{{ !empty($bodyClass) ? $bodyClass : '' }}">
<x-testa::header/>

<main>
    {{ $slot }}
</main>

<x-testa::footer/>

@vite('resources/js/app.js')
@if (isset($scripts))
    {{ $scripts }}
@endif
</body>
</html>
