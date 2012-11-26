<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

	class Welcome extends MY_Controller
	{
		function __construct()
		{
			parent::__construct();
			$this->load->library('logrus_auth');
		}

		public function index()
		{
			$this->data['logged_in'] = $this->logrus_auth->logged_in();

			// with the MY_Controller mod by Jamie Rumbelow we don't need to specify view here
		}

	}