/**
 * @file apip-quicktags.js
 * @brief 后台编辑器上的快捷按钮
 * @since 1.38.6
 * @version 1.41.0
 * @date 2026-02-10
 * @see apip_admin_scripts()
 */

QTags.addButton( 'eg_pre', 'pre', '<pre>', '</pre>');
QTags.addButton( 'eg_mysup', '引文', '[mysup sup_content=\"', '\" /]', 'p' );
QTags.addButton( 'eg_any', 'any', any_callback );
QTags.addButton( 'eg_fancybox', 'fancybox', fancybox_callback );

/**
 * @brief fancybox按钮的回调函数
 * @param {HTMLTextAreaElement} element 未使用
 * @param {HTMLTextAreaElement} canvas 选中字符串控件
 * @param {Object} ed 未使用
 * @date 2025-02-26
 * @version 1.38.6
 * @since 1.38.6
*/
function any_callback(element, canvas, ed) {
  var t = this, startPos, endPos, cursorPos, scrollTop, v = canvas.value, l, r, i;
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

/**
 * @brief fancybox按钮的回调函数，在图像url的外围增加img和a标签，并且添加fancybox需要的data-fancybox和data-caption描述
 * @param {HTMLTextAreaElement} element 未使用
 * @param {HTMLTextAreaElement} canvas 选中字符串控件
 * @param {Object} ed 未使用
 * @date 2026-02-11
 * @version 1.41.0
 * @since 1.41.0
*/
function fancybox_callback(element, canvas, ed) {
  var t, startPos, endPos, cursorPos, scrollTop, v = canvas.value, l, r, i, fname;
  var my_caption = prompt( '增加图片描述:', 'NONE' );
  if ( canvas.selectionStart || canvas.selectionStart === 0 ) { // FF, WebKit, Opera
    startPos = canvas.selectionStart;
    endPos = canvas.selectionEnd;
    cursorPos = endPos;
    scrollTop = canvas.scrollTop;
    l = v.substring(0, startPos); // left of the selection
    r = v.substring(endPos, v.length); // right of the selection
    i = v.substring(startPos, endPos); // inside the selection
    fname = i.split('/').pop().replace(/\.[^/.]+$/, "");
    if (startPos !== endPos) {
      if ('NONE' === my_caption) {
        my_caption = fname;
      }
      t = '<a href="' + i + '" data-fancybox="gallery" data-caption="' + my_caption + '"><img src="' + i + '" alt="' + fname + '" /></a>';
      canvas.value = l + t + r;
      cursorPos += t.length - i.length ;
    }
  }
  canvas.selectionStart = cursorPos;
  canvas.selectionEnd = cursorPos;
  canvas.scrollTop = scrollTop;
  canvas.focus();
}