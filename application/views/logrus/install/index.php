<style>
	label { font-weight: bold; }
</style>
	<h2>Install</h2>
		<p>This </p>
	<table class="table-bordered span8 offset2">
	<tr><td>
	<div class="span6 offset1">
	<?php if (empty($message)) { // no response, show the form ?>
		<?php echo form_open(uri_string(), array("id" => "install", "class" => "form-horizontal")); ?>
		<fieldset>
			<legend>First Member (i.e. YOU)</legend>
            <div class="control-group">
                <label class="control-label" for="member_email">Email Address <span class="label label-important">Required</span></label>
                <div class="controls">
                    <input type="email" value="<?php if (isset($var->member_email)) { echo $var->member_email; } ?>" name="member_email" id="member_email" placeholder="member@example.com"  />
                    <span class="form_error text-error" id="member_email_error"><?php if (isset($errors->member_email)) { echo $errors->member_email; } ?></span>
                    <div class="help-block">
                        This is the valid email address of this member
                    </div>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="member_display_name" >Members display name <span class="label label-important">Required</span></label>
                <div class="controls">
                    <input type="text" value="<?php if (isset($var->member_display_name)) { echo $var->member_display_name; } ?>" name="member_display_name" id="member_display_name" placeholder="Member Name" />
                    <span class="form_error text-error" id="member_display_name_error"><?php if (isset($errors->member_display_name)) { echo $errors->member_display_name; } ?></span>
                    <div class="help-block">
						This is the display name of this first member
                    </div>
                </div>
            </div>

		</fieldset>
	<fieldset>
	<legend>Install</legend>
	<div class="status"></div>
	<div class="control-group">
		<label class="control-label" for="auth_open_enrollment">Open enrollment <span class="label label-important">Required</span></label>
		<div class="controls">
			<select name="auth_open_enrollment" id="auth_open_enrollment" >
				<option value="0" <?php if (! $var->auth_open_enrollment) { echo 'selected'; } ?>>Disabled</option>
				<option value="1" <?php if ($var->auth_open_enrollment) { echo 'selected'; } ?>>Enabled</option>
			</select>
			<span class="form_error text-error" id="auth_open_enrollment_error"><?php if (isset($errors->auth_open_enrollment)) { echo $errors->auth_open_enrollment; } ?></span>
			<div class="help-block">
				This allows new members to create their own accounts
			</div>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="auth_login_url">Login URL <span class="label label-important">Required</span></label>
		<div class="controls">
			<input type="text" value="<?php if (isset($var->auth_login_url)) { echo $var->auth_login_url; } ?>" name="auth_login_url" id="auth_login_url" placeholder="Login URL"  />
			<span class="form_error text-error" id="auth_login_url_error"><?php if (isset($errors->auth_login_url)) { echo $errors->auth_login_url; } ?></span>
			<div class="help-block">
				This is the URL (relative to root) of the URL that contains the login firm
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="auth_logged_in_url">logged in URL <span class="label label-important">Required</span></label>
		<div class="controls">
			<input type="text" value="<?php if (isset($var->auth_logged_in_url)) { echo $var->auth_logged_in_url; } ?>" name="auth_logged_in_url" id="auth_logged_in_url" placeholder="logged in URL"  />
			<span class="form_error text-error" id="auth_logged_in_url_error"><?php if (isset($errors->auth_logged_in_url)) { echo $errors->auth_logged_in_url; } ?></span>
			<div class="help-block">
				This is the URL (relative to root) of the URL to send the member to after they log in
			</div>

		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="auth_password_reset_url">Password reset URL <span class="label label-important">Required</span></label>
		<div class="controls">
			<input type="text" value="<?php if (isset($var->auth_password_reset_url)) { echo $var->auth_password_reset_url; } ?>" name="auth_password_reset_url" id="auth_password_reset_url" placeholder="Password reset URL"  />
			<span class="form_error text-error" id="auth_password_reset_url_error"><?php if (isset($errors->auth_password_reset_url)) { echo $errors->auth_password_reset_url; } ?></span>
			<div class="help-block">
				This is the URL (relative to root) of the URL that contains your password reset URL
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="auth_failed_count">login failure attempts <span class="label label-important">Required</span></label>
		<div class="controls">
			<input type="number" value="<?php if (isset($var->auth_failed_count)) { echo $var->auth_failed_count; } ?>" name="auth_failed_count" id="auth_failed_count" placeholder="login failure attempts"  />
			<span class="form_error text-error" id="auth_failed_count_error"><?php if (isset($errors->auth_failed_count)) { echo $errors->auth_failed_count; } ?></span>
			<div class="help-block">
				This is the number of login failures that are allowed before the system temporarily locks the account for security
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="auth_failed_time">Failed login reset time <span class="label label-important">Required</span></label>
		<div class="controls">
			<input type="number" value="<?php if (isset($var->auth_failed_time)) { echo $var->auth_failed_time; } ?>" name="auth_failed_time" id="auth_failed_time" placeholder="Failed login reset time"  />
			<span class="form_error text-error" id="auth_failed_time_error"><?php if (isset($errors->auth_failed_time)) { echo $errors->auth_failed_time; } ?></span>
			<div class="help-block">
				This is the number of seconds to lock the account if it has too many failed logins
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="auth_keep_login_duration">Time to keep login active <span class="label label-important">Required</span></label>
		<div class="controls">
			<input type="number" value="<?php if (isset($var->auth_keep_login_duration)) { echo $var->auth_keep_login_duration; } ?>" name="auth_keep_login_duration" id="auth_keep_login_duration" placeholder="Time to keep login active"  />
			<span class="form_error text-error" id="auth_keep_login_duration_error"><?php if (isset($errors->auth_keep_login_duration)) { echo $errors->auth_keep_login_duration; } ?></span>
			<div class="help-block">
				This is the number of seconds a login is kept active without activity.  Must be equal or shorter than the cookie
				life specified in config.php
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="auth_use_ssl">Force SSL <span class="label label-important">Required</span></label>
		<div class="controls">
			<select name="auth_use_ssl" id="auth_use_ssl" >
				<option value="0" selected>Disabled</option>
				<option value="1">Enabled</option>
			</select>
			<span class="form_error text-error" id="auth_use_ssl_error"><?php if (isset($errors->auth_use_ssl)) { echo $errors->auth_use_ssl; } ?></span>
			<div class="help-block">
				This will force a redirect to https:// if on
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="auth_session_username">Username session cookie <span class="label label-important">Required</span></label>
		<div class="controls">
			<input type="text" value="<?php if (isset($var->auth_session_username)) { echo $var->auth_session_username; } ?>" name="auth_session_username" id="auth_session_username" placeholder="Username session cookie"  />
			<span class="form_error text-error" id="auth_session_username_error"><?php if (isset($errors->auth_session_username)) { echo $errors->auth_session_username; } ?></span>
            <div class="help-block">
                Name of the session cookie that holds the username
            </div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="auth_session_id">Session id cookie <span class="label label-important">Required</span></label>
		<div class="controls">
			<input type="text" value="<?php if (isset($var->auth_session_id)) { echo $var->auth_session_id; } ?>" name="auth_session_id" id="auth_session_id" placeholder="Session id cookie"  />
			<span class="form_error text-error" id="auth_session_id_error"><?php if (isset($errors->auth_session_id)) { echo $errors->auth_session_id; } ?></span>
            <div class="help-block">
				Name of the session cookie that holds the session id
            </div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="auth_password_reset_expires">Seconds til password reset expires <span class="label label-important">Required</span></label>
		<div class="controls">
			<input type="number" value="<?php if (isset($var->auth_password_reset_expires)) { echo $var->auth_password_reset_expires; } ?>" name="auth_password_reset_expires" id="auth_password_reset_expires" placeholder="Seconds til password reset expires"  />
			<span class="form_error text-error" id="auth_password_reset_expires_error"><?php if (isset($errors->auth_password_reset_expires)) { echo $errors->auth_password_reset_expires; } ?></span>
            <div class="help-block">
				Number of seconds until the password reset expires.  Default 72 hours (3 days)
            </div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="auth_create_default_group">Create default group membership <span class="label label-important">Required</span></label>
		<div class="controls">
			<select name="auth_create_default_group" id="auth_create_default_group" >
                <option value="0">No</option>
                <option value="1" selected>Yes</option>
			</select>
			<span class="form_error text-error" id="auth_create_default_group_error"><?php if (isset($errors->auth_create_default_group)) { echo $errors->auth_create_default_group; } ?></span>
            <div class="help-block">
				Do we create a group membership by default?
            </div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="auth_default_group">Name of default group <span class="label label-important">Required</span> (if create default group is required)</label>
		<div class="controls">
			<input type="text" value="<?php if (isset($var->auth_default_group)) { echo $var->auth_default_group; } ?>" name="auth_default_group" id="auth_default_group" placeholder="Name of default group"  />
			<span class="form_error text-error" id="auth_default_group_error"><?php if (isset($errors->auth_default_group)) { echo $errors->auth_default_group; } ?></span>
            <div class="help-block">
				If we place new members into a default group, then what is its name?
            </div>
		</div>
	</div>
	<fieldset>
		<legend>Database</legend>
        <div class="help-block">
			You need to have your database configured already, and allow the configured user to create and alter tables.  This install process will create a few tables to handle the system, convert
			a few to InnoDB format and create foreign key constraints.
        </div>
		<div class="control-group">
			<label class="control-label" for="auth_table_prefix">prefix to attach to table names</label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var->auth_table_prefix)) { echo $var->auth_table_prefix; } ?>" name="auth_table_prefix" id="auth_table_prefix" placeholder="prefix to attach to table names"  />
				<span class="form_error text-error" id="auth_table_prefix_error"><?php if (isset($errors->auth_table_prefix)) { echo $errors->auth_table_prefix; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="groups">name of group table <span class="label label-important">Required</span></label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var->groups)) { echo $var->groups; } ?>" name="groups" id="groups" placeholder="name of group table"  />
				<span class="form_error text-error" id="groups_error"><?php if (isset($errors->groups)) { echo $errors->groups; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="password_resets">name of password resets table <span class="label label-important">Required</span></label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var->password_resets)) { echo $var->password_resets; } ?>" name="password_resets" id="password_resets" placeholder="name of password resets table"  />
				<span class="form_error text-error" id="password_resets_error"><?php if (isset($errors->password_resets)) { echo $errors->password_resets; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="member_groups">name of member_groups table <span class="label label-important">Required</span></label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var->member_groups)) { echo $var->member_groups; } ?>" name="member_groups" id="member_groups" placeholder="name of member_groups table"  />
				<span class="form_error text-error" id="member_groups_error"><?php if (isset($errors->member_groups)) { echo $errors->member_groups; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="failed_logins">name of failed_logins table <span class="label label-important">Required</span></label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var->failed_logins)) { echo $var->failed_logins; } ?>" name="failed_logins" id="failed_logins" placeholder="name of failed_logins table"  />
				<span class="form_error text-error" id="failed_logins_error"><?php if (isset($errors->failed_logins)) { echo $errors->failed_logins; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="members">name of members table <span class="label label-important">Required</span></label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var->members)) { echo $var->members; } ?>" name="members" id="members" placeholder="name of members table"  />
				<span class="form_error text-error" id="members_error"><?php if (isset($errors->members)) { echo $errors->members; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="sessions">name of sessions table <span class="label label-important">Required</span></label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var->sessions)) { echo $var->sessions; } ?>" name="sessions" id="sessions" placeholder="name of sessions table"  />
				<span class="form_error text-error" id="sessions_error"><?php if (isset($errors->sessions)) { echo $errors->sessions; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="profiles">name of profiles table <span class="label label-important">Required</span></label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var->profiles)) { echo $var->profiles; } ?>" name="profiles" id="profiles" placeholder="name of profiles table"  />
				<span class="form_error text-error" id="profiles_error"><?php if (isset($errors->profiles)) { echo $errors->profiles; } ?></span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>OAuth 2</legend>
		<div class="control-group">
			<label class="control-label" for="auth_use_oauth2">Use OAuth2 <span class="label label-important">Required</span></label>
			<div class="controls">
				<select name="auth_use_oauth2" id="auth_use_oauth2" >
					<option value="0">Disabled</option>
					<option value="1">Enabled</option>
				</select>
				<span class="form_error text-error" id="auth_use_oauth2_error"><?php if (isset($errors->auth_use_oauth2)) { echo $errors->auth_use_oauth2; } ?></span>
			</div>
		</div>

		<div id="oauth2_section" class="">
			<div class="control-group">
				<label class="control-label" for="enable_google">enable Google OAuth2 <span class="label label-important">Required</span></label>
				<div class="controls">
					<select name="enable_google" id="enable_google" >
						<option value="0">Disabled</option>
						<option value="1">Enabled</option>
					</select>
					<span class="form_error text-error" id="enable_google_error"><?php if (isset($errors->enable_google)) { echo $errors->enable_google; } ?></span>
				</div>
			</div>

			<div id="google_oauth2" class=" offset2">
				<div class="control-group">
					<label class="control-label" for="google_client_id">Google client ID</label>
					<div class="controls">
						<input type="text" value="<?php if (isset($var->google_client_id)) { echo $var->google_client_id; } ?>" name="google_client_id" id="google_client_id" placeholder="Google client ID"  />
						<span class="form_error text-error" id="google_client_id_error"><?php if (isset($errors->google_client_id)) { echo $errors->google_client_id; } ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="google_client_secret">Google client secret</label>
					<div class="controls">
						<input type="text" value="<?php if (isset($var->google_client_secret)) { echo $var->google_client_secret; } ?>" name="google_client_secret" id="google_client_secret" placeholder="Google client secret"  />
						<span class="form_error text-error" id="google_client_secret_error"><?php if (isset($errors->google_client_secret)) { echo $errors->google_client_secret; } ?></span>
					</div>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="enable_windowslive">enable Windows Live OAuth2 <span class="label label-important">Required</span></label>
				<div class="controls">
					<select name="enable_windowslive" id="enable_windowslive" >
						<option value="0">Disabled</option>
						<option value="1">Enabled</option>
					</select>

					<span class="form_error text-error" id="enable_windowslive_error"><?php if (isset($errors->enable_windowslive)) { echo $errors->enable_windowslive; } ?></span>
				</div>
			</div>

			<div id="windowslive_oauth2" class=" offset2">
				<div class="control-group">
					<label class="control-label" for="windowslive_client_id">Windows Live client ID</label>
					<div class="controls">
						<input type="text" value="<?php if (isset($var->windowslive_client_id)) { echo $var->windowslive_client_id; } ?>" name="windowslive_client_id" id="windowslive_client_id" placeholder="Windows Live client ID"  />
						<span class="form_error text-error" id="windowslive_client_id_error"><?php if (isset($errors->windowslive_client_id)) { echo $errors->windowslive_client_id; } ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="windowslive_client_secret">Windows Live client secret</label>
					<div class="controls">
						<input type="text" value="<?php if (isset($var->windowslive_client_secret)) { echo $var->windowslive_client_secret; } ?>" name="windowslive_client_secret" id="windowslive_client_secret" placeholder="Windows Live client secret"  />
						<span class="form_error text-error" id="windowslive_client_secret_error"><?php if (isset($errors->windowslive_client_secret)) { echo $errors->windowslive_client_secret; } ?></span>
					</div>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="enable_facebook">enable Facebook OAuth2 <span class="label label-important">Required</span></label>
				<div class="controls">
					<select name="enable_facebook" id="enable_facebook" >
						<option value="0">Disabled</option>
						<option value="1">Enabled</option>
					</select>

					<span class="form_error text-error" id="enable_facebook_error"><?php if (isset($errors->enable_facebook)) { echo $errors->enable_facebook; } ?></span>
				</div>
			</div>

			<div id="facebook_oauth2" class=" offset2">
				<div class="control-group">
					<label class="control-label" for="facebook_client_id">Facebook client ID</label>
					<div class="controls">
						<input type="text" value="<?php if (isset($var->facebook_client_id)) { echo $var->facebook_client_id; } ?>" name="facebook_client_id" id="facebook_client_id" placeholder="Facebook client ID"  />
						<span class="form_error text-error" id="facebook_client_id_error"><?php if (isset($errors->facebook_client_id)) { echo $errors->facebook_client_id; } ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="facebook_client_secret">Facebook client secret</label>
					<div class="controls">
						<input type="text" value="<?php if (isset($var->facebook_client_secret)) { echo $var->facebook_client_secret; } ?>" name="facebook_client_secret" id="facebook_client_secret" placeholder="Facebook client secret"  />
						<span class="form_error text-error" id="facebook_client_secret_error"><?php if (isset($errors->facebook_client_secret)) { echo $errors->facebook_client_secret; } ?></span>
					</div>
				</div>
			</div>

		</div>

	</fieldset>


	<div class="form-actions" id="submit_group">
		<button type="submit" class="btn btn-primary" id="submit_button">Save</button>
		or
		<a href="javascript: window.history.go(-1)">Cancel</a>
	</div></fieldset>
		<?php echo '<?php echo form_close(); ?>'; ?>
		<?php } else { echo $message; }  ?>
	</div>

	</td></tr>
	</table>
