$( document ).ready(function() {
							 
   // Auf Klick checken
   $('#be-branding-showborder').click(function() {
		$("#show-border-area").toggle(this.checked);
	});
   
   //Immer checken
   if ($("#be-branding-showborder").is(':checked')){
		$("#show-border-area").show();
	}
	
});