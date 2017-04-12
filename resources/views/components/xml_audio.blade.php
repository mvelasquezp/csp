<?xml version="1.0" encoding="UTF-8"?>
<files>
	<file>
		<mencion>{!! $mencion !!}</mencion>
		<keywords>
			@foreach($keywords as $keyword)
			<word>{!! trim($keyword) !!}</word>
			@endforeach
		</keywords>
	</file>
</files>