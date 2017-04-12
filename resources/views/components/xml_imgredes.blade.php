<?xml version="1.0" encoding="UTF-8"?>
<files>
	<file>
		<texto>{!! $texto !!}</texto>
		<keywords>
			@foreach($keywords as $keyword)
			<word>{!! trim($keyword) !!}</word>
			@endforeach
		</keywords>
	</file>
</files>