<?php

	class Member extends MY_Model {
		public function __construct() {
			parent::__construct();
			$this->load->config('logrus_auth');
			$prefix = $this->config->item('auth_table_prefix');
			$this->_table = $prefix . 'members';
		}

	}