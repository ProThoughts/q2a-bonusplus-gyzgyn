<?php
/*
	Plugin Name: q2a Bonus Plus
*/

class q2a_bonusplus_admin
{
	// initialize db-table 'eventlog' if it does not exist yet
	public function init_queries($tableslc) 
	{
		$tablename = qa_db_add_table_prefix('bonusplus');
		
		if(!in_array($tablename, $tableslc)) 
		{
			require_once QA_INCLUDE_DIR.'qa-app-users.php';
			
			return '
				CREATE TABLE `^bonusplus` (
				  `bonusid` int(10) NOT NULL AUTO_INCREMENT,
				  `bonuserid` int(10) UNSIGNED NOT NULL,
				  `receiverid` int(10) UNSIGNED NOT NULL,
				  `amount` int(10) NOT NULL,
				  `reasonid` int(10) UNSIGNED NOT NULL,
				  `bonusnote` varchar(255) NULL,
				  `bonustime` datetime NULL,
				  PRIMARY KEY (bonusid, bonuserid, receiverid, amount)
				) 
				ENGINE=MyISAM DEFAULT CHARSET=utf8;
			';
		}
		return null;
	} 

	// option's value is requested
	public function option_default($option) 
	{
		switch($option) 
		{
			case 'bonusplus_enabled':
			 	return 1;
			case 'bonusplus_exclude_css':
			 	return 0;
			case 'bonusplus_min_amount':
			 	return -100;
			case 'bonusplus_max_amount':
			 	return 100;
			case 'bonusplus_bonuser_level':
			 	return QA_USER_LEVEL_ADMIN;
			default:
				return null;
		}
	}
	
	public function allow_template($template)
	{
		return ($template!='admin');
	}       
	
	private $userlevels = [
		'expert' => QA_USER_LEVEL_EXPERT,
		'editor' => QA_USER_LEVEL_EDITOR,
		'mod'    => QA_USER_LEVEL_MODERATOR,
		'admin'  => QA_USER_LEVEL_ADMIN,
	];

	private $userlevels_text = [
		'expert' => 'Expert',
		'editor' => 'Editor',
		'mod'    => 'Moderator',
		'admin'  => 'Administrator',
	];
		
	public function admin_form(&$qa_content)
	{
		// process the admin form when admin hits save button
		$saved = qa_clicked('bonusplus_save');

		if ($saved) {
			qa_opt('bonusplus_enabled', (bool)qa_post_text('bonusplus_enabled_field')); // empty or 1
			qa_opt('bonusplus_exclude_css', (bool)qa_post_text('bonusplus_exclude_css_field')); // empty or 1
			
			$bonuslevel = qa_post_text('bonusplus_bonuser_level_field');
			if (!in_array($bonuslevel, array_keys($this->userlevels))) {
				$bonuslevel = 'admin';
			}
			qa_opt('bonusplus_bonuser_level', $this->userlevels[$bonuslevel]);
			
			qa_opt('bonusplus_min_amount', qa_post_text('bonusplus_min_amount_field'));
			qa_opt('bonusplus_max_amount', qa_post_text('bonusplus_max_amount_field'));
			
		}
		
		$bl_id = qa_opt('bonusplus_bonuser_level');
		$bl_alias = array_search($bl_id, $this->userlevels);
		$bl_value = $this->userlevels_text[$bl_alias];
		
		// form fields to display frontend for admin
		$fields = array();
		
		$fields[] = array(
			'type' => 'checkbox',
			'tags' => 'name="bonusplus_enabled_field" id="bonusplus_enabled_field"',
			'label' => qa_lang('q2a_bonusplus_lang/enable_plugin'),
			'value' => qa_opt('bonusplus_enabled'),
		);
		
		$fields[] = array(
			'type' => 'checkbox',
			'tags' => 'name="bonusplus_exclude_css_field" id="bonusplus_exclude_css_field"',
			'label' => qa_lang('q2a_bonusplus_lang/bonusplus_exclude_css'),
			'value' => qa_opt('bonusplus_exclude_css'),
			'note' => qa_lang('q2a_bonusplus_lang/bonusplus_exclude_css_note'),
		);
		
		
		$fields[] = array(
			'type' => 'select',
			'label' => qa_lang('q2a_bonusplus_lang/bonuser_level'),
			'tags' => 'name="bonusplus_bonuser_level_field" id="bonusplus_bonuser_level_field"',
			'options' => $this->userlevels_text,
			'value' => $bl_value,
			'note' => 'Which user level is able to give bonus points.',
		);
		
		$fields[] = array(
			'type' => 'number',
			'label' => qa_lang('q2a_bonusplus_lang/min_bonus_amount_label'),
			'tags' => 'name="bonusplus_min_amount_field"',
			'value' => qa_opt('bonusplus_min_amount'),
			'suffix' => 'points',
			'note' => qa_lang('q2a_bonusplus_lang/min_bonus_amount_note'),
		);
		
		$fields[] = array(
			'type' => 'number',
			'label' => qa_lang('q2a_bonusplus_lang/max_bonus_amount_label'),
			'tags' => 'name="bonusplus_max_amount_field"',
			'value' => qa_opt('bonusplus_max_amount'),
			'suffix' => 'points',
			'note' => qa_lang('q2a_bonusplus_lang/max_bonus_amount_note'),
		);
		
		return array(
			'ok' => $saved ? 'Settings saved' : null,
			
			'fields' => $fields,
			
			'buttons' => array(
				array(
					'label' => qa_lang_html('main/save_button'),
					'tags' => 'name="bonusplus_save"',
				),
			),
		);
	}
	
} // END q2a_bonusplus_admin

