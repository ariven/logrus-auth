<?php

/**
 * User: ariven
 * Date: 1/19/13
 * Time: 3:51 PM
 */


class Profile extends MY_Model
{
	public $before_create = array( 'created_at', 'updated_at' );
	public $before_update = array( 'updated_at' );

	public function __construct()
	{
		parent::__construct();
		$this->load->config('logrus_auth');
		$tables = $this->config->item('auth_tables');
		$prefix = $this->config->item('auth_table_prefix');

		$this->_table = $prefix . $tables['profiles'];

	}
}