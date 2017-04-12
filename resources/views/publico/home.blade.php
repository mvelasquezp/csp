<!DOCTYPE html>
<html>
	<head>
		<title>Bienvenido</title>
		<script type="text/javascript" src="{{ asset('js/jquery-1.12.1.min.js') }}"></script>
		<script type="text/javascript">
			function get_login() {
				var p = {
					user: document.getElementById("user").value,
					pswd: document.getElementById("pswd").value,
					_token: '{{ csrf_token() }}'
				};
				$.post("{{ url('ajax/auth/login') }}", p, function(response) {
					if(response.success) {
						location.href = "{{ url('intranet' )}}"
					}
					else {
						alert(response.message);
					}
				}, "json");
			}
			function init() {
				$("#bt-login").on("click", get_login);
			}
			$(init);
		</script>
	</head>
	<body>
		<div>
			<label>
				Usuario 
				<input type="text" id="user" />
			</label>
			<br/>
			<label>
				Clave 
				<input type="password" id="pswd" />
			</label>
			<br/>
			<input type="button" id="bt-login" value="Login" />
		</div>
	</body>
</html>