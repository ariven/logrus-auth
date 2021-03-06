<?php

/**
 * User: ariven
 * Date: 1/19/13
 * Time: 4:04 PM
 */


class Failed_logins extends MY_Model
{
	public $before_create = array('created_at'); // automagically track datetime

	public function __construct() {
		parent::__construct();
		$this->load->config('logrus_auth');
		$tables = $this->config->item('auth_tables');
		$prefix = $this->config->item('auth_table_prefix');

		$this->_table = $prefix . $tables['failed_logins'];
	}

	/**
	 * returns true if user specified by $user_id fails the failed login count.
	 * Does automatic pruning of old failed attempts.
	 *
	 * @param $id
	 * @return boolean
	 */
	function failed_count($id)
	{
		$this->delete_by(array(
							  'created_at <' => date('Y-m-d H:i:s', time() - $this->config->item('auth_failed_time'))
						 ));
		$count = $this->count_by(array(
			'member_id' => $id,
			'created_at >' => date('Y-m-d H:i:s', time() - $this->config->item('auth_failed_time'))
		));
		return ($count >= $this->config->item('auth_failed_count'));
	}

	/**
	 * Returns actual count of failures
	 *
	 * @param $id
	 * @return bool
	 */
	function fail_count($id)
	{
		$this->delete_by(array(
							  'created_at <' => date('Y-m-d H:i:s', time() - $this->config->item('auth_failed_time'))
						 ));
		$count = $this->count_by(array(
									  'member_id' => $id,
									  'created_at >' => date('Y-m-d H:i:s', time() - $this->config->item('auth_failed_time'))
								 ));
		return $count;
	}

	/**
	 * clears failed login count for a specific user
	 * @param $id
	 */
	function clear_failed($id)
	{
		return $this->delete_by(array('user_id' => $id));
	}


}