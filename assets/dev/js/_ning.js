/*window.console = window.console || {log:function(){}};
if (typeof console === "undefined"){
	console={};
	console.log = function(){};
}*/

/**
 *  _NING GLOBAL FUNCTIONS
 */
var _ning_global = {
    
    /* Adblocker detection */
	// _ning_global.checkAdStatus()
	checkAdStatus: function() {
		var adsActive = true;
		console.log( window.adning_no_adblock );
		
		if (window.adning_no_adblock !== true) {
			adsActive = false;
		}
		
		return adsActive;
	},
};





(function($, win) {

	/* ----------------------------------------------------------------
	 * AD BLOCKER Detection
	 * ---------------------------------------------------------------- */
	setTimeout(function() {
		if( !_ning_global.checkAdStatus() ){
			console.log('You are using AD Blocker!');
			
			$.ajax({
			   type: "POST",
			   url: _adn_.ajaxurl,
			   data: "action=adblocker_detected"
			}).done(function( obj ) {
			   
				// nothing gets returned.
				if( obj !== ''){
					msg = JSON.parse( obj );
				
					if( msg.alert ){ alert(msg.alert); }
				}
			});
		}
    }, 500);
    
}(jQuery, window));