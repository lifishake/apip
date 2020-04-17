<?php

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
        if ($month != 12) {
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



function apip_debug_page($val,$name)
{
    if (is_array($val)) {
        echo '<meta name="apip-debug-name'.$name.'" content="{'.print_r($val, true).'}">';
    }
    else{
        echo '<meta name="apip-debug-name'.$name.'" content="{'.$val.'}">';
    }
}

/**
 * 作用: 显示节日文字。
 * 来源: http://www.phpernote.com/php-function/867.html
 * URL:
 */
function apip_festival($post_id=0) {
    $chinese_festivals=array(
        "正月初一"=>"春节",
        "正月十三"=>"海神娘娘生日",
        "正月十五"=>"元宵节",
        "三月初三"=>"歌节",
        "四月初八"=>"浴佛节",
        "五月初五"=>"端午节",
        "七月十五"=>"中元节",
        "八月十五"=>"中秋节",
        "九月初九"=>"重阳节",
        "十月初一"=>"寒衣节",
        "腊月初八"=>"腊八",
        "腊月廿三"=>"小年",
    );
    $solar_festivals=array(
        "02/14"=>"圣瓦伦丁日",
        "04/01"=>"愚人节",
        "04/05"=>"清明",
        "04/30"=>"魔女之夜",
        "11/26"=>"破袜子日",
        "12/06"=>"圣尼可拉斯节",
        "12/22"=>"冬至",
        "12/24"=>"耶诞节",
    );
    $chistian_festivals=array(
        "5_2_7"=>"母亲节",
        "6_3_7"=>"父亲节",
        "7_3_1"=>"海之日",
    );
    //仲夏夜 6/19至25之间的星期五

    $ret = '';
    if ( 0 == $post_id) {
        $year = get_post_time('Y',false,get_the_ID());
        $month = get_post_time('m',false,get_the_ID());
        $day = get_post_time('j',false,get_the_ID());
    }
    else {
        $year = get_post_time('Y',false,$post_id);
        $month = get_post_time('m',false,$post_id);
        $day = get_post_time('j',false,$post_id);
    }
    $lunar=new Lunar();
    $lunar_day = $lunar->convertSolarToLunar($year,$month,$day);
    if (array_key_exists($lunar_day[1], $chinese_festivals)) {
        $ret .= $chinese_festivals[$lunar_day[1]];
    }
    $ret .= $lunar->isChuxi($lunar_day[0],$lunar_day[2],$lunar_day[3]);
    if ($ret !== "") {
        $ret = " / ".$ret;
    }
    $solar = $month."/".$day;
    if (array_key_exists($solar, $solar_festivals)) {
        if ($ret !== "") {
            $ret .=" / ";
        }
        $ret .= $solar_festivals[$solar];
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
    //$weather_result = array();
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
            $w_icon_str = 'wi-sleet';
            break;
        case	400	:	//	小雪
        case	401	:	//	中雪
        case	402	:	//	大雪
        case	403	:	//	暴雪
        case	404	:	//	雨夹雪
        case	405	:	//	雨雪天气
        case	406	:	//	阵雨夹雪
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
    
    
    if (count($tags) > 0) {
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
    
    if (count($cats) > 0) {
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
    for($i=4; $i>=0; $i--) {
        if ($h_start<50){
            $hsv_temp[1] = $hsv[1]+$i*8;
        }
        else {
            $hsv_temp[1] = $hsv[1]-$i*8;
            }
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
