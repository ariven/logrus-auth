<fieldset>
	<legend>Login</legend>
	<?php
	if ($auth_use_oauth2)
	{
		?>
		<h2>Log in using:</h2>

		<a class="btn" href="/auth/oauth2_session/google"><img src="/assets/img/gmail-ac.png"/></a>
		<a class="btn" href="/auth/oauth2_session/windowslive"><img src="/assets/img/hotmail-ac.png"/></a></a>
		<a class="btn" href="/auth/oauth2_session/facebook"><img src="/assets/img/facebook-ac.png"/></a></a><br/>

		<hr/>
		<h2>Or log in to a direct account:</h2>
		<?php
	}
	?>
	<?php echo form_open("/auth/login", array("id" => "login", "class" => "form form-horizontal")); ?>
	<fieldset>

		<div class="control-group">
			<label class="control-label" for="email">Email Address</label>

			<div class="controls">
				<input type="email" value="<?php if (isset($email))
				{
					echo $email;
				} ?>" name="email" id="email" placeholder="Email Address" size="50" maxlength="128"/>
				<span class="form_error" id="email_error"><?php if (isset($error['email']))
				{
					echo $error['email'];
				} ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="password">Password</label>

			<div class="controls">
				<input type="password" value="<?php if (isset($password))
				{
					echo $password;
				} ?>" name="password" id="password" placeholder="Password" size="50" maxlength="100"/>
				<span class="form_error" id="password_error"><?php if (isset($error['password']))
				{
					echo $error['password'];
				} ?></span>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary" id="submit">Log In</button>
				or <a href="/">Cancel</a>
				<br />
				<a href="/auth/reset_password">Lost your password?</a>
			</div>
		</div>
	</fieldset>
	<?php echo form_close(); ?>
	<hr/>
	<?php
	if ($auth_open_enrollment)
	{
		?>
		<h2>If you don't have an account</h2>
		<a class="btn btn-large" href="/auth/signup"><h3>Sign Up Free</h3></a>
		<?php
	}
	?>
</fieldset>
