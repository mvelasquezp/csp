<rows>
	@foreach($data as $row)
	<row id="{!! $row->articulo !!}">
		<cell></cell>
		<cell>{!! $row->titulo !!}</cell>
		<cell>{!! $row->medio !!}</cell>
		<cell>{!! $row->paginas !!}</cell>
		<cell>{!! $row->texto !!}</cell>
		<cell>ic_edit.png^Editar</cell>
		<cell>ic_erase.png^Retirar</cell>
		<cell>ic_attach.png^Adjuntar</cell>
		<cell>ic_switch.png^Mover</cell>
		<cell>ic_check.png^Ver adjuntos</cell>
	</row>
	@endforeach
</rows>