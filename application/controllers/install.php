<?php
	/**
	 * User: patrick
	 * Date: 11/14/12
	 * Time: 12:43 PM
	 */

	class Install extends MY_Controller
	{
		private $prefix;
		private $tables;

		function __construct()
		{
			parent::__construct();
			$this->config->load('logrus_auth');
			$this->prefix = $this->config->item('auth_table_prefix');
			$this->tables = $this->config->item('auth_tables');

			$this->load->helper('url');

			$this->load->dbforge();
		}

		function _html_purify($string)
		{
			return $this->security->xss_clean($string);
		}

		function config()
		{
			$this->load->library('form_validation');
			$is_ajax = $this->input->is_ajax_request();


			// set defaults
			$this->data['var']['open_enrollment']           = 1;
			$this->data['var']['use_oauth2']                = 0;
			$this->data['var']['login_url']                 = '/auth/login';
			$this->data['var']['logged_in_url']             = '/';
			$this->data['var']['failed_count']              = 5;
			$this->data['var']['failed_time']               = 15 * 60;
			$this->data['var']['keep_login_duration']       = 24 * 60 * 60;
			$this->data['var']['use_ssl']                   = 0;
			$this->data['var']['session_username']          = 'auth_username';
			$this->data['var']['session_id']                = 'auth_session_id';
			$this->data['var']['password_reset_expires']    = 60 * 60 * 24 * 3;
			$this->data['var']['create_default_group']      = 1;
			$this->data['var']['default_group']             = 'members';
			$this->data['var']['profile_image_directory']   = 'assets/images/profile'; // prefix with FCPATH
			$this->data['var']['table_prefix']              = 'logrus_';
			$this->data['var']['password_library']          = 'logrus/password';
			$this->data['var']['groups']                    = 'groups';
			$this->data['var']['password_resets']           = 'password_resets';
			$this->data['var']['member_groups']             = 'member_groups';
			$this->data['var']['failed_logins']             = 'failed_logins';
			$this->data['var']['members']                   = 'members';
			$this->data['var']['sessions']                  = 'sessions';
			$this->data['var']['profiles']                  = 'profiles';
			$this->data['var']['google_client_id']          = 'client_id';
			$this->data['var']['google_client_secret']      = 'client_secret';
			$this->data['var']['google_developer_key']      = 'developer_key';
			$this->data['var']['oauth2_use_google']         = 0;
			$this->data['var']['windowslive_client_id']     = 'client_id';
			$this->data['var']['windowslive_client_secret'] = 'client_secret';
			$this->data['var']['oauth2_use_windowslive']    = 0;
			$this->data['var']['facebook_client_id']        = 'client_id';
			$this->data['var']['facebook_client_secret']    = 'client_secret';
			$this->data['var']['oauth2_use_facebook']       = 0;

			$posts = $this->input->post('', TRUE);
			if ($posts)
			{
				foreach ($posts as $key => $value)
				{
					$this->data['var'][$key] = $value;
				}
			}


			$rules[] = array('field' => 'open_enrollment', 'label' => 'Open Enrollment', 'rules' => 'required');
			$rules[] = array('field' => 'use_oauth2', 'label' => 'Use OAUTH2', 'rules' => 'required');
			$rules[] = array('field' => 'login_url', 'label' => 'Login Url', 'rules' => 'trim|required');
			$rules[] = array('field' => 'logged_in_url', 'label' => 'Logged in URL', 'rules' => 'trim|required');
			$rules[] = array('field' => 'failed_count', 'label' => 'Failed login attempts before locking', 'rules' => 'trim|required');
			$rules[] = array('field' => 'failed_time', 'label' => 'Time in seconds for lockout', 'rules' => 'trim|required');
			$rules[] = array('field' => 'keep_login_duration', 'label' => 'Time in seconds to keep login active', 'rules' => 'trim|required');
			$rules[] = array('field' => 'use_ssl', 'label' => 'Force SSL website', 'rules' => 'trim|required');
			$rules[] = array('field' => 'session_username', 'label' => 'cookie name to hold username', 'rules' => 'trim|required');
			$rules[] = array('field' => 'session_id', 'label' => 'cookie name to hold session id', 'rules' => 'trim|required');
			$rules[] = array('field' => 'password_reset_expires', 'label' => 'Time in seconds to expire password reset requests', 'rules' => 'trim|required');
			$rules[] = array('field' => 'create_default_group', 'label' => 'Create a default group?', 'rules' => 'trim|required');
			$rules[] = array('field' => 'default_group', 'label' => 'Default Group name', 'rules' => 'trim|required');
			$rules[] = array('field' => 'profile_image_directory', 'label' => 'Image directory (in site path)', 'rules' => 'trim|required');
			$rules[] = array('field' => 'table_prefix', 'label' => 'Prefix for database tables', 'rules' => 'trim|required');
			$rules[] = array('field' => 'password_library', 'label' => 'Library to use to handle passwords', 'rules' => 'trim|required');
			$rules[] = array('field' => 'groups', 'label' => 'Name of groups table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'password_resets', 'label' => 'Name of password resets table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'member_groups', 'label' => 'Name of member groups table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'failed_logins', 'label' => 'Name of failed logins table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'members', 'label' => 'Name of member table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'sessions', 'label' => 'Name of user sessions table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'profiles', 'label' => 'Name of profiles table', 'rules' => 'trim|required');
			$rules[] = array('field' => 'google_client_id', 'label' => 'Google client id', 'rules' => 'trim|required');
			$rules[] = array('field' => 'google_client_secret', 'label' => 'Google client secret', 'rules' => 'trim|required');
			$rules[] = array('field' => 'google_developer_key', 'label' => 'Google developer key', 'rules' => 'trim|required');
			$rules[] = array('field' => 'oauth2_use_google', 'label' => 'Use google for oauth2', 'rules' => 'trim|required');
			$rules[] = array('field' => 'windowslive_client_id', 'label' => 'Windows Live client ID', 'rules' => 'trim|required');
			$rules[] = array('field' => 'windowslive_client_secret', 'label' => 'Windows Live client secret', 'rules' => 'trim|required');
			$rules[] = array('field' => 'oauth2_use_windowslive', 'label' => 'use Windows Live for oauth2', 'rules' => 'trim|required');
			$rules[] = array('field' => 'facebook_client_id', 'label' => 'Facebook Client ID', 'rules' => 'trim');
			$rules[] = array('field' => 'facebook_client_secret', 'label' => 'Facebook Client Secret', 'rules' => 'trim|required');
			$rules[] = array('field' => 'oauth2_use_facebook', 'label' => 'Use facebook for oauth2', 'rules' => 'trim|required');
			$this->form_validation->set_rules($rules);

			if ($this->form_validation->run() == FALSE)
			{
				// first load or failed form
				$error                = array(
					'open_enrollment'           => form_error('open_enrollment'),
					'use_oauth2'                => form_error('use_oauth2'),
					'login_url'                 => form_error('login_url'),
					'logged_in_url'             => form_error('logged_in_url'),
					'failed_count'              => form_error('failed_count'),
					'failed_time'               => form_error('failed_time'),
					'keep_login_duration'       => form_error('keep_login_duration'),
					'use_ssl'                   => form_error('use_ssl'),
					'session_username'          => form_error('session_username'),
					'session_id'                => form_error('session_id'),
					'password_reset_expires'    => form_error('password_reset_expires'),
					'create_default_group'      => form_error('create_default_group'),
					'default_group'             => form_error('default_group'),
					'profile_image_directory'   => form_error('profile_image_directory'),
					'table_prefix'              => form_error('table_prefix'),
					'password_library'          => form_error('password_library'),
					'groups'                    => form_error('groups'),
					'password_resets'           => form_error('password_resets'),
					'member_groups'             => form_error('member_groups'),
					'failed_logins'             => form_error('failed_logins'),
					'members'                   => form_error('members'),
					'sessions'                  => form_error('sessions'),
					'profiles'                  => form_error('profiles'),
					'google_client_id'          => form_error('google_client_id'),
					'google_client_secret'      => form_error('google_client_secret'),
					'google_developer_key'      => form_error('google_developer_key'),
					'oauth2_use_google'         => form_error('oauth2_use_google'),
					'windowslive_client_id'     => form_error('windowslive_client_id'),
					'windowslive_client_secret' => form_error('windowslive_client_secret'),
					'oauth2_use_windowslive'    => form_error('oauth2_use_windowslive'),
					'facebook_client_id'        => form_error('facebook_client_id'),
					'facebook_client_secret'    => form_error('facebook_client_secret'),
					'oauth2_use_facebook'       => form_error('oauth2_use_facebook'),
				);
				$this->data['errors'] = $error;
				$errors               = validation_errors();
				if (trim($errors))
				{
					$this->data['status'] = $this->msg->error($errors);
				}
			}
			else
			{
				$this->data['message'] = 'You just successfully submitted this form!';
			}
			$this->data['javascript'][] = 'install/config.js';

		}


		function index()
		{
			$this->view = FALSE;

			echo 'Installing database tables to default database... <br/>';
			echo 'Installing members... <br/>';
			$this->install_members();

			echo 'Installing failed_logins... <br/>';
			$this->install_failed_logins();

			echo 'Installing member_groups... <br/>';
			$this->install_member_groups();

			echo 'Installing groups... <br/>';
			$this->install_groups();

			echo 'Installing lgs... <br/>';
			$this->install_lgs();

			echo 'Installing password_resets... <br/>';
			$this->install_password_resets();

			echo 'Installing install_sessions... <br/>';
			$this->install_sessions();

			echo 'Installing profiles... <br />';
			$this->install_profiles();

			echo 'Installing constraints... <br/>';
			$this->install_constraints();
		}


		function install_members()
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
					 'active'          => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 1),
				));

			$this->dbforge->create_table($this->prefix . $this->tables['members'], TRUE);

			// Now make it InnoDB
			$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB', $this->prefix . $this->tables['members']));
		}


		function install_profiles()
		{
			$this->dbforge->add_field('id');
			$this->dbforge->add_field(
				array(
					 'member_id'       => array('type' => 'INT', 'constraint' => 9),
					 'profile_picture' => array('type' => 'VARCHAR', 'constraint' => 1024,),
				));

			$this->dbforge->create_table($this->prefix . $this->tables['profiles'], TRUE);

			// Now make it InnoDB
			$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB', $this->prefix . $this->tables['profiles']));
		}

		function install_failed_logins()
		{
			$this->dbforge->add_field('id');
			$this->dbforge->add_field(
				array(
					 'member_id'  => array('type' => 'INT', 'constraint' => 9,),
					 'created_at' => array('type' => 'DATETIME'),
				));

			$this->dbforge->create_table($this->prefix . $this->tables['failed_logins'], TRUE);

			// Now make it InnoDB
			$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB',
											$this->prefix . $this->tables['failed_logins']));
		}

		function install_member_groups()
		{
			$this->dbforge->add_field('id');
			$this->dbforge->add_field(
				array(
					 'group_id'  => array('type' => 'INT', 'constraint' => 9,),
					 'member_id' => array('type' => 'INT', 'constraint' => 9,),
				));

			$this->dbforge->create_table($this->prefix . $this->tables['member_groups'], TRUE);

			// Now make it InnoDB
			$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB',
											$this->prefix . $this->tables['member_groups']));
		}

		function install_groups()
		{
			$this->dbforge->add_field('id');
			$this->dbforge->add_field(
				array(
					 'tag'         => array('type' => 'VARCHAR', 'constraint' => 100,),
					 'description' => array('type' => 'VARCHAR', 'constraint' => 1024,),
				));

			$this->dbforge->create_table($this->prefix . $this->tables['groups'], TRUE);

			// Now make it InnoDB
			$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB', $this->prefix . $this->tables['groups']));
		}

		function install_lgs()
		{
			$this->dbforge->add_field('id');
			$this->dbforge->add_field(
				array(
					 'what'       => array('type' => 'TEXT'),
					 'created_at' => array('type' => 'DATETIME'),
					 'who'        => array('type' => 'VARCHAR', 'constraint' => 128),
					 'category'   => array('type' => 'VARCHAR', 'constraint' => 128)
				));

			$this->dbforge->create_table($this->prefix . 'lgs', TRUE);
		}

		function install_password_resets()
		{
			$this->dbforge->add_field('id');
			$this->dbforge->add_field(
				array(
					 'member_id'  => array('type' => 'INT', 'constraint' => 9),
					 'reset_code' => array('type' => 'VARCHAR', 'constraint' => 128,),
					 'created_at' => array('type' => 'DATETIME'),
				));

			$this->dbforge->create_table($this->prefix . $this->tables['password_resets'], TRUE);

			// Now make it InnoDB
			$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB',
											$this->prefix . $this->tables['password_resets']));
		}

		function install_sessions()
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

			$this->dbforge->create_table($this->prefix . $this->tables['sessions'], TRUE);

			// Now make it InnoDB
			$this->db->simple_query(sprintf('ALTER TABLE %s ENGINE=InnoDB', $this->prefix . $this->tables['sessions']));
		}

		function install_constraints()
		{

			$this->db->simple_query(
				sprintf(
					"ALTER TABLE `%s` ADD CONSTRAINT `fl_member_id_fkey` FOREIGN KEY (`member_id`) REFERENCES `%s` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
					$this->prefix . $this->tables['failed_logins'],
					$this->prefix . $this->tables['members']
				));
			$this->db->simple_query(
				sprintf(
					"ALTER TABLE `%s` ADD CONSTRAINT `mg_member_id_fkey` FOREIGN KEY(`member_id`) REFERENCES `%s` (`id`) ON DELETE CASCADE ON UPDATE CASCADE, ADD CONSTRAINT `mg_group_id_fkey` FOREIGN KEY(`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
					$this->prefix . $this->tables['member_groups'],
					$this->prefix . $this->tables['members']
				));
			$this->db->simple_query(
				sprintf(
					"ALTER TABLE `%s` ADD CONSTRAINT `pr_member_id` FOREIGN KEY(`member_id`) REFERENCES `%s` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
					$this->prefix . $this->tables['password_resets'],
					$this->prefix . $this->tables['members']
				));
			$this->db->simple_query(
				sprintf(
					"ALTER TABLE `%s` ADD CONSTRAINT `profile_member_user_id_fkey` FOREIGN KEY(`member_id`) REFERENCES `%s` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
					$this->prefix . $this->tables['profiles'],
					$this->prefix . $this->tables['members']
				));
			$this->db->simple_query(
				sprintf(
					"ALTER TABLE `%s` ADD CONSTRAINT `session_member_id_fkey` FOREIGN KEY(`member_id`) REFERENCES `%s` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
					$this->prefix . $this->tables['sessions'],
					$this->prefix . $this->tables['members']
				));

		}
	}