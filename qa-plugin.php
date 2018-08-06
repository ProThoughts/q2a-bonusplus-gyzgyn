<?php
/*
	Plugin Name: Bonus Plus 
	Plugin Description: Customizes Bonus Point system. 
	Plugin URI: https://github.com/ihlassovbetov/q2a-bonusplus
	Plugin Version: 1.0
	Plugin Date: 02/08/2018
	Plugin Author: Yhlas Sovbetov
	Plugin Author URI: https://github.com/ihlassovbetov
	Plugin License: GPLv3
	Plugin Tested on: Q2A 1.7.5

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.gnu.org/licenses/gpl.html
	
*/

if(!defined('QA_VERSION'))
{
	header('Location: ../../');
	exit;
}

// language file
qa_register_plugin_phrases('q2a-bonusplus-lang.php', 'q2a_bonusplus_lang');

// page
qa_register_plugin_module('page', 'q2a-bonusplus-page.php', 'q2a_bonusplus_page', 'q2a bonus plus Page');

// layer 
qa_register_plugin_layer('q2a-bonusplus-layer.php', 'q2a bonus plus layer');

// admin
qa_register_plugin_module('module', 'q2a-bonusplus-admin.php', 'q2a_bonusplus_admin', 'q2a bonus plus Admin');



	function q2a_save_bonusreasons($bonuserid, $receiverid, $amount, $reasonid, $bonusnote, $bonustime)
	{
		$bonusnote = strip_tags($bonusnote);
		
		qa_db_query_sub('
			INSERT INTO `^bonusplus` (`bonuserid`, `receiverid`, `amount`, `reasonid`, `bonusnote`, `bonustime` ) 
			VALUES (#, #, #, $, $, $)
			ON DUPLICATE KEY
			UPDATE `bonuserid`=#, `receiverid`=#, `amount`=#, `reasonid`=#, `bonusnote`=$, `bonustime`=$
		', 
		$bonuserid, $receiverid, $amount, $reasonid, $bonusnote, $bonustime, 
		$bonuserid, $receiverid, $amount, $reasonid, $bonusnote, $bonustime
		);
	}
	
	function qa_set_bonusplus_to_userpoints($receiverid)
	{
		$sumbonus = qa_db_read_one_value(qa_db_query_sub(
			"SELECT SUM(amount) FROM ^bonusplus WHERE receiverid = #",
			$receiverid
		), true);
		
		if ($sumbonus) {
			qa_db_query_sub(
			"INSERT INTO ^userpoints (userid, bonus) VALUES ($, #) ON DUPLICATE KEY UPDATE bonus=#",
			$receiverid, $sumbonus, $sumbonus
			);
		}

	}
	
	function q2a_bonusplus_reasonname($reasonid)
	{
		return qa_lang('q2a_bonusplus_lang/reason_'.$reasonid);
	}
	
	function q2a_bonusplus_user_sum($userid)
	{
		$sumbonus = qa_db_read_one_value(qa_db_query_sub(
			"SELECT SUM(amount) FROM ^bonusplus WHERE receiverid = #",
			$userid
		), true);
		return $sumbonus;
	}
	
	function q2a_bonusplus_receiver_history($userid)
	{
		$records = qa_db_query_sub(
			"SELECT bonuserid, receiverid, amount, reasonid, bonusnote, bonustime
			FROM ^bonusplus
			WHERE receiverid = #",
			$userid
		);
		return $records;
	}
	
	function q2a_bonusplus_bonuser_history($userid)
	{
		$records = qa_db_query_sub(
			"SELECT bonuserid, receiverid, amount, reasonid, bonusnote, bonustime
			FROM ^bonusplus
			WHERE bonuserid = #",
			$userid
		);
		return $records;
	}
