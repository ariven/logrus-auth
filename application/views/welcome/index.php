<h1>Welcome to the Authenticator</h1>

<?php
	if ($logged_in)
	{
?>
		You can <a href="/auth/logout">log out here</a>
<?php
	}
	else
	{
?>
	<a href="/auth/login">Log In Here</a>

<?php

	}
?>