<?php
/**
*
* @package CPFs for Autogroups Extension
* @copyright (c) 2019 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\cpfautogroup\core;

use phpbb\db\driver\driver_interface;

/**
* CPF auto groups functions
*/
class functions
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string The database table for CPF auto group data */
	protected $autogroups_cpf_data_table;

	/**
	* Constructor for CPF functions
	*
	* @param \phpbb_db_driver	$db							The db connection
	* @param string				$autogroups_cpf_data_table	Name of the table used to store CPF auto group data
	*
	* @access public
	*/
	public function __construct(driver_interface $db, $autogroups_cpf_data_table)
	{
		$this->db 							= $db;
		$this->autogroups_cpf_data_table 	= $autogroups_cpf_data_table;
	}

	/**
	* Get CPF data
	*
	* @return string field data
	* @access public
	*/
	public function cpf_data_get($get_field, $field_type, $options = false)
	{
		$field_use = ($options) ? 'cpf_options' : $get_field;

		$sql = 'SELECT *
			FROM ' . $this->autogroups_cpf_data_table . '
			WHERE cpf_field_type = "' . $field_type . '"';

		$result = $this->db->sql_query($sql);
		$field	= $this->db->sql_fetchfield($field_use);

		$this->db->sql_freeresult($result);

		if ($get_field == 'cpf_field_name' && !$field)
		{
			trigger_error($this->language->lang('CPF_NOT_CONFIGURED') . adm_back_link($this->u_action), E_USER_WARNING);
		}
		else if ($field_use == 'cpf_options')
		{
			$options	= json_decode($field, true);
			$field 		= $options[$get_field];
		}

		return $field;
	}

	/**
	* Get CPF data for CPF autogroups
	*
	* @return array cron data
	* @access public
	*/
	public function get_cpf_event_data($trigger)
	{
		$sql = 'SELECT *
			FROM ' . $this->autogroups_cpf_data_table . "
			WHERE cpf_event_trigger = $trigger";

		$result = $this->db->sql_query($sql);

		$event_data = array();
		$row = $this->db->sql_fetchrow($result);
		if ($row)
		{
			$event_data = array(
				'cpf_cron' 			=> $row['cpf_cron'],
				'cpf_cron_time' 	=> $row['cpf_cron_time'],
				'cpf_event_trigger'	=> $row['cpf_event_trigger'],
				'cpf_field_type'	=> $row['cpf_field_type'],
				'cpf_users'			=> $row['cpf_users'],
			);
		}

		$this->db->sql_freeresult($result);

		return $event_data;
	}

	/**
	* Get cron status for CPF autogroups
	*
	* @return array cron data
	* @access public
	*/
	public function get_cron_status()
	{
		$sql = 'SELECT *
			FROM ' . $this->autogroups_cpf_data_table;

		$result = $this->db->sql_query($sql);

		$cron_status = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$cron_status[] = array(
				'cpf_cron' 			=> $row['cpf_cron'],
				'cpf_cron_time' 	=> $row['cpf_cron_time'],
				'cpf_event_trigger'	=> $row['cpf_event_trigger'],
				'cpf_field_type'	=> $row['cpf_field_type'],
				'cpf_users'			=> $row['cpf_users'],
			);
		}

		$this->db->sql_freeresult($result);

		return $cron_status;
	}

	/**
	* Get the CPF conditions
	*
	* @return array conditions
	* @access public
	*/
	public function get_cpf_conditions($type)
	{
		$sql_array  = '';
		// Is there a conditional group?
		if ($this->cpf_data_get('cpf_group_use', $type, true))
		{
			$sql_array .= ' AND ' . $this->db->sql_in_set('ug.group_id', $this->cpf_data_get('cpf_group', $type, true));
		}

		return $sql_array;
	}

	/**
	* Update the Cron run time
	*
	* @return array conditions
	* @access public
	*/
	public function cpf_cron_update($type)
	{
		$sql = 'UPDATE ' . $this->autogroups_cpf_data_table .'
			SET cpf_cron_time = ' . time() . '
			WHERE cpf_field_type = ' . $type;

		$this->db->sql_query($sql);
	}
}
