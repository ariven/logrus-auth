	<?php echo form_open("/auth/reset_password", array("id"    => "reset_password",
													   "class" => "form-horizontal")); ?>
	<fieldset>
		<legend>Reset your password</legend>

		<p>This will send an email to your email address.  In that email will be instructions on the next step
		that you need to take to reset your password.</p>
		<div class="control-group">
		<label class="control-label" for="email">Your Email Address</label>

		<div class="controls">
			<input type="email" value="<?php if (isset($email))
			{
				echo $email;
			} ?>" name="email" id="email" placeholder="Your Email Address" size="100" maxlength="100"/>
			<span class="form_error" id="email_error"><?php if (isset($error['email']))
			{
				echo $error['email'];
			} ?></span>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary" id="submit">Reset Password</button>
			or <a href="javascript: window.history.go(-1)">Cancel</a>
		</div>
	</div>
	</fieldset>
	<?php echo form_close(); ?>
