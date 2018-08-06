<?php
/*
	Plugin Name: q2a Bonus Plus
*/
class q2a_bonusplus_page
{
	
	var $directory;
	var $urltoroot;
	
	function load_module($directory, $urltoroot)
	{
		$this->directory = $directory;
		$this->urltoroot = $urltoroot;
	}
	
	// for display in admin interface under admin/pages
	function suggest_requests() 
	{	
		return array(
			array(
				'title' => 'Ajax Bonus', // title of page
				'request' => 'ajaxbonus', // request name
				'nav' => 'M', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
			),
		);
	}
	
	// for url query
	function match_request($request)
	{
		if ($request=='ajaxbonus') 
		{
			return true;
		}
		return false;
	}
	function process_request($request)
	{	
		// only logged in users
		if(!qa_is_logged_in())
		{
			exit();
		}
		
		
		// receving bonus data by AJAX post
		$postdata = qa_post_text('ajaxdata');
		
		if(!empty($postdata)) 
		{
			$bonusdata = json_decode($postdata, true);
			$bonusdata = str_replace('&quot;', '"', $bonusdata);
			$bonuserid = (int)$bonusdata['bonuserid'];
			$receiverid = (int)$bonusdata['receiverid'];
			$amount = $bonusdata['amount'];
			$reasonid = (int)$bonusdata['reasonid'];
			$bonusnote = empty($bonusdata['bonusnote']) ? null : trim($bonusdata['bonusnote']);
			
			$bonustime = gmdate( 'Y-m-d H:i:s', time() );
			
			$ajaxreturn = '';
			
			$error = '';
			
			if(empty($bonuserid) || empty($receiverid) || empty($amount) || empty($reasonid))
			{
				$reply = array( 'error' => "missing" );
				echo json_encode( $reply );
				return;
			}		
			
			/*require_once QA_INCLUDE_DIR . 'app/votes.php';*/
			/*require_once QA_INCLUDE_DIR . 'pages/user-profile.php';*/
			
			q2a_save_bonusreasons($bonuserid, $receiverid, $amount, $reasonid, $bonusnote, $bonustime);
			
			require_once QA_INCLUDE_DIR . 'db/points.php';
			
			qa_set_bonusplus_to_userpoints($receiverid);
			qa_db_points_update_ifuser($receiverid, null);
			
			if($error)
			{
				$reply = array(
					'error' => $error,
				);
				echo json_encode( $reply );
				return;
			}
			
			$reply = array('success' => '1');
			echo json_encode( $reply );
			return;
			
		} // END AJAX RETURN
		else 
		{
			echo 'Unexpected error. No data transferred.';
			exit();
		}
		
		return;
	} // end process_request
	
}; // END q2a_bonusplus_page