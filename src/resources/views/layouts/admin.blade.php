<!doctype type="html">
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <mata name="viewport" content="device-width, initial-scale=1, shrink0to-fit=no">
        @vite(['resources/js/app.js', 'resources/scss/app.sass'])
</head>
<body>
<div id="app">
    <div class="container mx-auto">
        @yield('content')
    </div>
</div>
</body>
</html>
