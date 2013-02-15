<?php
/**
 * User: patrick
 * Date: 1/30/13
 * Time: 9:18 AM
 *
 */

class Auth extends CI_Controller
{

	protected $data; // used on some forms

	public function __construct()
	{
		parent::__construct();
		$this->config->load('logrus_auth');
		$this->load->library('logrus/logrus_auth');
		$this->load->helper('url');

	}

	public function index()
	{
		redirect($this->config->item('auth_login_url')); // we don't allow direct access to here by default
	}

	/**
	 * uses Phil Sturgeons OAuth2 library https://github.com/philsturgeon/codeigniter-oauth2  You will need it to
	 * enact the oauth2 implementation in logrus_auth
	 *
	 * @param bool $provider
	 */
	public function oauth2_session($provider_name = FALSE)
	{
		if (! $provider_name)
		{
			redirect($this->config->item('auth_login_url'));
		}
		$this->load->library('oauth2'); // uses Phil Sturgeons OAuth2 library https://github.com/philsturgeon/codeigniter-oauth2
		$auth_access   = $this->config->item('auth_access');
		$client_id     = $auth_access[$provider_name]['client_id'];
		$client_secret = $auth_access[$provider_name]['client_secret'];

		$provider = $this->oauth2->provider($provider_name, array('id' => $client_id, 'secret' => $client_secret));

		if ($auth_access[$provider_name]['enabled'])
		{
			if ($this->input->get('code'))
			{
				try
				{
					$token = $provider->access($this->input->get('code'));
					$user  = $provider->get_user_info($token);
					// log him in, if not a member and if auth_open_enrollment, create account
					$result = $this->logrus_auth->oauth2_member_login($provider->name, $token, $user);
					if (! $result)
					{
						$this->load->view('logrus/auth/oauth2_login_failed',
										  array('message' => $this->logrus_auth->message));
					}
					// no more work after if, since it redirects by default
				} catch (OAuth2_Exception $e)
				{
					$this->load->view('logrus/auth/oauth2_login_failed',
									  array('message' => 'Unable to log you in at this time'));
				}
			}
			else
			{
				$provider->authorize();
			}
		}
		else
		{
			$this->load->view('logrus/auth/oauth2_not_enabled');
		}
	}

	/**
	 * so sorry, not for you
	 */
	public function site_closed()
	{
		$this->load->view('logrus/auth/site_closed');
	}

	/**
	 * Logs in the member.
	 * Also, if you want to use the javascript functionality, there is a script at /assets/js/logrus/auth/login.js
	 */
	function login()
	{
		$this->load->library('form_validation');
		$is_ajax = $this->input->is_ajax_request();

		$this->data['open_enrollment'] = $this->config->item('auth_open_enrollment');
		$this->data['use_oauth2']      = $this->config->item('auth_use_oath2');

		$var               = new stdClass(); // Can put database object here with = clone $db_object;
		$var->email        = html_purify($this->input->post('email'));
		$var->password     = html_purify($this->input->post('password'));
		$this->data['var'] = $var;

		$rules[] = array('field' => 'email', 'label' => 'Email Address', 'rules' => 'trim|required|strtolower');
		$rules[] = array('field' => 'password', 'label' => 'Your password', 'rules' => 'required');
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == FALSE)
		{
			// first load or failed form
			$error           = new stdClass();
			$error->email    = form_error('email');
			$error->password = form_error('password');

			$this->data['errors'] = $error;
			$errors               = validation_errors();
			if (trim($errors))
			{
				$this->data['status'] = $errors;
			}
			$message = $this->load->view('logrus/auth/login_form', $this->data, TRUE);
		}
		else
		{
			if (! $this->logrus_auth->login_with_redirect($var->email, $var->password))
			{
				$message = $this->load->view('logrus/auth/failed_login', array('message' => $this->logrus_auth->message), TRUE);
			}
			// no else, we are redirected if we log in
		}

		if ($is_ajax)
		{
			// set header to json type
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: application/json');
			// if you don't have $this->set_response() use the previous 3 lines
			//$this->set_response('json');
			echo json_encode($this->data);
		}
		else
		{
			// $this->add_script('/assets/js/logrus/auth/login.js');
		}

		echo $message;
	}


	function logout()
	{
		$this->logrus_auth->logout();
	}

	/**
	 * Allows new members to sign up.  We send an email with password reset link in it so that we can confirm their
	 * email address
	 */
	function signup()
	{
		$this->load->library('form_validation');

		$is_ajax = $this->input->is_ajax_request();

		$message = '';

		if ($this->config->item('auth_open_enrollment'))
		{
			$var               = new stdClass(); // Can put database object here with = clone $db_object;
			$var->email        = html_purify($this->input->post('email'));
			$var->display_name = html_purify($this->input->post('display_name'));
			$this->data['var'] = $var;

			$rules[] = array('field' => 'email', 'label' => 'Email Address', 'rules' => 'trim|required|strtolower|is_unique[members.email]');
			$rules[] = array('field' => 'display_name', 'label' => 'Your name', 'rules' => 'trim|required');
			$this->form_validation->set_rules($rules);

			if ($this->form_validation->run() == FALSE)
			{
				// first load or failed form
				$error               = new stdClass();
				$error->email        = form_error('email');
				$error->display_name = form_error('display_name');

				$this->data['errors'] = $error;
				$errors               = validation_errors();
				if (trim($errors))
				{
					$this->data['status'] = $errors;
				}
				$message = $this->load->view('logrus/auth/signup_form', $this->data, TRUE);
			}
			else
			{
				$this->load->library('logrus/logrus_member');

				$create = $this->logrus_member->create($var->email, $var->display_name);
				if ($create)
				{
					$member = $this->logrus_member->get($var->email);
					if ($member)
					{
						$this->load->library('logrus/logrus_password');
						$this->logrus_password->generate_reset_code($var->email, 'new');
					}
					$message = $this->load->view('logrus/auth/created_account', '', TRUE);
				}
				else
				{
					$message = $this->load->view('logrus/auth/error_creating_account', '', TRUE);
				}
			}
		}
		else
		{
			$message = $this->load->view('logrus/auth/site_closed', '', TRUE);
		}

		if ($is_ajax)
		{
			// set header to json type
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: application/json');
			// if you don't have $this->set_response() use the previous 3 lines
			// $this->set_response('json');
			echo json_encode($this->data);
		}
		else
		{
			// $this->add_script('assets/js/logrus/auth/signup.js');
		}
		echo $message;
	}

	public function confirm_email_code($confirm)
	{
		$this->load->library('logrus/logrus_password');
		$code = $this->logrus_password->get_reset_code($confirm);
		if ($code and $this->logrus_password->validate_reset_code($code->member_id, $confirm))
		{
			$this->load->library('logrus/logrus_member');
			$member = $this->logrus_member->get_by_field('id', $code->member_id);
			$update = $this->logrus_member->set_field($member->email, 'email_confirmed', 1);
			if ($update)
			{
				$message = $this->load->view('logrus/auth/email_confirmed');
			}
			else
			{
				$message = $this->load->view('logrus/auth/error_confirming_email');
			}
		}
		else
		{
			if ($code)
			{
				$message = $this->load->view('logrus/auth/expired_code');
			}
			else
			{
				$message = $this->load->view('logrus/auth/invalid_code');
			}
		}

		echo $message;
	}

	/**
	 * initiates an email confirmation request
	 */
	public function confirm_email()
	{
		if (! $this->logrus_auth->logged_in())
		{
			$message = $this->load->view('logrus/auth/must_be_logged_in', '', TRUE);
		}
		else
		{
			$this->load->library('logrus/logrus_password');
			print_r($this->logrus_auth->member);
			$this->logrus_password->generate_reset_code(
				$this->logrus_auth->member->email,
				'confirm_email'
			);
			$message = $this->load->view('logrus/auth/reset_code_sent', '', TRUE);
		}

		echo $message;
	}

	public function reset_password()
	{
		$this->load->library('form_validation');
		$is_ajax = $this->input->is_ajax_request();

		$var               = new stdClass(); // Can put database object here with = clone $db_object;
		$var->email        = html_purify($this->input->post('email'));
		$this->data['var'] = $var;

		$rules[] = array('field' => 'email', 'label' => 'Your email address', 'rules' => 'trim|required|strtolower');
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == FALSE)
		{
			// first load or failed form
			$error        = new stdClass();
			$error->email = form_error('email');

			$this->data['errors'] = $error;
			$errors               = validation_errors();
			if (trim($errors))
			{
				$this->data['status'] = $errors;
			}
			$message = $this->load->view('logrus/auth/reset_password', $this->data, TRUE);
		}
		else
		{
			$this->load->library('logrus/logrus_member');
			$member = $this->logrus_member->get($var->email);
			if ($member)
			{
				$this->load->library('logrus/logrus_password');
				$this->logrus_password->generate_reset_code($var->email);
			}
			$message = $this->load->view('logrus/auth/reset_code_sent', '', TRUE);
		}

		if ($is_ajax)
		{

			// set header to json type
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: application/json');
			// if you don't have $this->set_response() use the previous 3 lines
			//$this->set_response('json');
			echo json_encode($this->data);
		}
		else
		{
			// $this->add_script('assets/js/logrus/auth/reset_password.js');
		}
		echo $message;
	}


	public function password_reset($confirm)
	{
		$this->load->library('form_validation');
		$is_ajax = $this->input->is_ajax_request();

		$this->load->library('logrus/logrus_password');
		$this->load->library('logrus/logrus_member');
		$code = $this->logrus_password->get_reset_code($confirm);
		if ($code and $this->logrus_password->validate_reset_code($code->member_id, $confirm))
		{
			$var               = new stdClass(); // Can put database object here with = clone $db_object;
			$var->password     = html_purify($this->input->post('password'));
			$var->confirm      = html_purify($this->input->post('confirm'));
			$this->data['var'] = $var;

			$rules[] = array('field' => 'password', 'label' => 'Password', 'rules' => 'required');
			$rules[] = array('field' => 'confirm', 'label' => 'Confirm your password', 'rules' => 'required|matches[password]');
			$this->form_validation->set_rules($rules);

			if ($this->form_validation->run() == FALSE)
			{
				// first load or failed form
				$error           = new stdClass();
				$error->password = form_error('password');
				$error->confirm  = form_error('confirm');

				$this->data['errors'] = $error;
				$errors               = validation_errors();
				if (trim($errors))
				{
					$this->data['status'] = $this->msg->error($errors);
				}
				$message = $this->load->view('logrus/auth/password_reset', $this->data, TRUE);
			}
			else
			{
				$member = $this->logrus_member->get_by_field('id', $code->member_id);
				if ($member)
				{
					$success = $this->logrus_password->set_password_with_reset($member->email, $confirm,
																				$var->password);
					if ($success)
					{
						$message = $this->load->view('logrus/auth/reset_password_changed', '', TRUE);
					}
					else
					{
						$message = $this->load->view('logrus/auth/reset_password_not_changed', '', TRUE);
					}
				}
				else
				{
					$message = $this->load->view('logrus/auth/error_loading_account', '', TRUE);
				}
			}
		}
		else
		{
			if ($code)
			{
				$message = $this->load->view('logrus/auth/expired_code');
			}
			else
			{
				$message = $this->load->view('logrus/auth/invalid_code');
			}
		}


		if ($is_ajax)
		{
			$this->view = FALSE; // disable view for json
			// set header to json type
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: application/json');
			// if you don't have $this->set_response() use the previous 3 lines
			// $this->set_response('json');
			echo json_encode($this->data);
		}
		else
		{
			//$this->add_script('/assets/js/logrus/auth/password_reset.js');
		}
		echo $message;
	}

	public function change_password()
	{
		if (! $this->logrus_auth->logged_in())
		{
			$message = $this->load->view('logrus/auth/must_be_logged_in', '', TRUE);
		}
		else
		{
			$this->load->library('form_validation');
			$is_ajax = $this->input->is_ajax_request();

			$var                   = new stdClass(); // Can put database object here with = clone $db_object;
			$var->current_password = html_purify($this->input->post('current_password'));
			$var->new_password     = html_purify($this->input->post('new_password'));
			$var->confirm_password = html_purify($this->input->post('confirm_password'));
			$this->data['var']     = $var;

			$rules[] = array('field' => 'current_password', 'label' => 'Current Password', 'rules' => 'required');
			$rules[] = array('field' => 'new_password', 'label' => 'New password', 'rules' => 'required');
			$rules[] = array('field' => 'confirm_password', 'label' => 'Confirm your new password', 'rules' => 'required|matches[new_password]');
			$this->form_validation->set_rules($rules);

			if ($this->form_validation->run() == FALSE)
			{
				// first load or failed form
				$error                   = new stdClass();
				$error->current_password = form_error('current_password');
				$error->new_password     = form_error('new_password');
				$error->confirm_password = form_error('confirm_password');

				$this->data['errors'] = $error;
				$errors               = validation_errors();
				if (trim($errors))
				{
					$this->data['status'] = $this->msg->error($errors);
				}
				$message = $this->load->view('logrus/auth/change_password', $this->data, TRUE);
			}
			else
			{
				$this->load->library('logrus/logrus_password');
				if ($this->logrus_password->validate($this->logrus_auth->member->email, $var->password))
				{
					if ($this->logrus_password->set_password($this->logrus_auth->member->email, $var->new_password))
					{
						$message = $this->load->view('logrus/auth/password_successfully_changed', '', TRUE);
					}
					else
					{
						$message = $this->load->view('logrus/auth/password_unsuccessfully_changed', '', TRUE);
					}
				}
				else
				{
					$message = $this->load->view('logrus/auth/incorrect_password_for_change');
				}
				$this->data['message'] = 'You just successfully submitted this form!';
			}

			if ($is_ajax)
			{
				// set header to json type
				header('Cache-Control: no-cache, must-revalidate');
				header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
				header('Content-type: application/json');
				// if you don't have $this->set_response() use the previous 3 lines
				//$this->set_response('json');
				echo json_encode($this->data);
			}
			else
			{
				//$this->add_script('assets/js/logrus/auth/change_password.js');
			}
		}
		echo $message;
	}

}