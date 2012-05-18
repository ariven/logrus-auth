<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Session_auth extends MY_Model {
	/**
	 * constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->load->config('logrus_auth');
		$prefix = $this->config->item('auth_table_prefix');
		$this->_table = $prefix . 'session_auth';
	}
}