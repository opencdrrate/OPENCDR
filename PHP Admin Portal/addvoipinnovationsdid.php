<?php
	include 'lib/Page.php';
	$scripts = '';
	
	$scripts = <<< HEREDOC
	<script type="text/javascript" src="lib/jquery-1.7.2.js"></script>
HEREDOC;
?>

<?php echo GetPageHead('Add VOIP Innovations DID','listdids.php',$scripts);?>
 
<div id="body">
<table>
<tr>
	<td style="width:150px">State</td>
	<td>
	<select id="state">
	<option value=""> - Choose a state - </option>
	<option value="AL">AL</option>
	<option value="AK">AK</option>
	<option value="AZ">AZ</option>
	<option value="AR">AR</option>
	<option value="CA">CA</option>
	<option value="CO">CO</option>
	<option value="CT">CT</option>
	<option value="DE">DE</option>
	<option value="DC">DC</option>
	<option value="FL">FL</option>
	<option value="GA">GA</option>
	<option value="HI">HI</option>
	<option value="ID">ID</option>
	<option value="IL">IL</option>
	<option value="IN">IN</option>
	<option value="IA">IA</option>
	<option value="KS">KS</option>
	<option value="KY">KY</option>
	<option value="LA">LA</option>
	<option value="ME">ME</option>
	<option value="MD">MD</option>
	<option value="MA">MA</option>
	<option value="MI">MI</option>
	<option value="MN">MN</option>
	<option value="MS">MS</option>
	<option value="MO">MO</option>
	<option value="MT">MT</option>
	<option value="NE">NE</option>
	<option value="NV">NV</option>
	<option value="NH">NH</option>
	<option value="NJ">NJ</option>
	<option value="NM">NM</option>
	<option value="NY">NY</option>
	<option value="NC">NC</option>
	<option value="ND">ND</option>
	<option value="OH">OH</option>
	<option value="OK">OK</option>
	<option value="PA">PA</option>
	<option value="RI">RI</option>
	<option value="SC">SC</option>
	<option value="SD">SD</option>
	<option value="TN">TN</option>
	<option value="TX">TX</option>
	<option value="UT">UT</option>
	<option value="VT">VT</option>
	<option value="VA">VA</option>
	<option value="WA">WA</option>
	<option value="WV">WV</option>
	<option value="WI">WI</option>
	<option value="WY">WY</option>
	<option value="AS">AS</option>
	<option value="GU">GU</option>
	<option value="MP">MP</option>
	<option value="PR">PR</option>
	<option value="VI">VI</option>
	</select>
	</td>
</tr>
<tr>
	<td style="width:150px">Rate Center</td>
	<td>
	<select id="ratecenter">
		<option value=""> All </option>
	</select>
	</td>
</tr>
<tr>
	<td style="width:150px">Tier</td>
	<td>
	<select id="tier">
		<option value=""> All </option>
	</select>
	</td>
</tr>
<tr>
	<td style="width:150px">LATA ID</td>
	<td>
	<select id="lata">
		<option value=""> All </option>
	</select>
	</td>
</tr>
</table>
<p id="result">Please select a state.</p>

<p id="showresults"><button>Show Results</button></p>

<script>

function confirmAddDid(){
	var agree=confirm("Are you sure you want to purchase these DIDs?");
	if (agree){
		$("#assignDID").submit();
		return true;
	}
	else{
		return false;
	}
}
function ResetMenus(){
	$("#ratecenter").html('<option value=""> All </option>').show();
	$("#tier").html('<option value=""> All </option>').show();
	$("#lata").html('<option value=""> All </option>').show();
	$("#showresults").hide();
}
function PopulateMenus(state,ratecenter,tier,lata){
	$("#showresults").hide();
	if(state == ""){
		$("#result").html('<em>No results</em>');
		ResetMenus();
	}
	else{
		$("#result").html('<blink>Retrieving data...</blink>');
		
		$.ajax({
			type: "GET",
			url: "lib/ajax_getvoipdid.php?state=" + state
						+ "&ratecenter="+ratecenter
						+ "&tier="+tier
						+ "&lata="+lata
						+ "&function=count",
			success: function(msg){
				if (msg.indexOf("Error") != -1){
					$("#result").html('<font color="red">'+msg+'</font>');
				}
				else if (msg != ""){
					$("#result").html("Found " + msg + " DIDs");
					$("#showresults").show('<button>Show Results</button>');
				}
				else{
					$("#result").html('<em>No item result</em>');
					return;
				}
			}
		});
		//Populate Rate Center menu
		$.ajax({
			type: "GET",
			url: "lib/ajax_getvoipdid.php?state=" + state
						+ "&ratecenter="+ratecenter
						+ "&tier="+tier
						+ "&lata="+lata
						+ "&function=ratecenter",
			success: function(msg){
				if (msg != ""){
					$("#ratecenter").html(msg).show();
				}
			}
		});
		//Populate tier menu
		$.ajax({
			type: "GET",
			url: "lib/ajax_getvoipdid.php?state=" + state
						+ "&ratecenter="+ratecenter
						+ "&tier="+tier
						+ "&lata="+lata
						+ "&function=tier",
			success: function(msg){
				if (msg != ""){
					$("#tier").html(msg).show();
				}
			}
		});
		//Populate LATA menu
		$.ajax({
			type: "GET",
			url: "lib/ajax_getvoipdid.php?state=" + state
						+ "&ratecenter="+ratecenter
						+ "&tier="+tier
						+ "&lata="+lata
						+ "&function=lata",
			success: function(msg){
				if (msg != ""){
					$("#lata").html(msg).show();
				}
			}
		});
	}
}
function DisplayResults(state,ratecenter,tier,lata){
	$("#showresults").hide();
	$("#result").html('<blink>Retrieving data...</blink>');
	
	$.ajax({
			type: "GET",
			url: "lib/ajax_getvoipdid.php?state=" + state
						+ "&ratecenter="+ratecenter
						+ "&tier="+tier
						+ "&lata="+lata
						+ "&function=showall",
			success: function(msg){
				if (msg != ""){
					$("#result").html( msg ).show();
				}
				else{
					$("#result").html('<em>No item result</em>');
					return;
				}
			}
		});
}

$(document).ready(function(){
	$("#showresults").hide();
	$("#state").change( function(){
		ResetMenus();
		PopulateMenus($("#state").val(),
			$("#ratecenter").val(),
			$("#tier").val(),
			$("#lata").val());
	});
	$("#ratecenter").change( function(){
		PopulateMenus($("#state").val(),
			$("#ratecenter").val(),
			$("#tier").val(),
			$("#lata").val());
	});
	$("#tier").change( function(){
		PopulateMenus($("#state").val(),
			$("#ratecenter").val(),
			$("#tier").val(),
			$("#lata").val());
	});
	$("#lata").change( function(){
		PopulateMenus($("#state").val(),
			$("#ratecenter").val(),
			$("#tier").val(),
			$("#lata").val());
	});
	
	$("#showresults").click( function(){
		DisplayResults($("#state").val(),
			$("#ratecenter").val(),
			$("#tier").val(),
			$("#lata").val());
			
	});
	
	$("#assignDIDLink").hide();
});

</script>
</div>
<?php echo GetPageFoot();?>