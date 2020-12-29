<?php
/**
*
* @package CPFs for Autogroups Extension
* @copyright (c) 2019 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\cpfautogroup\controller;

use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use phpbb\log\log;
use phpbb\language\language;
use phpbb\db\driver\driver_interface;
use david63\cpfautogroup\core\functions;

/**
* Admin manage controller
*/
class acp_manage_controller implements acp_manage_interface
{
	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpBB extension */
	protected $php_ext;

	/** @var string The database table for CPF auto group data */
	protected $autogroups_cpf_data_table;

	/** @var \david63\cpfautogroup\core\functions */
	protected $functions;

	/** @var string phpBB tables */
	protected $tables;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor for admin manage controller
	*
	* @param \phpbb\request\request					$request					Request object
	* @param \phpbb\template\template				$template					Template object
	* @param \phpbb\user							$user						User object
	* @param \phpbb\log\log							$log						Log object
	* @param \phpbb\language\language				$language					Language object
	* @param \phpbb_db_driver						$db							The db connection
	* @param string									$phpbb_root_path			phpBB root path
	* @param string									$php_ext            		phpBB file extension
	* @param string									$autogroups_cpf_data_table	Name of the table used to store CPF auto group data
	* @param \david63\cpfautogroup\core\functions	$functions					Functions for the extension
	* @param array									$tables						phpBB db tables
	*
	* @return \david63\cpfautogroup\controller\acp_manage_controller
	* @access public
	*/
	public function __construct(request $request, template $template, user $user, log $log, language $language, driver_interface $db, $phpbb_root_path, $php_ext, $autogroups_cpf_data_table, functions $functions, $tables)
	{
		$this->request						= $request;
		$this->template						= $template;
		$this->user							= $user;
		$this->log							= $log;
		$this->language						= $language;
		$this->db							= $db;
		$this->phpbb_root_path				= $phpbb_root_path;
		$this->php_ext						= $php_ext;
		$this->autogroups_cpf_data_table 	= $autogroups_cpf_data_table;
		$this->functions					= $functions;
		$this->tables						= $tables;
	}

	/**
	* Display the options a user can configure for this extension
	*
	* @return null
	* @access public
	*/
	public function display_options()
	{
		// Add the language files
		$this->language->add_lang('acp_cpfautogroup', $this->functions->get_ext_namespace());

		// Create a form key for preventing CSRF attacks
		$form_key = 'cpfautogroup';
		add_form_key($form_key);

		$back = false;

		// Start initial var setup
		$action					= $this->request->variable('action', '');
		$agree					= ($action == 'agree') ? true : false;
		$cpf_cron				= $this->request->variable('cpf_cron', 0);
		$cpf_cron_time			= $this->request->variable('cpf_cron_time', 24);
		$cpf_date_type			= $this->request->variable('cpf_date_type', '');
		$cpf_event_trigger		= $this->request->variable('cpf_event_trigger', 0);
		$cpf_field_name 		= $this->request->variable('cpf_field_name', '');
		$cpf_group 				= $this->request->variable('cpf_group', 0);
		$cpf_group_use 			= $this->request->variable('cpf_group_use', 0);
		$cpf_non_active_period 	= $this->request->variable('cpf_non_active_period', 0);
		$cpf_non_post_period 	= $this->request->variable('cpf_non_post_period', 0);
		$cpf_users 				= $this->request->variable('cpf_users', 0);
		$field_type 			= $this->get_field_type($this->request->variable('cpf_field_name', ''));
		$submit					= ($this->request->is_set_post('submit')) ? true : false;

		// Is the submitted form is valid
		if ($submit)
		{
			if (!check_form_key($form_key))
			{
				trigger_error($this->language->lang('FORM_INVALID'));
			}
		}

		// Is the form being submitted
		if ($submit || $agree)
		{
			// Let's do some validation of the input data
			// Has a CPF field name been selected?
			if (!$cpf_field_name)
			{
				trigger_error($this->language->lang('FIELD_NAME_NOT_SELECTED') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Do we have either a trigger or cron set?
			if (!$cpf_cron && !$cpf_event_trigger)
			{
				trigger_error($this->language->lang('CRON_EVENT_ERROR') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Validate the CPF field type as not all field types are implemented
			if (in_array($field_type, array('dropdown', 'googleplus', 'string', 'text', 'url')))
			{
				trigger_error($this->language->lang('INVALID_FIELD_TYPE') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// If date type field is selected has a date interval selected?
			if ($field_type == 'date' && $cpf_date_type == 'NONE')
			{
				trigger_error($this->language->lang('DATE_TYPE_ERROR') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Is field type unique?
			$sql = 'SELECT cpf_field_type
				FROM ' . $this->autogroups_cpf_data_table . "
				WHERE cpf_field_type = '" . $field_type . "'";

			$result = $this->db->sql_query($sql);
			$type	= $this->db->sql_fetchfield('cpf_field_type');

			$this->db->sql_freeresult($result);

			// If there is already a field type in the database
			// Ask the user to confirm the overwite
			if ($type)
			{
				$update = false;

				if (confirm_box(true))
				{
					$update = true;
				}
				else
				{
					confirm_box(false, $this->language->lang('CPF_FIELD_TYPE_EXISTS'), build_hidden_fields(array(
						'action'				=> 'agree',
						'cpf_cron'				=> $cpf_cron,
						'cpf_cron_time'			=> $cpf_cron_time,
						'cpf_date_type'			=> $cpf_date_type,
						'cpf_event_trigger'		=> $cpf_event_trigger,
						'cpf_field_name'		=> $cpf_field_name,
						'cpf_field_type'		=> $field_type,
						'cpf_group'				=> $cpf_group,
						'cpf_group_use'			=> $cpf_group_use,
						'cpf_non_active_period'	=> $cpf_non_active_period,
						'cpf_non_post_period'	=> $cpf_non_post_period,
						'cpf_users'				=> $cpf_users,
					)));
				}
			}
			else
			{
				$update = true;
			}

			// If no errors, process the form data
			if ($update)
			{
				// Set the options the user configured
				$this->set_options();

				// Add option settings change action to the admin log
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CPF_AUTOGROUP');

				// Option settings have been updated and logged
				// Confirm this to the user and provide link back to previous page
				trigger_error($this->language->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
			}
		}

		// Create the CPF summary
		$summary = false;

		// Do we have any data?
		$sql = 'SELECT COUNT(cpf_field_type) AS field_count
			FROM ' . $this->autogroups_cpf_data_table;

		$result			= $this->db->sql_query($sql);
		$field_count	= (int) $this->db->sql_fetchfield('field_count');

		$this->db->sql_freeresult($result);

		if ($field_count)
		{
			$summary = true;

			// We need the "get_group_name" function for later
			if (!function_exists('get_group_name'))
			{
				include_once($this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext);
			}

			// Get the CPF summary data
			$sql = 'SELECT c.*, pf.field_name, pl.lang_name
				FROM ' . $this->autogroups_cpf_data_table . ' c, ' . $this->tables['profile_fields'] . ' pf, ' . $this->tables['profile_lang'] . ' pl, ' . $this->tables['lang'] . ' l
				WHERE c.cpf_field_name = pf.field_name
					AND pf.field_id  = pl.field_id
					AND pl.lang_id = l.lang_id
					AND pf.field_active = 1
					AND l.lang_iso = "' . $this->user->data['user_lang'] . '"
				ORDER BY c.cpf_field_type';

			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				// Get the options for this row
				$options 		= '';
				$get_options	= json_decode($row['cpf_options'], true);

				$options .= ($get_options['cpf_group_use']) ? $this->language->lang('GROUP') . $this->language->lang('COLON') . get_group_name($get_options['cpf_group']) . '<br>' : '';
				$options .= ($get_options['cpf_non_active_period']) ? $this->language->lang('NON_ACTIVE_CPF') . '<br>' : '';
				$options .= ($get_options['cpf_non_post_period']) ? $this->language->lang('NON_POST_CPF')  . '<br>' : '';
				$options .= ($row['cpf_field_type'] == 'date') ? $this->language->lang('DATE_TIME') . $this->language->lang('COLON') . $this->get_lang_var('cpf_datetypes', $get_options['cpf_date_type']) : '';

				$this->template->assign_block_vars('cpf_summary', array(
					'CPF_SUMMARY_FIELD'			=> $row['lang_name'],
					'CPF_SUMMARY_FIELD_TYPE'	=> $this->get_lang_var('cpf_fieldtypes', $row['cpf_field_type']),
					'CPF_SUMMARY_USERS'			=> ($row['cpf_users']) ?  $this->language->lang('SINGLE') :  $this->language->lang('ALL'),
					'CPF_SUMMARY_EVENT_CRON'	=> (!$row['cpf_cron']) ? $this->language->lang('E') . $this->get_lang_var('cpf_event_trigger', $row['cpf_event_trigger']) : $this->language->lang('C') . $this->language->lang('EVERY') . ' ' . $row['cpf_cron_time'] . ' ' . $this->language->lang('HOURS'),
					'CPF_SUMMARY_CONDITIONS'	=> $options,
				));
			}

			$this->db->sql_freeresult($result);
		}

		// Create the event trigger select
		$event_trigger_options = '';
		foreach ($this->language->lang_raw('cpf_event_trigger') as $key => $event_trigger)
		{
			$selected = ($cpf_event_trigger == $key) ? ' selected="selected"' : '';
			$event_trigger_options .= '<option value="' . $key . '"' . $selected . '>' . $event_trigger . '</option>';
		}

		// Create the date type select
		$date_type_options = '';
		foreach ($this->language->lang_raw('cpf_datetypes') as $key => $date_type)
		{
			$selected = ($cpf_date_type == $key) ? ' selected="selected"' : '';
			$date_type_options .= '<option value="' . $key . '"' . $selected . '>' . $date_type . '</option>';
		}

		// Find the group_id for the BOT group
		$sql = 'SELECT group_id
			FROM ' . $this->tables['groups'] . "
			WHERE group_name = '" . $this->db->sql_escape('BOTS') . "'
				AND group_type = " . GROUP_SPECIAL;

		$result 		= $this->db->sql_query($sql);
		$bot_group_id	= $this->db->sql_fetchfield('group_id');

		$this->db->sql_freeresult($result);

		// Template vars for header panel
		$this->template->assign_vars(array(
			'HEAD_TITLE'		=> $this->language->lang('CPF_AUTOGROUP'),
			'HEAD_DESCRIPTION'	=> $this->language->lang('CPF_AUTOGROUP_MANAGE_EXPLAIN'),

			'NAMESPACE'			=> $this->functions->get_ext_namespace('twig'),

			'S_BACK'			=> $back,
			'S_VERSION_CHECK'	=> $this->functions->version_check(),

			'VERSION_NUMBER'	=> $this->functions->get_this_version(),
		));

		$this->template->assign_vars(array(
			'CPF_CRON_TIME'				=> $cpf_cron_time,
			'CPF_CRON_USE'				=> $cpf_cron,
			'CPF_GROUP_USE'				=> $cpf_group_use,
			'CPF_NON_ACTIVE_PERIOD'		=> $cpf_non_active_period,
			'CPF_NON_POST_PERIOD'		=> $cpf_non_post_period,
			'CPF_USERS'					=> $cpf_users,

			'S_CPF_SELECT'				=> $this->cpf_select($cpf_field_name),
			'S_DATE_TYPE_SELECT'		=> $date_type_options,
			'S_EVENT_TRIGGER_SELECT'	=> $event_trigger_options,
			'S_GROUP_SELECT'			=> group_select_options($cpf_group, array($bot_group_id)),

			'U_ACTION'			   		=> $this->u_action,
			'U_SUMMARY'					=> $summary,
		));
	}

	/**
	* Set the options a user can configure
	*
	* @return null
	* @access protected
	*/
	protected function set_options()
	{
		$options 	= [];
		$field_type = $this->get_field_type($this->request->variable('cpf_field_name', ''));

		$options['cpf_date_type']			= $this->request->variable('cpf_date_type', '');
		$options['cpf_group'] 				= $this->request->variable('cpf_group', 0);
		$options['cpf_group_use'] 			= $this->request->variable('cpf_group_use', 0);
		$options['cpf_non_post_period'] 	= $this->request->variable('cpf_non_post_period', 0);
		$options['cpf_non_active_period'] 	= $this->request->variable('cpf_non_active_period', 0);
		$options							= json_encode($options);

		$sql = 'INSERT INTO ' . $this->autogroups_cpf_data_table . '(cpf_field_type, cpf_field_name, cpf_event_trigger, cpf_users, cpf_cron, cpf_cron_time, cpf_options)
				VALUES (
					"' . $field_type . '",
					"' . $this->request->variable('cpf_field_name', '') . '",
					' . (int) $this->request->variable('cpf_event_trigger', 1) . ',
					' . (int) $this->request->variable('cpf_users', 0) . ',
					' . (int) $this->request->variable('cpf_cron', 0) . ',
					' . (int) $this->request->variable('cpf_cron_time', 24) . ',
					' . "'" . $options . "'" . '
				)
				ON DUPLICATE KEY UPDATE
					cpf_field_name 		= "' . $this->request->variable('cpf_field_name', '') . '",
					cpf_event_trigger	= ' . (int) $this->request->variable('cpf_event_trigger', 1) . ',
					cpf_users 			= ' . (int) $this->request->variable('cpf_users', 0) . ',
					cpf_cron 			= ' . (int) $this->request->variable('cpf_cron', 0) . ',
					cpf_cron_time 		= ' . (int) $this->request->variable('cpf_cron_time', 24) . ',
					cpf_options 		= ' . "'" . $options . "'" . '';

		$this->db->sql_query($sql);
	}

	/**
	* Get a language variable from a language variable array
	*
	* @return $data
	* @access protected
	*/
	public function get_lang_var($lang_array, $lang_key)
	{
		foreach ($this->language->lang_raw($lang_array) as $key => $data)
		{
			if ($key == $lang_key)
			{
				return $data;
			}
		}
	}

	/**
	* Create the CPF select
	*
	* @return $s_cpf_options
	* @access protected
	*/
	protected function cpf_select($field_name)
	{
		$sql = 'SELECT pf.field_name, pl.lang_name
			FROM ' . $this->tables['profile_fields'] . ' pf, ' . $this->tables['profile_lang'] . ' pl, ' . $this->tables['lang'] . ' l
			WHERE pf.field_id  = pl.field_id
				AND pl.lang_id = l.lang_id
				AND pf.field_active = 1
				AND l.lang_iso = "' . $this->user->data['user_lang'] . '"
				ORDER BY pl.lang_name';

		$result = $this->db->sql_query($sql);

		$s_cpf_options = '<option value=0>' . $this->language->lang('SELECT_CPF_FIELD') . '</option>';
		while ($row = $this->db->sql_fetchrow($result))
		{
			$selected = ($row['field_name'] == $field_name) ? ' selected="selected"' : '';
			$s_cpf_options .= '<option value="' . $row['field_name'] . '"' . $selected . '>' . $this->language->lang($row['lang_name']) . '</option>';
		}

		$this->db->sql_freeresult($result);

		return $s_cpf_options;
	}

	/**
	* Get field type
	*
	* @return string Field type name
	* @access public
	*/
	public function get_field_type($field_name)
	{
		$sql = 'SELECT field_type
			FROM ' . $this->tables['profile_fields'] . '
			WHERE field_name = "' . $field_name . '"';

		$result 	= $this->db->sql_query($sql);
		$field_type	= $this->db->sql_fetchfield('field_type');

		$this->db->sql_freeresult($result);

		// Strip off profilefields.type. from the start of the field
		return substr($field_type, 19);
	}

	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return null
	* @access public
	*/
	public function set_page_url($u_action)
	{
		return $this->u_action = $u_action;
	}
}
