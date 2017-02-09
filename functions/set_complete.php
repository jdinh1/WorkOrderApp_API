<?php
if (!isset($_POST["verify"]) && !isset($_POST["workorder_id"]))
        return;
require_once 'include/Config.php';
$workorder_id = $_POST["workorder_id"];
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

$sql = "update work_order set status_id='3', completed_date=SYSDATE() where id='$echo $workorder_id'";
$result = $conn->query($sql);
if ($result) {
$type = array();
    $type["error"] = FALSE;
    $type["error_msg"] = "Successfully marked #00".$workorder_id. " as complete.";
    echo json_encode($type);
}
$conn->close();


?>

