<?php if (empty($message)) { // no response, show the form ?>
<?php echo form_open(uri_string(), array("id" => "signup", "class" => "form-horizontal")); ?>
<fieldset>
	<legend>Signup</legend>
	<div class="status"></div>
	<div class="control-group">
		<label class="control-label" for="email">Email Address</label>
		<div class="controls">
			<input type="email" value="<?php if (isset($var->email)) { echo $var->email; } ?>" name="email" id="email" placeholder="Email Address" maxlength="128" />
			<span class="form_error text-error" id="email_error"><?php if (isset($errors->email)) { echo $errors->email; } ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="display_name">Your name</label>
		<div class="controls">
			<input type="text" value="<?php if (isset($var->display_name)) { echo $var->display_name; } ?>" name="display_name" id="display_name" placeholder="Your name" maxlength="128" />
			<span class="form_error text-error" id="display_name_error"><?php if (isset($errors->display_name)) { echo $errors->display_name; } ?></span>
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