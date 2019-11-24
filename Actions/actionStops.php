<?php  
require_once(dirname(__FILE__) . '/../DataLink/AccessLayer.php');

$input = filter_input_array(INPUT_POST);

$stopID = filter_var(trim($input["id"]), FILTER_SANITIZE_STRING);
$stop = filter_var(trim($input["stop"]), FILTER_SANITIZE_STRING);
$AccessLayer = new AccessLayer();

if($input["action"] === 'edit') {
    $AccessLayer->update_stop($stopID, $stop);
}

if($input["action"] === 'delete') {
    $AccessLayer->remove_stop($stopID);
}
