<?php

//Class lunar
/*
 * 作用: 计算农历相关 除夕判断为自行添加
 * 来源: 忘了
 * URL:
*/
class Lunar{
	var $MIN_YEAR=1891;
	var $MAX_YEAR=2100;
	var $lunarInfo=array(
		array(0,2,9,21936),array(6,1,30,9656),array(0,2,17,9584),array(0,2,6,21168),array(5,1,26,43344),array(0,2,13,59728),
		array(0,2,2,27296),array(3,1,22,44368),array(0,2,10,43856),array(8,1,30,19304),array(0,2,19,19168),array(0,2,8,42352),
		array(5,1,29,21096),array(0,2,16,53856),array(0,2,4,55632),array(4,1,25,27304),array(0,2,13,22176),array(0,2,2,39632),
		array(2,1,22,19176),array(0,2,10,19168),array(6,1,30,42200),array(0,2,18,42192),array(0,2,6,53840),array(5,1,26,54568),
		array(0,2,14,46400),array(0,2,3,54944),array(2,1,23,38608),array(0,2,11,38320),array(7,2,1,18872),array(0,2,20,18800),
		array(0,2,8,42160),array(5,1,28,45656),array(0,2,16,27216),array(0,2,5,27968),array(4,1,24,44456),array(0,2,13,11104),
		array(0,2,2,38256),array(2,1,23,18808),array(0,2,10,18800),array(6,1,30,25776),array(0,2,17,54432),array(0,2,6,59984),
		array(5,1,26,27976),array(0,2,14,23248),array(0,2,4,11104),array(3,1,24,37744),array(0,2,11,37600),array(7,1,31,51560),
		array(0,2,19,51536),array(0,2,8,54432),array(6,1,27,55888),array(0,2,15,46416),array(0,2,5,22176),array(4,1,25,43736),
		array(0,2,13,9680),array(0,2,2,37584),array(2,1,22,51544),array(0,2,10,43344),array(7,1,29,46248),array(0,2,17,27808),
		array(0,2,6,46416),array(5,1,27,21928),array(0,2,14,19872),array(0,2,3,42416),array(3,1,24,21176),array(0,2,12,21168),
		array(8,1,31,43344),array(0,2,18,59728),array(0,2,8,27296),array(6,1,28,44368),array(0,2,15,43856),array(0,2,5,19296),
		array(4,1,25,42352),array(0,2,13,42352),array(0,2,2,21088),array(3,1,21,59696),array(0,2,9,55632),array(7,1,30,23208),
		array(0,2,17,22176),array(0,2,6,38608),array(5,1,27,19176),array(0,2,15,19152),array(0,2,3,42192),array(4,1,23,53864),
		array(0,2,11,53840),array(8,1,31,54568),array(0,2,18,46400),array(0,2,7,46752),array(6,1,28,38608),array(0,2,16,38320),
		array(0,2,5,18864),array(4,1,25,42168),array(0,2,13,42160),array(10,2,2,45656),array(0,2,20,27216),array(0,2,9,27968),
		array(6,1,29,44448),array(0,2,17,43872),array(0,2,6,38256),array(5,1,27,18808),array(0,2,15,18800),array(0,2,4,25776),
		array(3,1,23,27216),array(0,2,10,59984),array(8,1,31,27432),array(0,2,19,23232),array(0,2,7,43872),array(5,1,28,37736),
		array(0,2,16,37600),array(0,2,5,51552),array(4,1,24,54440),array(0,2,12,54432),array(0,2,1,55888),array(2,1,22,23208),
		array(0,2,9,22176),array(7,1,29,43736),array(0,2,18,9680),array(0,2,7,37584),array(5,1,26,51544),array(0,2,14,43344),
		array(0,2,3,46240),array(4,1,23,46416),array(0,2,10,44368),array(9,1,31,21928),array(0,2,19,19360),array(0,2,8,42416),
		array(6,1,28,21176),array(0,2,16,21168),array(0,2,5,43312),array(4,1,25,29864),array(0,2,12,27296),array(0,2,1,44368),
		array(2,1,22,19880),array(0,2,10,19296),array(6,1,29,42352),array(0,2,17,42208),array(0,2,6,53856),array(5,1,26,59696),
		array(0,2,13,54576),array(0,2,3,23200),array(3,1,23,27472),array(0,2,11,38608),array(11,1,31,19176),array(0,2,19,19152),
		array(0,2,8,42192),array(6,1,28,53848),array(0,2,15,53840),array(0,2,4,54560),array(5,1,24,55968),array(0,2,12,46496),
		array(0,2,1,22224),array(2,1,22,19160),array(0,2,10,18864),array(7,1,30,42168),array(0,2,17,42160),array(0,2,6,43600),
		array(5,1,26,46376),array(0,2,14,27936),array(0,2,2,44448),array(3,1,23,21936),array(0,2,11,37744),array(8,2,1,18808),
		array(0,2,19,18800),array(0,2,8,25776),array(6,1,28,27216),array(0,2,15,59984),array(0,2,4,27424),array(4,1,24,43872),
		array(0,2,12,43744),array(0,2,2,37600),array(3,1,21,51568),array(0,2,9,51552),array(7,1,29,54440),array(0,2,17,54432),
		array(0,2,5,55888),array(5,1,26,23208),array(0,2,14,22176),array(0,2,3,42704),array(4,1,23,21224),array(0,2,11,21200),
		array(8,1,31,43352),array(0,2,19,43344),array(0,2,7,46240),array(6,1,27,46416),array(0,2,15,44368),array(0,2,5,21920),
		array(4,1,24,42448),array(0,2,12,42416),array(0,2,2,21168),array(3,1,22,43320),array(0,2,9,26928),array(7,1,29,29336),
		array(0,2,17,27296),array(0,2,6,44368),array(5,1,26,19880),array(0,2,14,19296),array(0,2,3,42352),array(4,1,24,21104),
		array(0,2,10,53856),array(8,1,30,59696),array(0,2,18,54560),array(0,2,7,55968),array(6,1,27,27472),array(0,2,15,22224),
		array(0,2,5,19168),array(4,1,25,42216),array(0,2,12,42192),array(0,2,1,53584),array(2,1,21,55592),array(0,2,9,54560)
	);

	function convertSolarToLunar($year,$month,$date){//debugger;
		$yearData=$this->lunarInfo[$year-$this->MIN_YEAR];
		if($year==$this->MIN_YEAR&&$month<=2&&$date<=9){ 
			return array(1891,'正月初一',1,1);
		}
		return $this->getLunarByBetween($year,$this->getDaysBetweenSolar($year,$month,$date,$yearData[1],$yearData[2]));
	}

	function getLunarMonthDays($year,$month){ 
		$monthData=$this->getLunarMonths($year);
		return $monthData[$month-1];
	}

	function getLunarMonths($year){ 
		$yearData=$this->lunarInfo[$year-$this->MIN_YEAR];
		$leapMonth=$yearData[0];
		$bit=decbin($yearData[3]);
		for ($i=0;$i<strlen($bit);$i ++){
			$bitArray[$i]=substr($bit,$i,1);
		}
		for($k=0,$klen=16-count($bitArray);$k<$klen;$k++){ 
			array_unshift($bitArray,'0');
		}
		$bitArray=array_slice($bitArray,0,($leapMonth==0?12:13));
		for($i=0;$i<count($bitArray);$i++){ 
			$bitArray[$i]=$bitArray[$i] + 29;
		}
		return $bitArray;
	}

	function getLunarYearDays($year){ 
		$yearData=$this->lunarInfo[$year-$this->MIN_YEAR];
		$monthArray=$this->getLunarYearMonths($year);
		$len=count($monthArray);
		return ($monthArray[$len-1]==0?$monthArray[$len-2]:$monthArray[$len-1]);
    }

    function isChuxi($year, $month, $day){
        $monthData=$this->getLunarMonths($year);
        if ($month != count($monthData)) {
            return "";
        }

        $monthDays = $this->getLunarMonthDays($year, $month);
        if ($monthDays == $day) {
            return "除夕";
        }
        return "";
    }
    
	function getLunarYearMonths($year){//debugger;
		$monthData=$this->getLunarMonths($year);
		$res=array();
		$temp=0;
		$yearData=$this->lunarInfo[$year-$this->MIN_YEAR];
		$len=($yearData[0]==0?12:13);
		for($i=0;$i<$len;$i++){ 
			$temp=0;
			for($j=0;$j<=$i;$j++){ 
				$temp+=$monthData[$j];
			}
			array_push($res,$temp);
		}
		return $res;
	}

	function getLeapMonth($year){ 
		$yearData=$this->lunarInfo[$year-$this->MIN_YEAR];
		return $yearData[0];
	}

	function getDaysBetweenLunar($year,$month,$date){ 
		$yearMonth=$this->getLunarMonths($year);
		$res=0;
		for($i=1;$i<$month;$i++){ 
			$res+=$yearMonth[$i-1];
		}
		$res+=$date-1;
		return $res;
	}

	function getDaysBetweenSolar($year,$cmonth,$cdate,$dmonth,$ddate){ 
		$a=mktime(0,0,0,$cmonth,$cdate,$year);
		$b=mktime(0,0,0,$dmonth,$ddate,$year);
		return ceil(($a-$b)/24/3600);
	}

	function getLunarByBetween($year,$between){//debugger;
		$lunarArray=array();
		$yearMonth=array();
		$t=0;
		$e=0;
		$leapMonth=0;
		$m='';
		if($between==0){ 
			array_push($lunarArray,$year,'正月初一');
			$t=1;
			$e=1;
		}else{ 
			$year=$between>0? $year : ($year-1);
			$yearMonth=$this->getLunarYearMonths($year);
			$leapMonth=$this->getLeapMonth($year);
			$between=$between>0?$between : ($this->getLunarYearDays($year)+$between);
			for($i=0;$i<13;$i++){ 
				if($between==$yearMonth[$i]){ 
					$t=$i+2;
					$e=1;
					break;
				}else if($between<$yearMonth[$i]){ 
					$t=$i+1;
					$e=$between-(empty($yearMonth[$i-1])?0:$yearMonth[$i-1])+1;
					break;
				}
			}
			$m=($leapMonth!=0&&$t==$leapMonth+1)?('闰'.$this->getCapitalNum($t- 1,true)):$this->getCapitalNum(($leapMonth!=0&&$leapMonth+1<$t?($t-1):$t),true);
			array_push($lunarArray,$year,$m.$this->getCapitalNum($e,false));
		}
		array_push($lunarArray,$t,$e);
		array_push($lunarArray,$leapMonth);// 闰几月 
		return $lunarArray;
	}

	function getCapitalNum($num,$isMonth){ 
		$isMonth=$isMonth||false;
		$dateHash=array('0'=>'','1'=>'一','2'=>'二','3'=>'三','4'=>'四','5'=>'五','6'=>'六','7'=>'七','8'=>'八','9'=>'九','10'=>'十 ');
		$monthHash=array('0'=>'','1'=>'正月','2'=>'二月','3'=>'三月','4'=>'四月','5'=>'五月','6'=>'六月','7'=>'七月','8'=>'八月','9'=>'九月','10'=>'十月','11'=>'冬月','12'=>'腊月');
		$res='';
		if($isMonth){ 
			$res=$monthHash[$num];
		}else{ 
			if($num<=10){ 
				$res='初'.$dateHash[$num];
			}else if($num>10&&$num<20){ 
				$res='十'.$dateHash[$num-10];
			}else if($num==20){ 
				$res="二十";
			}else if($num>20&&$num<30){ 
				$res="廿".$dateHash[$num-20];
			}else if($num==30){ 
				$res="三十";
			}
		}
		return $res;
	}
}
// class Lunar

/*
 * 作用: 取得1900-2100年间公历日期转为干支记日的结果
 * 来源: 百度知道
 * 参数: int    $year       公历年份
 * 参数: int    $month      公历月份（1-12）
 * 参数: int    $month      公历日期
 * URL:
 * 返回值：array()
 *                  =>'num', 1-60 该干支在干支表中的序号。干支表：甲子(1)、乙丑(2)、丙寅(3)……癸亥(60)
 *                  =>'gan', 该日天干中文文字
 *                  =>'zhi', 该日地支中文文字
 *                  =>'day', 干支中文名
*/
function get_ganzhi($year, $month, $day) {
    $ret = array();
    $term_gan = array("甲","乙","丙","丁","戊","己","庚","辛","壬","癸");
    $term_zhi = array("子","丑","寅","卯","辰","巳","午","未","申","酉","戌","亥");
    $um = (141-$month*11)%12+3;//(MOD(9-$AC3*11,12)+3)
    $bc = ($year - floor($um/13)) %100;
    $r = floor($bc/4)*6 + 
        5*(floor($bc/4)*3 + $bc%4) + 
        30*($month%2+1) + 
        floor(($um*3-7)/5)+$day + 
        44*floor(($year - floor($um/13)) /100) + 
        floor(floor(($year - floor($um/13)) /100)/4)+9;
    $ret['num'] = $r % 60 +1;
    $ret['gan'] = $term_gan[($r - 1) % 10];
    $ret['zhi'] = $term_zhi[($r - 1) % 12];
    $ret['day'] = $ret['gan'].$ret['zhi'];
    return $ret;
}

/*
 * 作用: 判断是否是【某月第几个星期几】方法构成的节日
 * 来源: 自产
 * 参数: int    $month      公历月份（1-12）
 * 参数: int    $month      公历日期
 * 参数: int    $w          星期几（星期天0，星期一1...星期六6）
 * 返回值：string 节日名或者空字符串
*/
function is_cristian_festivel($month, $day, $w) {
    $chistian_festivals=array(
        "5_2_0"=>"母亲节",
        "6_3_0"=>"父亲节",
        "7_3_1"=>"海之日",
        "11_4_4"=>"感恩节",
    );
    $count = 0;
    $d = $day;
    while($d >=0 ) {
        $count++;
        $d -=7;
    }
    $ret = "";
    $chistian_str = sprintf("%d_%d_%d", $month, $count, $w);
    if (array_key_exists($chistian_str, $chistian_festivals)) {
        $ret = $chistian_festivals[$chistian_str];
    }
    return $ret;
}

/*
 * 作用: 判断是否是仲夏节（6/19至25之间的星期五（瑞典））
 * 来源: 自产
 * 参数: int    $month      公历月份（1-12）
 * 参数: int    $month      公历日期
 * 参数: int    $w          星期几（星期天0，星期一1...星期六6）
 * 返回值：bool 
*/
function is_mid_summer_festivel($month, $day, $w) {
    if($month != 6) {
        return false;
    }
    if ($day <19 || $day >25) {
        return false;
    }
    if ($w == 5) {
        return true;
    }
    return false;
}

/*
 * 作用: 在页面开始处显示debug信息
 * 来源: 自产
*/
function apip_debug_page($val,$name)
{
    if (is_array($val)) {
        echo '<meta name="apip-debug-name'.$name.'" content="{'.print_r($val, true).'}">';
    }
    else{
        echo '<meta name="apip-debug-name'.$name.'" content="{'.$val.'}">';
    }
}

/*
 * 作用: 根据节气编号取节气日期（1900-2100）
 * 来源: https://www.csdn.net/tags/MtTaAg4sMzIwMTMtYmxvZwO0O0OO0O0O.html
 * 参数: int    $year       公历年
 * 参数: int    $no         节日序号，见下表
        1  小寒     2  大寒     3  立春     4  雨水     5  惊蛰     6  春分
        7  清明     8  谷雨     9  立夏     10 小满     11 芒种     12 夏至
        13 小暑     14 大暑     15 立秋     16 处暑     17 白露     18 秋分
        19 寒露     20 霜降     21 立冬     22 小雪     23 大雪     24 冬至
 * 返回值:  int 该节气的公历日
 */
function get_term_day($year, $no)
{
    $solarTerms = [
        '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e', '97bcf97c3598082c95f8c965cc920f',
        '97bd0b06bdb0722c965ce1cfcc920f', 'b027097bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e',
        '97bcf97c359801ec95f8c965cc920f', '97bd0b06bdb0722c965ce1cfcc920f', 'b027097bd097c36b0b6fc9274c91aa',
        '97b6b97bd19801ec9210c965cc920e', '97bcf97c359801ec95f8c965cc920f', '97bd0b06bdb0722c965ce1cfcc920f',
        'b027097bd097c36b0b6fc9274c91aa', '9778397bd19801ec9210c965cc920e', '97b6b97bd19801ec95f8c965cc920f',
        '97bd09801d98082c95f8e1cfcc920f', '97bd097bd097c36b0b6fc9210c8dc2', '9778397bd197c36c9210c9274c91aa',
        '97b6b97bd19801ec95f8c965cc920e', '97bd09801d98082c95f8e1cfcc920f', '97bd097bd097c36b0b6fc9210c8dc2',
        '9778397bd097c36c9210c9274c91aa', '97b6b97bd19801ec95f8c965cc920e', '97bcf97c3598082c95f8e1cfcc920f',
        '97bd097bd097c36b0b6fc9210c8dc2', '9778397bd097c36c9210c9274c91aa', '97b6b97bd19801ec9210c965cc920e',
        '97bcf97c3598082c95f8c965cc920f', '97bd097bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
        '97b6b97bd19801ec9210c965cc920e', '97bcf97c3598082c95f8c965cc920f', '97bd097bd097c35b0b6fc920fb0722',
        '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e', '97bcf97c359801ec95f8c965cc920f',
        '97bd097bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e',
        '97bcf97c359801ec95f8c965cc920f', '97bd097bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
        '97b6b97bd19801ec9210c965cc920e', '97bcf97c359801ec95f8c965cc920f', '97bd097bd07f595b0b6fc920fb0722',
        '9778397bd097c36b0b6fc9210c8dc2', '9778397bd19801ec9210c9274c920e', '97b6b97bd19801ec95f8c965cc920f',
        '97bd07f5307f595b0b0bc920fb0722', '7f0e397bd097c36b0b6fc9210c8dc2', '9778397bd097c36c9210c9274c920e',
        '97b6b97bd19801ec95f8c965cc920f', '97bd07f5307f595b0b0bc920fb0722', '7f0e397bd097c36b0b6fc9210c8dc2',
        '9778397bd097c36c9210c9274c91aa', '97b6b97bd19801ec9210c965cc920e', '97bd07f1487f595b0b0bc920fb0722',
        '7f0e397bd097c36b0b6fc9210c8dc2', '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e',
        '97bcf7f1487f595b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
        '97b6b97bd19801ec9210c965cc920e', '97bcf7f1487f595b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722',
        '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e', '97bcf7f1487f531b0b0bb0b6fb0722',
        '7f0e397bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e',
        '97bcf7f1487f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
        '97b6b97bd19801ec9210c9274c920e', '97bcf7f0e47f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722',
        '9778397bd097c36b0b6fc9210c91aa', '97b6b97bd197c36c9210c9274c920e', '97bcf7f0e47f531b0b0bb0b6fb0722',
        '7f0e397bd07f595b0b0bc920fb0722', '9778397bd097c36b0b6fc9210c8dc2', '9778397bd097c36c9210c9274c920e',
        '97b6b7f0e47f531b0723b0b6fb0722', '7f0e37f5307f595b0b0bc920fb0722', '7f0e397bd097c36b0b6fc9210c8dc2',
        '9778397bd097c36b0b70c9274c91aa', '97b6b7f0e47f531b0723b0b6fb0721', '7f0e37f1487f595b0b0bb0b6fb0722',
        '7f0e397bd097c35b0b6fc9210c8dc2', '9778397bd097c36b0b6fc9274c91aa', '97b6b7f0e47f531b0723b0b6fb0721',
        '7f0e27f1487f595b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
        '97b6b7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722',
        '9778397bd097c36b0b6fc9274c91aa', '97b6b7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722',
        '7f0e397bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa', '97b6b7f0e47f531b0723b0b6fb0721',
        '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
        '97b6b7f0e47f531b0723b0787b0721', '7f0e27f0e47f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722',
        '9778397bd097c36b0b6fc9210c91aa', '97b6b7f0e47f149b0723b0787b0721', '7f0e27f0e47f531b0723b0b6fb0722',
        '7f0e397bd07f595b0b0bc920fb0722', '9778397bd097c36b0b6fc9210c8dc2', '977837f0e37f149b0723b0787b0721',
        '7f07e7f0e47f531b0723b0b6fb0722', '7f0e37f5307f595b0b0bc920fb0722', '7f0e397bd097c35b0b6fc9210c8dc2',
        '977837f0e37f14998082b0787b0721', '7f07e7f0e47f531b0723b0b6fb0721', '7f0e37f1487f595b0b0bb0b6fb0722',
        '7f0e397bd097c35b0b6fc9210c8dc2', '977837f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721',
        '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722', '977837f0e37f14998082b0787b06bd',
        '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722',
        '977837f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722',
        '7f0e397bd07f595b0b0bc920fb0722', '977837f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721',
        '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722', '977837f0e37f14998082b0787b06bd',
        '7f07e7f0e47f149b0723b0787b0721', '7f0e27f0e47f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722',
        '977837f0e37f14998082b0723b06bd', '7f07e7f0e37f149b0723b0787b0721', '7f0e27f0e47f531b0723b0b6fb0722',
        '7f0e397bd07f595b0b0bc920fb0722', '977837f0e37f14898082b0723b02d5', '7ec967f0e37f14998082b0787b0721',
        '7f07e7f0e47f531b0723b0b6fb0722', '7f0e37f1487f595b0b0bb0b6fb0722', '7f0e37f0e37f14898082b0723b02d5',
        '7ec967f0e37f14998082b0787b0721', '7f07e7f0e47f531b0723b0b6fb0722', '7f0e37f1487f531b0b0bb0b6fb0722',
        '7f0e37f0e37f14898082b0723b02d5', '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721',
        '7f0e37f1487f531b0b0bb0b6fb0722', '7f0e37f0e37f14898082b072297c35', '7ec967f0e37f14998082b0787b06bd',
        '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e37f0e37f14898082b072297c35',
        '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722',
        '7f0e37f0e366aa89801eb072297c35', '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f149b0723b0787b0721',
        '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e37f0e366aa89801eb072297c35', '7ec967f0e37f14998082b0723b06bd',
        '7f07e7f0e47f149b0723b0787b0721', '7f0e27f0e47f531b0723b0b6fb0722', '7f0e37f0e366aa89801eb072297c35',
        '7ec967f0e37f14998082b0723b06bd', '7f07e7f0e37f14998083b0787b0721', '7f0e27f0e47f531b0723b0b6fb0722',
        '7f0e37f0e366aa89801eb072297c35', '7ec967f0e37f14898082b0723b02d5', '7f07e7f0e37f14998082b0787b0721',
        '7f07e7f0e47f531b0723b0b6fb0722', '7f0e36665b66aa89801e9808297c35', '665f67f0e37f14898082b0723b02d5',
        '7ec967f0e37f14998082b0787b0721', '7f07e7f0e47f531b0723b0b6fb0722', '7f0e36665b66a449801e9808297c35',
        '665f67f0e37f14898082b0723b02d5', '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721',
        '7f0e36665b66a449801e9808297c35', '665f67f0e37f14898082b072297c35', '7ec967f0e37f14998082b0787b06bd',
        '7f07e7f0e47f531b0723b0b6fb0721', '7f0e26665b66a449801e9808297c35', '665f67f0e37f1489801eb072297c35',
        '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722',
    ];
    if ($year < 1900 || $year > 2100) {
        return -1;
    }
    if ($no < 1 || $no > 24) {
        return -1;
    }
    $solarTermsOfYear = array_map('hexdec', str_split($solarTerms[$year - 1900], 5));
    $positions = [
        0 => [0, 1],
        1 => [1, 2],
        2 => [3, 1],
        3 => [4, 2],
    ];
    $group = intval(($no - 1) / 4);
    list($offset, $length) = $positions[($no - 1) % 4];
    return substr($solarTermsOfYear[$group], $offset, $length);
}

/*
 * 作用: 判断是否是节气
 * 参数: int    $year       公历年份
 * 参数: int    $month      公历月份（1-12）
 * 参数: int    $month      公历日期
 * 返回值:  string 节气名或空字符串
 */
function is_jieqi($year, $month, $day){
    $cnt = ($month - 1)*2 + 1;
    $cnt1 = ($month - 1)*2 + 2;
    $idx = -1;
    $solar_terms=["小寒","大寒","立春","雨水","惊蛰","春分","清明","谷雨","立夏","小满","芒种","立夏","小暑","大暑","立秋","处暑","白露","秋分","寒露","霜降","立冬","小雪","大雪","冬至"];
    if ($day == get_term_day($year, $cnt)) {
        $idx = $cnt -1;
    }
    else if ($day == get_term_day($year, $cnt1)) {
        $idx = $cnt1 -1;
    }
    if($idx<0||$idx>23) {
        return "";
    }
    return $solar_terms[$idx];
}

/**
 * 作用: 显示节日文字。
 * 来源: 自产
 * 参数: int    $post_id        post号。非0时取post号，0使用get_the_ID()
 */
function apip_festival($post_id=0) {
    $chinese_festivals=array(
        "正月初一"=>"春节",
        "正月十三"=>"海神生日",
        "正月十五"=>"元宵节",
        "二月初二"=>"龙抬头",
        "三月初三"=>"歌节",
        "三月廿三"=>"天后诞",
        "四月初八"=>"浴佛节",
        "五月初五"=>"端午节",
        "六月廿四"=>"火把节",
        "七月初七"=>"乞巧节",
        "七月十五"=>"中元节",
        "八月十五"=>"中秋节",
        "九月初九"=>"重阳节",
        "十月初一"=>"寒衣节",
	    "十月十五"=>"下元节",
        "十月十六"=>"盘王节",
        "腊月初八"=>"腊八",
	    "腊月十六"=>"尾牙",
        "腊月廿三"=>"小年",
    );
    $solar_festivals=array(
        "02/14"=>"圣瓦伦丁日",
        "03/14"=>"白色情人节",
        "04/01"=>"愚人节",
        "04/30"=>"魔女之夜",
	    "06/24"=>"圣约翰节",
	    "10/31"=>"万圣节前夜",
	    "11/01"=>"万圣节",
        "11/26"=>"破袜子日",
        "12/06"=>"圣尼可拉斯节",
        "12/24"=>"耶诞节前夜",
        "12/25"=>"耶诞节",
    );
	$static_festivals_muslim=array(
		"2000/01/08" => "开斋节",
		"2000/12/28" => "开斋节",
		"2001/12/17" => "开斋节",
		"2002/12/06" => "开斋节",
		"2003/11/26" => "开斋节",
		"2004/11/14" => "开斋节",
		"2005/11/04" => "开斋节",
		"2006/10/24" => "开斋节",
		"2007/10/13" => "开斋节",
		"2008/10/02" => "开斋节",
		"2009/09/21" => "开斋节",
		"2010/09/10" => "开斋节",
		"2011/08/31" => "开斋节",
		"2012/08/19" => "开斋节",
		"2013/08/08" => "开斋节",
		"2014/07/28" => "开斋节",
		"2015/07/18" => "开斋节",
		"2016/07/06" => "开斋节",
		"2017/06/25" => "开斋节",
		"2018/06/15" => "开斋节",
		"2019/06/04" => "开斋节",
		"2020/05/24" => "开斋节",
		"2021/05/13" => "开斋节",
		"2022/05/03" => "开斋节",
		"2023/04/22" => "开斋节",
		"2024/04/10" => "开斋节",
		"2025/03/31" => "开斋节",
		"2026/03/20" => "开斋节",
		"2027/03/10" => "开斋节",
		"2028/02/27" => "开斋节",
		"2029/02/15" => "开斋节",
		"2030/02/05" => "开斋节",
		"2031/01/25" => "开斋节",
		"2032/01/14" => "开斋节",
		"2033/01/03" => "开斋节",
		"2033/12/23" => "开斋节",
		"2034/12/12" => "开斋节",
		"2035/12/02" => "开斋节",
		"2036/11/20" => "开斋节",
		"2037/11/10" => "开斋节",
		"2038/10/30" => "开斋节",
		"2039/10/19" => "开斋节",
		"2040/10/08" => "开斋节",
		"2041/09/27" => "开斋节",
		"2042/09/16" => "开斋节",
		"2043/09/06" => "开斋节",
		"2044/08/25" => "开斋节",
		"2045/08/15" => "开斋节",
		"2046/08/04" => "开斋节",
		"2047/07/24" => "开斋节",
		"2048/07/13" => "开斋节",
		"2049/07/02" => "开斋节",
		"2050/06/21" => "开斋节",
		"2051/06/11" => "开斋节",
		"2052/05/30" => "开斋节",
		"2053/05/19" => "开斋节",
		"2054/05/09" => "开斋节",
		"2055/04/28" => "开斋节",
		"2056/04/17" => "开斋节",
		"2057/04/06" => "开斋节",
		"2058/03/26" => "开斋节",
		"2059/03/16" => "开斋节",
		"2060/03/04" => "开斋节",
		"2061/02/21" => "开斋节",
		"2062/02/11" => "开斋节",
		"2063/01/31" => "开斋节",
		"2064/01/20" => "开斋节",
		"2065/01/09" => "开斋节",
		"2065/12/29" => "开斋节",
		"2066/12/19" => "开斋节",
		"2067/12/08" => "开斋节",
		"2068/11/26" => "开斋节",
		"2069/11/16" => "开斋节",
		"2070/11/05" => "开斋节",
		"2071/10/25" => "开斋节",
		"2000/03/16" => "古尔邦节",
		"2001/03/06" => "古尔邦节",
		"2002/02/23" => "古尔邦节",
		"2003/02/12" => "古尔邦节",
		"2004/02/02" => "古尔邦节",
		"2005/01/21" => "古尔邦节",
		"2006/01/10" => "古尔邦节",
		"2006/12/31" => "古尔邦节",
		"2007/12/20" => "古尔邦节",
		"2008/12/09" => "古尔邦节",
		"2009/11/28" => "古尔邦节",
		"2010/11/17" => "古尔邦节",
		"2011/11/07" => "古尔邦节",
		"2012/10/26" => "古尔邦节",
		"2013/10/15" => "古尔邦节",
		"2014/10/04" => "古尔邦节",
		"2015/09/24" => "古尔邦节",
		"2016/09/12" => "古尔邦节",
		"2017/09/02" => "古尔邦节",
		"2018/08/21" => "古尔邦节",
		"2019/08/11" => "古尔邦节",
		"2020/07/31" => "古尔邦节",
		"2021/07/20" => "古尔邦节",
		"2022/07/10" => "古尔邦节",
		"2023/06/29" => "古尔邦节",
		"2024/06/17" => "古尔邦节",
		"2025/06/07" => "古尔邦节",
		"2026/05/27" => "古尔邦节",
		"2027/05/17" => "古尔邦节",
		"2028/05/05" => "古尔邦节",
		"2029/04/24" => "古尔邦节",
		"2030/04/14" => "古尔邦节",
		"2031/04/03" => "古尔邦节",
		"2032/03/22" => "古尔邦节",
		"2033/03/12" => "古尔邦节",
		"2034/03/01" => "古尔邦节",
		"2035/02/18" => "古尔邦节",
		"2036/02/08" => "古尔邦节",
		"2037/01/27" => "古尔邦节",
		"2038/01/17" => "古尔邦节",
		"2039/01/06" => "古尔邦节",
		"2039/12/26" => "古尔邦节",
		"2040/12/15" => "古尔邦节",
		"2041/12/04" => "古尔邦节",
		"2042/11/23" => "古尔邦节",
		"2043/11/13" => "古尔邦节",
		"2044/11/01" => "古尔邦节",
		"2045/10/22" => "古尔邦节",
		"2046/10/11" => "古尔邦节",
		"2047/09/30" => "古尔邦节",
		"2048/09/19" => "古尔邦节",
		"2049/09/08" => "古尔邦节",
		"2050/08/28" => "古尔邦节",
		"2051/08/18" => "古尔邦节",
		"2052/08/06" => "古尔邦节",
		"2053/07/26" => "古尔邦节",
		"2054/07/16" => "古尔邦节",
		"2055/07/05" => "古尔邦节",
		"2056/06/24" => "古尔邦节",
		"2057/06/13" => "古尔邦节",
		"2058/06/02" => "古尔邦节",
		"2059/05/23" => "古尔邦节",
		"2060/05/11" => "古尔邦节",
		"2061/04/30" => "古尔邦节",
		"2062/04/20" => "古尔邦节",
		"2063/04/09" => "古尔邦节",
		"2064/03/28" => "古尔邦节",
		"2065/03/18" => "古尔邦节",
		"2066/03/07" => "古尔邦节",
		"2067/02/25" => "古尔邦节",
		"2068/02/14" => "古尔邦节",
		"2069/02/02" => "古尔邦节",
		"2070/01/23" => "古尔邦节",
		"2071/01/12" => "古尔邦节",
	);
	$static_festivals_christian=array(
		"2000/04/23" => "复活节",
		"2001/04/15" => "复活节",
		"2002/03/31" => "复活节",
		"2003/04/20" => "复活节",
		"2004/04/11" => "复活节",
		"2005/03/27" => "复活节",
		"2006/04/16" => "复活节",
		"2007/04/08" => "复活节",
		"2008/03/23" => "复活节",
		"2009/04/12" => "复活节",
		"2010/04/04" => "复活节",
		"2011/04/24" => "复活节",
		"2012/04/08" => "复活节",
		"2013/03/31" => "复活节",
		"2014/04/20" => "复活节",
		"2015/04/05" => "复活节",
		"2016/03/27" => "复活节",
		"2017/04/16" => "复活节",
		"2018/04/01" => "复活节",
		"2019/04/21" => "复活节",
		"2020/04/12" => "复活节",
		"2021/04/04" => "复活节",
		"2022/04/17" => "复活节",
		"2023/04/09" => "复活节",
		"2024/03/31" => "复活节",
		"2025/04/20" => "复活节",
		"2026/04/05" => "复活节",
		"2027/03/28" => "复活节",
		"2028/04/16" => "复活节",
		"2029/04/01" => "复活节",
		"2030/04/21" => "复活节",
		"2031/04/13" => "复活节",
		"2032/03/28" => "复活节",
		"2033/04/17" => "复活节",
		"2034/04/09" => "复活节",
		"2035/03/25" => "复活节",
		"2036/04/13" => "复活节",
		"2037/04/05" => "复活节",
		"2038/04/25" => "复活节",
		"2039/04/10" => "复活节",
		"2040/04/01" => "复活节",
		"2041/04/21" => "复活节",
		"2042/04/06" => "复活节",
		"2043/03/29" => "复活节",
		"2044/04/17" => "复活节",
		"2045/04/09" => "复活节",
		"2046/03/25" => "复活节",
		"2047/04/14" => "复活节",
		"2048/04/05" => "复活节",
		"2049/04/18" => "复活节",
		"2048/04/05" => "复活节",
		"2050/04/10" => "复活节",
		"2051/04/02" => "复活节",
		"2052/04/21" => "复活节",
		"2053/04/06" => "复活节",
		"2054/03/29" => "复活节",
		"2055/04/18" => "复活节",
		"2056/04/02" => "复活节",
		"2057/04/22" => "复活节",
		"2058/04/14" => "复活节",
		"2059/03/30" => "复活节",
		"2060/04/18" => "复活节",
		"2061/04/10" => "复活节",
		"2062/03/26" => "复活节",
		"2063/04/15" => "复活节",
		"2064/04/06" => "复活节",
		"2065/03/29" => "复活节",
		"2066/04/11" => "复活节",
		"2067/04/03" => "复活节",
		"2068/04/22" => "复活节",
		"2069/04/14" => "复活节",
		"2070/03/30" => "复活节",
		"2071/04/19" => "复活节",
		"2000/06/01" => "耶稣升天日",
		"2001/05/24" => "耶稣升天日",
		"2002/05/09" => "耶稣升天日",
		"2003/05/29" => "耶稣升天日",
		"2004/05/20" => "耶稣升天日",
		"2005/05/05" => "耶稣升天日",
		"2006/05/25" => "耶稣升天日",
		"2007/05/17" => "耶稣升天日",
		"2008/05/01" => "耶稣升天日",
		"2009/05/21" => "耶稣升天日",
		"2010/05/13" => "耶稣升天日",
		"2011/06/02" => "耶稣升天日",
		"2012/05/17" => "耶稣升天日",
		"2013/05/09" => "耶稣升天日",
		"2014/05/29" => "耶稣升天日",
		"2015/05/14" => "耶稣升天日",
		"2016/05/05" => "耶稣升天日",
		"2017/05/25" => "耶稣升天日",
		"2018/05/10" => "耶稣升天日",
		"2019/05/30" => "耶稣升天日",
		"2020/05/21" => "耶稣升天日",
		"2021/05/13" => "耶稣升天日",
		"2022/05/26" => "耶稣升天日",
		"2023/05/18" => "耶稣升天日",
		"2024/05/09" => "耶稣升天日",
		"2025/05/29" => "耶稣升天日",
		"2026/05/14" => "耶稣升天日",
		"2027/05/06" => "耶稣升天日",
		"2028/05/25" => "耶稣升天日",
		"2029/05/10" => "耶稣升天日",
		"2030/05/30" => "耶稣升天日",
		"2031/05/22" => "耶稣升天日",
		"2032/05/06" => "耶稣升天日",
		"2033/05/26" => "耶稣升天日",
		"2034/05/18" => "耶稣升天日",
		"2035/05/03" => "耶稣升天日",
		"2036/05/22" => "耶稣升天日",
		"2037/05/14" => "耶稣升天日",
		"2038/06/03" => "耶稣升天日",
		"2039/05/19" => "耶稣升天日",
		"2040/05/10" => "耶稣升天日",
		"2041/05/30" => "耶稣升天日",
		"2042/05/15" => "耶稣升天日",
		"2043/05/07" => "耶稣升天日",
		"2044/05/26" => "耶稣升天日",
		"2045/05/18" => "耶稣升天日",
		"2046/05/03" => "耶稣升天日",
		"2047/05/23" => "耶稣升天日",
		"2048/05/14" => "耶稣升天日",
		"2049/05/27" => "耶稣升天日",
		"2050/05/19" => "耶稣升天日",
		"2051/05/11" => "耶稣升天日",
		"2052/05/30" => "耶稣升天日",
		"2053/05/15" => "耶稣升天日",
		"2054/05/07" => "耶稣升天日",
		"2055/05/27" => "耶稣升天日",
		"2056/05/11" => "耶稣升天日",
		"2057/05/31" => "耶稣升天日",
		"2058/05/23" => "耶稣升天日",
		"2059/05/08" => "耶稣升天日",
		"2060/05/27" => "耶稣升天日",
		"2061/05/19" => "耶稣升天日",
		"2062/05/04" => "耶稣升天日",
		"2063/05/24" => "耶稣升天日",
		"2064/05/15" => "耶稣升天日",
		"2065/05/07" => "耶稣升天日",
		"2066/05/20" => "耶稣升天日",
		"2067/05/12" => "耶稣升天日",
		"2068/05/31" => "耶稣升天日",
		"2069/05/23" => "耶稣升天日",
		"2070/05/08" => "耶稣升天日",
		"2071/05/28" => "耶稣升天日",
	);
    $disp_solar_terms=array(
        "清明"=>"清明节",
        "立春"=>"立春",
        "立秋"=>"立秋",
        "冬至"=>"冬至",
    );

    if ( 0 == $post_id) {
        $year = get_post_time('Y',false,get_the_ID());
        $month = get_post_time('m',false,get_the_ID());
        $day = get_post_time('j',false,get_the_ID());
        $weekday = get_post_time('w',false,get_the_ID());
    }
    else {
        $year = get_post_time('Y',false,$post_id);
        $month = get_post_time('m',false,$post_id);
        $day = get_post_time('j',false,$post_id);
        $weekday = get_post_time('w',false,$post_id);
    }
    $ret = "";
    $lunar=new Lunar();
    $lunar_day = $lunar->convertSolarToLunar($year,$month,$day);

    //特殊节日
    //除夕 因为重要最先判断
    $tmp = $lunar->isChuxi($lunar_day[0],$lunar_day[2],$lunar_day[3]);
    if ($tmp !== "") {
        $ret .= " / ".$tmp;
    }

    //农历节日
    if (array_key_exists($lunar_day[1], $chinese_festivals)) {
        $ret .= " / ".$chinese_festivals[$lunar_day[1]];
    }

    //公历节日
    $solar = $month."/".$day;
    if (array_key_exists($solar, $solar_festivals)) {
        $ret .= " / ".$solar_festivals[$solar];
    }

    //节日节气
    $tmp = is_jieqi($year,$month,$day);
    if ($tmp !== "") {
        if (array_key_exists($tmp, $disp_solar_terms)) {
            $ret .= " / ".$disp_solar_terms[$tmp];
        }
    }

    //星期有关的节日
    $tmp = is_cristian_festivel($month, $day, $weekday);
    if ($tmp !== "") {
        $ret .= " / ".$tmp;
    }

    //特殊判断方法的节日
    //仲夏夜
    if (is_mid_summer_festivel($month, $day, $weekday)) {
        $ret .=" / 仲夏夜";
    }

    //复活节
	/* 2021/4/7删除,改为查表
    if (function_exists('easter_date')) {
        //需要安装php库calendar。实用意义不大，不如查表。
        if ( 3==$month+0 || 4==$month+0 && 0 == $weekday ) {
            $eastern = easter_date($year);
            //easter_date取的是零点时间戳，此时转换出来的$day是12点以前的，所以要+1
            if ($day+0 == 1) {
                $estr = "03-31";
            } else {
                $estr = sprintf("%02d-%02d",$month+0, $day - 1);
            }
            
            if (date("m-d", $eastern) == $estr) {
                $ret .=" / 复活节";
            }
        }
    }
	
	$uuu=array();
	for ($i = 2000; $i<2037; ++$i) {
		$eastern = easter_date($i)+3600*12;
		$uuu[$i]=date("Y-m-d", $eastern);
	}
	*/
    //入伏 夏至后的第三个庚日，如果夏至本身是庚日，那就往后记20天。
    if (7 == $month+0 && $day+0 > 10) {
        $xiazhi_day = get_term_day($year, 12);
        $xiazhiganzhi = get_ganzhi($year, 6, $xiazhi_day);
        $xiazhigan = ($xiazhiganzhi['num'] - 1) % 10;
        if ($xiazhigan == 7) {
            //夏至为庚日
            $delta = 0;
        }
        else if ($xiazhigan<7) {
            //夏至在庚日前
            $delta = 7 - $xiazhigan;
        }
        else {
            //夏至在庚日后
            $delta = 10 - ($xiazhigan - 7);
        }
        $tgt_day = $xiazhi_day + $delta + 20 - 30;//一定在阳历7月，所以要减去30号。
        if ($tgt_day == $day) {
            $ret .=" / 入伏";
        }
    }

	//查表节日
	$tmp = sprintf("%04d/%02d/%02d",$year+0, $month+0, $day+0);
	//基督教节日
	if (array_key_exists($tmp, $static_festivals_christian)) {
        $ret .= " / ".$static_festivals_christian[$tmp];
    }
	//伊斯兰节日
	//重要性最低，最后查。
	if (array_key_exists($tmp, $static_festivals_muslim)) {
        $ret .= " / ".$static_festivals_muslim[$tmp];
    }
    if ($ret) {
        $ret = '<span class="festival">'.$ret."</span>";
    }
    return $ret;
}

/**
 * 作用: 显示heweather情报所包含的文字。
 * 资源: CSS和图标来自，http://erikflowers.github.io/weather-icons，图标字体使用 SIL OFL 1.1 -  http://scripts.sil.org/OFL授权；
 * CSS使用MIT License - http://opensource.org/licenses/mit-license.html授权。
 * 来源: 自产
 * URL:
 */
function apip_get_heweather( $style='', $post_id=0)
{
    $ret = '';
    if ( 0 == $post_id) {
        $weather_result = get_post_meta(get_the_ID(),'apip_heweather',false);
    }
    else {
        $weather_result = get_post_meta($post_id,'apip_heweather',false);
    }
    if (empty($weather_result)) {
        return "NONE";
    }
    if (!empty($weather_result[0]['error'])){
        return "ERROR";
    }
    if (null==($weather_result[0]['time'])){
        return "INVALID";
    }

    $then = $weather_result[0]['result'];
    if (!isset($then['cond_code']) && isset($then['icon'])) {
        return apip_get_heweather_v7($then, $style);
    }
    $cond_code = (int)($then['cond_code']);
    switch($cond_code) {
        case	100	:	//	晴
        case	102	:	//	少云
        case	201	:	//	平静
        case	202	:	//	微风
            $w_icon_str = 'wi-day-sunny';
            break;
        case	101	:	//	多云
        case	103	:	//	晴间多云
            $w_icon_str = 'wi-day-cloudy-high';
            break;
        case	104	:	//	阴
            $w_icon_str = 'wi-cloudy';
            break;
        case	200	:	//	有风
            $w_icon_str = 'wi-cloudy';
            break;
        case	203	:	//	和风
        case	204	:	//	清风
            $w_icon_str = 'wi-day-light-wind';
            break;
        case	205	:	//	强风/劲风
        case	206	:	//	疾风
        case	207	:	//	大风
        case	208	:	//	烈风
            $w_icon_str = 'wi-day-windy';
            break;
        case	209	:	//	风暴
        case	210	:	//	狂爆风
        case	211	:	//	飓风
            $w_icon_str = 'wi-strong-wind';
            break;
        case	212	:	//	龙卷风
            $w_icon_str = 'wi-tornado';
            break;
        case	213	:	//	热带风暴
            $w_icon_str = 'wi-hurricane';
            break;
        case	309	:	//	毛毛雨/细雨
        case	300	:	//	阵雨
        case	301	:	//	强阵雨
            $w_icon_str = 'wi-showers';
            break;
        case	302	:	//	雷阵雨
        case	303	:	//	强雷阵雨
            $w_icon_str = 'wi-storm-showers';
            break;
        case	304	:	//	雷阵雨伴有冰雹
            $w_icon_str = 'wi-hail';
            break;
        case	305	:	//	小雨
        case	306	:	//	中雨
        case    399 :   //  雨
            $w_icon_str = 'wi-rain';
            break;
        case	307	:	//	大雨
            $w_icon_str = 'wi-rain-mix';
            break;
        case	308	:	//	极端降雨
        case	310	:	//	暴雨
        case	311	:	//	大暴雨
        case	312	:	//	特大暴雨
            $w_icon_str = 'wi-raindrops';
            break;
        case	313	:	//	冻雨
        case	404	:	//	雨夹雪
        case	405	:	//	雨雪天气
        case	406	:	//	阵雨夹雪
            $w_icon_str = 'wi-sleet';
            break;
        case	400	:	//	小雪
        case	401	:	//	中雪
        case	402	:	//	大雪
        case	403	:	//	暴雪
        case	407	:	//	阵雪
            $w_icon_str = 'wi-snow';
            break;
        case	500	:	//	薄雾
        case	501	:	//	雾
            $w_icon_str = 'wi-fog';
            break;
        case	502	:	//	霾
            $w_icon_str = 'wi-smog';
            break;
        case	503	:	//	扬沙
        case	504	:	//	浮尘
            $w_icon_str = 'wi-dust';
            break;
        case	507	:	//	沙尘暴
        case	508	:	//	强沙尘暴
            $w_icon_str = 'wi-sandstorm';
            break;
        case	900	:	//	热
            $w_icon_str = 'wi-hot';
            break;
        case	901	:	//	冷
            $w_icon_str = 'wi-snowflake-cold';
            break;
        case	999	:	//	未知
        default:
            $w_icon_str = 'wi-na';
            break;
    }
    $wind_str = '';
    if ((int)$then['wind_spd'] > 38) {
        $wind_icon_str = "from-".$then['wind_deg']."-deg";
        $wind_str = $then['wind_dir'].$then['wind_sc']."级 ";
    }
    $ret = '<i class="wi '.$w_icon_str.' icon"></i>';
    if ('notext'!=$style) {
        $ret .= $then['cond_txt'];
    }
    if ( '' !== $wind_str) {
        $ret .= '  <i class="wi wi-wind '.$wind_icon_str.'"></i> ';
        if ('notext'!=$style) {
            $ret .= $wind_str;
        }
    }
    if ('notext'!=$style) {
        $ret .= '  <i class="wi wi-thermometer"></i> ';
        $ret .= $then['tmp'].'&#8451;';
    }
    if ('plain' == $style)
    {
        $ret = $then['cond_txt'].$wind_str.$then['tmp'].'&#8451;';
    }
    return $ret;
}

function apip_get_heweather_v7( $then, $style)
{
    $ret = '';
    if (!is_array($then)) {
        return "ERROR";
    }
    $cond_code = (int)($then['icon']);
    switch($cond_code) {
        case	100	:	//	晴
        case	150	:	//	晴（夜）
        case	102	:	//	少云
        case	152	:	//	少云（夜）
        case	201	:	//	平静
        case	202	:	//	微风
            $w_icon_str = 'wi-day-sunny';
            break;
        case	101	:	//	多云
        case	151	:	//	多云（夜）
        case	103	:	//	晴间多云
        case	153	:	//	晴间多云（夜）
            $w_icon_str = 'wi-day-cloudy-high';
            break;
        case	104	:	//	阴
            $w_icon_str = 'wi-cloudy';
            break;
        case	200	:	//	有风
            $w_icon_str = 'wi-cloudy';
            break;
        case	203	:	//	和风
        case	204	:	//	清风
            $w_icon_str = 'wi-day-light-wind';
            break;
        case	205	:	//	强风/劲风
        case	206	:	//	疾风
        case	207	:	//	大风
        case	208	:	//	烈风
            $w_icon_str = 'wi-day-windy';
            break;
        case	209	:	//	风暴
        case	210	:	//	狂爆风
        case	211	:	//	飓风
            $w_icon_str = 'wi-strong-wind';
            break;
        case	212	:	//	龙卷风
            $w_icon_str = 'wi-tornado';
            break;
        case	213	:	//	热带风暴
            $w_icon_str = 'wi-hurricane';
            break;
        case	309	:	//	毛毛雨/细雨
        case	300	:	//	阵雨
        case	301	:	//	强阵雨        
        case	350	:	//	阵雨（夜）
        case	351	:	//	强阵雨（夜）
            $w_icon_str = 'wi-showers';
            break;
        case	302	:	//	雷阵雨
        case	303	:	//	强雷阵雨
            $w_icon_str = 'wi-storm-showers';
            break;
        case	304	:	//	雷阵雨伴有冰雹
            $w_icon_str = 'wi-hail';
            break;
        case	305	:	//	小雨
        case	306	:	//	中雨
        case    399 :   //  雨
            $w_icon_str = 'wi-rain';
            break;
        case	307	:	//	大雨
            $w_icon_str = 'wi-rain-mix';
            break;
        case	308	:	//	极端降雨
        case	310	:	//	暴雨
        case	311	:	//	大暴雨
        case	312	:	//	特大暴雨
            $w_icon_str = 'wi-raindrops';
            break;
        case	313	:	//	冻雨
        case	404	:	//	雨夹雪、
        case	405	:	//	雨雪天气
        case	406	:	//	阵雨夹雪
        case	456	:	//	阵雨夹雪（夜）
            $w_icon_str = 'wi-sleet';
            break;
        case	400	:	//	小雪
        case	401	:	//	中雪
        case	402	:	//	大雪
        case	403	:	//	暴雪
        case	407	:	//	阵雪
        case	457	:	//	阵雪（夜）
            $w_icon_str = 'wi-snow';
            break;
        case	500	:	//	薄雾
        case	501	:	//	雾
            $w_icon_str = 'wi-fog';
            break;
        case	502	:	//	霾
            $w_icon_str = 'wi-smog';
            break;
        case	503	:	//	扬沙
        case	504	:	//	浮尘
            $w_icon_str = 'wi-dust';
            break;
        case	507	:	//	沙尘暴
        case	508	:	//	强沙尘暴
            $w_icon_str = 'wi-sandstorm';
            break;
        case	900	:	//	热
            $w_icon_str = 'wi-hot';
            break;
        case	901	:	//	冷
            $w_icon_str = 'wi-snowflake-cold';
            break;
        case	999	:	//	未知
        default:
            $w_icon_str = 'wi-na';
            break;
    }
    $wind_str = '';
    if ((int)$then['windSpeed'] > 38) {
        $wind_icon_str = "from-".$then['wind360']."-deg";
        $wind_str = $then['windDir'].$then['windScale']."级 ";
    }
    $ret = '<i class="wi '.$w_icon_str.' icon"></i>';
    if ('notext'!=$style) {
        $ret .= $then['text'];
    }
    if ( '' !== $wind_str) {
        $ret .= '  <i class="wi wi-wind '.$wind_icon_str.'"></i> ';
        if ('notext'!=$style) {
            $ret .= $wind_str;
        }
    }
    if ('notext'!=$style) {
        $ret .= '  <i class="wi wi-thermometer"></i> ';
        $ret .= $then['temp'].'&#8451;';
    }
    if ('plain' == $style)
    {
        $ret = $then['text'].$wind_str.$then['temp'].'&#8451;';
    }
    return $ret;
}

/**
* 作用: 显示留言测试问题
* 来源: 插件改编，汉化和格式修改
* URL: https://github.com/nrkbeta/nrkbetaquiz
*/
function apip_commentquiz_form(){
    if ( !is_single() || !comments_open() || !apip_option_check('apip_commentquiz_enable')) {
        return;
    };
    $quizs = get_post_meta(get_the_ID(), 'apipcommentquiz');
    if ( empty($quizs) ) {
        return;
    }
    ?>
  <div class="apipcommentquiz"
    data-apipcommentquiz="<?php echo esc_attr(rawurlencode(json_encode($quizs))); ?>"
    data-apipcommentquiz-error="<?php echo esc_attr('回答错误，请重试', 'apipcommentquiz'); ?>">
    <h2>答对问题，留言框就会出现</h2>
    <p>
      珍爱生命，拒绝尬聊。<br/>只要贵站的订阅通道畅通且言之有物，本人一定会回访。
    </p>
  </div>
<?php }


/**
 * 作用: 显示最近更新文章
 * 来源: 自产
 * URL:
 */
function apip_recent_post()
{
    global $apip_options;
    $limit = $apip_options['local_definition_count'] ? $apip_options['local_definition_count'] : 5 ;
    $ret = '<ul class = "apip-recent-content">' ;
    $recent_posts = get_posts( array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'orderby' => 'modified',
                    'order' => 'DESC',
                    'numberposts' => $limit
               ) );
    foreach ( $recent_posts as $recent_post ) :
    $ret = $ret.'<li> <a class="recent-post" href="'.get_permalink( $recent_post->ID ).'">' ;
    $ret = $ret.$recent_post->post_title.'</a></li>' ;
    endforeach;
    $ret = $ret.'</ul>' ;
    return $ret;
}

/**
 * 作用: 显示历史相同日文章
 * 来源: 自产
 * URL:
 */
function apip_sameday_post()
{
    global $wpdb;
    $month = get_post_time('m');
    $day = get_post_time('j');
    $id = get_the_ID() ;
    global $apip_options;
    $limit = $apip_options['local_definition_count'] ? $apip_options['local_definition_count'] : 5 ;
    $ret = '<ul class = "apip-history-content">' ;

    $samedays = apip_get_sameday_his_posts($limit);
    if (0 === count($samedays)) {
        return "";
    }


    foreach ( $samedays as $history_post ) {
        $post_id = $history_post['object_id'];
        $ret = $ret.'<li><span class="func-before suffix">['.$history_post['year'].']</span><a class="sameday-post" href="'.get_permalink( $post_id ).'">' ;
        $ret = $ret.get_the_title($post_id).'</a></li>' ;
    }
        
    $ret .= '</ul>' ;
    return $ret;
}

/**
 * 作用: 取得历史同日的N篇文章
 * 来源: 自产
 * 参数: limit: 条数    order:DESC 新文章在先, ASC 旧文章在先, NEARBY 临近文章在先
 * 返回值: array('object_id','year') 文章ID和年份
 */
function apip_get_sameday_his_posts( $limit = 5, $order = "DESC") {
    global $wpdb;
    $month = get_post_time('m');
    $day = get_post_time('j');
    $year = get_post_time('Y');
    $id = get_the_ID() ;
    $ret = array();
    $realorder = $order;
    if ( "NEARBY" === $order ){
        $realorder = "DESC";
    }
    global $apip_options;
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'orderby' => 'post_date',
        'order'   => $realorder,
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'post__not_in' => array(
            $id,
        ),
        'date_query' => array(
            array(
                'month' => $month,
                'day'   => $day,
            ),
        ),
    );
    $the_query = new WP_Query($args);

    foreach ($the_query->posts as $p) {
        $temp = array();
        $temp['object_id'] = $p->ID;
        $temp['year'] = get_post_time('Y',false,$p->ID);
        if ( empty($ret) ) {
            array_push($ret, $temp);
        }
        else if ( "NEARBY" === $order && abs($ret[0]['year'] - $year) >= abs($year - get_post_time('Y',false,$p->ID))){
            $ta = array($temp);
            $ret = array_merge($ta,$ret);
        }
        else{
            array_push($ret, $temp);
        }
    }
    $ret = array_slice($ret, 0, $limit);
    return $ret;
}

/**
 * 作用: 显示随机文章
 * 来源: 自产
 * URL:
 */
function apip_random_post( $exclude, $count = 5 )
{
    $ret = array() ;
    if ( 0 == $count )
    {
        return $ret ;
    }
    $random_posts = get_posts( array( 'exclude' => array($exclude,1), 'orderby' => 'rand', 'posts_per_page'=>$count ) ) ;
    return $random_posts ;
}

function apip_get_post_evaluate($post_id, $standard){
    $tags = get_the_terms( $post_id, 'post_tag') ;
    $cats = get_the_terms( $post_id, 'category') ;
    $myVal = array_sum($standard);
    $evaluate = 0.0;
    if (is_array($tags) && count($tags) >0) {
        foreach ($tags as $tag) {
            if ( array_key_exists($tag->term_taxonomy_id, $standard)) {
                $evaluate += $standard[$tag->term_taxonomy_id];
            }
        }
    }   
    foreach ($cats as $cat) {
        if ( array_key_exists($cat->term_taxonomy_id, $standard)) {
            $evaluate += $standard[$cat->term_taxonomy_id];
        }
    }
    return ceil(($evaluate * 100) / $myVal);
}

/**
 * 作用: 取得关系最紧密的N篇文章
 * 来源: 自产
 * 返回值: array('object_id','evaluate') 文章ID和权重分
 */
function apip_get_related_posts( $limit = 5,$exclude=NULL) {
    global $wpdb ;
    $post_id = get_the_ID() ;
    $tags = get_the_terms( $post_id, 'post_tag') ;
    $cats = get_the_terms( $post_id, 'category') ;
    $ret = array();
    $tag_taxonomy_ids = array();
    $cat_taxonomy_ids = array();
    $standard = array();
    if (is_array($exclude)) {
        $exclude[] = $post_id;
    }
    else {
        $exclude = array();
        $exclude[] = $post_id;
    }
    
    
    if ( is_array($tags) && count($tags) > 0) {
        foreach ($tags as $tag) {
            $fVal = 0.0;
            if ($tag->count > 1) {
                $fVal = 169/$tag->count; 
                $tag_taxonomy_ids[] = $tag->term_taxonomy_id;
            }
            else {
                $fVal = 1.69; 
            }
            $standard[$tag->term_taxonomy_id] = $fVal;       
        }
    }
    
    if ( is_array($cats) && count($cats) > 0) {
        foreach ($cats as $cat) {
            $fVal = 0.0;
            $ancestors = get_ancestors( $cat->term_taxonomy_id, "category" );
            $gen = count($ancestors);
            if ($gen > 0) {
                $fVal = pow(1.3, $gen) * $gen;
            } else {
                $fVal = 1;
            }
            
            $cat_taxonomy_ids[] = $cat->term_taxonomy_id;
            $standard[$cat->term_taxonomy_id] = $fVal; 
            if ($gen>0) {
                $i = $gen - 1;
                foreach ($ancestors as $ancestor) {
                    $fVal = pow(1.3, $i) * $i;
                    $standard[$ancestor] = $fVal;
                    if ($i > 1) {
                        $cat_taxonomy_ids[] = $ancestor;
                    }                  
                }
            }
        }
    }

    $myVal = array_sum($standard);

    //https://wordpress.stackexchange.com/questions/155937/wp-query-mixing-category-in-and-tag-in-together
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'ignore_sticky_posts' => 1,
        'orderby' => 'modified',
        'post_status' => 'publish',
        'post__not_in' => $exclude,
        'tax_query' => array(
            'relation' => 'OR',
            array(
                'taxonomy' => 'category',
                'field' => 'term_taxonomy_id',
                'terms' => $cat_taxonomy_ids,
                'include_children' => false ,
            ),
            array(
                'taxonomy' => 'post_tag',
                'field' => 'term_taxonomy_id',
                'terms' => $tag_taxonomy_ids,
            )
        )
    );
    
    $the_query = new WP_Query( $args );
    foreach ($the_query->posts as $p) {
        $eva = apip_get_post_evaluate($p->ID, $standard);
        $temp = array();
        $temp['object_id'] = $p->ID;
        $temp['evaluate'] = $eva;
        array_push($ret, $temp);
    }
    wp_reset_postdata();

    if (count( $ret )< $limit) {
        $random_posts = apip_random_post( get_the_ID(), $limit - count( $ret ) ) ;
        foreach ($random_posts as $p) {
            $temp = array();
            $temp['object_id'] = $p->ID;
            $temp['evaluate'] = 0;
            array_push($ret, $temp);
        }
    }

    $e = array_column($ret, 'evaluate');
    array_multisort($e, SORT_DESC, $ret);
    $ret = array_slice($ret, 0, $limit);
    return $ret;
}

/**
 * 作用: 显示相关
 * 来源: 自产
 * URL:
 */
function apip_related_post()
{
    global $apip_options;
    $limit = ($apip_options['local_definition_count'] > 0 )? $apip_options['local_definition_count'] : 5 ;
    $relats = apip_get_related_posts($limit);

    $ret = '<ul class = "apip-ralated-content">' ;
    foreach ( $relats as $rel ) :
    $ret .= sprintf("<li><a class=\"related-post\" href=\"%s\">%s</a><span class=\"func-after suffix\">[%s&#37;]</span></li>",
            get_permalink( $rel['object_id'] ),
            get_the_title( $rel['object_id'] ),
            $rel['evaluate'] );
    endforeach;
    $ret .= '</ul>' ;
    return $ret;
}

/*
 * 作用: 显示作者最愿意回复的n个留言者的链接
 * 来源: 自产
 * URL:
*/
function apip_get_links()
{
    global $wpdb;
    $limit = 13; //取多少条，可以自己改
    $scope = "6 MONTH"; //可以使用的时间关键字:SECOND,MINUTE,HOUR,DAY,WEEK,MONTH,QUARTER,YEAR...
    $sql = "SELECT comment_author_email, comment_author_url, comment_author, SUM(comment_length) as words_sum
            FROM $wpdb->comments aa
            INNER JOIN
            (
            SELECT comment_parent, char_length(comment_content) as comment_length
            FROM $wpdb->comments
            WHERE  user_id <> 0
            AND DATE_SUB( CURDATE( ) , INTERVAL $scope ) <= comment_date_gmt
            ORDER BY comment_date_gmt DESC
            )
            AS bb
            WHERE aa.comment_ID = bb.comment_parent
            AND aa.comment_author_url <>''
            GROUP BY comment_author_email
            ORDER BY words_sum DESC
            LIMIT $limit";
    $result = $wpdb->get_results($sql);
    return $result;
}

function apip_get_prev_post() {
    if (isset($_SESSION['last_tax'])&& isset($_SESSION['tax_ids']) && count($_SESSION['tax_ids'])>1) {
        $ID = get_the_ID();
        $pos = array_search($ID, $_SESSION['tax_ids']);
        if ( FALSE === $pos ) {
            return NULL;
        }
        $count = count($_SESSION['tax_ids']);
        if ( $pos > 0) {
            return get_post($_SESSION['tax_ids'][$pos -1]);
        }
        else {
            return NULL;
        }
    }
    else{
        return get_previous_post();
    }  
}

function apip_get_next_post() {
    if (isset($_SESSION['last_tax'])&& isset($_SESSION['tax_ids'])&& count($_SESSION['tax_ids'])>1) {       
        $ID = get_the_ID();
        $pos = array_search($ID, $_SESSION['tax_ids']);
        if ( FALSE === $pos ) {
            return NULL;
        }
        $count = count($_SESSION['tax_ids']);
        if ( $pos < $count - 1) {
            return get_post($_SESSION['tax_ids'][$pos +1]);
        }
        else {
            return NULL;
        }
    }
    else{
        return get_next_post();
    } 
}

/*
 * 作用: 取得上一篇/下一篇.如果在归档/搜索的情况下,在范围内查找.
 * 来源: 自产
 * URL:
*/
function apip_get_post_navagation($args=array()){
   $args = wp_parse_args( $args, array(
        'prev_text'          => '%title',
        'next_text'          => '%title',
        'screen_reader_text' => '文章导航',
    ) );
    /*
    'in_same_term'       => false,
    'excluded_terms'     => '',
    'taxonomy'           => 'category',
    这三个参数被忽略。
     */

    if ( !is_single() ){
        return;
    }

    $ID = get_the_ID();
    if (isset($_SESSION['last_tax'])&& isset($_SESSION['tax_ids'])&& count($_SESSION['tax_ids'])>1) {      
        $pos = array_search($ID, $_SESSION['tax_ids']);
        if ( FALSE === $pos ) {
            the_post_navigation($args);
            return;
        }
        $count = count($_SESSION['tax_ids']);
        $next_id = 0;
        $previous_id = 0;
        $previous="";
        $next="";
        if ( $pos < $count - 1) {
            $next_id = $_SESSION['tax_ids'][$pos +1];
        }
        if ($pos > 0 ) {
            $previous_id = $_SESSION['tax_ids'][$pos -1];
        }
        if ($previous_id > 0)
        {
            $previous = str_replace( '%title', get_the_title( $previous_id ), $args['prev_text'] );
            $previous = '<a href="'.get_permalink( $previous_id).'" rel="prev">'.$previous.'</a>';
            $previous = '<div class="nav-previous">'.$previous.'</div>';
        }
        if ($next_id > 0)
        {
            $next = str_replace( '%title', get_the_title( $next_id ), $args['next_text'] );		 		
            $next = '<a href="'.get_permalink( $next_id ).'" rel="next">'.$next.'</a>';        
            $next = '<div class="nav-next">'.$next.'</div>';
        }
        if ( "" === $desc = $_SESSION['last_tax'] )
        {
            $desc = $args['screen_reader_text'];
        }
        $navigation = _navigation_markup( $previous . $next, 'post-navigation', $desc );
        echo $navigation;
    }
    else{
        the_post_navigation($args);
        return;
    } 
}

/*
 * 作用: 移除某个类的filter方法
 * 来源: http://wordpress.stackexchange.com/questions/57079/how-to-remove-a-filter-that-is-an-anonymous-object
 * URL: http://wordpress.stackexchange.com/questions/57079/how-to-remove-a-filter-that-is-an-anonymous-object
*/
function apip_remove_anonymous_object_hook( $tag, $class, $method )
{
    $filters = $GLOBALS['wp_filter'][ $tag ];

    if ( empty ( $filters ) )
    {
        return;
    }
    foreach ( $filters as $priority => $filter )
    {
        foreach ( $filter as $identifier => $function )
        {
            if ( is_array( $function)
                and is_a( $function['function'][0], $class )
                and $method === $function['function'][1]
            )
            {
                //action也可以用remove_filter删除。
                remove_filter(
                    $tag,
                    array ( $function['function'][0], $method ),
                    $priority
                );
            }
        }
    }
}

/*
 * 作用: 移除wpembed相关的内部插件
 * 来源: Disable Embeds
 * URL: https://pascalbirchler.com
*/
function apip_disable_embeds_tiny_mce_plugin( $plugins ) {
	return array_diff( $plugins, array( 'wpembed' ) );
}
/* 同上 */
function apip_disable_embeds_rewrites( $rules ) {
	foreach ( $rules as $rule => $rewrite ) {
		if ( false !== strpos( $rewrite, 'embed=true' ) ) {
			unset( $rules[ $rule ] );
		}
	}

	return $rules;
}
/* 同上 */
function apip_disable_embeds_remove_rewrite_rules() {
	add_filter( 'rewrite_rules_array', 'apip_disable_embeds_rewrites' );
	flush_rewrite_rules();
}
/* 同上 */
function apip_disable_embeds_flush_rewrite_rules() {
	remove_filter( 'rewrite_rules_array', 'apip_disable_embeds_rewrites' );
	flush_rewrite_rules();
}


function apip_media_upload_nextgen() {

    // Not in use
    $errors = false;

	// Generate TinyMCE HTML output
	if ( isset($_POST['send']) ) {
		$keys = array_keys($_POST['send']);
		$send_id = (int) array_shift($keys);
		$image = $_POST['image'][$send_id];
		$alttext = stripslashes( htmlspecialchars ($image['alttext'], ENT_QUOTES));
		$description = stripslashes (htmlspecialchars($image['description'], ENT_QUOTES));

		// here is no new line allowed
		$clean_description = preg_replace("/\n|\r\n|\r$/", " ", $description);
		$img = nggdb::find_image($send_id);
		$thumbcode = $img->get_thumbcode();

        // Create a shell displayed-gallery so we can inspect its settings
        $registry = C_Component_Registry::get_instance();
        $mapper   = $registry->get_utility('I_Displayed_Gallery_Mapper');
        $factory  = $registry->get_utility('I_Component_Factory');
        $args = array(
            'display_type' => NGG_BASIC_SINGLEPIC
        );
        $displayed_gallery = $factory->create('displayed_gallery', $args, $mapper);
        $domain = str_replace(array('http://','https://'), '', get_bloginfo('url'));
        $urls = array();
        $urls[] = 'http://'.$domain;
        $urls[] = 'https://'.$domain;
        $image['thumb'] = str_replace($urls, '', $image['thumb']);
        $image['url'] = str_replace($urls, '', $image['url']);
		// Build output
		if ($image['size'] == "thumbnail")
			$html = "<img src='{$image['thumb']}' alt='{$alttext}' />";
        else
            $html = '';

		// Wrap the link to the fullsize image around
		$html = "<a {$thumbcode} href='{$image['url']}' title='{$clean_description}'>{$html}</a>";

		if ($image['size'] == "full" || $image['size'] == "singlepic")
			$html = "<img src='{$image['url']}' alt='{$alttext}' />";


		media_upload_nextgen_save_image();

		// Return it to TinyMCE
		return media_send_to_editor($html);
	}

	// Save button
	if ( isset($_POST['save']) ) {
		media_upload_nextgen_save_image();
	}

	return wp_iframe( 'media_upload_nextgen_form', $errors );
}

/**
 * 作用: HEX描述的颜色值转成RGB
 * 来源: Oblique原版
 */
if (!function_exists('hex2rgb'))
{
function hex2rgb($color) {
	if ($color[0] == '#' ) {
		$color = substr( $color, 1 );
	}
	$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
    $rgb =  array_map('hexdec', $hex);
	return $rgb;
}
}

/**
 * 作用: RGB颜色值转成HSV描述
 * 来源: http://stackoverflow.com/questions/1773698/rgb-to-hsv-in-php
 * 输出的范围0-360, 0-100, 0-100!!
 */
if (!function_exists('rgb2hsv'))
{
function rgb2hsv(array $rgb)
{
	list($R,$G,$B) = $rgb;
    $R = ($R / 255);
    $G = ($G / 255);
    $B = ($B / 255);

    $maxRGB = max($R, $G, $B);
    $minRGB = min($R, $G, $B);
    $chroma = $maxRGB - $minRGB;

    $computedV = floor(100 * $maxRGB);

    if ($chroma == 0)
        return array(0, 0, $computedV);

    $computedS = floor(100 * ($chroma / $maxRGB));

    if ($R == $minRGB)
        $h = 3 - (($G - $B) / $chroma);
    elseif ($B == $minRGB)
        $h = 1 - (($R - $G) / $chroma);
    else // $G == $minRGB
        $h = 5 - (($B - $R) / $chroma);

    $computedH = floor(60 * $h);

    return array($computedH, $computedS, $computedV);
}
}

if (!function_exists('hsv2rgb'))
{
function hsv2rgb(array $hsv) {
	list($H,$S,$V) = $hsv;
	//1
	$H /= 60;
	//2
	$I = floor($H);
	$F = $H - $I;
	$S /= 100;
	$V /= 100;
	//3
	$M = round( $V * (1 - $S) * 255);
	$N = round( $V * (1 - $S * $F) * 255 );
	$K = round( $V * (1 - $S * (1 - $F)) * 255 );
	$V = round( $V * 255) ;
	//4
	switch ($I) {
		case 0:
			list($R,$G,$B) = array($V,$K,$M);
			break;
		case 1:
			list($R,$G,$B) = array($N,$V,$M);
			break;
		case 2:
			list($R,$G,$B) = array($M,$V,$K);
			break;
		case 3:
			list($R,$G,$B) = array($M,$N,$V);
			break;
		case 4:
			list($R,$G,$B) = array($K,$M,$V);
			break;
		case 5:
		case 6: //for when $H=1 is given
			list($R,$G,$B) = array($V,$M,$N);
			break;
	}
	return array($R, $G, $B);
}
}
function apip_get_link_colors( $color_str)
{
    $rgb = hex2rgb($color_str);
    $hsv = rgb2hsv($rgb);
    $ret = array();
    $hsv_temp = $hsv;
    $h_start = $hsv[1];
    for($i=7; $i>=0; $i--) {
        $hsv_temp[0] = ($hsv[0]+$i*25)%360;
        $rgb_temp = hsv2rgb($hsv_temp);
        $ret[] =sprintf("#%1$02X%2$02X%3$02X",$rgb_temp[0],$rgb_temp[1],$rgb_temp[2]) ;
    }
        return $ret;
}
function apip_get_bg_colors($color_str,$trancy = 0.3)
{
    $rgb = hex2rgb($color_str);
    $hsv = rgb2hsv($rgb);
    $ret = array();
    $hsv_temp = $hsv;
    $h_start = $hsv[1];
    for($i=4; $i>=0; $i--) {
        if ($h_start >50)
        {
            $hsv_temp[1] = $hsv[1]-9*$i;
        }
        else{
            $hsv_temp[1] = $hsv[1]+9*$i;
        }
        $rgb_temp = hsv2rgb($hsv_temp);
        $ret[] =sprintf("RGBA(%d,%d,%d,%1.1f)",$rgb_temp[0],$rgb_temp[1],$rgb_temp[2],$trancy) ;
    }
    $ret =array_reverse($ret);
    return $ret;
}
