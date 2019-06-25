<?php
/**
*
* @package CPFs for Autogroups Extension
* @copyright (c) 2019 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\cpfautogroup\conditions\type;

use Symfony\Component\DependencyInjection\ContainerInterface;

use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\autogroups\conditions\type\base;

use david63\cpfautogroup\core\functions;
use david63\cpfautogroup\core\constants;

/**
* Auto Groups CPF class
*/
class cpf_date extends base
{
	/** @var \david63\cpfautogroup\core\functions */
	protected $functions;

	/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface                	$container              	Service container interface
	 * @param \phpbb\db\driver\driver_interface 	$db                     	Database object
	 * @param \phpbb\language\language          	$language               	Language object
	 * @param string                            	$autogroups_rules_table 	Name of the table used to store auto group rules data
	 * @param string                            	$autogroups_types_table 	Name of the table used to store auto group types data
	 * @param string                            	$phpbb_root_path        	phpBB root path
	 * @param string                            	$php_ext                	phpEx
	 * @param \david63\cpfautogroup\core\functions	functions					Functions for the extension
	 *
	 * @access public
	 */
	public function __construct(ContainerInterface $container, driver_interface $db, language $language, $autogroups_rules_table, $autogroups_types_table, $phpbb_root_path, $php_ext, functions $functions)
	{
		parent::__construct($container, $db, $language, $autogroups_rules_table, $autogroups_types_table, $phpbb_root_path, $php_ext);

		$this->functions = $functions;
	}

	/**
	* Get condition type
	*
	* @return string Condition type
	* @access public
	*/
	public function get_condition_type()
	{
		return 'david63.cpfautogroup.autogroups.type.cpf_date';
	}

	/**
	* Get condition field (this is the field to check)
	*
	* @return string Condition field name
	* @access public
	*/
	public function get_condition_field()
	{
		$cpf_field = 'pf_' . $this->functions->cpf_data_get('cpf_field_name', 'date');
		return $cpf_field;
	}

	/**
	* Get condition type name
	*
	* @return string Condition type name
	* @access public
	*/
	public function get_condition_type_name()
	{
		return $this->language->lang('CPF_AUTOGROUPS_TYPE_DATE');
	}

	/**
	 * Get users to apply to this condition
	 * CPF auto group is called by events when sessions are checked
	 * By default, get users that have between the min/max
	 * values assigned to this type and any users currently in groups
	 * assigned to this type, otherwise use the user_id(s) supplied in
	 * the $options arg.
	 *
	 * @param array $options Array of optional data
	 * @return array Array of users ids as keys and their condition data as values
	 * @access public
	 */
	public function get_users_for_condition($options = array())
	{
		// The user data this condition needs to check
		$condition_data = array(
			$this->get_condition_field(),
		);

		// Merge default options, empty user array as the default
		$options = array_merge(array(
			'users'		=> array(),
		), $options);

		$sql_array = array(
			'SELECT' => 'u.user_id, pfd.' . implode(', u.', $condition_data),
			'FROM' => array(
				USERS_TABLE => 'u',
			),
			'LEFT_JOIN' => array(
				array(
					'FROM' => array(USER_GROUP_TABLE => 'ug'),
					'ON' => 'u.user_id = ug.user_id',
				),
				array(
					'FROM' => array(PROFILE_FIELDS_DATA_TABLE => 'pfd'),
					'ON' => 'u.user_id = pfd.user_id',
				),
			),
			'WHERE' => $this->sql_where_clause($options) . '
				AND ' . $this->db->sql_in_set('u.user_type', array(USER_INACTIVE, USER_IGNORE), true),

			'GROUP_BY' => 'u.user_id',
		);

		// Are there any CPF conditions?
		$sql_array['WHERE'] .= $this->functions->get_cpf_conditions('date');

		$sql 	= $this->db->sql_build_query('SELECT_DISTINCT', $sql_array);
		$result = $this->db->sql_query($sql);

		// Get auto group rule data for this type
		$group_rules = $this->get_group_rules($this->get_condition_type());

		// Initialise some variables that we need
		$user_data 		= array();
		$cpf_field 		= 'pf_' . $this->functions->cpf_data_get('cpf_field_name', 'date');
		$cpf_date_type	= $this->functions->cpf_data_get('cpf_date_type', 'date', true);
		$min_value 		= $min_calc = 0;

		// Here we can manipulate the CPF fields
		while ($row = $this->db->sql_fetchrow($result))
		{
			foreach ($group_rules as $key => $rule)
			{
				switch ($cpf_date_type)
				{
					case 'MINUTE':
						$max_calc = time() - ($group_rules[$key]['autogroups_max_value'] * constants::CPF_MINUTE);
						$min_calc = time() - ($group_rules[$key]['autogroups_min_value'] * constants::CPF_MINUTE);
					break;

				   	case 'HOUR':
						$max_calc = time() - ($group_rules[$key]['autogroups_max_value'] * constants::CPF_HOUR);
						$min_calc = time() - ($group_rules[$key]['autogroups_min_value'] * constants::CPF_HOUR);
					break;

					case 'DAY':
						$max_calc = time() - ($group_rules[$key]['autogroups_max_value'] * constants::CPF_DAY);
						$min_calc = time() - ($group_rules[$key]['autogroups_min_value'] * constants::CPF_DAY);
					break;

					case 'WEEK':
						$max_calc = time() - ($group_rules[$key]['autogroups_max_value'] * constants::CPF_WEEK);
						$min_calc = time() - ($group_rules[$key]['autogroups_min_value'] * constants::CPF_WEEK);
					break;

					case 'MONTH':
						$max_calc = time() - ($group_rules[$key]['autogroups_max_value'] * constants::CPF_MONTH);
						$min_calc = time() - ($group_rules[$key]['autogroups_min_value'] * constants::CPF_MONTH);
					break;

					case 'YEAR':
						$max_calc = time() - ($group_rules[$key]['autogroups_max_value'] * constants::CPF_YEAR);
						$min_calc = time() - ($group_rules[$key]['autogroups_min_value'] * constants::CPF_YEAR);
					break;
				}

				$data_row 		= $row;
				$cpf_field_time = strtotime(str_replace(' ', '0', $data_row[$cpf_field]));

				$data_row[$cpf_field] = 0;
				if ($cpf_field_time < $min_calc)
				{
					$data_row[$cpf_field] = $group_rules[$key]['autogroups_min_value'];
				}
				else if ($cpf_field_time < $max_calc)
				{
					$data_row[$cpf_field] = $group_rules[$key]['autogroups_max_value'];
				}

				if (!empty($user_data) && array_key_exists($row['user_id'], $user_data))
				{
	   				$data_row[$cpf_field] = ($data_row[$cpf_field] > $user_data[$row['user_id']][$cpf_field]) ? $data_row[$cpf_field] : $user_data[$row['user_id']][$cpf_field];
				}
				$user_data[$row['user_id']]	= $data_row;
			}
		}

		$this->db->sql_freeresult($result);

		return $user_data;
	}

	/**
	 * Helper to generate the needed sql where clause. If user ids were
	 * supplied, use them. Otherwise find all qualified users to check.
	 *
	 * @param array $options Array of optional data
	 * @return string SQL where clause
	 * @access protected
	 */
	protected function sql_where_clause($options)
	{
		// If we have user id data, return a sql_in_set of user_ids
		if (!empty($options['users']))
		{
			return $this->db->sql_in_set('u.user_id', $this->helper->prepare_users_for_query($options['users']));
		}

		$sql_where 	= $group_ids = array();
		$extremes 	= array('min' => '>=', 'max' => '<=');

		$cpf_field 		= 'pfd.pf_' . $this->functions->cpf_data_get('cpf_field_name', 'date');

		// Get auto group rule data for this type
		$group_rules = $this->get_group_rules($this->get_condition_type());

		foreach ($group_rules as $group_rule)
		{
			$where = array();
			foreach ($extremes as $end => $sign)
			{
				if (!empty($group_rule['autogroups_' . $end . '_value']))
				{
					$where[] = "$cpf_field $sign " . $group_rule['autogroups_' . $end . '_value'];
				}
			}
			$sql_where[] = '(' . implode(' AND ', $where) . ')';
			$group_ids[] = $group_rule['autogroups_group_id'];
		}

		return '(' . ((sizeof($sql_where)) ? implode(' OR ', $sql_where) . ' OR ' : '') . $this->db->sql_in_set('ug.group_id', $group_ids, false, true) . ')';
	}
}
