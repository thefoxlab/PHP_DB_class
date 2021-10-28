<?php

/* Permission Code */
unset($permission_array);

if($_SESSION['roll_id']==4){
    //----------------------for staff----------------------
    $permission_array = array(
        "settings.php",
        "payment-settings.php",
        "maintenance.php",
        "add_branch.php",
        "update_branch.php",
        "add_department.php",
        "update_department.php",
        "add_bank.php",
        "update_bank.php",
        "add_staff.php",
        "update_staff.php",
        "change_password.php",
        "view_and_generate_salary.php",
        "add_salary_setting.php",
        "add_cash_salary_summary.php",
        "cash_summary.php",
        "cash_salary_detail.php",
        "update_cash_salary_summary.php",
        "manage_leaves.php",
        "add_salary_setting.php",
        "maintenance.php"
    );
}
elseif($_SESSION['roll_id']==3){
    //-----------------------for dep_manager----------------
    $permission_array = array(
        "settings.php",
        "payment-settings.php",
        "maintenance.php",
        "add_branch.php",
        "update_branch.php",
        "add_department.php",
        "update_department.php",
        "add_bank.php",
        "update_bank.php",
        "add_staff.php",
        "update_staff.php",
        "add_cash_salary_summary.php",
        "cash_summary.php",
        "cash_salary_detail.php",
        "update_cash_salary_summary.php",
        "manage_leaves.php",
        "add_salary_setting.php",
        "maintenance.php"
    );
}
elseif($_SESSION['roll_id']==2){
    //-----------------for branch_manager---------------------
    $permission_array = array(
        "settings.php",
        "payment-settings.php",
        "maintenance.php",
        "add_branch.php",
        "update_branch.php",
        "add_department.php",
        "update_department.php",
        "add_bank.php",
        "update_bank.php",
        "add_cash_salary_summary.php",
        "cash_summary.php",
        "cash_salary_detail.php",
        "update_cash_salary_summary.php",
        "maintenance.php"
    );
}
else{
    //-----------------for Admin---------------------
    $permission_array=array("all");

    $admin_branch = array(
        "maintenance.php",
        "send_message.php",
        "compose_message.php",
        "view_message.php",
        "add_product.php",
        "view_product.php",
        "product_detail.php",
        "update_product.php",
        "stock_in_summary.php",
        "add_quotation_estimation.php",
        "view_quotation_estimation.php",
        "update_quotation_estimation.php",
        "quotation_estimation_detail.php",
        "add_bank.php",
        "view_bank.php",
        "bank_detail.php",
        "update_bank.php",
        "view_account_statment.php",
        "upload_statement.php",
        "add_staff.php",
        "view_staff.php",
        "staff_detail.php",
        "update_staff.php",
        "supplier_statement.php",
        "view_invoice_gst.php",
        "jan_to_desc_gst.php",
        "today_profit_loss.php",
        "profit_loss_allbranch.php",
        "settings.php",
        "payment-settings.php",
        "gst_setting.php",
        "sst_setting.php",
        "add_bank.php",
        "view_bank.php",
        "bank_detail.php",
        "update_bank.php",
        "add_branch.php",
        "view_branch.php",
        "branch_detail.php",
        "update_branch.php",
        "add_department.php",
        "view_department.php",
        "department_detail.php",
        "update_department.php",
        "notaccess.php",
        "dashboard.php"
    );
}
//var_dump($permission_array);
$page=basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
//echo $page;

if(in_array($page, $permission_array)){
    //header("location: notaccess.php?role=".$_SESSION['roll_id']);
    header("location: dashboard.php?role=".$_SESSION['roll_id']);
}


if($_SESSION['branch_id']=="all" || $_SESSION['branch_id']==""){
    if(isset($admin_branch) && !in_array($page, $admin_branch)){
        //header("location: notaccess.php?role=".$_SESSION['roll_id']);
        header("location: dashboard.php?role=".$_SESSION['roll_id']);
    }
}
/* Permission Code */


/* Convert Number in String in indian currancy formate */
function convertToIndianCurrency($number) {

    if($_SESSION['currency_symbol'] == "RM"){
        $currancy = "Ringgit";
    }
    elseif($_SESSION['currency_symbol'] == "USD"){
        $currancy = "Doller";
    }
    elseif($_SESSION['currency_symbol'] == "Rs."){
        $currancy = "Rupees";
    }
    elseif($_SESSION['currency_symbol'] == "POUND"){
        $currancy = "Pound";
    }
    elseif($_SESSION['currency_symbol'] == "EURO"){
        $currancy = "Euro";
    }
    $no = round($number);
    $decimal = round($number - ($no = floor($number)), 2) * 100;    
    $digits_length = strlen($no);    
    $i = 0;
    $str = array();
    $words = array(
        0 => '',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
        20 => 'Twenty',
        30 => 'Thirty',
        40 => 'Forty',
        50 => 'Fifty',
        60 => 'Sixty',
        70 => 'Seventy',
        80 => 'Eighty',
        90 => 'Ninety');
    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;            
            $str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural;
        } else {
            $str [] = null;
        }  
    }
    
    $Rupees = implode(' ', array_reverse($str));
    $paise = ($decimal) ? "And " . ($words[$decimal - $decimal%10]) ." " .($words[$decimal%10]) : '';
    return ($Rupees ? $Rupees .$currancy.' ': '') . $paise . " Only";
}
/* Convert Number in String in indian currancy formate */

/*Logic To Get The Invoice Number Starts */
function getinvoicenumber($branch_id){
    global $niveaesoft_connection;
    $companycode = $_SESSION['companycode'];
    $branch_table_name = $companycode."_branch";
    $basic_detail_table_name = $companycode."_basic_detail";
    $tax_invoice_detail_table_name = $companycode."_tax_invoice_detail";

    $sel_inv_startno = "SELECT `invoice_start_no`,`last_invoice_no` FROM `$branch_table_name` WHERE `branch_id`=$branch_id";
    $rs_inv_startno = @mysqli_query($niveaesoft_connection,$sel_inv_startno);
    $fh_inv_startno = @mysqli_fetch_array($rs_inv_startno);
    $inv_strtno = $fh_inv_startno['invoice_start_no'];
    $last_invoice_no = $fh_inv_startno['last_invoice_no'];

    if($last_invoice_no == '0'){
        $invno = $inv_strtno;
    }
    else{
        $invno = $last_invoice_no + 1;
    }
    return $invno;
}
/* Logic To Get The Invoice Number Ends */


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