function PopulateMenus(state,ratecenter,tier,lata){
	$('.Didlist').html('<blink>Please wait</blink>');
	var state = $('.stateDropdown').val();
	var ratecenter = $('.ratecenterDropdown').val();
	var tier = $('.tierDropdown').val();
	var lata = $('.lataDropdown').val();
	$.ajax({
			type: "POST",
			url: 'ratecenters/' + state + '/'
								+ ratecenter + '/'
								+ tier + '/'
								+ lata, 
			success: function(msg){
				$('.ratecenterDropdown').html(msg);
			},
			statusCode: {
				404: function() {
					$('.Didlist').html("page not found");
				}
			}
		});
	$.ajax({
			type: "POST",
			url: 'tiers/' + state + '/'
								+ ratecenter + '/'
								+ tier + '/'
								+ lata,  
			success: function(msg){
				$('.tierDropdown').html(msg);
			},
			statusCode: {
				404: function() {
					$('.Didlist').html("page not found");
				}
			}
		});
	$.ajax({
			type: "POST",
			url: 'latas/' + state + '/'
								+ ratecenter + '/'
								+ tier + '/'
								+ lata, 
			success: function(msg){
				$('.lataDropdown').html(msg);
			},
			statusCode: {
				404: function() {
					$('.Didlist').html("page not found");
				}
			}
		});	
	$.ajax({
			type: "POST",
			url: 'countvoipdids/' + state + '/'
								+ ratecenter + '/'
								+ tier + '/'
								+ lata, 
			success: function(msg){
				$('.Didlist').html(msg);
			},
			statusCode: {
				404: function() {
					$('.Didlist').html("page not found");
				}
			}
		});
}
function ResetMenus(){
	$('.ratecenterDropdown').html('<option value=""> All </option>').show();
	$('.tierDropdown').html('<option value=""> All </option>').show();
	$('.lataDropdown').html('<option value=""> All </option>').show();
	$('.Didlist').html('');
}
$(document).ready(function() {
	var state = $('.stateDropdown').val();
	var ratecenter = $('.ratecenterDropdown').val();
	var tier = $('.tierDropdown').val();
	var lata = $('.lataDropdown').val();
	$('.stateDropdown').change( function(){
		ResetMenus();
		PopulateMenus($('.stateDropdown').val(),
			$('.ratecenterDropdown').val(),
			$('.tierDropdown').val(),
			$('.lataDropdown').val());
	});
	
	$('.ratecenterDropdown').change( function(){
		PopulateMenus($('.stateDropdown').val(),
			$('.ratecenterDropdown').val(),
			$('.tierDropdown').val(),
			$('.lataDropdown').val());
	});
	
	$('.tierDropdown').change( function(){
		PopulateMenus($('.stateDropdown').val(),
			$('.ratecenterDropdown').val(),
			$('.tierDropdown').val(),
			$('.lataDropdown').val());
	});
	
	$('.lataDropdown').change( function(){
		PopulateMenus($('.stateDropdown').val(),
			$('.ratecenterDropdown').val(),
			$('.tierDropdown').val(),
			$('.lataDropdown').val());
	});
    $('.viewdidbutton').click( function () {
		$('.Didlist').html('<blink>Please wait</blink>');
		var state = $('.stateDropdown').val();

        $.ajax({
			type: "POST",
			url: 'dids/' + $('.stateDropdown').val() + '/'
								+ $('.ratecenterDropdown').val() + '/'
								+ $('.tierDropdown').val() + '/'
								+ $('.lataDropdown').val(), 
			success: function(msg){
				$('.Didlist').html(msg);
			},
			statusCode: {
				404: function() {
					$('.Didlist').html("page not found");
				}
			}
		});
 	});
});