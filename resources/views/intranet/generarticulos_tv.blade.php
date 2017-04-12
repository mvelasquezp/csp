<!DOCTYPE html>
<html>
	<head>
		<title>Subida de artículos - Televisión</title>
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
			#articulos-container{height:100%;padding:5px;width:100%;}
			#articulos-container>a{background:#fff9c4;box-shadow:1px 1px 2px #404040;display:inline-block;margin:5px;padding:0;text-decoration:none;vertical-align:middle;}
			#articulos-container>a>div{padding:5px;width:160px;transition:background 150ms;}
			#articulos-container>a:hover>div{background:rgba(0,0,0,.05);}
			#articulos-container>a>div>p{color:#404040;font-family:Verdana;font-size:11px;margin:0;text-align:left;}
			#articulos-container>a>div>p>b{font-size:12px;}
			#video-container{background:#404040;display:table;height:100%;padding:20px;width:100%;}
			#video-container>div{display:table-cell;margin:0 auto;text-align:center;vertical-align:middle;}
			#video-container>div>video{display:inline-block;width:100%;}
		</style>
		<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.selectareas.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ asset('dhtmlx/codebase/dhtmlx.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}" />
		<script type="text/javascript">
			var dhxWins, myLayout, myForm, myWindow, winToolbar;
			var ics_path;
			var config = {
				formData: [
					{type: "settings", position: "label-left", offsetLeft: 20, inputWidth: 275},
					{type: "label", label: "Desde aquí puede subir fragmentos de audio al sistema.", offsetLeft:10 },
					{type: "label", label: "Complete los datos y, a continuación, elija el archivo de audio correspondiente", offsetLeft:10 },
					{type: "combo", label: "Medio", labelWidth: 65, name: "cbMedio", inputWidth: 200, options: []},
					{type: "input", label: "Título", labelWidth: 65, name: "tbTitulo", validate: "NotEmpty"},
					{type: "input", label: "Programa", labelWidth: 65, name: "tbPrograma", validate: "NotEmpty"},
					{type: "input", label: "Pal.Clave", labelWidth: 65, name: "tbKeywords", validate: "NotEmpty"},
					{type: "input", label: "Hora", labelWidth: 65, name: "tbHora", inputWidth: 120, validate: "NotEmpty"},
					{type: "input", label: "Mención", labelWidth: 65, name: "tbMencion", inputWidth: 120, validate: "NotEmpty"},
					{type: "file", label: "Archivo", labelWidth: 65, name: "tbArchivo"},
					{type: "block", offsetLeft: 47, inputWidth: 325, list:[
						{type: "button", value: "<i class='fa fa-floppy-o'></i> Guardar", name: "btGuarda"},
						{type: "newcolumn"},
						{type: "button", value: "<i class='fa fa-bell-o'></i> Enviar alerta", name: "btAlerta"}
					]}
				]
			};
			function load_audio() {
				var a = $(this);
				myWindow = dhxWins.createWindow("win-audio", 0, 0, 720, 600);
				myWindow.center();
				myWindow.keepInViewport();
				myWindow.setText("Espere...");
				myWindow.progressOn();
				var p = {
					_token: "{{ csrf_token() }}",
					fid: a.data("fid"),
					mid: a.data("mid"),
					aid: a.data("aid")
				};
				$.post("{{ url('ajax/data/if_video') }}", p, function(response) {
					myWindow.progressOff();
					if(response.success) {
						var audio_prop = response.prop;
						myWindow.setText("Revisando clip de video | " + audio_prop.articulo + " [De: " + audio_prop.medio + "]");
						winToolbar = myWindow.attachToolbar();
						winToolbar.setIconsPath(ics_path);
						winToolbar.addText("lbFile", null, "<b>" + audio_prop.archivo + "</b>");
						winToolbar.addSeparator();
						winToolbar.addText("lbFile", null, "<i>Mención en " + audio_prop.inicio + "</i>");
						winToolbar.addButton("btGoto", null, "Ir a la mención", "/icons/goto.png");
						winToolbar.addButton("btClose", null, "Cerrar", "/icons/close.png");
						myWindow.attachHTMLString('<div id="video-container"><div></div></div>');
						$("#video-container").children("div").eq(0).append(
							$("<video/>").attr({id:"video-file", controls: true, autoplay: true}).append(
								$("<source/>").attr({src:response.source, type:"video/mp4"})
							).append("Su navegador no soporta el control de audio.")
						);
						winToolbar.attachEvent("onClick", function(id) {
							switch(id) {
								case "btGoto":
									var inicio = audio_prop.inicio.split(":");
									var start = parseInt(inicio[0]) * 60 + parseInt(inicio[1]);
									var audio = document.getElementById("video-file");
									audio.currentTime = start;
									break;
								case "btClose": myWindow.close();break;
								default: break;
							}
						});
					}
					else {
						myWindow.close();
						dhtmlx.alert({
							title: "Error",
							type: "alert-error",
							text: response.message
						});
					}
				}, "json");
			}
			function populate_videos(videos) {
				$("#articulos-container").empty();
				for(var i in videos) {
					var video = videos[i];
					$("#articulos-container").append(
						$("<a/>").attr({
							href: "javascript:void(0)"
						}).data({
							fid: video.fid,
							mid: video.mid,
							aid: video.aid
						}).append(
							$("<div/>").append(
								$("<p/>").append($("<b/>").html(video.nom))
							).append(
								$("<p/>").html(video.nmd)
							).append(
								$("<p/>").html("Publicado: " + video.fup)
							)
						).on("click", load_audio)
					);
				}
			}
			function initComponents(data) {
				ics_path = data.ics_path;
				dhxWins = new dhtmlXWindows();
				dhxWins.attachViewportTo(document.body);
				myLayout = new dhtmlXLayoutObject(document.body, "2U");
				myLayout.attachHeader("header");
				myLayout.cells("a").setWidth(500);
				myLayout.cells("a").setText("Registrar clip de video");
				myLayout.cells("b").setText("Videos del día");
				myLayout.cells("b").attachHTMLString('<div id="articulos-container"></div>');
				//cargar videos
				var videos = data.files;
				populate_videos(videos);
				//formulario de ingreso
				myForm = myLayout.cells("a").attachForm();
					config.formData[3].options = data.cbmedio;
				myForm.loadStruct(config.formData);
				myForm.enableLiveValidation(true);
				myForm.attachEvent("onButtonClick", function(id) {
					switch(id) {
						case "btGuarda":
							myForm.validate();
							var form_data = new FormData();
							$.each($("input[type=file]")[0].files, function(i, file) {
								form_data.append("file-" + i, file);
							});
							form_data.append("_token", "{{ csrf_token() }}");
							form_data.append("mid", myForm.getItemValue("cbMedio"));
							form_data.append("ttl", myForm.getItemValue("tbTitulo"));
							form_data.append("prg", myForm.getItemValue("tbPrograma"));
							form_data.append("kws", myForm.getItemValue("tbKeywords"));
							form_data.append("hra", myForm.getItemValue("tbHora"));
							form_data.append("mnc", myForm.getItemValue("tbMencion"));
							myLayout.progressOn();
							$.ajax({
								url: "{{ url('ajax/data/sv_video') }}",
								data: form_data,
								cache: false,
								contentType: false,
								processData: false,
								type: "POST",
								success: function(response) {
									if(response.success) {
										myForm.setItemValue("tbTitulo", "");
										myForm.setItemValue("tbPrograma", "");
										myForm.setItemValue("tbKeywords", "");
										myForm.setItemValue("tbHora", "");
										myForm.setItemValue("tbMencion", "");
										$("input[name=tbArchivo]").val("");
										dhtmlx.alert({
											title: "Operación completada",
											ok: "Aceptar",
											text: response.message
										});
										//recargar videos
										$.post("{{ url('ajax/components/genera-articulos-tv') }}", { _token: "{{ csrf_token() }}" }, function(rsp) {
											if(rsp.success) {
												populate_videos(rsp.data.files);
											}
											else dhtmlx.alert({
												title: "Error",
												type: "alert-error",
												text: rsp.message
											});
											myLayout.progressOff();
										}, "json");
									}
									else {
										myLayout.progressOff();
										dhtmlx.alert({
											title: "Error",
											type: "alert-error",
											text: response.message
										});
									}
								}
							});
							break;
						case "btAlerta": break;
					}
				});
				$("textarea[name=tbCaptura]").pastableTextarea();
				$("textarea[name=tbCaptura]").on('pasteImage', function (ev, idata){
					$("#dv-captura").empty().append(
						$("<img/>").attr({src:idata.dataURL, title:"Captura tomada"}).addClass("img-adjust")
					);
				}).on('pasteImageError', function(ev, idata){
					alert('Oops: ' + idata.message);
					if(idata.url){
						alert('But we got its url anyway:' + idata.url)
					}
				}).on('pasteText', function (ev, idata){
					console.log("text: " + idata.text);
				});
			}
			function init() {
				$.post("{{ url('ajax/components/genera-articulos-tv') }}", { _token: "{{ csrf_token() }}" }, function(response) {
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