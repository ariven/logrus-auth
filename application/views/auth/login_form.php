	<?php echo form_open("/auth/login", array("id" => "login", "class" => "form form-horizontal")); ?>
	<fieldset>
		<legend>Login</legend>

		<div class="control-group">
		<label class="control-label" for="email">Email Address</label>
		<div class="controls">
			<input type="email" value="<?php if (isset($email)) { echo $email; } ?>" name="email" id="email" placeholder="Email Address" size="50" maxlength="128" />
			<span class="form_error" id="email_error"><?php if (isset($error['email'])) { echo $error['email']; } ?></span>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="password">Password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($password)) { echo $password; } ?>" name="password" id="password" placeholder="Password" size="50" maxlength="100" />
			<span class="form_error" id="password_error"><?php if (isset($error['password'])) { echo $error['password']; } ?></span>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary" id="submit">Log In</button> or <a href="/">Cancel</a>
		</div>
	</div>
	</fieldset>
	<?php echo form_close(); ?>