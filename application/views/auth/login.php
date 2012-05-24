<fieldset>
	<legend>Log In</legend>
	<h2>Log in using:</h2>

	<a class="btn" href="/auth/oauth2_session/google"><img src="/assets/images/gmail-ac.png" /></a>
	<a class="btn" href="/auth/oauth2_session/windowslive"><img src="/assets/images/hotmail-ac.png" /></a></a>
	<a class="btn" href="/auth/oauth2_session/facebook"><img src="/assets/images/facebook-ac.png" /></a></a><br/>

	<hr />
	<h2>Or log in to a direct account:</h2>
	<?php $this->load->view('auth/login_form'); ?>
	<hr />
	<h2>If you don't have an account you can</h2>
	<a class="btn btn-large" href="/auth/signup"><h3>Sign Up Free</h3></a>

</fieldset>
