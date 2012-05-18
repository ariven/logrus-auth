<fieldset>
	<legend>signup</legend>
	<?php echo form_open("/auth/signup", array("id" => "signup", "class" => "form-horizontal")); ?>

	<div class="control-group">
		<label class="control-label" for="email">* Email Address</label>
		<div class="controls">
			<input type="email" value="<?php if (isset($email)) { echo $email; } ?>" name="email" id="email" placeholder="Email Address" size="50" maxlength="128" />
			<span class="form_error" id="email_error"><?php if (isset($error['email'])) { echo $error['email']; } ?></span>
			<span id="account_exists"></span>
			<br />
			<span class="label label-important">Important</span> a confirmation email will be sent to your email address. You will need to validate your address by following instructions included
			in that email before full site functionality is available.
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="email">* Password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($password)) { echo $password; } ?>" name="password" id="password" placeholder="Password" size="50" maxlength="128" />
			<span class="form_error" id="password_error"><?php if (isset($error['password'])) { echo $error['password']; } ?></span>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="email">* Confirm Password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($confirm)) { echo $confirm; } ?>" name="confirm" id="confirm" placeholder="confirm" size="50" maxlength="128" />
			<span class="form_error" id="confirm_error"><?php if (isset($error['confirm'])) { echo $error['confirm']; } ?></span>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="display_name">Display Name</label>
		<div class="controls">
			<input type="text" value="<?php if (isset($display_name)) { echo $display_name; } ?>" name="display_name" id="display_name" placeholder="Display Name" size="50" maxlength="128" />
			<span class="form_error" id="display_name_error"><?php if (isset($error['display_name'])) { echo $error['display_name']; } ?></span>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary" id="submit">Save</button> or <a href="javascript: window.history.go(-1)">Cancel</a>
		</div>
	</div>
	<?php echo form_close(); ?>
</fieldset>