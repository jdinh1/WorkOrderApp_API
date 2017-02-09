<?php 

// getting status and return to app
if(!isset($_POST["verify"]))
        return;

require_once 'include/Config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

$sql = "SELECT id, status_name as name FROM status";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    // output data of each row
    $type = array("error" => "FALSE");
    while($row = $result->fetch_assoc()) {
        $type[$row["id"]] = $row["name"];
    }
    echo json_encode($type);
} else {
    $type["error"] = TRUE;
    $type["error_msg"] = "Failed getting status data from server";
    echo json_encode($type);
}
$conn->close();

?>

