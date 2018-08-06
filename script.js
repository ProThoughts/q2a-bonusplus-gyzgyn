$(document).ready(function(){
	// prevent submit
	$(".qa-form-wide-button-givebonusplus").attr("type", "button");

	$(".qa-form-wide-button-givebonusplus").click( function(){
		var bonuserid = $(this).data("bonuserid");
		var receiverid = $(this).data("receiverid");
		
		$("#bonusplus-popup").show();
		
		$(".qa-bonusplus-wrap .close-btn").click( function(){
			$("#bonusplus-popup").hide();
		});
		
		// focus on first element, then Enter and Escape key work
		$('.qa-bonusplus-wrap input').first().focus();
		
		$(".qa-sendbonus-button").click( function(){
			var reasonid = $("input[name=qa-bonus-reason]:checked").val();
			var bonusnote = $(".qa-bonus-reason-text").val();
			var amount = $("input[name=amount]").val();
			
			var dataArray = {
				bonuserid: bonuserid,
				receiverid: receiverid,
				amount: amount,
				reasonid: reasonid,
				bonusnote: bonusnote,
			};
			
			var senddata = JSON.stringify(dataArray);
			console.log("sending: "+senddata);
			
			// send ajax
			$.ajax({
				 type: "POST",
				 url: bonusAjaxURL,
				 data: { ajaxdata: senddata },
				 dataType:"json",
				 cache: false,
				 success: function(data){
					console.log("got server data:");
					console.log(data);
					
					if(typeof data.error !== "undefined"){
						alert(data.error);
					}
					else{
						location.reload();
					}
				 },
				 error: function(data){
					console.log("Ajax error:");
					console.log(data);
				 }
			});
		});
		
	}); // END click	
});