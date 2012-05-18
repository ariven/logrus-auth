<fieldset>
	<legend>Change your password</legend>
	<?php echo form_open("/auth/change_password", array("id" => "change_password", "class" => "form-horizontal")); ?>

	<div class="control-group">
		<label class="control-label" for="password">Current Password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($password)) { echo $password; } ?>" name="password" id="password" placeholder="Current Password" size="50" maxlength="100" />
			<span class="form_error" id="password_error"><?php if (isset($error['password'])) { echo $error['password']; } ?></span>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="new_password">New Password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($new_password)) { echo $new_password; } ?>" name="new_password" id="new_password" placeholder="New Password" size="50" maxlength="100" />
			<span class="form_error" id="new_password_error"><?php if (isset($error['new_password'])) { echo $error['new_password']; } ?></span>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="confirm_password">Confirm new password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($confirm_password)) { echo $confirm_password; } ?>" name="confirm_password" id="confirm_password" placeholder="Confirm new password" size="50" maxlength="100" />
			<span class="form_error" id="confirm_password_error"><?php if (isset($error['confirm_password'])) { echo $error['confirm_password']; } ?></span>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary" id="submit">Save</button> or <a href="javascript: window.history.go(-1)">Cancel</a>
		</div>
	</div>
	<?php echo form_close(); ?>
</fieldset>