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

			$this->load->dbforge();
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
					 'member_id' => array('type' => 'INT', 'constraint' => 9),
					 'profile_picture'           => array('type' => 'VARCHAR', 'constraint' => 1024,),
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
					 'member_id' => array('type' => 'INT', 'constraint' => 9,),
					 'fail_date' => array('type' => 'DATETIME'),
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
					 'fail_date' => array('type' => 'DATETIME'),
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
					 'category'     => array('type' => 'VARCHAR', 'constraint' => 128)
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
					 'reset_date' => array('type' => 'DATETIME'),
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