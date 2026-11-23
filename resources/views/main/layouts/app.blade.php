<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        @stack('metaData')

        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        <title>{!! $pageTitle ?? config('app.name', 'Cosmic') !!}</title>

        @if($useRecaptcha)
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endif

        @vite(['resources/js/app.js'])
        @vite(['resources/css/tailwind.css'])

        @stack('scripts')

        {!! $options->widgets('widget_header') !!}
    </head>
    <body>
        @include('main.layouts.navbar')

        {{ $slot }}

        @include('main.layouts.footer')

        {!! $options->widgets('widget_footer') !!}
        @stack('scriptsBottom')
    </body>
</html>