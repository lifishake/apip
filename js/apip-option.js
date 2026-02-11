/**
 * @file apip-option.js
 * @brief 所有前台页面都需要用的js
 * @since 1.30.4
 * @version 1.40.8
 * @date 2026-01-05
 * @see apip_scripts()
 */

//var lazyload = [];

//全局变量初始值
/* <![CDATA[ */
var apipScriptData = apipScriptData || {
  lazyload: false,
};
/* ]]> */

/**
 * @brief 通过unveil()实现lazyload
 * @requires unveil-ui.min.js
 * @see apip_lazyload_filter()
 * @date 2026-01-05
 * @version 1.40.8
 * @since 1.30.4
*/
jQuery( document ).ready( function( $ ) {
  if (apipScriptData.lazyload) {
    $( 'img[data-unveil="true"]' ).unveil( 200 );
  }
});

var HoldLog = console.log;
console.log = function() {} ;
let now1 = new Date();

/**
 * @brief 控制台提示信息
 * @date 2026-01-05
 * @version 1.41.0
 * @since 1.38.3
*/
queueMicrotask( () => {
  const Log = function() {
    HoldLog.apply(console, arguments);
  };

  const grt = new Date("11/26/2005 10:00:00");
  now1.setTime(now1.getTime() + 250);
  const days = (now1 - grt) / 1000 / 60 / 60 / 24;
  const dnum = Math.floor(days);
  const ascll = `
╔═╗╔═╗╦ ╦╔═╗╔═╗  ╔╦╗╦╔═╗╔═╗
╠═╝║╣ ║║║╠═╣║╣    ║ ║╠═╝╚═╗
╩  ╚═╝╚╩╝╩ ╩╚═╝   ╩ ╩╩  ╚═╝`;

  setTimeout(Log.bind(console, `\n%c${ascll}%c \n`, "color:#ff4f87;font-family:consolas, menlo, mono;", ""));
  setTimeout(Log.bind(console, `\n%c※%c 本站已上线%c${dnum}%c天，采用了很多包了浆的老技术，喜欢尽管%c直接拿走勿问%c。\n`, "color:#f0ad4e", "","color:#ff4f87; font-weight:700;", "","color:#ff4f87; font-weight:700;", ""));
  setTimeout(Log.bind(console, "%c※%c 主题代码： %chttps://github.com/lifishake/sketchy%c ", "color:#f0ad4e", "","color:4f90d9; background-color:#ffdad8", ""));
  setTimeout(Log.bind(console, "%c※%c 插件代码： %chttps://github.com/lifishake/apip%c ", "color:#f0ad4e", "","color:4f90d9; background-color:#ffdad8", ""));
});