<?xml version="1.0" encoding="UTF-8"?>
<files>
	<file>
		<url>{!! $url !!}</url>
		<texto>{!! $texto !!}</texto>
		<txcorto>{!! $txcorto !!}</txcorto>
		<keywords>
			@foreach($keywords as $keyword)
			<word>{!! trim($keyword) !!}</word>
			@endforeach
		</keywords>
	</file>
</files>