<?php  if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

	class Msg
	{
		var $CI = '';

		function __construct()
		{
			$this->CI =& get_instance();
		}


		function success($what)
		{
			if (func_num_args() > 1)
			{
				$args = func_get_args();
				$msg  = array('content' => vsprintf(array_shift($args), $args));
			}
			else
			{
				$msg = array('content' => $what);
			}

			return $this->CI->load->view('template/success-box', $msg, TRUE);
		}

		function success_header($header, $msg)
		{
			return $this->CI->load->view('template/success-box', array('content' => $msg, 'title' => $header), TRUE);
		}

		function info($what)
		{
			if (func_num_args() > 1)
			{
				$args = func_get_args();
				$msg  = array('content' => vsprintf(array_shift($args), $args));
			}
			else
			{
				$msg = array('content' => $what);
			}

			return $this->CI->load->view('template/info-box', $msg, TRUE);
		}

		function info_header($header, $msg)
		{
			return $this->CI->load->view('template/info-box', array('content' => $msg, 'title' => $header), TRUE);
		}

		function error($what)
		{
			if (func_num_args() > 1)
			{
				$args = func_get_args();
				$msg  = array('content' => vsprintf(array_shift($args), $args));
			}
			else
			{
				$msg = array('content' => $what);
			}

			return $this->CI->load->view('template/error-box', $msg, TRUE);
		}

		function error_header($header, $msg)
		{
			return $this->CI->load->view('template/error-box', array('content' => $msg, 'title' => $header), TRUE);
		}

		function warning($what)
		{
			if (func_num_args() > 1)
			{
				$args = func_get_args();
				$msg  = array('content' => vsprintf(array_shift($args), $args));
			}
			else
			{
				$msg = array('content' => $what);
			}

			return $this->CI->load->view('template/warning-box', $msg, TRUE);

		}

		function warning_header($header, $msg)
		{
			return $this->CI->load->view('template/warning-box', array('content' => $msg, 'title' => $header), TRUE);
		}

	}