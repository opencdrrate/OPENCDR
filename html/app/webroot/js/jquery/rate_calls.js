function rate_calls(){
	var storedProcs=new Array(
					"fnCategorizeCDR",
					"fnRateIndeterminateJurisdictionCDR",
					"fnRateInternationalCDR",
					"fnRateInterstateCDR",
					"fnRateIntrastateCDR",
					"fnRateSimpleTerminationCDR",
					"fnRateTieredOriginationCDR",
					"fnRateTollFreeTerminationCDR",
					"fnRateTollFreeOriginationCDR",
					"fnRateRetailTerminationCDR",
					"fnRateRetailOriginationCDR",
					"fnRateRetailOriginationTollFreeCDR"
				);
	for(i = 0; i < storedProcs.length; i++){
		var progress = i + 1;
		var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function(e) {
				if (xhr.readyState == 4) {
					if(xhr.status == 200){
						$('.progress').html(xhr.responseText);
					}
				}
			};

			// start upload
		var url = 'RatingQueue/ratecalls/' + storedProcs[i] + '/'
								+ progress + '/'
								+ storedProcs.length;
			if(i + 1 < storedProcs.length){
				url += '/' + storedProcs[i+1];
			}
			xhr.open("POST", url, false);
			xhr.send();
	}
}