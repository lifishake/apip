/*global jQuery:false */
"use strict";

function jqueryArchiveListAnimate(clickedObj, listElements) {
  var symbol = '';
  if ( listElements.hasClass("apip-no-disp") ) {
    symbol = '[-]';
    listElements.fadeToggle(500);
  }
  else {
    symbol = '[+]';
    listElements.fadeToggle(500);
  }
  listElements.toggleClass("apip-no-disp");
  jQuery(clickedObj).children(".achp-symbol").html(symbol);
  jQuery(clickedObj).parent().toggleClass("achp-expanded");
}

jQuery(function() {
  jQuery(".achp-widget").each(function(){

      jQuery(this).on("click", "li.achp-parent a.achp-sig", function(e) {
        var elements = jQuery(this).siblings("ul").children("li");
        if (elements.length) {
          e.preventDefault(); 
          jqueryArchiveListAnimate(this, elements);
        }
        /*调整已经为空的ul行高*/
        jQuery(this).parent().children("ul.achp-child").toggleClass("apip-no-disp");
      });
  });
});