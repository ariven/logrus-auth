<fieldset>
	<legend>Confirm your email address</legend>
	<?php echo form_open("/auth/confirm_email", array("id" => "confirm_email", "class" => "form-horizontal")); ?>
	<input type="hidden" value="yes" name="confirm" id="confirm" />
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary" id="submit">Confirm Email</button> or <a href="javascript: window.history.go(-1)">Cancel</a>
		</div>
	</div>
	<?php echo form_close(); ?>
</fieldset>