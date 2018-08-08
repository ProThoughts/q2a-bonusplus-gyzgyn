<?php

/*
	Plugin Name: q2a Bonus Plus
*/

class qa_html_theme_layer extends qa_html_theme_base
{
	
	function head_script()
	{
		qa_html_theme_base::head_script();
		
		if(qa_is_logged_in() && $this->template=='user' && qa_get_logged_in_level() >= qa_opt('bonusplus_bonuser_level'))
		{
			$this->output('
				<script>
					var bonusAjaxURL = "'.qa_path('ajaxbonus').'";
					var bonuserid = '.qa_get_logged_in_userid().';
					var receiverid = '.qa_handle_to_userid(qa_request_part(1)).';
				</script>
			');  
			
			
			$this->output('
				<script defer type="text/javascript" src="'.QA_HTML_THEME_LAYER_URLTOROOT.'script.js"></script>
			');
		}
		
	} // end head_script
	
	function head_custom()
	{
		parent::head_custom();
		$hidecss = qa_opt('bonusplus_exclude_css') === '1';
		if ( !$hidecss && $this->template=="user" && qa_get_logged_in_level() >= qa_opt('bonuser_level'))
		{
			$bonuspluss_css = '
				<style>
				input#hide_button {
					display: none;
				}

				#bonusplus-popup {
					background: #000;
    				background: rgba(0,0,0,.75);
    				height: 100%;
    				width: 100%;
    				position: fixed;
    				top: 0;
    				left: 0;
    				display: none;
    				z-index: 5119;
				}

				#bonusplus-center {
					margin: 6% auto;
					width: auto;
					text-align: center;
				}

				.qa-bonusplus-wrap {
    				display: inline-block;
    				min-width: 250px;
    				position: relative;
    				background: #fff;
    				border: 1px solid #f00;
    				padding: 15px;
    				text-align: left;
    				z-index: 3335;
				}

				.qa-bonusplus-wrap div {
    				margin: 10px;
				}

				.qa-bonus-reason-text {
    				height: auto;
    				width: 100%;
				}

				.qa-bonusplus-wrap .close-btn {
    				position: absolute;
    				top: 5px;
    				right: 7px;
    				font-size: 20px;
    				color: #333;
    				cursor: pointer;
    				background: #eaeaea;
    				border-radius: 3px;
    				width: 20px;
    				height: 20px;
    				line-height: 20px;
    				text-align: center;
				}

				.cep {
    				float: left;
				}

				.sag {
    				float: right;
				}

				input[name="qa-bonusplus-radio"] {
    				margin: 0 2px 0 0;
				}
				</style>';
			$this->output_raw( $bonuspluss_css );
		}
	}
	
	public function form_button_data($button, $key, $style) {
		
		if (isset($button['tags']) && $button['tags'] == 'name="dosetbonus"') {
			$button['tags']='name="sendbonus" id="hide_button"';
		}
		
		qa_html_theme_base::form_button_data($button, $key, $style);		
	}
	
	public function form_number($field, $style)
	{
		if (isset($field['tags']) && $field['tags'] == 'name="bonus"') {
			$field['tags']='name="bonus" disabled';
		}
		qa_html_theme_base::form_number($field, $style);
	}
	
	public function form_note($field, $style, $columns)
	{
		if (isset($field['id']) && $field['id'] == 'bonus') {
			$field['note'] ='<button name="givebonusplus" value="givebonus" title="'.qa_lang('q2a_bonusplus_lang/give_bonus').'" type="submit" class="qa-form-wide-button qa-form-wide-button-givebonusplus" data-bonuserid="'.qa_get_logged_in_userid().'" data-receiverid="'.qa_handle_to_userid(qa_request_part(1)).'"><span>'.qa_lang('q2a_bonusplus_lang/give_bonus').'</span></button>';
		}
		qa_html_theme_base::form_note($field, $style, $columns);
	}

	
	public function body_hidden()
	{
		if(qa_is_logged_in() && $this->template=="user" && qa_get_logged_in_level() >= qa_opt('bonuser_level'))
		{
			$this->output('
			<div id="bonusplus-popup">
				<div id="bonusplus-center">
					<div class="qa-bonusplus-wrap">
						<h4>
							'.qa_lang('q2a_bonusplus_lang/reason').'
						</h4>
						<div class="bonus_amount">
							<span>'.qa_lang('q2a_bonusplus_lang/bonus_amount').'</span><br/>
							<input type="number" name="amount" min="'.qa_opt('q2a_min_bonus_amount').'" max="'.qa_opt('q2a_max_bonus_amount').'">
						</div>
						<div class="cep">
							<input type="radio" name="qa-bonus-reason" value="1" checked>
							<span>'.qa_lang('q2a_bonusplus_lang/reason_1').'</span><br/>
							<input type="radio" name="qa-bonus-reason" value="2">
							<span>'.qa_lang('q2a_bonusplus_lang/reason_2').'</span><br/>
							<input type="radio" name="qa-bonus-reason" value="3">
							<span>'.qa_lang('q2a_bonusplus_lang/reason_3').'</span><br/>
							<input type="radio" name="qa-bonus-reason" value="8">
							<span>'.qa_lang('q2a_bonusplus_lang/reason_8').'</span><br/>
						</div>
						<div class="sag">
							<input type="radio" name="qa-bonus-reason" value="4">
							<span>'.qa_lang('q2a_bonusplus_lang/reason_4').'</span><br/>
							<input type="radio" name="qa-bonus-reason" value="5">
							<span>'.qa_lang('q2a_bonusplus_lang/reason_5').'</span><br/>
							<input type="radio" name="qa-bonus-reason" value="6">
							<span>'.qa_lang('q2a_bonusplus_lang/reason_6').'</span><br/>
							<input type="radio" name="qa-bonus-reason" value="7">
							<span>'.qa_lang('q2a_bonusplus_lang/reason_7').'</span><br/>
						</div>
						
						<div class="qa-bonus-reason-text-wrap">
							<span>'.qa_lang('q2a_bonusplus_lang/reason_text').'</span><br/>
							<textarea name="qa-bonus-reason-text" class="qa-bonus-reason-text" rows="3" maxlength="100" placeholder="Write details"></textarea><br/>
							<input type="button" class="qa-sendbonus-button" value="'.qa_lang('q2a_bonusplus_lang/send').'">
						</div>
						<div class="close-btn">×</div>
					</div>
				</div>
			</div>
			');
		}
		// default method call outputs the form buttons
		qa_html_theme_base::body_hidden();
		
	} // END function body_hidden()
	
} // end qa_html_theme_layer

