<!DOCTYPE html>
<html>
	<head>
		<title>Subida de diarios</title>
		<meta charset="utf-8" />
		<script type="text/javascript" src="{{ asset('js/jquery-1.12.1.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('dhtmlx/codebase/dhtmlx.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/dropzone.js') }}"></script>
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
		</style>
		<link rel="stylesheet" type="text/css" href="{{ asset('dhtmlx/codebase/dhtmlx.css') }}" />
		<script type="text/javascript">
			var myLayout, toolbarMedio, comboMedio, formUpload, gridMedios;
			var config = {
				formData: [
					{type: "settings", position: "label-left", offsetLeft:15},
					{type: "label", label: "", labelWidth: 300, offsetTop:10},
					{type: "fieldset", label: "Formatos permitidos: JPG, ZIP", list:[{
						type: "upload",
						name: "myFiles",
						titleScreen: false,
						inputWidth: 300,
						inputHeight: 400,
						autoRemove: true,
						url: "",
						_swfLogs: "enabled",
						swfPath: "uploader.swf",
						swfUrl: "php/dhtmlxform_item_upload.php"
					}]}
				]
			};
			function cargar_lista_medios() {
				gridMedios.clearAll();
				myLayout.cells("b").progressOn();
	            gridMedios.load("{{ url('data/grids/ls_medios') }}", function() {
	            	myLayout.cells("b").progressOff();
	            });
			}
			function initComponents(data) {
				myLayout = new dhtmlXLayoutObject(document.body, "2U");
				myLayout.attachHeader("header");
				myLayout.cells("a").setWidth(400);
				myLayout.cells("a").setText("Medio");
				myLayout.cells("b").hideHeader();
				toolbarMedio = myLayout.cells("a").attachToolbar();
				toolbarMedio.addText("lbMedio", null, "Seleccione");
				toolbarMedio.addText("cbMedio", null, '<div id="cbMedio" style="height:22px;margin-top:-2px;width:238px;background:red;"></div>');
				data.cbmedio.unshift({value:"0", text: "[ Seleccione ]", selected: true});
				comboMedio = new dhtmlXCombo({
					parent: "cbMedio",
					width: 240,
					filter: true,
					name: "combo",
					items: data.cbmedio
				});
				comboMedio.attachEvent("onChange", function(value, text){
					myLayout.cells("a").detachObject();
					config.formData[1].label = "Añadir páginas a " + text;
					config.formData[2].list[0].url = "{{ url('ajax/data/upload_files') }}/" + value;
					formUpload = myLayout.cells("a").attachForm(config.formData);
					formUpload.attachEvent("onUploadComplete", function(count){
						dhtmlx.message({
							text: "Se subió " + count + " imagen(es) al servidor",
							expire: 5000
						});
						cargar_lista_medios();
					});
				});
				gridMedios = myLayout.cells("b").attachGrid();
				gridMedios.setImagePath("{{ asset('dhtmlx/skins/skyblue/imgs') }}");
				gridMedios.setIconsPath("{{ asset('dhtmlx/skins/skyblue/imgs/icons') }}");
				gridMedios.setInitWidths("30,160,80,80,50,*");
				gridMedios.setHeader("ID,Medio,Estado,Tipo,Páginas,");
                gridMedios.setColAlign("right,left,left,left,right,left");
                gridMedios.setColTypes("rotxt,rotxt,rotxt,rotxt,ron,rotxt");
                gridMedios.attachHeader("#rspan,#text_filter,#select_filter,#select_filter,#rspan,#rspan");
	            gridMedios.init();
	            cargar_lista_medios();
			}
			function init() {
				$.post("{{ url('ajax/components/sube-diarios') }}", { _token: "{{ csrf_token() }}" }, function(response) {
					if(response.success) {
						$("#loader").fadeOut(500, function() {
							$("#header").show();
							$("#loader").remove();
							initComponents(response.data);
							$("#header>img").height(24);
						});
					}
				}, "json");
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
				<a href="{{ url('intranet/genera-articulos') }}">Ingreso de artículos</a> | 
				<a href="{{ url('#') }}">Generar boletines</a> | 
				<a href="{{ url('#') }}">Revisión de boletines</a> | 
				<a href="{{ url('intranet') }}">Volver</a>
			</div>
		</div>
	</body>
</html>