
<form method="GET" action="">
	<input type="hidden" name="action" value="login"/>
	<table class="login" align="center">
		<tr><td colspan="2"><h1>Log in</h1></td></tr>
		<? if (!$wiki->isValidAuthentication) {?> <tr><td></td><td><h2>Invalid Username or Password</h2></td></tr> <?}?>
		<tr><td>Username:</td><td><input type="text" name="username"/></td></tr>
		<tr><td>Password:</td><td><input type="password" name="password"/></td></tr>
		<tr><td colspan="2" align="center"><input type="submit" name="login" value="Login"/></td></tr>
	</table>
</form>