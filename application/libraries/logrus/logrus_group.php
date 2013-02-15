<?php

/**
 * User: ariven
 * Date: 1/19/13
 * Time: 2:46 PM
 */

require_once('logrus_base.php');

/**
 * Group management functions
 */
class Logrus_group extends Logrus_base
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('logrus/groups');
		$this->load->model('logrus/member_groups');
	}

	/**
	 * Checks to see if the member is a member of the specified group
	 *
	 * @param $username
	 * @param $group
	 * @return bool
	 */
	public function is_member($email, $group)
	{
		$this->load->library('logrus/logrus_member');
		$group  = $this->groups->get_by('tag', $group);
		$member = $this->logrus_member->get($email);
		if ($member and $group)
		{
			return $this->member_groups->validate_membership($member->id, $group->id);
		}

		return FALSE;
	}

	/**
	 * Adds a group to a member, creates the group if it does not exist
	 *
	 * @param $username
	 * @param $group_tag
	 */
	public function add_group($email, $group_tag)
	{
		$email = $this->clean_email($email);
		$this->load->library('logrus/logrus_member');
		$member  = $this->logrus_member->get($email);
		$group   = $this->groups->get_by('tag', $group_tag);
		$success = TRUE;

		if (! $group)
		{
			$success = $this->create_group($group_tag, $group_tag);
			$group   = $this->groups->get_by('tag', $group_tag);
		}

		if ($success)
		{
			$check = $this->member_groups->get_by(array('member_id' => $member->id, 'group_id' => $group->id));
			if ($check)
			{
				return TRUE; // member is already in the group
			}
			$success = $this->member_groups->insert(array('member_id' => $member->id, 'group_id' => $group->id));
			if ($success)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * @param $username
	 * @param $group
	 */
	public function remove_group($email, $group)
	{
		$email = $this->clean_email($email);
		$this->load->library('logrus/logrus_member');

		$member  = $this->logrus_member->get($email);

		if (is_numeric($group))
		{
			$group_record = $this->groups->get($group);
		}
		else
		{
			$group_record = $this->groups->get_by(array('tag' => $group));
		}
		if ($group_record and $member)
		{
			$this->load->model('logrus/member_groups');
			$member_group = $this->member_groups->get_by(array('member_id' => $member->id, 'group_id' => $group_record->id));
			if ($member_group)
			{
				return $this->member_groups->delete($member_group->id);
			}
		}

		return TRUE; // no group, no member, no need to delete
	}

	public function create_group($tag, $description)
	{
		$this->load->model('logrus/groups');

		return $this->groups->insert(array('tag' => $tag, 'description' => $description));
	}

	public function delete_group($tag)
	{
		$this->load->model('logrus/groups');

		return $this->groups->delete_by(array('tag' => $tag));
	}
}