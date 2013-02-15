<?php if (empty($message)) { // no response, show the form ?>
<?php echo form_open(uri_string(), array("id" => "change_password", "class" => "form-horizontal")); ?>
<fieldset>
	<legend>Change_password</legend>
	<div class="status"></div>
	<div class="control-group">
		<label class="control-label" for="current_password">Current Password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($var->current_password)) { echo $var->current_password; } ?>" name="current_password" id="current_password" placeholder="Current Password" maxlength="128" />
			<span class="form_error text-error" id="current_password_error"><?php if (isset($errors->current_password)) { echo $errors->current_password; } ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="new_password">New password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($var->new_password)) { echo $var->new_password; } ?>" name="new_password" id="new_password" placeholder="New password" maxlength="128" />
			<span class="form_error text-error" id="new_password_error"><?php if (isset($errors->new_password)) { echo $errors->new_password; } ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="confirm_password">Confirm your new password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($var->confirm_password)) { echo $var->confirm_password; } ?>" name="confirm_password" id="confirm_password" placeholder="Confirm your new password" maxlength="128" />
			<span class="form_error text-error" id="confirm_password_error"><?php if (isset($errors->confirm_password)) { echo $errors->confirm_password; } ?></span>
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