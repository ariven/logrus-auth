<?php

	$config['auth_open_enrollment']     = TRUE; // if TRUE, will allow to create accounts, otherwise, will tell them no
	$config['auth_use_oauth2']          = FALSE;
	$config['auth_login_url']           = '/'; // URL to send to if not logged in
	$config['auth_logged_in_url']       = '/'; // url to send to after logging in
	$config['auth_failed_count']        = 20; // attempts to allow before locking account
	$config['auth_failed_time']         = 15 * 60; // 15 minutes to track failed logins
	$config['auth_keep_login_duration'] = 60 * 60 * 24 * 7; // time in seconds
	$config['auth_use_ssl']             = FALSE; // do we force SSL?

	$config['auth_session_username'] = 'auth_username'; // email in our implementation
	$config['auth_session_id']       = 'auth_session_id'; // unique session ID from database

	$config['auth_password_reset_expires'] = 60 * 60 * 24 * 3; // 3 days to keep reset before expiring

	$config['auth_create_default_group'] = TRUE;
	$config['auth_default_group']        = 'members';

	$config['auth_profile_image_directory'] = FCPATH . '/assets/images/profile'; // where profile images are stored, no trailing slash, will also end up with a subdir /thumb

	$config['auth_table_prefix'] = ''; // default table prefix

	$config['auth_password_library'] = 'logrus/password'; // password library

	/**
	 * names of the individual tables
	 */
	$config['auth_tables']['groups']          = 'groups'; // master group table
	$config['auth_tables']['password_resets'] = 'password_resets'; // password reset requests
	$config['auth_tables']['member_groups']   = 'member_groups'; // groups a user is in
	$config['auth_tables']['failed_logins']   = 'failed_logins'; // failed login tracking
	$config['auth_tables']['members']         = 'members'; // user master table
	$config['auth_tables']['sessions']        = 'session_auth'; // user sessions
	$config['auth_tables']['profiles']        = 'profiles'; // configurable metadata table

	$config['auth_oauth2_use_google']      = FALSE;
	$config['auth_oauth2_use_windowslive'] = FALSE;
	$config['auth_oauth2_use_facebook']    = FALSE;

// https://code.google.com/apis/console to get your keys
	$config['auth_access']['google']['client_id']          = 'CLIENT ID HERE';
	$config['auth_access']['google']['client_secret']      = 'CLIENT SECRET HERE';
	$config['auth_access']['google']['developer_key']      = 'DEVELOPER KEY HERE';
	$config['auth_access']['windowslive']['client_id']     = 'CLIENT ID HERE';
	$config['auth_access']['windowslive']['client_secret'] = 'CLIENT SECRET HERE';
	$config['auth_access']['facebook']['client_id']        = 'CLIENT ID HERE';
	$config['auth_access']['facebook']['client_secret']    = 'CLIENT SECRET HERE';
