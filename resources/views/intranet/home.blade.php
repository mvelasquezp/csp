<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Bienvenido a Spotlight</title>
		<link rel="stylesheet" type="text/css" href="{{ asset('css/home.css') }}" />
		<script type="text/javascript" src="{{ asset('js/jquery-1.12.1.min.js') }}"></script>
		<style type="text/css">
			.full-screen-div{background-color:rgba(0,0,0,.5);display:none;height:100%;position:absolute;left:0;top:0;width:100%;}
			.full-screen-div>div{left:50%;top:50%;height:230px;width:800px;margin-left:-400px;margin-top:-115px;position:absolute;background-color:#f8f8f8;padding:20px;border-radius:10px;}
			.full-screen-div>div>h3{margin:0;}
			.full-screen-div>div>ul{list-style-type:none;margin:0;padding:20px 0;text-align:center;}
			.full-screen-div>div>ul>li{display:inline-block;vertical-align:top;}
			.full-screen-div>div>ul>li>a{display:block;height:120px;width:120px;color:#000;text-decoration:none;margin:0 10px;padding:45px 0;}
			.square-1{background-color:rgb(140,180,230);}
			.square-2{background-color:rgb(230,185,185);}
			.square-3{background-color:rgb(215,230,190);}
			.square-4{background-color:rgb(205,195,220);}
			.square-5{background-color:rgb(250,215,180);}
		</style>
		<script type="text/javascript">
			function showOpts() {
				$(".full-screen-div").fadeIn(150);
			}
			$(function() {
				$(".full-screen-div").on("click", function() {$(".full-screen-div").fadeOut(150);});
			});
		</script>
	</head>
	<body>
		<div>
			<div>
				<header>
					<table>
						<thead></thead>
						<tbody>
							<tr>
								<td style="text-align:center;" width="15%">
									<img src="{{ asset('images/logo.png') }}" />
								</td>
								<td style="text-align:right;" width="85%">
									<div class="ls-container">
										<a href="{{ url('intranet/busqueda') }}" class="ls-item">
											<img src="{{ asset('images/icons/search.png') }}" />
											<p>Búsqueda</p>
										</a>
										<a href="#" class="ls-item">
											<img src="{{ asset('images/icons/users.png') }}" />
											<p>Usuarios</p>
										</a>
										<a href="#" class="ls-item">
											<img src="{{ asset('images/icons/register.png') }}" />
											<p>Registro</p>
										</a>
										<a href="#" class="ls-item">
											<img src="{{ asset('images/icons/settings.png') }}" />
											<p>Ajustes</p>
										</a>
										<a href="#" class="ls-item">
											<img src="{{ asset('images/icons/logout.png') }}" />
											<p>Salir</p>
										</a>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</header>
				<nav>
					<a href="{{ url('intranet/sube-diarios') }}">Subida de diarios</a> | 
					<a href="javascript:showOpts()">Ingreso de artículos</a> | 
					<a href="{{ url('intranet/boletines') }}">Generar boletines</a> | 
					<a href="#">Revisión de boletines</a>
				</nav>
				<section>
					<a href="#" class="square square-big square-white">
						<table>
							<tbody>
								<tr>
									<td rowspan="2" width="25%" style="vertical-align:middle;">
										<img src="{{ asset('images/icons/user.png') }}" />
									</td>
									<td width="75%">
										<h3>Bienvenido</h3>
										<p id="p-username">mvelasquezp</p>
									</td>
								</tr>
								<tr class="tr-footer">
									<td>
										<p>Último ingreso: <span id="last-access">2017-03-05</span></p>
									</td>
								</tr>
							</tbody>
						</table>
					</a>
					<a href="#" class="square square-big square-blue">
						<h4>Portadas del día</h4>
					</a>
					<a href="#" class="square square-small square-gray">
						<div>
							<div>
								<img src="{{ asset('images/icons/chart.png') }}" />
								<p>Resumen y estadísticas</p>
							</div>
						</div>
					</a>
					<div class="square square-small square-green">
						<h4>Mensajes y recordatorios</h4>
						<ul>
							<li><a href="#">Enviar boletín a Minas S.A.C.</a></li>
							<li><a href="#">Comprar el pan</a></li>
							<li><a href="#">Revisar edición especial El Tromercio</a></li>
						</ul>
					</div>
				</section>
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