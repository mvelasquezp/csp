<?xml version="1.0" encoding="UTF-8"?>
<root>
	<boletin>
		<id>{{ $id_boletin }}</id>
		<bloques>
			@foreach($bloques as $bloque)
			<id>{{ $bloque["id"] }}</id>
			<name>{!! $bloque["name"] !!}</name>
			<articulos>
				@foreach($bloque["arts"] as $articulo)
				<articulo>
					<title>{!! $articulo["name"] !!}</title>
					<medio>{!! $articulo["medio"] !!}</medio>
					<text>{!! $articulo["text"] !!}</text>
					<page>{{ $articulo["pages"] }}</page>
					<date>{{ $articulo["date"] }}</date>
					@if(isset($articulo["att"]))
					<attachments>
						@foreach($articulo["att"] as $adjunto)
						<attach>
							<header>{!! $adjunto["header"] !!}</header>
							<url>{!! $adjunto["url"] !!}</url>
						</attach>
						@endforeach
					</attachments>
					@endif
				</articulo>
				@endforeach
			</articulos>
			@endforeach
		</bloques>
	</boletin>
</root>