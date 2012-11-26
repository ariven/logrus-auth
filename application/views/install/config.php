<?php if (empty($message)) { // no response, show the form ?>
<?php echo form_open(uri_string(), array("id" => "install", "class" => "form ")); ?>
	<fieldset class="offset4 span4">
		<legend>Install</legend>
		<div class="status"></div>
		<div class="control-group">
			<label class="control-label" for="open_enrollment">Let people create their own accounts</label>
			<div class="controls">
				<select name="open_enrollment" id="open_enrollment">
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select>
				<span class="form_error text-error" id="open_enrollment_error"><?php if (isset($errors['open_enrollment'])) { echo $errors['open_enrollment']; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="login_url">Login Url</label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var['login_url'])) { echo $var['login_url']; } ?>" name="login_url" id="login_url" placeholder="Login Url"  />
				<span class="form_error text-error" id="login_url_error"><?php if (isset($errors['login_url'])) { echo $errors['login_url']; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="logged_in_url">Logged in URL</label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var['logged_in_url'])) { echo $var['logged_in_url']; } ?>" name="logged_in_url" id="logged_in_url" placeholder="Logged in URL"  />
				<span class="form_error text-error" id="logged_in_url_error"><?php if (isset($errors['logged_in_url'])) { echo $errors['logged_in_url']; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="failed_count">Failed login attempts before locking</label>
			<div class="controls">
				<input type="number" value="<?php if (isset($var['failed_count'])) { echo $var['failed_count']; } ?>" name="failed_count" id="failed_count" placeholder="Failed login attempts before locking"  />
				<span class="form_error text-error" id="failed_count_error"><?php if (isset($errors['failed_count'])) { echo $errors['failed_count']; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="failed_time">Time in seconds for lockout</label>
			<div class="controls">
				<input type="number" value="<?php if (isset($var['failed_time'])) { echo $var['failed_time']; } ?>" name="failed_time" id="failed_time" placeholder="Time in seconds for lockout"  />
				<span class="form_error text-error" id="failed_time_error"><?php if (isset($errors['failed_time'])) { echo $errors['failed_time']; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="keep_login_duration">Time in seconds to keep login active</label>
			<div class="controls">
				<input type="number" value="<?php if (isset($var['keep_login_duration'])) { echo $var['keep_login_duration']; } ?>" name="keep_login_duration" id="keep_login_duration" placeholder="Time in seconds to keep login active"  />
				<span class="form_error text-error" id="keep_login_duration_error"><?php if (isset($errors['keep_login_duration'])) { echo $errors['keep_login_duration']; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="use_ssl">Force SSL website</label>
			<div class="controls">
				<select name="use_ssl" id="use_ssl" >
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select>

				<span class="form_error text-error" id="use_ssl_error"><?php if (isset($errors['use_ssl'])) { echo $errors['use_ssl']; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="session_username">cookie name to hold username</label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var['session_username'])) { echo $var['session_username']; } ?>" name="session_username" id="session_username" placeholder="cookie name to hold username"  />
				<span class="form_error text-error" id="session_username_error"><?php if (isset($errors['session_username'])) { echo $errors['session_username']; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="session_id">cookie name to hold session id</label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var['session_id'])) { echo $var['session_id']; } ?>" name="session_id" id="session_id" placeholder="cookie name to hold session id"  />
				<span class="form_error text-error" id="session_id_error"><?php if (isset($errors['session_id'])) { echo $errors['session_id']; } ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="password_reset_expires">Time in seconds to expire password reset requests</label>
			<div class="controls">
				<input type="number" value="<?php if (isset($var['password_reset_expires'])) { echo $var['password_reset_expires']; } ?>" name="password_reset_expires" id="password_reset_expires" placeholder="Time in seconds to expire password reset requests"  />
				<span class="form_error text-error" id="password_reset_expires_error"><?php if (isset($errors['password_reset_expires'])) { echo $errors['password_reset_expires']; } ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="profile_image_directory">Image directory (in site path)</label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var['profile_image_directory'])) { echo $var['profile_image_directory']; } ?>" name="profile_image_directory" id="profile_image_directory" placeholder="Image directory (in site path)"  />
				<span class="form_error text-error" id="profile_image_directory_error"><?php if (isset($errors['profile_image_directory'])) { echo $errors['profile_image_directory']; } ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="password_library">Library to use to handle passwords</label>
			<div class="controls">
				<input type="text" value="<?php if (isset($var['password_library'])) { echo $var['password_library']; } ?>" name="password_library" id="password_library" placeholder="Library to use to handle passwords"  />
				<span class="form_error text-error" id="password_library_error"><?php if (isset($errors['password_library'])) { echo $errors['password_library']; } ?></span>
			</div>
		</div>




		<fieldset>
			<legend>Database configuration</legend>
			<div class="control-group">
				<label class="control-label" for="create_default_group">Create a default group?</label>
				<div class="controls">
					<select name="create_default_group" id="create_default_group" >
						<option value="0">No</option>
						<option value="1">Yes</option>
					</select>

					<span class="form_error text-error" id="create_default_group_error"><?php if (isset($errors['create_default_group'])) { echo $errors['create_default_group']; } ?></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="default_group">Default Group name</label>
				<div class="controls">
					<input type="text" value="<?php if (isset($var['default_group'])) { echo $var['default_group']; } ?>" name="default_group" id="default_group" placeholder="Default Group name"  />
					<span class="form_error text-error" id="default_group_error"><?php if (isset($errors['default_group'])) { echo $errors['default_group']; } ?></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="table_prefix">Prefix for database tables</label>
				<div class="controls">
					<input type="text" value="<?php if (isset($var['table_prefix'])) { echo $var['table_prefix']; } ?>" name="table_prefix" id="table_prefix" placeholder="Prefix for database tables"  />
					<span class="form_error text-error" id="table_prefix_error"><?php if (isset($errors['table_prefix'])) { echo $errors['table_prefix']; } ?></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="groups">Name of groups table</label>
				<div class="controls">
					<input type="text" value="<?php if (isset($var['groups'])) { echo $var['groups']; } ?>" name="groups" id="groups" placeholder="Name of groups table"  />
					<span class="form_error text-error" id="groups_error"><?php if (isset($errors['groups'])) { echo $errors['groups']; } ?></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="password_resets">Name of password resets table</label>
				<div class="controls">
					<input type="text" value="<?php if (isset($var['password_resets'])) { echo $var['password_resets']; } ?>" name="password_resets" id="password_resets" placeholder="Name of password resets table"  />
					<span class="form_error text-error" id="password_resets_error"><?php if (isset($errors['password_resets'])) { echo $errors['password_resets']; } ?></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="member_groups">Name of member groups table</label>
				<div class="controls">
					<input type="text" value="<?php if (isset($var['member_groups'])) { echo $var['member_groups']; } ?>" name="member_groups" id="member_groups" placeholder="Name of member groups table"  />
					<span class="form_error text-error" id="member_groups_error"><?php if (isset($errors['member_groups'])) { echo $errors['member_groups']; } ?></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="failed_logins">Name of failed logins table</label>
				<div class="controls">
					<input type="text" value="<?php if (isset($var['failed_logins'])) { echo $var['failed_logins']; } ?>" name="failed_logins" id="failed_logins" placeholder="Name of failed logins table"  />
					<span class="form_error text-error" id="failed_logins_error"><?php if (isset($errors['failed_logins'])) { echo $errors['failed_logins']; } ?></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="members">Name of member table</label>
				<div class="controls">
					<input type="text" value="<?php if (isset($var['members'])) { echo $var['members']; } ?>" name="members" id="members" placeholder="Name of member table"  />
					<span class="form_error text-error" id="members_error"><?php if (isset($errors['members'])) { echo $errors['members']; } ?></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="sessions">Name of user sessions table</label>
				<div class="controls">
					<input type="text" value="<?php if (isset($var['sessions'])) { echo $var['sessions']; } ?>" name="sessions" id="sessions" placeholder="Name of user sessions table"  />
					<span class="form_error text-error" id="sessions_error"><?php if (isset($errors['sessions'])) { echo $errors['sessions']; } ?></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="profiles">Name of profiles table</label>
				<div class="controls">
					<input type="text" value="<?php if (isset($var['profiles'])) { echo $var['profiles']; } ?>" name="profiles" id="profiles" placeholder="Name of profiles table"  />
					<span class="form_error text-error" id="profiles_error"><?php if (isset($errors['profiles'])) { echo $errors['profiles']; } ?></span>
				</div>
			</div>

		</fieldset>



		<fieldset>
			<legend>OAUTH2 Configuration</legend>

			<div class="control-group">
				<label class="control-label" for="use_oauth2">Use OAUTH2</label>
				<div class="controls">
					<select name="use_oauth2" id="use_oauth2" >
						<option value="0">No</option>
						<option value="1">Yes</option>
					</select>

					<span class="form_error text-error" id="use_oauth2_error"><?php if (isset($errors['use_oauth2'])) { echo $errors['use_oauth2']; } ?></span>
				</div>
			</div>

			<span id="oauth2" style="display:none;">
				<div class="control-group">
					<label class="control-label" for="oauth2_use_google">Use google for oauth2</label>
					<div class="controls">
						<select name="oauth2_use_google" id="oauth2_use_google" >
							<option value="0">No</option>
							<option value="1">Yes</option>
						</select>

						<span class="form_error text-error" id="oauth2_use_google_error"><?php if (isset($errors['oauth2_use_google'])) { echo $errors['oauth2_use_google']; } ?></span>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="google_client_id">Google client id</label>
					<div class="controls">
						<input type="text" value="<?php if (isset($var['google_client_id'])) { echo $var['google_client_id']; } ?>" name="google_client_id" id="google_client_id" placeholder="Google client id"  />
						<span class="form_error text-error" id="google_client_id_error"><?php if (isset($errors['google_client_id'])) { echo $errors['google_client_id']; } ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="google_client_secret">Google client secret</label>
					<div class="controls">
						<input type="text" value="<?php if (isset($var['google_client_secret'])) { echo $var['google_client_secret']; } ?>" name="google_client_secret" id="google_client_secret" placeholder="Google client secret"  />
						<span class="form_error text-error" id="google_client_secret_error"><?php if (isset($errors['google_client_secret'])) { echo $errors['google_client_secret']; } ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="google_developer_key">Google developer key</label>
					<div class="controls">
						<input type="text" value="<?php if (isset($var['google_developer_key'])) { echo $var['google_developer_key']; } ?>" name="google_developer_key" id="google_developer_key" placeholder="Google developer key"  />
						<span class="form_error text-error" id="google_developer_key_error"><?php if (isset($errors['google_developer_key'])) { echo $errors['google_developer_key']; } ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="oauth2_use_windowslive">use Windows Live for oauth2</label>
					<div class="controls">
						<select name="oauth2_use_windowslive" id="oauth2_use_windowslive" >
							<option value="0">No</option>
							<option value="1">Yes</option>
						</select>

						<span class="form_error text-error" id="oauth2_use_windowslive_error"><?php if (isset($errors['oauth2_use_windowslive'])) { echo $errors['oauth2_use_windowslive']; } ?></span>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="windowslive_client_id">Windows Live client ID</label>
					<div class="controls">
						<input type="text" value="<?php if (isset($var['windowslive_client_id'])) { echo $var['windowslive_client_id']; } ?>" name="windowslive_client_id" id="windowslive_client_id" placeholder="Windows Live client ID"  />
						<span class="form_error text-error" id="windowslive_client_id_error"><?php if (isset($errors['windowslive_client_id'])) { echo $errors['windowslive_client_id']; } ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="windowslive_client_secret">Windows Live client secret</label>
					<div class="controls">
						<input type="text" value="<?php if (isset($var['windowslive_client_secret'])) { echo $var['windowslive_client_secret']; } ?>" name="windowslive_client_secret" id="windowslive_client_secret" placeholder="Windows Live client secret"  />
						<span class="form_error text-error" id="windowslive_client_secret_error"><?php if (isset($errors['windowslive_client_secret'])) { echo $errors['windowslive_client_secret']; } ?></span>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="oauth2_use_facebook">Use facebook for oauth2</label>
					<div class="controls">
						<select name="oauth2_use_facebook" id="oauth2_use_facebook" >
							<option value="0">No</option>
							<option value="1">Yes</option>
						</select>

						<span class="form_error text-error" id="oauth2_use_facebook_error"><?php if (isset($errors['oauth2_use_facebook'])) { echo $errors['oauth2_use_facebook']; } ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="facebook_client_id">Facebook Client ID</label>
					<div class="controls">
						<input type="text" value="<?php if (isset($var['facebook_client_id'])) { echo $var['facebook_client_id']; } ?>" name="facebook_client_id" id="facebook_client_id" placeholder="Facebook Client ID"  />
						<span class="form_error text-error" id="facebook_client_id_error"><?php if (isset($errors['facebook_client_id'])) { echo $errors['facebook_client_id']; } ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="facebook_client_secret">Facebook Client Secret</label>
					<div class="controls">
						<input type="text" value="<?php if (isset($var['facebook_client_secret'])) { echo $var['facebook_client_secret']; } ?>" name="facebook_client_secret" id="facebook_client_secret" placeholder="Facebook Client Secret"  />
						<span class="form_error text-error" id="facebook_client_secret_error"><?php if (isset($errors['facebook_client_secret'])) { echo $errors['facebook_client_secret']; } ?></span>
					</div>
				</div>
			</span>
		</fieldset>

		<div class="control-group submit_group">
			<div class="controls submit_controls">
				<button type="submit" class="btn btn-primary" id="submit_button">Save</button> or <a href="javascript: window.history.go(-1)">Cancel</a>
			</div>
		</div>
	</fieldset>
<?php echo form_close(); ?>
<?php } else { echo $message; }  ?>