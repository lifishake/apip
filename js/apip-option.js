let lazyload = false;

jQuery( document ).ready( function( $ ) {
	if ('undefined'!==lazyload && lazyload) {
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

	setTimeout(Log.bind(console, `\n%c${ascll}%c \n`, "color:#ff4f87", ""));

	setTimeout(Log.bind(console, "%c※%c 从 %chttps://github.com/lifishake%c 下载主题 %csketchy%c 和插件 %capip%c 的源代码可以获得更好的效果。", "color:#f0ad4e", "","color:#425AEF", "", "color:white; background-color:#ff4f87", "", "color:white; background-color:#ff4f87", ""));
	setTimeout(Log.bind(console, "%c※%c 自取%c勿问%c。", "color:#f0ad4e", "","color:#ff4f87; font-weight:700;", ""));
}
);