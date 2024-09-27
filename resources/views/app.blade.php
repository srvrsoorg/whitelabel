<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	@if($siteSettings)
		<title>{{ $siteSettings->app_name }}</title>
	@endif

	@if ($siteSettings && $siteSettings->favicon)    
		<link rel="icon" href="{{$siteSettings->favicon}}">
	@else
		<link rel="icon" href="/favicon.ico">  
	@endif

	<link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//maxst.icons8.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">

	<script>
		window.siteSettings = @json($siteSettings)
	</script>

	@vite(['resources/css/app.css', 'resources/js/app.js'])

	@if($siteSettings && $siteSettings->header && $siteSettings->header != 'null')
		@empty(!$siteSettings->header)
			{!! $siteSettings->header !!}
		@endempty
	@endif
</head>
<body class="min-h-screen h-full">
	<div id="app" class="h-full min-h-screen"></div>
	
	@if($siteSettings && $siteSettings->footer && $siteSettings->footer != 'null')
		@empty(!$siteSettings->footer)
			{!! $siteSettings->footer !!}
		@endempty
	@endif
</body>
</html>