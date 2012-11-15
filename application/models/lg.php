<?php if (!defined('BASEPATH'))
{
	exit('No direct script access allowed');
}

	/**
	 * table lgs
	 * created_at datetime
	 * who varchar(128)
	 * what text
	 * category varchar(128)
	 */
	class Lg extends MY_Model
	{

		public $who = '-'; // who performed the action

		public function __construct($config = array())
		{
			parent::__construct();
			$this->before_create[] = 'created_at';
			// purge old records
			$this->config->load('lg');
			$expire_days = $this->config->item('lg_expiration_days');

			$old_date = date('Y-m-d H:i:s', strtotime(sprintf('-%d days', $expire_days)));
			$this->delete_by('created_at <', $old_date);
		}

		/**
		 * allow us to override the default definition of $this->who as set by __construct
		 *
		 * @param type $who
		 */
		function set_who($who)
		{
			$this->who = $who;
		}


		function line($what, $type, $who = 'default')
		{
			if ($who == 'default')
			{
				$who = $this->who;
			}
			$this->insert(array('who' => $who, 'what' => strip_tags($what), 'category' => $type));
		}


		/**
		 * creates an info log entry
		 *
		 * @param string $what
		 */
		function info($what)
		{
			if ($this->config->item('lg_info'))
			{
				if (func_num_args() > 1)
				{
					$args = func_get_args();
					$msg  = vsprintf(array_shift($args), $args);
				}
				else
				{
					$msg = $what;
				}
				$this->line($msg, 'info', $this->who);
			}

			return $msg;
		}

		/**
		 * creates an error log entry
		 *
		 * @param string $what
		 */
		function error($what)
		{
			if ($this->config->item('lg_error'))
			{
				if (func_num_args() > 1)
				{
					$args = func_get_args();
					$msg  = vsprintf(array_shift($args), $args);
				}
				else
				{
					$msg = $what;
				}
				$this->line($msg, 'error', $this->who);
			}

			return $msg;
		}

		/**
		 * creates a warning log entry
		 *
		 * @param string $what
		 */
		function warning($what)
		{
			if ($this->config->item('lg_warning'))
			{
				if (func_num_args() > 1)
				{
					$args = func_get_args();
					$msg  = vsprintf(array_shift($args), $args);
				}
				else
				{
					$msg = $what;
				}
				$this->line($msg, 'warning', $this->who);
			}


			return $msg;
		}

		/**
		 * creates a debug log entry
		 *
		 * @param string $what
		 */
		function debug($what)
		{
			if ($this->config->item('lg_debug'))
			{
				if (func_num_args() > 1)
				{
					$args = func_get_args();
					$msg  = vsprintf(array_shift($args), $args);
				}
				else
				{
					$msg = $what;
				}
				$this->line($msg, 'debug', $this->who);
			}


			return $msg;
		}

		function webhook($what)
		{
			if ($this->config->item('lg_webhook'))
			{
				if (func_num_args() > 1)
				{
					$args = func_get_args();
					$msg  = vsprintf(array_shift($args), $args);
				}
				else
				{
					$msg = $what;
				}
				$this->line($msg, 'webhook', 'webhook');
			}
			return $msg;
		}

	}