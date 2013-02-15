<?php
/**
 * User: patrick
 * Date: 11/14/12
 * Time: 12:43 PM
 */


/**
 * install logic:
 *
 * refuse to run if config files exist
 *   Present form with defaults filled in
 *   make sure to highlight required stuff that cant be defaulted
 */


class Logrus_install extends CI_Controller
{
	private $_prefix;
	private $_tables;


	function __construct()
	{
		parent::__construct();

//		$this->output->enable_profiler(TRUE);

		$this->load->helper('url');
		$this->load->dbforge();
	}


	function alert_error($what)
	{
		if (func_num_args() > 1)
		{
			$args = func_get_args();
			$msg  = vsprintf(array_shift($args), $args);
		}
		else
		{
			$msg = $what;
		}

		return sprintf('<div class="alert alert-error">%s</div>', $msg);
	}

	function alert_info($what)
	{
		if (func_num_args() > 1)
		{
			$args = func_get_args();
			$msg  = vsprintf(array_shift($args), $args);
		}
		else
		{
			$msg = $what;
		}

		return sprintf('<div class="alert alert-info">%s</div>', $msg);
	}

	function install()
	{
		$data = array();
		$this->load->library('form_validation');
		$is_ajax = $this->input->is_ajax_request();

		$config_file = APPPATH . 'config/logrus_auth.php';
		if (file_exists($config_file))
		{
			$data['message'] = 'Configuration already exists...';
		}
		else
		{

			$var = new stdClass(); // Can put database object here with = clone $db_object;

			$var->auth_open_enrollment        = FALSE;
			$var->auth_login_url              = '/auth/login';
			$var->auth_logged_in_url          = '/';
			$var->auth_password_reset_url     = '/auth/reset_password';
			$var->auth_failed_count           = 5;
			$var->auth_failed_time            = 900; // 15 minutes
			$var->auth_keep_login_duration    = 604800; // 7 days
			$var->auth_use_ssl                = FALSE;
			$var->auth_session_username       = 'auth_username';
			$var->auth_session_id             = 'auth_session_id';
			$var->auth_password_reset_expires = 259200; // 3 days
			$var->auth_create_default_group   = TRUE;
			$var->auth_default_group          = 'members';
			$var->auth_table_prefix           = 'logrus_';
			$var->groups                      = 'groups';
			$var->password_resets             = 'password_resets';
			$var->member_groups               = 'member_groups';
			$var->failed_logins               = 'failed_logins';
			$var->members                     = 'members';
			$var->sessions                    = 'sessions_auth';
			$var->profiles                    = 'profiles';

			// OAuth2 section
			$var->auth_use_oauth2           = FALSE;
			$var->enable_google             = FALSE;
			$var->enable_windowslive        = FALSE;
			$var->enable_facebook           = FALSE;
			$var->google_client_id          = 'CLIENT ID';
			$var->google_client_secret      = 'CLIENT SECRET';
			$var->windowslive_client_id     = 'CLIENT ID';
			$var->windowslive_client_secret = 'CLIENT SECRET';
			$var->facebook_client_id        = 'CLIENT ID';
			$var->facebook_client_secret    = 'CLIENT SECRET';

			if ($this->input->post())
			{
				$var->member_email                = html_purify($this->input->post('member_email'));
				$var->member_display_name         = html_purify($this->input->post('member_display_name'));
				$var->auth_open_enrollment        = html_purify($this->input->post('auth_open_enrollment'));
				$var->auth_login_url              = html_purify($this->input->post('auth_login_url'));
				$var->auth_logged_in_url          = html_purify($this->input->post('auth_logged_in_url'));
				$var->auth_password_reset_url     = html_purify($this->input->post('auth_password_reset_url'));
				$var->auth_failed_count           = html_purify($this->input->post('auth_failed_count'));
				$var->auth_failed_time            = html_purify($this->input->post('auth_failed_time'));
				$var->auth_keep_login_duration    = html_purify($this->input->post('auth_keep_login_duration'));
				$var->auth_use_ssl                = html_purify($this->input->post('auth_use_ssl'));
				$var->auth_session_username       = html_purify($this->input->post('auth_session_username'));
				$var->auth_session_id             = html_purify($this->input->post('auth_session_id'));
				$var->auth_password_reset_expires = html_purify($this->input->post('auth_password_reset_expires'));
				$var->auth_create_default_group   = html_purify($this->input->post('auth_create_default_group'));
				$var->auth_default_group          = html_purify($this->input->post('auth_default_group'));
				$var->auth_table_prefix           = html_purify($this->input->post('auth_table_prefix'));
				$var->groups                      = html_purify($this->input->post('groups'));
				$var->password_resets             = html_purify($this->input->post('password_resets'));
				$var->member_groups               = html_purify($this->input->post('member_groups'));
				$var->failed_logins               = html_purify($this->input->post('failed_logins'));
				$var->members                     = html_purify($this->input->post('members'));
				$var->sessions                    = html_purify($this->input->post('sessions'));
				$var->profiles                    = html_purify($this->input->post('profiles'));

				// OAuth2 section
				$var->auth_use_oauth2           = html_purify($this->input->post('auth_use_oauth2'));
				$var->enable_google             = html_purify($this->input->post('enable_google'));
				$var->enable_windowslive        = html_purify($this->input->post('enable_windowslive'));
				$var->enable_facebook           = html_purify($this->input->post('enable_facebook'));
				$var->google_client_id          = html_purify($this->input->post('google_client_id'));
				$var->google_client_secret      = html_purify($this->input->post('google_client_secret'));
				$var->windowslive_client_id     = html_purify($this->input->post('windowslive_client_id'));
				$var->windowslive_client_secret = html_purify($this->input->post('windowslive_client_secret'));
				$var->facebook_client_id        = html_purify($this->input->post('facebook_client_id'));
				$var->facebook_client_secret    = html_purify($this->input->post('facebook_client_secret'));
			}


			$data['var'] = $var;


			$rules[] = array('field' => 'member_email', 'label' => 'Member Email', 'rules' => 'trim|strtolower|required|valid_email');
			$rules[] = array('field' => 'member_display_name', 'label' => 'Member Name', 'rules' => 'trim|required');
			$rules[] = array('field' => 'auth_open_enrollment', 'label' => 'Open enrollment', 'rules' => 'trim|required');
			$rules[] = array('field' => 'auth_use_oauth2', 'label' => 'Use OAuth2', 'rules' => 'trim|required');
			$rules[] = array('field' => 'auth_login_url', 'label' => 'Login URL', 'rules' => 'trim|required');
			$rules[] = array('field' => 'auth_logged_in_url', 'label' => 'logged in URL', 'rules' => 'trim|required');
			$rules[] = array('field' => 'auth_password_reset_url', 'label' => 'Password reset URL', 'rules' => 'trim|required');
			$rules[] = array('field' => 'auth_failed_count', 'label' => 'login failure attempts', 'rules' => 'trim|required');
			$rules[] = array('field' => 'auth_failed_time', 'label' => 'Failed login reset time', 'rules' => 'trim|required');
			$rules[] = array('field' => 'auth_keep_login_duration', 'label' => 'Time to keep login active', 'rules' => 'trim|required');
			$rules[] = array('field' => 'auth_use_ssl', 'label' => 'Force SSL', 'rules' => 'trim|required');
			$rules[] = array('field' => 'auth_session_username', 'label' => 'Username session cookie', 'rules' => 'trim|required');
			$rules[] = array('field' => 'auth_session_id', 'label' => 'Session id cookie', 'rules' => 'trim|required');
			$rules[] = array('field' => 'auth_password_reset_expires', 'label' => 'Seconds til password reset expires', 'rules' => 'trim|required');
			$rules[] = array('field' => 'auth_create_default_group', 'label' => 'Create default group membership', 'rules' => 'trim|required');

			// if auth_create_default group is true, you have to have a default group
			if ($this->input->post('auth_create_default_group'))
			{
				$rules[] = array('field' => 'auth_default_group', 'label' => 'Name of default group', 'rules' => 'trim|required');
			}
			else
			{
				$rules[] = array('field' => 'auth_default_group', 'label' => 'Name of default group', 'rules' => 'trim');
			}
			$rules[] = array('field' => 'auth_table_prefix', 'label' => 'prefix to attach to table names', 'rules' => 'trim');
			$rules[] = array('field' => 'groups', 'label' => 'name of group table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'password_resets', 'label' => 'name of password resets table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'member_groups', 'label' => 'name of member_groups table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'failed_logins', 'label' => 'name of failed_logins table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'members', 'label' => 'name of members table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'sessions', 'label' => 'name of sessions table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'profiles', 'label' => 'name of profiles table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'enable_google', 'label' => 'enable Google OAuth2', 'rules' => 'trim|required');
			$rules[] = array('field' => 'enable_windowslive', 'label' => 'enable Windows Live OAuth2', 'rules' => 'trim|required');
			$rules[] = array('field' => 'enable_facebook', 'label' => 'enable Facebook OAuth2', 'rules' => 'trim|required');

			$rules[] = array('field' => 'google_client_id', 'label' => 'Google client ID', 'rules' => 'trim');
			$rules[] = array('field' => 'google_client_secret', 'label' => 'Google client secret', 'rules' => 'trim');
			$rules[] = array('field' => 'windowslive_client_id', 'label' => 'Windows Live client ID', 'rules' => 'trim');
			$rules[] = array('field' => 'windowslive_client_secret', 'label' => 'Windows Live client secret', 'rules' => 'trim');
			$rules[] = array('field' => 'facebook_client_id', 'label' => 'Facebook client ID', 'rules' => 'trim');
			$rules[] = array('field' => 'facebook_client_secret', 'label' => 'Facebook client secret', 'rules' => 'trim');

			$this->form_validation->set_rules($rules);

			if ($this->form_validation->run() == FALSE)
			{
				// first load or failed form
				$error                              = new stdClass();
				$error->auth_open_enrollment        = form_error('auth_open_enrollment');
				$error->auth_use_oauth2             = form_error('auth_use_oauth2');
				$error->auth_login_url              = form_error('auth_login_url');
				$error->auth_logged_in_url          = form_error('auth_logged_in_url');
				$error->auth_password_reset_url     = form_error('auth_password_reset_url');
				$error->auth_failed_count           = form_error('auth_failed_count');
				$error->auth_failed_time            = form_error('auth_failed_time');
				$error->auth_keep_login_duration    = form_error('auth_keep_login_duration');
				$error->auth_use_ssl                = form_error('auth_use_ssl');
				$error->auth_session_username       = form_error('auth_session_username');
				$error->auth_session_id             = form_error('auth_session_id');
				$error->auth_password_reset_expires = form_error('auth_password_reset_expires');
				$error->auth_create_default_group   = form_error('auth_create_default_group');
				$error->auth_default_group          = form_error('auth_default_group');
				$error->auth_table_prefix           = form_error('auth_table_prefix');
				$error->groups                      = form_error('groups');
				$error->password_resets             = form_error('password_resets');
				$error->member_groups               = form_error('member_groups');
				$error->failed_logins               = form_error('failed_logins');
				$error->members                     = form_error('members');
				$error->sessions                    = form_error('sessions');
				$error->profiles                    = form_error('profiles');
				$error->enable_google               = form_error('enable_google');
				$error->enable_windowslive          = form_error('enable_windowslive');
				$error->enable_facebook             = form_error('enable_facebook');
				$error->client_id                   = form_error('client_id');
				$error->client_secret               = form_error('client_secret');
				$error->developer_key               = form_error('developer_key');

				$data['errors'] = $error;
				$errors         = validation_errors();
				if (trim($errors))
				{
					$data['status'] = $errors;
				}
				$data['message'] = $this->load->view('logrus/install/index', $data, TRUE);
			}
			else
			{
				$vars['auth_open_enrollment']        = ($var->auth_open_enrollment) ? 'TRUE' : 'FALSE';
				$vars['auth_login_url']              = $var->auth_login_url;
				$vars['auth_logged_in_url']          = $var->auth_logged_in_url;
				$vars['auth_password_reset_url']     = $var->auth_password_reset_url;
				$vars['auth_failed_count']           = $var->auth_failed_count;
				$vars['auth_failed_time']            = $var->auth_failed_time;
				$vars['auth_keep_login_duration']    = $var->auth_keep_login_duration;
				$vars['auth_use_ssl']                = ($var->auth_use_ssl) ? 'TRUE' : 'FALSE';
				$vars['auth_session_username']       = $var->auth_session_username;
				$vars['auth_session_id']             = $var->auth_session_id;
				$vars['auth_password_reset_expires'] = $var->auth_password_reset_expires;
				$vars['auth_create_default_group']   = ($var->auth_create_default_group) ? 'TRUE' : 'FALSE';
				$vars['auth_default_group']          = $var->auth_default_group;
				$vars['auth_table_prefix']           = $var->auth_table_prefix;

				$tables['groups']          = $var->groups;
				$tables['password_resets'] = $var->password_resets;
				$tables['member_groups']   = $var->member_groups;
				$tables['failed_logins']   = $var->failed_logins;
				$tables['members']         = $var->members;
				$tables['sessions']        = $var->sessions;
				$tables['profiles']        = $var->profiles;

				$vars['auth_use_oauth2']           = ($var->auth_use_oauth2) ? 'TRUE' : 'FALSE';
				$vars['enable_google']             = ($var->enable_google) ? 'TRUE' : 'FALSE';
				$vars['enable_windowslive']        = ($var->enable_windowslive) ? 'TRUE' : 'FALSE';
				$vars['enable_facebook']           = ($var->enable_facebook) ? 'TRUE' : 'FALSE';
				$vars['google_client_id']          = $var->google_client_id;
				$vars['google_client_secret']      = $var->google_client_secret;
				$vars['windowslive_client_id']     = $var->windowslive_client_id;
				$vars['windowslive_client_secret'] = $var->windowslive_client_secret;
				$vars['facebook_client_id']        = $var->facebook_client_id;
				$vars['facebook_client_secret']    = $var->facebook_client_secret;

				$data['vars']   = $vars;
				$data['tables'] = $tables;

				$loc = $config_file;
				$fil = $this->load->view('logrus/install/config_template', $data, TRUE);
				file_put_contents($loc, $fil);
				$data['message'] = $this->alert_info('Created config file.');


				// database stuff
				$this->config->load('logrus_auth');
				$this->_prefix = $this->config->item('auth_table_prefix');
				$this->_tables = $this->config->item('auth_tables');
				$this->_install_members();
				$this->_install_failed_logins();
				$this->_install_member_groups();
				$this->_install_groups();
				$this->_install_lgs();
				$this->_install_password_resets();
				$this->_install_sessions();
				$this->_install_profiles();
				$this->_install_constraints();
				$data['message'] .= $this->alert_info('Created database tables.');


				// first member
				$this->load->library('logrus/logrus_member');
				if ($this->logrus_member->create($var->member_email, $var->member_display_name))
				{
					$this->load->library('logrus/logrus_password');
					$this->logrus_password->generate_reset_code($var->member_email, 'new');
					$data['message'] .= $this->alert_info('A new member has been created for %s &lt;%s&gt; and a password reset email has been sent to that email address to allow you to choose your password and activate the account',
														  $var->member_display_name, $var->member_email);
				}
				else
				{
					$data['message'] .= $this->alert_error('There was an error creating initial user');
				}
				$data['message'] .= '<div>';
				$data['message'] .= 'Generated config: <br />';
				$data['message'] .= sprintf('<textarea style="width:100%%; height:200px" class="well">%s</textarea>',
											htmlentities($fil));
				$data['message'] .= '</div>';

			}

		}

		if ($is_ajax)
		{
			// set header to json type
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: application/json');
			echo json_encode($this->data);
		}
		else
		{
			//load view here, or render if using a template library
			$data['scripts'] = '/assets/js/logrus/install/index.js';


			$this->load->view('logrus/template', $data);

		}

	}


	function _install_members()
	{

		$this->dbforge->add_field('id');
		$this->dbforge->add_field(
			array(
				 'email'           => array('type' => 'VARCHAR', 'constraint' => 128,),
				 'email_confirmed' => array('type' => 'TINYINT', 'constraint' => 1,),
				 'hash'            => array('type' => 'VARCHAR', 'constraint' => 256,),
				 'created_at'      => array('type' => 'DATETIME'),
				 'updated_at'      => array('type' => 'DATETIME'),
				 'display_name'    => array('type' => 'VARCHAR', 'constraint' => 128),
				 'login_authority' => array('type' => 'VARCHAR', 'constraint' => 128),
				 'oauth_token'     => array('type' => 'VARCHAR', 'constraint' => 128),
				 'active'          => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 1),
			));

		$this->dbforge->create_table($this->_prefix . $this->_tables['members'], TRUE);

		// Now make it InnoDB
		$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB', $this->_prefix . $this->_tables['members']));
	}


	function _install_profiles()
	{
		$this->dbforge->add_field('id');
		$this->dbforge->add_field(
			array(
				 'member_id'       => array('type' => 'INT', 'constraint' => 9),
				 'profile_picture' => array('type' => 'VARCHAR', 'constraint' => 1024,),
				 'created_at'      => array('type' => 'DATETIME'),
				 'updated_at'      => array('type' => 'DATETIME'),
			));

		$this->dbforge->create_table($this->_prefix . $this->_tables['profiles'], TRUE);

		// Now make it InnoDB
		$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB', $this->_prefix . $this->_tables['profiles']));
	}

	function _install_failed_logins()
	{
		$this->dbforge->add_field('id');
		$this->dbforge->add_field(
			array(
				 'member_id'  => array('type' => 'INT', 'constraint' => 9,),
				 'created_at' => array('type' => 'DATETIME'),
			));

		$this->dbforge->create_table($this->_prefix . $this->_tables['failed_logins'], TRUE);

		// Now make it InnoDB
		$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB',
										$this->_prefix . $this->_tables['failed_logins']));
	}

	function _install_member_groups()
	{
		$this->dbforge->add_field('id');
		$this->dbforge->add_field(
			array(
				 'group_id'   => array('type' => 'INT', 'constraint' => 9,),
				 'member_id'  => array('type' => 'INT', 'constraint' => 9,),
				 'created_at' => array('type' => 'DATETIME'),
				 'updated_at' => array('type' => 'DATETIME'),
			));

		$this->dbforge->create_table($this->_prefix . $this->_tables['member_groups'], TRUE);

		// Now make it InnoDB
		$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB',
										$this->_prefix . $this->_tables['member_groups']));
	}

	function _install_groups()
	{
		$this->dbforge->add_field('id');
		$this->dbforge->add_field(
			array(
				 'tag'         => array('type' => 'VARCHAR', 'constraint' => 100,),
				 'description' => array('type' => 'VARCHAR', 'constraint' => 1024,),
				 'created_at'  => array('type' => 'DATETIME'),
				 'updated_at'  => array('type' => 'DATETIME'),
			));

		$this->dbforge->create_table($this->_prefix . $this->_tables['groups'], TRUE);

		// Now make it InnoDB
		$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB', $this->_prefix . $this->_tables['groups']));
	}

	function _install_lgs()
	{
		$this->dbforge->add_field('id');
		$this->dbforge->add_field(
			array(
				 'what'       => array('type' => 'TEXT'),
				 'created_at' => array('type' => 'DATETIME'),
				 'who'        => array('type' => 'VARCHAR', 'constraint' => 128),
				 'category'   => array('type' => 'VARCHAR', 'constraint' => 128),
			));

		$this->dbforge->create_table($this->_prefix . 'lgs', TRUE);
	}

	function _install_password_resets()
	{
		$this->dbforge->add_field('id');
		$this->dbforge->add_field(
			array(
				 'member_id'  => array('type' => 'INT', 'constraint' => 9),
				 'reset_code' => array('type' => 'VARCHAR', 'constraint' => 128,),
				 'created_at' => array('type' => 'DATETIME'),
			));

		$this->dbforge->create_table($this->_prefix . $this->_tables['password_resets'], TRUE);

		// Now make it InnoDB
		$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB',
										$this->_prefix . $this->_tables['password_resets']));
	}

	function _install_sessions()
	{
		$this->dbforge->add_field('id');
		$this->dbforge->add_field(
			array(
				 'member_id'       => array('type' => 'INT', 'constraint' => 9),
				 'session_id'      => array('type' => 'VARCHAR', 'constraint' => 128,),
				 'ip_address'      => array('type' => 'VARCHAR', 'constraint' => 128,),
				 'last_login'      => array('type' => 'DATETIME'),
				 'logged_in'       => array('type' => 'TINYINT', 'constraint' => 1,),
				 'login_authority' => array('type' => 'VARCHAR', 'constraint' => 128,),
			));

		$this->dbforge->create_table($this->_prefix . $this->_tables['sessions'], TRUE);

		// Now make it InnoDB
		$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB', $this->_prefix . $this->_tables['sessions']));
	}

	function _install_constraints()
	{

		$this->db->simple_query(
			sprintf(
				"ALTER TABLE `%s` ADD CONSTRAINT `fl_member_id_fkey` FOREIGN KEY (`member_id`) REFERENCES `%s` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
				$this->_prefix . $this->_tables['failed_logins'],
				$this->_prefix . $this->_tables['members']
			));
		$this->db->simple_query(
			sprintf(
				"ALTER TABLE `%s` ADD CONSTRAINT `mg_member_id_fkey` FOREIGN KEY(`member_id`) REFERENCES `%s` (`id`) ON DELETE CASCADE ON UPDATE CASCADE, ADD CONSTRAINT `mg_group_id_fkey` FOREIGN KEY(`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
				$this->_prefix . $this->_tables['member_groups'],
				$this->_prefix . $this->_tables['members']
			));
		$this->db->simple_query(
			sprintf(
				"ALTER TABLE `%s` ADD CONSTRAINT `pr_member_id` FOREIGN KEY(`member_id`) REFERENCES `%s` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
				$this->_prefix . $this->_tables['password_resets'],
				$this->_prefix . $this->_tables['members']
			));
		$this->db->simple_query(
			sprintf(
				"ALTER TABLE `%s` ADD CONSTRAINT `profile_member_user_id_fkey` FOREIGN KEY(`member_id`) REFERENCES `%s` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
				$this->_prefix . $this->_tables['profiles'],
				$this->_prefix . $this->_tables['members']
			));
		$this->db->simple_query(
			sprintf(
				"ALTER TABLE `%s` ADD CONSTRAINT `session_member_id_fkey` FOREIGN KEY(`member_id`) REFERENCES `%s` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
				$this->_prefix . $this->_tables['sessions'],
				$this->_prefix . $this->_tables['members']
			));

	}
}