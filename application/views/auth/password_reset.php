<fieldset>
	<legend>Select a new password</legend>
	<?php echo form_open("/auth/password_reset/" . $code, array("id" => "password_reset", "class" => "form-horizontal")); ?>

	<div class="control-group">
		<label class="control-label" for="password">New password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($password)) { echo $password; } ?>" name="password" id="password" placeholder="New password" size="50" maxlength="100" />
			<span class="form_error" id="password_error"><?php if (isset($error['password'])) { echo $error['password']; } ?></span>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="confirm">Confirm new password</label>
		<div class="controls">
			<input type="password" value="<?php if (isset($confirm)) { echo $confirm; } ?>" name="confirm" id="confirm" placeholder="Confirm new password" size="50" maxlength="100" />
			<span class="form_error" id="confirm_error"><?php if (isset($error['confirm'])) { echo $error['confirm']; } ?></span>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary" id="submit">Save</button> or <a href="javascript: window.history.go(-1)">Cancel</a>
		</div>
	</div>
	<?php echo form_close(); ?>
</fieldset>