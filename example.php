<?php
require_once('include/connection.php');

$table = "url";
$pk = "url_id";

$select = "*,CONCAT(url_id,'',2) AS fake_sub_q";
$list = $db->get($table,$select,$pk." != '1'",[[$pk=>'ASC'], ['name'=>'ASC']],5);

$name = "NEW LIST - ".time();;
echo "<pre>";print_r($list);

$inserted_id = $db->save($table,['name'=>$name]);
echo $inserted_id;
$rs = $db->update($table,['name'=>$name],[$pk=>2]);

$list = $db->get($table,$select,$pk." != '1'",[[$pk=>'ASC'], ['name'=>'ASC']],5);
echo "<pre>";print_r($list);exit;


/* DATATAHBLE */

require_once(BASE_PATH.'/include/Datatables.php');

$datatables = new Datatables($db);
$companycode = $_SESSION['companycode'];

$table = "";

$field = "
$table.manu_bill_material_id AS id,
$table.date AS date,";
$datatables->select($field);
$datatables->from($table);

$where = "del != 'yes' ".dt_where();
$datatables->where($where);
$jsonData = $datatables->generate();
die($jsonData);        
?>