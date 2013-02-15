<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }


class Session_auth extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->config('logrus_auth');
		$tables = $this->config->item('auth_tables');
		$prefix = $this->config->item('auth_table_prefix');

		$this->_table = $prefix . $tables['sessions'];
	}
}