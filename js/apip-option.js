(
	
	function( $ ) {	
		$(document).ready(function() {
			if (enablelazyload) {
				$("img").unveil(200);
			}
		});
		
});