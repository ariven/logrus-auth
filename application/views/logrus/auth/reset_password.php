<?php if (empty($message)) { // no response, show the form ?>
<?php echo form_open(uri_string(), array("id" => "reset_password", "class" => "form-horizontal")); ?>
<fieldset>
	<legend>Reset_password</legend>
	<div class="status"></div>
	<div class="control-group">
		<label class="control-label" for="email">Your email address</label>
		<div class="controls">
			<input type="email" value="<?php if (isset($var->email)) { echo $var->email; } ?>" name="email" id="email" placeholder="Your email address" maxlength="128" />
			<span class="form_error text-error" id="email_error"><?php if (isset($errors->email)) { echo $errors->email; } ?></span>
		</div>
	</div>

	<div class="form-actions" id="submit_group">
		<button type="submit" class="btn btn-primary" id="submit_button">Reset my password</button>
		or
		<a href="javascript: window.history.go(-1)">Cancel</a>
	</div>
</fieldset>
<?php echo form_close(); ?>
<?php } else { echo $message; }  ?>