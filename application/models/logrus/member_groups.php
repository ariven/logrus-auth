<?php

/**
 * User: ariven
 * Date: 1/19/13
 * Time: 3:41 PM
 */

class Member_groups extends MY_Model
{
	public $before_create = array( 'created_at', 'updated_at' );
	public $before_update = array( 'updated_at' );

	public function __construct()
	{
		parent::__construct();
		$this->load->config('logrus_auth');
		$tables = $this->config->item('auth_tables');
		$prefix = $this->config->item('auth_table_prefix');
		$this->_table = $prefix . $tables['member_groups'];

	}

	/**
	 * Validate whether or not a member is in a group
	 * @param $member_id
	 * @param $group_id
	 * @return bool
	 */
	public function validate_membership($member_id, $group_id)
	{
		$member = $this->get_by(array('member_id' => $member_id, 'group_id' => $group_id));
		if ($member)
		{
			return TRUE;  // We found a record, so they are a member of that group
		}
		return FALSE;
	}

}