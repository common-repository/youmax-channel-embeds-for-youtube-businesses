//Youmax Call
(function($){	

	var youmaxOptions = {};
	$('div[id^=youmax_]').each(function(){
		youmaxOptions = {};
		youmaxOptions = $(this).data("youmax-options");
		$(this).youmax(youmaxOptions);
	});
	
}(jQuery));
