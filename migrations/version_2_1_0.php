<?php
/**
*
* @package CPFs for Autogroups Extension
* @copyright (c) 2019 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\cpfautogroup\migrations;

use phpbb\db\migration\migration;

class version_2_1_0 extends migration
{
	public function update_data()
	{
		return array(
			// Add the ACP module
			array('module.add', array(
				'acp', 'ACP_GROUPS', array(
					'module_basename'	=> '\david63\cpfautogroup\acp\cpfautogroup_module',
					'modes'				=> array('manage'),
				),
			)),
		);
	}

	/**
	 * Add the CPF auto groups table schema to the database:
	 *
	 * @return array Array of table schema
	 * @access public
	 */
	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'autogroups_cpf_data'	=> array(
					'COLUMNS' => array(
						'cpf_field_type'	=> array('VCHAR:100', ''),
						'cpf_field_name'	=> array('VCHAR:255', ''),
						'cpf_event_trigger'	=> array('TINT:2', 1),
						'cpf_users'			=> array('BOOL', 0),
						'cpf_cron'			=> array('BOOL', 0),
						'cpf_cron_time'		=> array('UINT', 0),
						'cpf_cron_last_run'	=> array('INT:12', 0),
						'cpf_options'		=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY' => 'cpf_field_type',
				),
			),
		);
	}

	/**
	 * Drop the CPF auto groups table schema from the database
	 *
	 * @return array Array of table schema
	 * @access public
	 */
	public function revert_schema()
	{
		return array(
			'drop_tables'	=> array(
				$this->table_prefix . 'autogroups_cpf_data',
			),
		);
	}
}
