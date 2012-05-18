<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


	class Password_resets extends MY_Model
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->config('logrus_auth');
			$prefix       = $this->config->item('auth_table_prefix');
			$this->_table = $prefix . 'password_resets';

		}

	}