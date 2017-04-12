<rows>
	@foreach($data as $index => $row)
	<row id="{{ $row->id }}">
		@foreach($row as $key => $value)
		<cell>{!! $value !!}</cell>
		@endforeach
		@if(strcmp($row->est, 'Pendiente') == 0)
		<cell>new.png^Preparar boletín</cell>
		@else
		<cell>blank.png^ </cell>
		@endif
	</row>
	@endforeach
</rows>