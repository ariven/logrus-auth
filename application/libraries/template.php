<?php

	if (!defined('BASEPATH'))
		exit('No direct script access allowed');

	/**
	 * 2012-04-04 - added cmsg back in, changed $this->message, warning and error to check for empty string and ignore
	 * 2012-04-03 - imported from vitamistmarket.com
	 * created 2012-03-05
	 *
	 * @author patrick
	 *
	 * Template class to render a page given the data for the class
	 */
	class Template
	{

		protected $is_admin = FALSE; // if true we are an admin
		protected $is_cs = FALSE; // if true, we are a cs agent
		protected $is_active = FALSE; // if true member is active and can be shown content


		protected $ci = ''; // holder for CI instance


		protected $script_files = array(); // .js files to include
		protected $external_script_files = array(); // .js files to include from other servers
		protected $ready_script = array(); // script to put in $.ready() section
		protected $stylesheets = array(); // extra .css sheets to include

		protected $template_directory; // default template, set from config/template
		protected $current_template = 'default';
		protected $section_list = array(); // names of sections, loaded from config
		protected $sections = array(); // array of sections
		protected $menu_list = array(); // names of menus, loaded from config
		protected $menus = array(); // array of menus

		protected $meta = array(); // stuff for header like title, description, etc

		/**
		 * initializes data
		 */

		function __construct()
		{
			$this->ci = & get_instance();
			$this->ci->load->config('template');

			$this->template_directory = $this->ci->config->item('default_template');
			$this->current_template   = $this->ci->config->item('default_template');
			$this->select_template($this->current_template);
			$this->meta = $this->ci->config->item('template_meta');

		}

		/**
		 * configures for a specific template as defined in the config file
		 *
		 * @param type $which
		 */
		function select_template($which)
		{
			$section_array = $this->ci->config->item('template_sections');
			$menu_array    = $this->ci->config->item('template_menus');

			$this->current_template  = $which;
			$this->template_sections = $section_array[$which];
			$this->menu_list         = $menu_array[$which];

			// make sure each section exists in arraay
			foreach ($section_array[$this->current_template] as $section)
			{
				if (!isset($this->sections[$section]))
				{
					$this->sections[$section] = array();
				}
			}

		}

		/**
		 * add a menu item
		 *
		 * @param string $which_menu which menu as configured in config file
		 * @param string $link the URL
		 * @param string $text the text to display
		 */
		function add_menu_item($which_menu, $link, $text)
		{
			$this->menus[$which_menu][] = array('link' => $link,
												'text' => $text);
		}

		/**
		 * adds content to a section.  All this content will be included in the render() call.  You
		 * can even use it for the main section content if you prefer, isntead of including that content
		 * in the render($content) call itself.
		 *
		 * @param string $section the section name
		 * @param string $text the content
		 */
		function content($section, $text)
		{
			$section_wraps = $this->ci->config->item('template_section_wrap');

			if (isset($section_wraps[$this->current_template][$section]))
			{
				$view_file = 'template/' . $this->template_directory . '/' . $section_wraps[$this->current_template][$section];
			}
			else
			{
				$view_file = 'template/' . $this->template_directory . '/' . $this->ci->config->item('template_generic_wrapper');
			}

			if (file_exists('application/views/' . $view_file))
			{
				$this->sections[$section][] = $this->ci->load->view($view_file, array('content' => $text), TRUE);
			} else
			{
				$this->sections[$section][] = $text;
			}

		}

		/**
		 * used to add title, description, etc.
		 *
		 * @param type $which
		 * @param type $what
		 */
		function add_meta($which, $what)
		{
			$this->meta[$which] = $what;
		}

		/**
		 * adds a javascript file to the current document
		 *
		 * @param unknown_type $name
		 */
		function add_script($name)
		{
			if (strpos($name, 'http') === FALSE)
			{
				$this->script_files[] = $name;
			}
			else
			{
				$this->external_script_files[] = $name;
			}
		}

		/**
		 * adds a stylesheet entry to the file
		 *
		 * @param string $name
		 */
		function add_stylesheet($name)
		{
			$this->stylesheets[] = $name;
		}

		/**
		 * adds a chunk of javascript to be placed in a jquery ready tag
		 *
		 * @param string $text
		 */
		function add_readyscript($text)
		{
			$this->ready_script[] = $text;
		}

		/**
		 * checks to see if view exists in template dir, if not, returns string if it is a string,
		 * or the ['content'] item if array.
		 * This allows for a little leeway if the view file doesn't exist because we didn't configure it
		 *
		 * @param string $which
		 * @param string $content
		 */
		function _load_view($which, $content)
		{
			$view_file = 'template/' . $this->template_directory . '/' . $this->ci->config->item($which);

			if (file_exists('application/views/' . $view_file))
			{
				if (is_array($content))
				{
					return $this->ci->load->view($view_file, $content, TRUE);
				}
				else
				{
					return $this->ci->load->view($view_file, array('content' => $content), TRUE);
				}
			}
			else
			{
				if (is_array($content))
				{
					if (isset($content['content']))
					{
						return $content['content'];
					}
				}
				else
				{
					return $content;
				}
			}
		}


		/**
		 * loads template specific version of a file.  Must be configured in template master config file
		 *
		 * @param unknown_type $which
		 * @param unknown_type $content
		 */
		function load_view($which, $content)
		{
			return $this->_load_view($which, $content);
		}

		/**
		 * processes menu items to wrap them with the template wrapper file prior to render
		 */
		function process_menus()
		{
			$menus         = array();
			$template_menu = $this->ci->config->item('template_menu');
			foreach ($this->menus as $which => $menu)
			{
				$menus[$which] = $this->ci->load->view('template/' . $this->template_directory . '/' . $template_menu[$this->current_template][$which], array('items' => $menu), TRUE);
			}
			return $menus;
		}

		/**
		 * wraps the section with its config file specified wrapper
		 *
		 * @param string $section
		 * @param string $content
		 * @return string
		 */
		function section_wrap($section, $content)
		{
			$wraps = $this->ci->config->item('template_section_wrap');
			if (isset($wraps[$this->current_template][$section]))
			{
				return $this->ci->load->view('template/' . $this->template_directory . '/' . $wraps[$this->current_template][$section], array('content' => $content), TRUE);
			} else
			{
				return $content;
			}
		}

		/**
		 * prepares the sections for rendering
		 *
		 * @return array
		 */
		function process_sections()
		{
			$the_sections = array();
			foreach ($this->sections as $which => $sect)
			{
				$the_sections[$which] = '';
				foreach ($sect as $item)
				{
					$the_sections[$which] .= $this->section_wrap($which, $item);
				}

			}
			return $the_sections;
		}

		/**
		 * renders content
		 *
		 * @param array or string $content  if array, ['content'] is the main content, if string it is content to pass into the display as is
		 */
		function render($content = '')
		{

			if ($content <> '')
			{
				if (is_array($content))
				{
					$this->content('content', $content['content']);
				}
				else
				{
					$this->content('content', $content);
				}
			}

			$data['stylesheets']      = $this->stylesheets;
			$data['ready_script']     = $this->ready_script;
			$data['include_scripts']  = $this->script_files;
			$data['external_scripts'] = $this->external_script_files;
			$data['meta']             = $this->meta;
			$data['menu']             = $this->process_menus();
			if ($this->logrus_auth->member)
			{
				$data['member_name'] = ($this->logrus_auth->member->display_name <> '') ? $this->logrus_auth->member->display_name : $this->logrus_auth->member->email;
				$data['member'] = $this->logrus_auth->member;
			}
			else
			{
				$data['member_name'] = '';
			}


			$auto_data                = $this->ci->config->item('template_section_autodata');
			if (is_array($auto_data))
			{
				foreach ($auto_data[$this->current_template] as $section_name => $section_data)
				{
					if (isset($section_data['model']))
					{

						$model_name = $section_data['model'];
						$this->ci->load->model($model_name);

						$get_function = $section_data['model_function_name'];
						$s_data       = $this->
							ci->
						$model_name->
						$get_function();
						$this->content($section_name, $s_data);
					}
				}
			}

			$data['section'] = $this->process_sections(); // prepare for display

			$view_file = 'template/' . $this->template_directory . '/' . $this->ci->config->item('template_main_tpl');
			$this->ci->load->view($view_file, $data);
		}

		/**
		 * __get
		 *
		 * Allows library to access CI's loaded classes using the same
		 * syntax as controllers.
		 *
		 * @param    string
		 * @access private
		 */
		function __get($key)
		{
			return $this->ci->$key;
		}

	}

