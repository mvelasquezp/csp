<rows>
	@foreach($data as $index => $row)
	<row id="r_{{ $index }}">
		@foreach($row as $key => $value)
		<cell>{!! $value !!}</cell>
		@endforeach
	</row>
	@endforeach
</rows>