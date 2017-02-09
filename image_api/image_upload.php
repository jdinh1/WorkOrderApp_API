<?php

header('Content-type : bitmap; charset=utf-8');

if (isset($_POST["encoded_string1"])) {
    $encoded_string = array();
    $image_name = array();

    $encoded_string[0] = $_POST["encoded_string1"];
    $image_name[0] = $_POST["image_name1"];
    
    if (isset($_POST["encoded_string2"])) {
        $encoded_string[1] = $_POST["encoded_string2"];
        $image_name[1] = $_POST["image_name2"];
    
    }

    if (isset($_POST["encoded_string3"])) {
        $encoded_string[2] = $_POST["encoded_string3"];
        $image_name[2] = $_POST["image_name3"];
    }

    for ($i = 0; $i < count($encoded_string); $i++) {
        $decoded_string = base64_decode($encoded_string[$i]);

        $path = 'images/'.$image_name[$i];

        $file = fopen($path, 'wb');

        $is_written = fwrite($file, $decoded_string);
        fclose($file);
    
        if ($is_written > 0) {
            require_once 'include/Config.php';
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
            $stmt = $conn->prepare("INSERT INTO wo_images(name,path,uploaded_at) VALUES(?, ?, NOW())");
            $stmt->bind_param("ss", $image_name[$i],$path);
            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                echo "Success.";
            } else {
                echo "Failed.";
            }
         }
    }
}
?>
