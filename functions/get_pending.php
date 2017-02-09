<?php
if (!isset($_POST["verify"]))
        return;
require_once 'include/Config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

$sql = "SELECT * FROM complete_view where status_name IN ('Pending') GROUP BY(id)";
$result = $conn->query($sql);

$type = array();
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
                $type[] = $row;
        }
    echo json_encode($type);
} else {
    $type["error"] = TRUE;
    $type["error_msg"] = "Failed getting equipment data from server";
    echo json_encode($type);
}
$conn->close();

?>

