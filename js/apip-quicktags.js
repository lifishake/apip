QTags.addButton( 'eg_pre', 'pre', '<pre>', '</pre>');
QTags.addButton( 'eg_mysup', '引文', '[mysup sup_content=\"', '\" /]', 'p' );
QTags.addButton( 'eg_any', 'any', any_callback );

function any_callback(element, canvas, ed) {
  var t = this, startPos, endPos, cursorPos, scrollTop, v = canvas.value, l, r, i, sel, endTag = v ? t.tagEnd : '';
  var my_stuff = prompt( 'Enter Title Tag:', '' );
  if ( canvas.selectionStart || canvas.selectionStart === 0 ) { // FF, WebKit, Opera
    startPos = canvas.selectionStart;
    endPos = canvas.selectionEnd;

    cursorPos = endPos;
    scrollTop = canvas.scrollTop;
    l = v.substring(0, startPos); // left of the selection
    r = v.substring(endPos, v.length); // right of the selection
    i = v.substring(startPos, endPos); // inside the selection
    if (startPos !== endPos) {
      canvas.value = l + '<'+my_stuff+'>' + i + '</'+my_stuff+'>' + r;
      cursorPos += my_stuff.length + my_stuff.length + 5;
    }
  }
  canvas.selectionStart = cursorPos;
  canvas.selectionEnd = cursorPos;
  canvas.scrollTop = scrollTop;
  canvas.focus();
}