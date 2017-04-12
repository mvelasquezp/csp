<!DOCTYPE html>
<html>
	<head>
		<title>Subida de artículos - Televisión</title>
		<meta charset="utf-8" />
		<script type="text/javascript" src="{{ asset('js/jquery-1.12.1.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('dhtmlx/codebase/dhtmlx.js') }}"></script>
		<style type="text/css">
			html, body { height: 100%;margin: 0px;padding: 0px;overflow: hidden;width: 100%; }
			body{background-color:#344557;}
			#header{background:#019d49;color:#f8f8f8;display:none;padding:5px;}
			#header>img{display:inline-block;height:18px;}
			#header>div{float:right;}
			#header>div>a{color:#f8f8f8;display:inline-block;font-family:Verdana;font-size:12px;padding:3px;text-decoration:none;transition:background 150ms;}
			#header>div>a:hover{background:rgba(0,0,0,.15);}
			#loader{left: 50%;margin-left:-200px;margin-top:-150px;position:absolute;top: 50%;}
			.tiles-container{padding:5px;height:100%;width:100%;}
			.tiles-container>a{display:inline-block;vertical-align:top;margin:5px;box-shadow:1px 1px 2px #000;padding:0;}
			.tiles-container>a>div{height:75px;width:250px;padding:10px;}
			.tiles-container>a>div>p{margin:0;padding:0;}
			.selected-block{background-color:crimson;}
		</style>
		<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.selectareas.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ asset('dhtmlx/codebase/dhtmlx.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}" />
		<script type="text/javascript">
			var dhxWins, myLayout, myWindow, myEditWindow, myGrid, myTabbar, gridBloques, myForm, myAttachWindow, attachForm;
			var ics_path, hiddenRows, ultraData;
			var config = {
				editFormData: [
					{type: "settings", position: "label-left", labelWidth: 65, offsetLeft: 20, inputWidth: 275},
					{type: "input", label: "Título", name: "tbTitulo"},
					{type: "input", label: "Texto", name: "tbText", rows:10, inputWidth: 350},
					{type: "block", offsetLeft: 47, inputWidth: 400, list:[
						{type: "button", value: "<i class='fa fa-floppy-o'></i> Guardar", name: "btGuarda"},
						{type: "newcolumn"},
						{type: "button", value: "<i class='fa fa-bell-o'></i> Cancelar", name: "btAlerta"}
					]}
				],
				attachFormData: [
					{type: "settings", position: "label-left", labelWidth: 120, offsetLeft: 20, inputWidth: 200},
					{type: "combo", label: "Dónde adjuntar", name: "cbWhere", options:[]},
					{type: "block", offsetLeft: 47, inputWidth: 400, list:[
						{type: "button", value: "<i class='fa fa-floppy-o'></i> Adjuntar", name: "btAdjunta"},
						{type: "newcolumn"},
						{type: "button", value: "<i class='fa fa-bell-o'></i> Cancelar", name: "btAlerta"}
					]}
				]
			};
			function guardaEstructuraBoletin() {
				var id_bloque = $(".selected-block").eq(0).data("bid");
				var nombre_bloque = $(".selected-block").eq(0).children("div").eq(0).children("p").eq(0).children("b").eq(0).html();
				var nBloques = ultraData.length;
				var idx_bloque = 0;
				for(var i = 0; i < nBloques; i++) {
					if(ultraData[i].id == id_bloque) idx_bloque = i;
				}//idx_bloque es el indice del bloque actual
				//recorrer grid y guardar datos
				var nRows = gridBloques.getRowsNum();
            	for(var i = 0; i < nRows; i++) {
            		var cRowId = gridBloques.getRowId(i);
            		if(hiddenRows.indexOf(cRowId) == -1) {//articulo normal
            			articulos.push({
            				name: gridBloques.cells2(i,1).getValue(),
							medio: gridBloques.cells2(i,2).getValue(),
							pages: gridBloques.cells2(i,3).getValue(),
							text: gridBloques.cells2(i,4).getValue()
            			});
            		}
            		else {//es adjunto
            			//
            		}
            	}
			}
			function myWindowOnClose(windowId) {
				if(windowId.getId() == "myWindow") {
					if(window.confirm("¿Desea guardar los cambios?")) guardaEstructuraBoletin();
					$(".selected-block").removeClass("selected-block");
				}
				return true;
			}
			function carga_boletines() {
				myGrid.clearAll();
				myLayout.cells("a").progressOn();
	            myGrid.load("{{ url('data/grids/boletines_dia') }}", function() {
	            	myGrid.forEachRow(function(rowId) {
	            		myGrid.setCellTextStyle(rowId, 6, "cursor:pointer;");
	            	});
	            	myLayout.cells("a").progressOff();
	            });
			}
			function gridBloquesBuilIndexes() {
				var nRows = gridBloques.getRowsNum();
				var j = 1;
            	for(var i = 0; i < nRows; i++) {
            		if(hiddenRows.indexOf(gridBloques.getRowId(i)) == -1) {
            			gridBloques.cells2(i,0).setValue(j);
            			j++;
            		}
            	}
            	/*var idx = 1;
            	gridBloques.forEachRow(function(rowId) {
            		if(hiddenRows.indexOf(rowId) == -1) {
            			gridBloques.cells(rowId,0).setValue(idx);
            			idx++;
            		}
            	});*/
			}
			function gridBloquesOnDrop(sId,tId,sObj,tObj,sInd,tInd) {
				gridBloquesBuilIndexes();
			}
			function myFormOnButtonClick(name) {
				switch(name) {
					case "btGuarda":
						var texto = myForm.getInput("tbText").value;
						myForm.setItemValue("tbText", gridBloques.cells(gridBloques.getSelectedRowId(),4).setValue(texto));
						myEditWindow.close();
						break;
					case "btAlerta": myEditWindow.close();break;
					default: break;
				}
			}
			function gridBloquesOnRowHide(rowId,state) {
				if(state) hiddenRows.push(rowId);
				else {
					var index = hiddenRows.indexOf(rowId);
					hiddenRows.splice(index, 1);
				}
				gridBloquesBuilIndexes();
			}
			function attachFormOnButtonClick(id) {
				switch(id) {
					case "btAdjunta":
						var clickedRowId = gridBloques.getSelectedRowId();
						var selRowId = attachForm.getItemValue("cbWhere");
						var adjuntos = gridBloques.cells(selRowId,10).getValue();
						adjuntos = adjuntos + (adjuntos == "" ? "" : ",") + clickedRowId;
						gridBloques.cells(selRowId,10).setValue(adjuntos);
						gridBloques.setRowHidden(clickedRowId,true);
						break;
					default:break;
				}
				myAttachWindow.close();
			}
			function gridBloquesOnRowSelect(rowId, colId) {
				switch(colId) {
					case 5:
						myEditWindow = dhxWins.createWindow("myEditWindow",0,0,480,320);
						myEditWindow.center();
						myEditWindow.keepInViewport();
						myEditWindow.setText("Editando artículo");
						myForm = myEditWindow.attachForm();
						myForm.loadStruct(config.editFormData);
						myForm.setItemValue("tbTitulo", gridBloques.cells(rowId,1).getValue());
						myForm.setItemValue("tbText", gridBloques.cells(rowId,4).getValue());
						myForm.attachEvent("onButtonClick", myFormOnButtonClick);
						break;
					case 6:
						dhtmlx.confirm({
							title: "Confirmar acción",
							ok: "Si",
							cancel: "No",
							text: "¿Seguro que desea eliminar el artículo seleccionado?",
							callback: function(result){
								if(result) {
									//gridBloques.setRowHidden(rowId, true);
									gridBloques.deleteRow(rowId);
									gridBloquesBuilIndexes();
								}
							}
						});
						break;
					case 7:
						myAttachWindow = dhxWins.createWindow("myAttachWindow",0,0,480,120);
						myAttachWindow.center();
						myAttachWindow.keepInViewport();
						myAttachWindow.setText("Adjuntar artículo");
						var combo = [{value:0,text:"[Seleccione]",selected:true}];
						var nRows = gridBloques.getRowsNum();
		            	for(var i = 0; i < nRows; i++) {
		            		var cRowId = gridBloques.getRowId(i);
		            		if(hiddenRows.indexOf(cRowId) == -1 && cRowId != rowId) {
		            			combo.push({
		            				value: cRowId,
		            				text: gridBloques.cells2(i,1).getValue()
		            			});
		            		}
		            	}
		            	config.attachFormData[1].options = combo;
						attachForm = myAttachWindow.attachForm();
						attachForm.loadStruct(config.attachFormData);
						attachForm.attachEvent("onButtonClick",attachFormOnButtonClick);
						break;
					case 8:
						var destino = prompt("Seleccione fila de destino", 1);
						var rowDestino = gridBloques.getRowId(parseInt(destino) - 1);
						gridBloques.moveRow(rowId,"row_sibling",rowDestino);
						break;
					case 9: break;
					default: break;
				}
			}
			function bloqueOnClick() {
				hiddenRows = new Array();
				var a = $(this);
				a.addClass("selected-block");
				var bid = a.data("bid");
				var bloque = a.data("aid");
				/*if(!dhxWins.isWindow("myWindow"))*/ myWindow = dhxWins.createWindow("myWindow",0,0,1080,480);
				myWindow.setText("Bloque " + bloque);
				myWindow.center();
				myWindow.keepInViewport();
				myWindow.attachEvent("onClose",myWindowOnClose);
				gridBloques = myWindow.attachGrid();
				gridBloques.setImagePath(ics_path);
				gridBloques.setIconsPath(ics_path);
				gridBloques.setInitWidths("20,160,160,80,*,30,30,30,30,30,0");
				gridBloques.setHeader(",Título,Medio,Página,Texto,Acciones,#cspan,#cspan,#cspan,#cspan,x");
                gridBloques.setColAlign("right.left,left,left,left,left,left,left,left,left,left");
                gridBloques.setColTypes("ron,rotxt,rotxt,rotxt,rotxt,img,img,img,img,img,rotxt");
                gridBloques.attachHeader("#rspan,#text_filter,#select_filter,#select_filter,#text_filter");
				gridBloques.enableDragAndDrop(true);
				gridBloques.init();
				gridBloques.attachEvent("onDrop",gridBloquesOnDrop);
				gridBloques.attachEvent("onRowSelect",gridBloquesOnRowSelect);
				gridBloques.attachEvent("onRowHide",gridBloquesOnRowHide);
                gridBloques.load("{{ url('data/grids/ls_articulos_bloque') }}/" + bid, function() {
                	gridBloquesBuilIndexes();
                	gridBloques.forEachRow(function(rId) {
                		for(var k = 5; k < 10; k++) gridBloques.setCellTextStyle(rId,k,"cursor:pointer;");
                	});
                });
			}
			function myGridOnRowSelect(rowId, colId) {
				if(colId == 6) {
					myLayout.cells("a").collapse();
					myLayout.cells("b").detachObject();
					myLayout.cells("b").attachHTMLString('<div class="tiles-container" id="tiles-container"></div>');
					var p = {_token:'{{ csrf_token() }}', bid:rowId};
					$.post("{{ url('ajax/components/est_boletin') }}", p, function(response) {
						if(response.success) {
							ultraData = new Array();
							var nu_bloques = response.num;
							var bloques = response.struct;
							for(var i = 0; i < nu_bloques; i++) {
								var bloque = bloques[i];
								ultraData.push({id:bloque.id,name:bloque.name,arts:[]});
								//
								$("#tiles-container").append(
									$("<a/>").attr("href","javascript:void(0)").data("aid", bloque.id).data("bid",rowId).append(
										$("<div/>").append(
											$("<p/>").append($("<b/>").html(bloque.nombre))
										)
									).on("click", bloqueOnClick)
								);/*
								var id_tab = "tab_" + i;
								if(i == 0) myTabbar.addTab(id_tab, bloque.nombre, null, null, true);
								else myTabbar.addTab(id_tab, bloque.nombre);
								gridBloques.push(myTabbar.tabs(id_tab).attachGrid());
								gridBloques[i].setImagePath(ics_path);
								gridBloques[i].setIconsPath(ics_path);
								gridBloques[i].setInitWidths("200,200,100,*,30,30,30,30,30");
								gridBloques[i].setHeader("Título,Medio,Página,Texto,Acciones,#cspan,#cspan,#cspan,#cspan");
				                gridBloques[i].setColAlign("left,left,left,left,left,left,left,left,left");
				                gridBloques[i].setColTypes("rotxt,rotxt,rotxt,rotxt,img,img,img,img,img");
				                gridBloques[i].attachHeader("#text_filter,#select_filter,#select_filter,#text_filter");
								gridBloques[i].enableDragAndDrop(true);
				                gridBloques[i].load("{{ url('data/grids/ls_articulos_bloque') }}/" + rowId);
								gridBloques[i].init();
								gridBloques[i].attachEvent("onRowSelect", function(rId,cId) {
									alert("grid " + i + " | rowId = " + rId + " | colId = " + cId);
									switch(cId) {
										case 4: break;
										case 5: gridBloques[i].deleteRow(rId);break;
										case 6: break;
										case 7: break;
										case 8: break;
										default: break;
									}
								});*/
							}
						}
						else alert(response.message);
						myLayout.cells("b").progressOff();
					}, "json");
				}
			}
			function initComponents(data) {
				ics_path = data.ics_path;
				dhxWins = new dhtmlXWindows();
				dhxWins.attachViewportTo(document.body);
				myLayout = new dhtmlXLayoutObject(document.body, "2U");
				myLayout.attachHeader("header");
				myLayout.cells("a").setWidth(640);
				myLayout.cells("a").setText("Boletines del día");
				//myLayout.cells("b").attachHTMLString('<div></div>');
				myLayout.cells("b").setText("Bloques del boletín");
				myLayout.cells("b").attachHTMLString('<p style="font-family:Verdana;font-size:12px;margin:0;padding:15px;">Seleccione un boletin de la lista para cargar los bloques</p>');
				myGrid = myLayout.cells("a").attachGrid();
				myGrid.setImagePath(data.ics_path);
				myGrid.setIconsPath(data.ics_path);
				myGrid.setInitWidths("40,*,100,120,100,100,60");
				myGrid.setHeader("ID,Nombre,Tipo,Cliente,Estado,Encargado,Accion");
                myGrid.setColAlign("right,left,left,left,left,left,left");
                myGrid.setColTypes("rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,img");
                myGrid.attachHeader("#rspan,#text_filter,#select_filter,#text_filter,#select_filter,#text_filter,#rspan");
	            myGrid.init();
	            myGrid.attachEvent("onRowSelect", myGridOnRowSelect);
	            carga_boletines();
			}
			function init() {
				$.post("{{ url('ajax/components/boletines') }}", { _token: "{{ csrf_token() }}" }, function(response) {
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
				<a href="{{ url('intranet/genera-articulos-web') }}">Ingreso de artículos</a> | 
				<a href="{{ url('#') }}">Generar boletines</a> | 
				<a href="{{ url('#') }}">Revisión de boletines</a> | 
				<a href="{{ url('intranet') }}">Volver</a>
			</div>
		</div>
	</body>
</html>