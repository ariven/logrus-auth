<?php

	/**
	 * the profile table is used to allow the site to have its own dynamic table tied to the member table.
	 *
	 * The only fields that are required is the member_id field and the profile_picture from oauth2
	 */
	class Profile extends MY_Model
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->config('logrus_auth');
			$tables = $this->config->item('auth_tables');
			$prefix = $this->config->item('auth_table_prefix');

			$this->_table = $prefix . $tables['profiles'];

		}

	}