<?php if (empty($message)) { // no response, show the form ?>
<?php echo form_open(uri_string(), array("id" => "login", "class" => "form-horizontal")); ?>
<fieldset>
	<legend>Login</legend>
	<div class="status"></div>
<?php
	if ($use_oauth2)
	{
	?>
	<div class="control-group">
	<h2>Log in using:</h2>

	<a class="btn" href="/auth/oauth2_session/google"><img src="/assets/img/gmail-ac.png"/></a>
	<a class="btn" href="/auth/oauth2_session/windowslive"><img src="/assets/img/hotmail-ac.png"/></a></a>
	<a class="btn" href="/auth/oauth2_session/facebook"><img src="/assets/img/facebook-ac.png"/></a></a><br/>
	</div>
	<hr/>
	<h2>Or log in to a direct account:</h2>
	<?php
}
?>
	<div class="control-group">
		<label class="control-label" for="email">Email Address</label>
		<div class="controls">
			<input type="email" value="<?php if (isset($var->email)) { echo $var->email; } ?>" name="email" id="email" placeholder="Email Address" maxlength="128" />
			<span class="form_error text-error" id="email_error"><?php if (isset($errors->email)) { echo $errors->email; } ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="password">Your password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($var->password)) { echo $var->password; } ?>" name="password" id="password" placeholder="Your password" maxlength="128" />
			<span class="form_error text-error" id="password_error"><?php if (isset($errors->password)) { echo $errors->password; } ?></span>
		</div>
	</div>

	<div class="form-actions" id="submit_group">
		<button type="submit" class="btn btn-primary" id="submit_button">Log In</button>
		or
		<a href="javascript: window.history.go(-1)">Cancel</a>
		<br />
		<a href="<?php echo $this->config->item('auth_password_reset_url'); ?>">Forgot your password?</a>
	</div>
</fieldset>
<?php echo form_close(); ?>
<hr/>
<?php
	if ($open_enrollment)
	{
?>
	<hr />
	<h2>If you don't have an account</h2>
	<a class="btn btn-large" href="/auth/signup"><h3>Sign Up Free</h3></a>
	<?php
	}
	?>
<?php } else { echo $message; }  ?>
