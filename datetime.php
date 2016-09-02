<?php


/**
 * 时间转换成周
 * @param unknown $start_time
 * @param unknown $end_time
 * @return multitype:multitype:string  multitype:unknown string
 */
function weekFormat($start_time,$end_time){
	$str_start_time = strtotime($start_time);
	$str_end_time = strtotime($end_time);
	$day_time = 86400;
	$time_array = array();
	//开始
	$weekday = date('N',($str_start_time));
	if($weekday>1 && $weekday<7){
		$remain = 7-$weekday;
		$first_week_end = date('Y-m-d',strtotime("+{$remain} day",$str_start_time));
	}else{
		$first_week_end = $weekday == 1 ? date('Y-m-d',strtotime("+6 day",$str_start_time)) : date('Y-m-d',$str_start_time);
	}
	$time_array[] = array('start_time'=>date('Y-m-d',$str_start_time),'end_time'=>$first_week_end);
	
	//第一周后的开始
	$week_start = strtotime($first_week_end) +$day_time; //星期一
	$new_end_time = strtotime("+1 day",$str_end_time);
	$diff_time = $new_end_time-$week_start;
	$num = ceil($diff_time/$day_time);
	for($i = 0;$i<$num;$i=$i+7){
		$week_start_time = date('Y-m-d',$week_start + $day_time*$i);
		$week_end_time = date('Y-m-d',strtotime($week_start_time) + $day_time*6);	
		$time_array[] = array('start_time'=>$week_start_time,'end_time'=>$week_end_time);		
	}
	$num = count($time_array);
	$time_array[$num-1]['end_time'] = date('Y-m-d',$str_end_time);
	return $time_array;
}

/**
 * 本周、上周、本月、上月等
 * @return array
 */
function timeFormat(){
	$now = date("Ymd",strtotime("now"));
	//本周一
	$current_week =  date("Ymd",strtotime("-1 week Monday"));
	//上周日
	$pre_week =  date("Ymd",strtotime("-1 week Sunday"));
	//下周一
	$next_week = date("Ymd",strtotime("+0 week Monday"));
	//本周日
	$current_s = date("Ymd",strtotime("+0 week Sunday"));

	// "*********第几个月:";
	$month =  date('n');
	// "*********本周周几:";
	$week =  date("w");
	// "*********本月天数:";
	$day_count =  date("t");

	// '<br>上周起始时间:<br>';
	$pre_week_times[] =  date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1-7,date("Y")));
	$pre_week_times[] =  date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7-7,date("Y")));
	// '<br>本周起始时间:<br>';
	$current_week_times[] = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
	$current_week_times[] =  date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y")));

	//'<br>上月起始时间:<br>';
	$pre_month_times[] =  date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
	$pre_month_times[] = date("Y-m-d H:i:s",mktime(23,59,59,date("m") ,0,date("Y")));
	// '<br>本月起始时间:<br>';
	$current_month_times[] =  date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),1,date("Y")));
	$current_month_times[] = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("t"),date("Y")));

	$season = ceil((date('n'))/3);//当月是第几季度
	// '<br>本季度起始时间:<br>';
	$current_quarter_times[] =  date('Y-m-d H:i:s', mktime(0, 0, 0,$season*3-3+1,1,date('Y')));
	$current_quarter_times[] =  date('Y-m-d H:i:s', mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y')));

	$season = ceil((date('n'))/3)-1;//上季度是第几季度
	//'<br>上季度起始时间:<br>';
	$pre_quarter_times[] =  date('Y-m-d H:i:s', mktime(0, 0, 0,$season*3-3+1,1,date('Y')));
	$pre_quarter_timesp[] =  date('Y-m-d H:i:s', mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y')));

	return array('pre_week'=>$pre_week_times,'current_week'=>$current_week_times,'pre_month'=>$pre_month_times,'current_month'=>$current_month_times);
}

//计算两个日期之间相差天数
function daysFormat($begin_time,$end_time)
{
	$begin_time = strtotime($begin_time);
	$end_time = strtotime($end_time);
	if ( $begin_time < $end_time ) { 
        $starttime = $begin_time; 
        $endtime = $end_time; 
    } else { 
        $starttime = $end_time; 
        $endtime = $begin_time; 
    } 
    $timediff = $endtime - $starttime; 
    $days = intval( $timediff / 86400 ); 
    $remain = $timediff % 86400; 
    $hours = intval( $remain / 3600 ); 
    $remain = $remain % 3600; 
    $mins = intval( $remain / 60 ); 
    $secs = $remain % 60; 
    $res = array( "day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs ); 
    return $res; 
}
