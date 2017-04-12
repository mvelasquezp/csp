<!DOCTYPE html>
<html>
	<head>
		<title>Subida de diarios</title>
		<meta charset="utf-8" />
		<script type="text/javascript" src="{{ asset('js/jquery-1.12.1.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('dhtmlx/codebase/dhtmlx.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/jquery.selectareas.min.js') }}"></script>
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
			#articulos-container{height:100%;padding:5px;width:100%;}
			#articulos-container>a{background:#fff9c4;box-shadow:1px 1px 2px #404040;display:inline-block;margin:5px;padding:0;text-decoration:none;vertical-align:middle;}
			#articulos-container>a>div{padding:5px;width:120px;}
			#articulos-container>a>div>p{color:#404040;font-family:Verdana;font-size:11px;margin:0;text-align:left;}
			#articulos-container>a>div>p>b{font-size:12px;}
		</style>
		<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.selectareas.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ asset('dhtmlx/codebase/dhtmlx.css') }}" />
		<script type="text/javascript">
			var myLayout, myToolbar, cbMedio, cbPagina, evCbPagina, cropToolbar;
			var selectionExists, imgEscala;
			var config = {
				//
			};
			function carga_pagina() {
				$("#articulos-container").empty();
				var medio = $("input[name=cbMedio]").val();
				var pagina = $("input[name=cbPagina]").val();
				var param = {
					_token: "{{ csrf_token() }}",
					mid: medio,
					pid: pagina,
					sh: $("#dv_img").width() - 25
				};
				$.post("{{ url('ajax/data/img_preview') }}", param, function(rsp) {
					if(rsp.success) {
						$("#dv_img").empty().append(
							$("<img/>").attr({
								id: "img_pagina",
								src: rsp.imgdata,
								style: "margin:0 auto;"
							})
						);
						//cropper
						imgEscala = rsp.escala;
						$("img#img_pagina").selectAreas({
							minSize: [10, 10]
						});
						//articulos
						var articulos = rsp.cortes;
						for(var i in articulos) {
							var articulo = articulos[i];
							$("#articulos-container").append(
								$("<a/>").attr({
									href: "javascript:void(0)"
								}).data({
									id: articulo.aid,
									mid: medio,
									pid: pagina
								}).append(
									$("<div/>").append(
										$("<p/>").append($("<b/>").html(articulo.nom))
									).append(
										$("<p/>").html(articulo.scn)
									).append(
										$("<p/>").html(articulo.num + " corte(s)")
									)
								)
							);
						}
					}
					else alert(rsp.message);
				}, "json");
			}
			function initComponents(data) {
				myLayout = new dhtmlXLayoutObject(document.body, "2U");
				myLayout.attachHeader("header");
				myLayout.cells("b").setWidth(400);
				myLayout.cells("b").setText("Cortes realizados");
				myLayout.cells("b").collapse();
				myLayout.cells("b").attachHTMLString('<div id="articulos-container"></div>');
				myLayout.cells("a").hideHeader();
				//toolbar
				myToolbar = myLayout.attachToolbar();
				myToolbar.setIconsPath(data.ics_path);
				myToolbar.addText("lbMedio", null, "Medio");
				myToolbar.addText("cbMedio", null, '<div id="cbMedio" style="height:22px;margin-top:-3px;width:198px;background:red;"></div>');
				myToolbar.addText("lbPagina", null, "Página");
				myToolbar.addText("cbPagina", null, '<div id="cbPagina" style="height:22px;margin-top:-3px;width:118px;background:red;"></div>');
				myToolbar.addText("lbSeccion", null, "Sección");
				myToolbar.addInput("tbSeccion", null, null, 120);
				myToolbar.addText("lbTitulo", null, "Título");
				myToolbar.addInput("tbTitulo", null, null, 200);
				//combos
				cbMedio = new dhtmlXCombo({
					parent: "cbMedio",
					width: 200,
					filter: true,
					name: "cbMedio",
					items: data.cbmedio
				}).attachEvent("onChange", function(value, text) {
					if(evCbPagina) cbPagina.detachEvent(evCbPagina);
					cbPagina.clearAll();
					cbPagina.setComboText("Espere...");
					cbPagina.setComboValue(0);
					var p = { _token: '{{ csrf_token() }}', mid: value };
					$.post("{{ url('ajax/data/ls_cmb_paginas') }}", p, function(response) {
						cbPagina.setComboText("Seleccione");
						if(response.success) {
							cbPagina.load({options: response.data});
							evCbPagina = cbPagina.attachEvent("onChange", function(v, t) {
								myToolbar.setValue("tbSeccion", "");
								myToolbar.setValue("tbTitulo", "");
								myLayout.cells("a").detachObject();
								myLayout.cells("a").attachHTMLString('<div style="height:100%;overflow:auto;width:100%;"><div id="dv_img" style="margin:0 auto;"></div></div>');
								carga_pagina();
							})
							myLayout.cells("a").detachObject();
						}
						else alert(response.message);
					}, "json");
				});
				cbPagina = new dhtmlXCombo({
					parent: "cbPagina",
					width: 120,
					name: "cbPagina",
					items: [{value:0, text:"Seleccione medio", selected:true}]
				});
				//crop toolbar
				cropToolbar = myLayout.cells("a").attachToolbar();
				cropToolbar.setIconsPath(data.ics_path);
				cropToolbar.addText("lbToolbar", null, "<b>Seleccione una opción</b>");
				cropToolbar.addSeparator();
				cropToolbar.addButton("add", null, "Añadir nueva área", "/icons/crop.png");
				cropToolbar.addButton("reset", null, "Eliminar todos los cortes", "/icons/clear.png");
				cropToolbar.addButton("save", null, "Guardar cortes", "/icons/save.png");
				cropToolbar.attachEvent("onClick", function(id) {
					switch(id) {
						case "add":
							var areaOptions = {
								x: 0,
								y: 0,
								width: 100,
								height: 100,
							};
							$("img#img_pagina").selectAreas("add", areaOptions);
							break;
						case "reset":
							$("img#img_pagina").selectAreas("reset");
							break;
						case "save":
							myLayout.cells("a").progressOn();
							var areas = $("img#img_pagina").selectAreas("areas");
							var cortes = new Array();
							$.each(areas, function (id, area) {
								cortes.push({
									xi: Math.round(area.x * imgEscala),
									yi: Math.round(area.y * imgEscala),
									xf: Math.round((area.x + area.width) * imgEscala),
									yf: Math.round((area.y + area.height) * imgEscala),
									sx: Math.round(area.width * imgEscala),
									sy: Math.round(area.height * imgEscala)
								});
							});
							var params = {
								_token: "{{ csrf_token() }}",
								mid: $("input[name=cbMedio]").val(),
								pid: $("input[name=cbPagina]").val(),
								scc: myToolbar.getValue("tbSeccion"),
								ttl: myToolbar.getValue("tbTitulo"),
								cortes: cortes,
								sx: $("#dv_img").width() - 25
							};
							$.post("{{ url('ajax/data/sv_cortes') }}", params, function(response) {
								myLayout.cells("a").progressOff();
								if(response.success) {
									myToolbar.setValue("tbTitulo", "");
									carga_pagina();
								}
								else {
									alert(response.message);
								}
							}, "json");
							break;
					}
				});
			}
			function init() {
				$.post("{{ url('ajax/components/genera-articulos-prensa') }}", { _token: "{{ csrf_token() }}" }, function(response) {
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