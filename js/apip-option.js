//var lazyload = [];

/* <![CDATA[ */
var apipScriptData = apipScriptData || {
	lazyload: false,
};
/* ]]> */

jQuery( document ).ready( function( $ ) {
	if (apipScriptData.lazyload) {
		$( 'img[data-unveil="true"]' ).unveil( 200 );
	}
});

var HoldLog = console.log;
console.log = function() {} ;
let now1 = new Date();
queueMicrotask( () => {
	const Log = function() {
		HoldLog.apply(console, arguments);
	};
	//在恢复前输出日志
	const grt = new Date("11/26/2005 10:00:00");
	//此处修改你的建站时间或者网站上线时间
	now1.setTime(now1.getTime() + 250);
	const days = (now1 - grt) / 1000 / 60 / 60 / 24;
	const dnum = Math.floor(days);
	const ascll = `
╔═╗╔═╗╦ ╦╔═╗╔═╗  ╔╦╗╦╔═╗╔═╗
╠═╝║╣ ║║║╠═╣║╣    ║ ║╠═╝╚═╗
╩  ╚═╝╚╩╝╩ ╩╚═╝   ╩ ╩╩  ╚═╝`;

	setTimeout(Log.bind(console, `\n%c${ascll}%c \n`, "color:#ff4f87;font-family:consolas, menlo, mono;", ""));

	setTimeout(Log.bind(console, "%c※%c 主题代码： %chttps://github.com/lifishake/sketchy%c ", "color:#f0ad4e", "","color:4f90d9; background-color:#ffdad8", ""));
	setTimeout(Log.bind(console, "%c※%c 插件代码： %chttps://github.com/lifishake/apip%c ", "color:#f0ad4e", "","color:4f90d9; background-color:#ffdad8", ""));
	setTimeout(Log.bind(console, "%c※%c 自取%c勿问%c。", "color:#f0ad4e", "","color:#ff4f87; font-weight:700;", ""));
});