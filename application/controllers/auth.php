<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Auth extends CI_Controller
	{

		protected $verify_url;

		/**
		 * constructor
		 */
		function __construct()
		{
			parent::__construct();

			$this->config->load('logrus_auth');
			$this->load->library('logrus_auth');

			if ($this->logrus_auth->logged_in())
			{
				$display = $this->logrus_auth->member->display_name == '' ? $this->logrus_auth->member->email : $this->logrus_auth->member->display_name;
				$this->template->add_menu_item('member', '/auth/change_password', '<i class="icon-lock"> </i> Change Password');
				$this->template->add_menu_item('member', '', '<hr />');
				$this->template->add_menu_item('member', '/auth/logout', '<i class="icon-off"> </i> Log Out');
			}
			else
			{
				$this->template->add_menu_item('alt', '/auth/login', 'Log In');
			}

		}

		function index()
		{
			$this->load->helper('url');
			redirect('/');
		}

		/**
		 * Receives a POST with parameter email to check if a user is registered, returns json response
		 */
		function ajax_email_exists()
		{
			$this->load->model('logrus/member');

			$email  = strtolower(trim($this->input->post('email')));
			$member = $this->member->get_by('email', $email);

			if ($member)
			{
				$response['registered'] = TRUE;
			}
			else
			{
				$response['registered'] = FALSE;
			}
			echo json_encode($response);
		}

		/**
		 * handles an ajax login POST request.  returns json response
		 */
		function ajax_login()
		{
			$this->load->model('logrus/member');
			$this->load->library('logrus_auth');

			$email    = strtolower(trim($this->input->post('email', TRUE)));
			$password = $this->input->post('password');

			$member = $this->member->get_by('email', $email);
			if ($member)
			{
				if ($this->logrus_auth->login($email, $password, TRUE))
				{
					$response['status'] = 'OK';
				}
				else
				{
					$response['status'] = 'passwordError';
				}
			}
			else
			{
				$response['status'] = 'passwordError';
			}

			echo json_encode($response);
		}

		/**
		 * login url
		 */
		function login()
		{
			$this->load->library('form_validation');
			$is_ajax    = $this->input->is_ajax_request();
			$ajax_error = FALSE;
			$success    = TRUE;

			$rules[] = array('field' => 'email',
							 'label' => 'Email Address',
							 'rules' => 'trim|required');
			$rules[] = array('field' => 'password',
							 'label' => 'Password',
							 'rules' => 'required');

			// load any variables to refill the form
			$variables['email']    = $this->input->post('email', TRUE);
			$variables['password'] = $this->input->post('password', TRUE);

			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() == FALSE)
			{
				// first load or failed form
				$error              = array(
					'email'    => form_error('email'),
					'password' => form_error('password'),
				);
				$variables['error'] = $error;
				if ($is_ajax)
				{
					if (count($error) > 0)
					{
						$ajax_error = TRUE;
					}
					$message = $error;
				}
				else
				{
					$message = $this->load->view('auth/login_form', $variables, TRUE);
				}
			}
			else
			{
				$email  = strtolower(trim($variables['email']));
				$member = $this->member->get_by('email', $email);
				if ($member)
				{
					$password = $variables['password'];
					if ($this->logrus_auth->login($email, $password, TRUE))
					{
						// $message = $this->msg->info('Logged in.');
						redirect($this->config->item('auth_logged_in_url'));
					}
					else
					{
						$message = $this->logrus_auth->message;
					}
				}
				else
				{
					$message = $this->msg->error('email address or password did not match our records.');
				}
			}

			if ($is_ajax)
			{
				if ($ajax_error)
				{
					echo json_encode($message);
				}
				else
				{
					echo json_encode(array('form_message' => $message));
				}
			}
			else
			{
				$config['content'] = $message;
				$this->template->render($config);
			}

		}

		/**
		 * signup to site
		 */
		function signup()
		{
			$this->load->library('form_validation');
			$this->load->library('logrus_auth');
			$this->load->model('logrus/member');
			if ($this->config->item('auth_open_enrollment'))
			{
				$is_ajax    = $this->input->is_ajax_request();
				$ajax_error = FALSE;

				$rules[] = array('field' => 'email',
								 'label' => 'Email Address',
								 'rules' => 'trim|required|valid_email');
				$rules[] = array('field' => 'display_name',
								 'label' => 'Display Name',
								 'rules' => 'trim');
				$rules[] = array('field' => 'password',
								 'label' => 'Password',
								 'rules' => 'required');
				$rules[] = array('field' => 'confirm',
								 'label' => 'Confirm your password',
								 'rules' => 'required|matches[password]');

				// load any variables to refill the form
				$variables['email']        = $this->input->get_post('email', TRUE);
				$variables['password']     = $this->input->post('password', TRUE);
				$variables['confirm']      = $this->input->post('confirm', TRUE);
				$variables['display_name'] = $this->input->post('display_name', TRUE);

				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE)
				{
					// first load or failed form
					$error              = array(
						'email'           => form_error('email'),
						'password'        => form_error('password'),
						'display_name'    => form_error('display_name'),
					);
					$variables['error'] = $error;
					if ($is_ajax)
					{
						if (count($error) > 0)
						{
							$ajax_error = TRUE;
						}
						$message = $error;
					}
					else
					{
						$message = $this->load->view('auth/signup_form', $variables, TRUE);
					}
				}
				else
				{
					$email        = strtolower(trim($variables['email']));
					$member_check = $this->member->get_by('email', $email);
					if ($member_check)
					{
						$message = '<span class="label label-important">Error</span> an account already exists with that email address.  Did you want to <a href=="/auth/reset_password">Reset your password</a> isntead?';
					}
					else
					{
						$insert = $this->member->insert(
							array(
								 'email'        => $email,
								 'created'      => date('Y-m-d H:i:s'),
								 'display_name' => $variables['display_name'],
								 'active'       => 1
							)
						);
						if ($insert)
						{
							$member = $this->member->get($insert);
							$this->logrus_auth->set_password($member->id, $variables['password']);
							$this->logrus_auth->reset_password($insert, 'new'); // send email
							$message = $this->load->view('auth/signup_completed');
						}
						else
						{
							$message = '<span class="label label-important">Error</span> a problem occured trying to create your account.  Please try again in a few minutes.';
						}
					}
				}
			}
			else
			{
				$message = '<span class="label label-important">Error</span> This site is closed for enrollment.  All accounts must be created by the administrators.';
			}
			// this is where you display results.  javascript version or full template version
			if ($is_ajax)
			{
				if ($ajax_error)
				{
					echo json_encode($message);
				}
				else
				{
					echo json_encode(array('form_message' => $message));
				}
			}
			else
			{
				//load view here, or render if using a template library
				$config['content'] = $message;
				$this->template->add_script('cookie.js');
				$this->template->add_script('auth/signup_form.js');
				$this->template->render($config);
			}
		}

		/**
		 * handles confirming a valid email address for people who want to have a native account
		 *
		 * @param $confirm_code
		 */
		function confirm_email_code($confirm_code)
		{
			$this->load->model('logrus/member');
			$code = $this->logrus_auth->valid_reset_code($confirm_code);
			if ($code)
			{
				$this->member->update($code->member_id, array('email_confirmed' => 1,
															  'active'          => 1));
				$message = $this->load->view('email_was_confirmed');
			}
			else
			{
				$message = $this->load->view('email_not_confirmed');
			}
			$this->template->render($message);
		}

		/**
		 * confirms email address
		 */
		function confirm_email()
		{
			$this->load->library('logrus_auth');
			$is_ajax    = $this->input->is_ajax_request();
			$ajax_error = FALSE;

			if (!$this->logrus_auth->logged_in())
			{
				$message = '<h3>Please log in before initiating confirmation request.</h3>';
			}
			else
			{
				$this->load->library('form_validation');

				$rules[] = array('field' => 'confirm',
								 'label' => 'Confirm',
								 'rules' => 'trim|required');

				// load any variables to refill the form
				$variables['confirm'] = $this->input->post('confirm', TRUE);
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE)
				{
					// first load or failed form
					$error              = array('confirm' => form_error('confirm'));
					$variables['error'] = $error;
					if ($is_ajax)
					{
						if (count($error) > 0)
						{
							$ajax_error = TRUE;
						}
						$message = $error;
					}
					else
					{
						$message = $this->load->view('auth/confirm_email', $variables, TRUE);
					}
				}
				else
				{
					if ($variables['confirm'] == 'yes')
					{
						if ($this->logrus_auth->member)
						{
							$this->logrus_auth->reset_password(
								$this->logrus_auth->member->id,
								'confirm_email'
							);
							$message = '<h3>We have sent a confirmation email to your email address. Please follow instructions in the email to finish validating your email address</h3>';
						}
						else
						{
							$message = '<h3>Please log in before initiating confirmation request.</h3>';
						}

					}
				}
			}

			if ($is_ajax)
			{
				if ($ajax_error)
				{
					echo json_encode($message);
				}
				else
				{
					echo json_encode(array('form_message' => $message));
				}
			}
			else
			{
				//load view here, or render if using a template library
				$config['content'] = $message;
				$this->template->add_script('auth/confirm_email.js');
				$this->template->render($config);
			}
		}

		/**
		 * logout of the site
		 */
		function logout()
		{
			$this->load->library('logrus_auth');
			$this->logrus_auth->log_out();
		}


		// //////////////////////////////////////////////////////////////////////
		// //  password management  /////////////////////////////////////////////
		// //////////////////////////////////////////////////////////////////////

		function password_reset($reset_code = 'none')
		{
			if ($reset_code == 'none')
			{
				$this->template->render(
					$this->msg->error('Invalid reset code.  Please copy the whole url from your email.')
				);
				return;
			}
			$this->load->library('logrus_auth');
			$this->load->library('form_validation');
			$is_ajax    = $this->input->is_ajax_request();
			$ajax_error = FALSE;

			$rules[] = array('field' => 'password',
							 'label' => 'New password',
							 'rules' => 'required');
			$rules[] = array('field' => 'confirm',
							 'label' => 'Confirm new password',
							 'rules' => 'required');

			// load any variables to refill the form
			$variables['code']     = $reset_code;
			$variables['password'] = $this->input->post('password', TRUE);
			$variables['confirm']  = $this->input->post('confirm', TRUE);

			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() == FALSE)
			{
				// first load or failed form
				$error              = array(
					'password' => form_error('password'),
					'confirm'  => form_error('confirm'),
				);
				$variables['error'] = $error;
				if ($is_ajax)
				{
					if (count($error) > 0)
					{
						$ajax_error = TRUE;
					}
					$message = $error;
				}
				else
				{
					$message = $this->load->view('auth/password_reset', $variables, TRUE);
				}
			}
			else
			{
				if ($this->logrus_auth->validate_and_set_password($reset_code, $variables['password']))
				{
					$message = $this->msg->info('Your password has been reset.  <a href="/auth/login">Login Here</a>');
				}
				else
				{
					$message = sprintf('There was a problem resetting your password. (%s) <a href=="/auth/reset_password">Try again?</a>', $this->logrus_auth->message);
				}
			}

			if ($is_ajax)
			{
				if ($ajax_error)
				{
					echo json_encode($message);
				}
				else
				{
					echo json_encode(array('form_message' => $message));
				}
			}
			else
			{
				$config['content'] = $message;
				$this->template->add_script('password_reset.js');
				$this->template->render($config);
			}
		}

		function reset_password()
		{
			$this->load->library('form_validation');
			$this->load->model('logrus/member');
			$this->load->library('logrus_auth');

			$is_ajax    = $this->input->is_ajax_request();
			$ajax_error = FALSE;

			$rules[] = array('field' => 'email',
							 'label' => 'Your Email Address',
							 'rules' => 'trim|required');

			$variables['email'] = $this->input->post('email', TRUE);

			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() == FALSE)
			{

				$error              = array(
					'email' => form_error('email'),
				);
				$variables['error'] = $error;
				if ($is_ajax)
				{
					if (count($error) > 0)
					{
						$ajax_error = TRUE;
					}
					$message = $error;
				}
				else
				{
					$message = $this->load->view('auth/reset_password', $variables, TRUE);
				}
			}
			else
			{
				$email  = strtolower(trim($variables['email']));
				$member = $this->member->get_by('email', $email);
				if ($member)
				{
					$this->logrus_auth->reset_password($member->id, 'reset');
					$message = $this->msg->info('Please check your email for a password reset email from us with further instructions');
				}
				else
				{
					$message = $this->msg->block('Please check your email for a password reset email from us with further instructions.');
				}
			}
			// this is where you display results.  javascript version or full template version
			if ($is_ajax)
			{
				if ($ajax_error)
				{
					echo json_encode($message);
				}
				else
				{
					echo json_encode(array('form_message' => $message));
				}
			}
			else
			{
				//load view here, or render if using a template library
				$config['content'] = $message;

				$this->template->add_script('auth/reset_password.js');
				$this->template->render($config);
			}

		}

		/**
		 * allows the user to change their password.  They must be logged in.
		 */
		function change_password()
		{
			if (!$this->logrus_auth->logged_in())
			{
				$message = '<h3>You must be logged in to change your password.</h3><a href="/auth/login">Log in here</a>';
			}
			else
			{
				$this->load->library('form_validation');
				$is_ajax    = $this->input->is_ajax_request();
				$ajax_error = FALSE;

				$rules[] = array('field' => 'password',
								 'label' => 'Current Password',
								 'rules' => 'required');
				$rules[] = array('field' => 'new_password',
								 'label' => 'New Password',
								 'rules' => 'trim|required|min_length[6]');
				$rules[] = array('field' => 'confirm_password',
								 'label' => 'Confirm new password',
								 'rules' => 'trim|required|matches[new_password]');

				// load any variables to refill the form
				$variables['password']         = $this->input->post('password', TRUE);
				$variables['new_password']     = $this->input->post('new_password', TRUE);
				$variables['confirm_password'] = $this->input->post('confirm_password', TRUE);

				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE)
				{
					$error              = array(
						'password'         => form_error('password'),
						'new_password'     => form_error('new_password'),
						'confirm_password' => form_error('confirm_password'),
					);
					$variables['error'] = $error;
					if ($is_ajax)
					{
						if (count($error) > 0)
						{
							$ajax_error = TRUE;
						}
						$message = $error;
					}
					else
					{
						$message = $this->load->view('auth/change_password', $variables, TRUE);
					}
				}
				else
				{
					$member = $this->logrus_auth->member;
					if ($this->logrus_auth->password_matches($member->id, $variables['password']))
					{
						if ($this->logrus_auth->set_password($member->id, $variables['new_password']))
						{
							$message = 'You have just successfully changed your password.';
						}
						else
						{
							$message = 'There was a problem saving your password.  Please try again.';
						}

					}
					else
					{
						$message = 'You need to enter your correct original password to change your password.';
						$message .= $this->load->view('auth/change_password', $variables, TRUE);
					}
				}
				// this is where you display results.  javascript version or full template version
			}

			if ($is_ajax)
			{
				if ($ajax_error)
				{
					echo json_encode($message);
				}
				else
				{
					echo json_encode(array('form_message' => $message));
				}
			}
			else
			{
				$config['content'] = $message;
				$this->template->add_script('cookie.js');
				$this->template->add_script('auth_change_password.js');
				$this->template->render($config);
			}
		}
	}