<?php if (empty($message)) { // no response, show the form ?>
<?php echo form_open(uri_string(), array("id" => "password_reset", "class" => "form-horizontal")); ?>
<fieldset>
	<legend>Password_reset</legend>
	<div class="status"></div>
	<div class="control-group">
		<label class="control-label" for="password">Password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($var->password)) { echo $var->password; } ?>" name="password" id="password" placeholder="Password" maxlength="128" />
			<span class="form_error text-error" id="password_error"><?php if (isset($errors->password)) { echo $errors->password; } ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="confirm">Confirm your password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($var->confirm)) { echo $var->confirm; } ?>" name="confirm" id="confirm" placeholder="Confirm your password" maxlength="128" />
			<span class="form_error text-error" id="confirm_error"><?php if (isset($errors->confirm)) { echo $errors->confirm; } ?></span>
		</div>
	</div>

	<div class="form-actions" id="submit_group">
		<button type="submit" class="btn btn-primary" id="submit_button">Save</button>
		or
		<a href="javascript: window.history.go(-1)">Cancel</a>
	</div>
</fieldset>
<?php echo form_close(); ?>
<?php } else { echo $message; }  ?>