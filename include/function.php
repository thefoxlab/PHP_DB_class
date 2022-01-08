<?php

/****** GET Substring *****/
function get_filter_content($str,$length){
    $get_str = strtolower(substr($str,0,$length));
    return ucwords($get_str);
}
/****** GET Substring *****/

function base_url(){
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    return $url;
}

function get_filename(){
    
    $uri_parts = explode('?', base_url(), 2);
    $info = pathinfo($uri_parts[0]);
    
    return $info['filename'];
}

function dt_to_db($date = "now")
{
    return $date && $date != "0000-00-00" ? date('Y-m-d',strtotime($date)) : $date;
}
function db_to_dt($date = "")
{
    return $date && !in_array($date, ['1970-01-01','0000-00-00']) ? date('d-m-Y',strtotime($date)) : "";
}
function formate_number($number=0){
    return $number > 0 ? $number+0 : $number;
}
function humanize($str = ""){
    return ucwords(str_replace("_", " ", $str));
}
function get_post($name){
    if($name )
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : "";
    }
    else{
        return $_REQUEST;
    }
    
}

function show_td_item($label,$val="",$isBr=true){
    $str = "";
    if($val)
    {
        if(is_numeric($val))
        {
            $val = $val+0;
        }
        $str .= '<b class="control-label">'.$label.' : </b> '.$val.'';
        if($isBr)
        {
            $str .= '<br/>';
        }
    }
	return $str;
}
function get_unit_arr(){
    return ['KGS','PCS','NOS','MTR','CMS','BOX','BAGS','DOZ','TON','SET','PAC','TBS','SQM','SQF','ROL','KLR','CTN','CAN','BTL','BDL','UNT.'];
}
function show_img($path,$name,$class="",$style=""){    
    $img_path = $path.$name;
    if($name)
    {
        return '<img src="'.$img_path.'" alt="img" class="'.$class.'" '.$style.'>';
    }
}

function dt_where($field = "date"){
    $from_date = '';
    $to_date = '';
    $dt_where = "";
    if ($from_date = get_post('from_date')) {
        $dt_where .=" AND DATE($field) >= '" . dt_to_db($from_date) . "'";
    }
    
    if ($to_date = get_post('to_date')) {
        $dt_where .=" AND DATE($field) <= '" . dt_to_db($to_date) . "'";
    }
    return $dt_where;
}
?>
