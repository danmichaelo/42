
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>42 : {{$title}}</title>
  <style>
    @import url(http://fonts.googleapis.com/css?family=Source+Code+Pro&subset=latin,latin-ext);
    @import url(http://fonts.googleapis.com/css?family=Titillium+Web);
  </style>
  <link rel="stylesheet" type="text/css" href="{{ URL::to('/css/anudaw/stylesheet.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ URL::to('/css/site.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ URL::to('/lib/glyphicons/css/halflings.css') }}" />
  <script type="text/javascript" src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
  <script type="text/javascript" src="{{ URL::to('/lib/jquery.scrollfollow.js') }}"></script>
</head>
<body>
  <div class="container">
    <header>
      <h1>
        @yield('header')
      </h1>
      <a href="/">/</a>
      ·
      <a href="/emner">Emner</a>
      ·
      <a href="/objekter">Objekter</a>
      ·
      <a href="/ontosaur">Ontosaur</a>
    </header>
    <div class="main">
      @yield('container')
    <div>
  </div>
</body>
</html>