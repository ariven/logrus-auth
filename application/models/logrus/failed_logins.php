<?php
	class Failed_logins extends MY_Model {
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
			$this->load->config('logrus_auth');
			$this->db->where('member_id', $id);
			$this->db->where('fail_date >', date('Y-m-d H:i:s', time() - $this->config->item('auth_failed_time')));

			$records = $this->get_all();
			if (count($records) >= $this->config->item('auth_failed_count'))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		/**
		 * clears failed login count for a specific user
		 * @param $id
		 */
		function clear_failed($id)
		{
			$this->db->where('user_id', $id);
			$this->db->delete($this->table);
		}
	}
