<!DOCTYPE html>
<html>
	<head>
		<title>Subida de artículos - Web</title>
		<meta charset="utf-8" />
		<script type="text/javascript" src="{{ asset('js/jquery-1.12.1.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('dhtmlx/codebase/dhtmlx.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/jquery.selectareas.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/paste.js') }}"></script>
		<style type="text/css">
			* { box-sizing: border-box; }
			html, body { height: 100%;margin: 0px;padding: 0px;overflow: hidden;width: 100%; }
			body{background-color:#344557;}
			#header{background:#019d49;color:#f8f8f8;display:none;padding:5px;}
			#header>img{display:inline-block;height:18px;}
			#header>div{float:right;}
			#header>div>a{color:#f8f8f8;display:inline-block;font-family:Verdana;font-size:12px;padding:3px;text-decoration:none;transition:background 150ms;}
			#header>div>a:hover{background:rgba(0,0,0,.15);}
			#loader{left: 50%;margin-left:-200px;margin-top:-150px;position:absolute;top: 50%;}
			#dv-captura{border: 1px solid #404040;height:100%;margin: 0 auto;width:100%;}
			.img-adjust{max-width:100%;max-height:100%;margin:0 auto;display:block;}
			.demo, .result{border: solid 1px #999;padding: 0.5em;margin: 0;box-sizing:content-box;width: 100%;}
			.result > img{border: solid 1px #ccc;height: 50px;}
			.pastable{transition: box-shadow ease .3s;}
			.pastable:hover{box-shadow: 0 0 3px black;}
			.pastable.pastable-focus{box-shadow: 0 0 10px black;}
		</style>
		<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.selectareas.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ asset('dhtmlx/codebase/dhtmlx.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}" />
		<script type="text/javascript">
			var myLayout, myForm;
			var config = {
				formData: [
					{type: "settings", position: "label-left", offsetLeft: 20, inputWidth: 275},
					{type: "label", label: "Tome una captura de pantalla o utilice la herramienta de recortes para capturar una imagen del artículo.", offsetLeft:10 },
					{type: "label", label: "A continuación, sitúese sobre el recuadro de 'Captura de pantalla' y pegue el contenido (Ctrl+V). La captura aparecerá en el recuadro del lado derecho.", offsetLeft:10 },
					{type: "input", label: "Título", labelWidth: 65, name: "tbTitulo", validate: "NotEmpty"},
					{type: "combo", label: "Medio", labelWidth: 65, name: "cbMedio", inputWidth: 200, options: []},
					{type: "input", label: "URL", labelWidth: 65, name: "tbUrl", validate: "NotEmpty"},
					{type: "input", label: "Texto", rows: 2, labelWidth: 65, name: "tbTexto", validate: "NotEmpty"},
					{type: "input", label: "Tx.Corto", rows: 2, labelWidth: 65, name: "tbTxCorto", validate: "NotEmpty"},
					{type: "input", label: "Pal.Clave", labelWidth: 65, name: "tbKeywords", validate: "NotEmpty"},
					{type: "input", label: "Captura", rows: 1, labelWidth: 65, name: "tbCaptura", value: "Una vez que haya capturado la imagen, coloque el cursor aquí, y presione Ctrl+V"},
					{type: "block", offsetLeft: 47, inputWidth: 325, list:[
						{type: "button", value: "<i class='fa fa-floppy-o'></i> Guardar", name: "btGuarda"},
						{type: "newcolumn"},
						{type: "button", value: "<i class='fa fa-bell-o'></i> Enviar alerta", name: "btAlerta"}
					]}
				]
			};
			function initComponents(data) {
				myLayout = new dhtmlXLayoutObject(document.body, "2U");
				myLayout.attachHeader("header");
				myLayout.cells("a").setWidth(400);
				myLayout.cells("a").setText("Registrar artículo web");
				myLayout.cells("b").setText("Vista previa de la imagen");
				myLayout.cells("b").attachHTMLString('<div id="dv-captura"></div>');
				//formulario de ingreso
				myForm = myLayout.cells("a").attachForm();
					config.formData[4].options = data.cbmedio;
				myForm.loadStruct(config.formData);
				myForm.enableLiveValidation(true);
				myForm.attachEvent("onButtonClick", function(id) {
					switch(id) {
						case "btGuarda":
							myForm.validate();
							var imagen = $("#dv-captura").children("img");
							if(imagen.length > 0) {
								myLayout.progressOn();
								var p = {
									_token: "{{ csrf_token() }}",
									img: imagen.eq(0).attr("src"),
									ttl: myForm.getItemValue("tbTitulo"),
									mid: myForm.getItemValue("cbMedio"),
									url: myForm.getItemValue("tbUrl"),
									txt: myForm.getItemValue("tbTexto"),
									stx: myForm.getItemValue("tbTxCorto"),
									kws: myForm.getItemValue("tbKeywords")
								};
								$.post("{{ url('ajax/data/sv_screenshot') }}", p, function(response) {
									myLayout.progressOff();
									if(response.success) {
										myForm.setItemValue("tbTitulo", "");
										myForm.setItemValue("tbUrl", "");
										myForm.setItemValue("tbTexto", "");
										myForm.setItemValue("tbTxCorto", "");
										myForm.setItemValue("tbAutor", "");
										myForm.setItemValue("tbKeywords", "");
										$("#dv-captura").empty();
										dhtmlx.alert({
											title: "Operación completada",
											ok: "Aceptar",
											text: response.message
										});
									}
									else dhtmlx.alert({
										title: "Error",
										type: "alert-error",
										text: response.message
									});
								}, "json");
							}
							else dhtmlx.alert({
								title: "Error",
								type: "alert-error",
								text: "Coloque una imagen para continuar"
							});
							break;
						case "btAlerta": break;
					}
				});
				$("textarea[name=tbCaptura]").pastableTextarea();
				$("textarea[name=tbCaptura]").on('pasteImage', function (ev, data){
					$("#dv-captura").empty().append(
						$("<img/>").attr({src:data.dataURL, title:"Captura tomada"}).addClass("img-adjust")
					);
				}).on('pasteImageError', function(ev, data){
					alert('Oops: ' + data.message);
					if(data.url){
						alert('But we got its url anyway:' + data.url)
					}
				}).on('pasteText', function (ev, data){
					console.log("text: " + data.text);
				});
			}
			function init() {
				$.post("{{ url('ajax/components/genera-articulos-web') }}", { _token: "{{ csrf_token() }}" }, function(response) {
					if(response.success) {
						$("#loader").fadeOut(500, function() {
							$("#header").show();
							$("#loader").remove();
							initComponents(response.data);
							$("#header>img").height(24);
						});
					}
				}, "json");
				$(".full-screen-div").on("click", function() {$(".full-screen-div").fadeOut(150);});
			}
			$(init);
		</script>
	</head>
	<body>
		<img id="loader" src="../images/icons/loader.gif" />
		<div id="header">
			<img src="../images/icons/logo.png" />
			<div>
				<a href="{{ url('intranet/sube-diarios') }}">Subida de diarios</a> | 
				<a href="javascript:showOpts()">Ingreso de artículos</a> | 
				<a href="{{ url('intranet/boletines') }}">Generar boletines</a> | 
				<a href="{{ url('#') }}">Revisión de boletines</a> | 
				<a href="{{ url('intranet') }}">Volver</a>
			</div>
		</div>
		<div class="full-screen-div">
			<div>
				<h3>Elige el tipo de medio del artículo</h3>
				<ul>
					<li><a class="square-1" href="{{ url('intranet/genera-articulos-prensa') }}">Prensa</a></li>
					<li><a class="square-2" href="{{ url('intranet/genera-articulos-redes') }}">Redes Sociales</a></li>
					<li><a class="square-3" href="{{ url('intranet/genera-articulos-web') }}">Web</a></li>
					<li><a class="square-4" href="{{ url('intranet/genera-articulos-radio') }}">Radio</a></li>
					<li><a class="square-5" href="{{ url('intranet/genera-articulos-tv') }}">Televisión</a></li>
				</ul>
			</div>
		</div>
	</body>
</html>