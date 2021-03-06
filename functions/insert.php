<?php

if (isset($_POST["encoded_string1"]) && isset($_POST["building"])
        && isset($_POST["priority"]) && isset($_POST["problem"])
        && isset($_POST["equipment"]) && isset($_POST["user_id"]))
{
    $encoded_string = array();
    $image_name = array();
    $building = $_POST["building"];
    $priority = $_POST["priority"];
    $problem = $_POST["problem"];
    $equipment = $_POST["equipment"];
    $encoded_string[0] = $_POST["encoded_string1"];
    $image_name[0] = $_POST["image_name1"];
    $user_id = $_POST["user_id"];
    if (isset($_POST["encoded_string2"]))
    {
        $encoded_string[1] = $_POST["encoded_string2"];
        $image_name[1] = $_POST["image_name2"];

    }

    if (isset($_POST["encoded_string3"]))
    {
        $encoded_string[2] = $_POST["encoded_string3"];
        $image_name[2] = $_POST["image_name3"];
    }

    $mystring = NULL;
    if (isset($_POST["description"])) {
        $mystring = $_POST["description"];
    } else {
        $mystring = "N/A";
    }

    $reposnse = array("error" => FALSE);

    // inserting into problem table
    require_once 'include/Config.php';
    try
    {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);


        $stmt = $conn->prepare("INSERT INTO problem(problem_description, type_of_problem_id, room_id, equipment_id, priority_id) VALUES(?,?,?,?,?)");
        $siiii = 'siiii';
        $stmt->bind_param($siiii, $mystring, $problem ,$building,$equipment,$priority);
        $result = $stmt->execute();
        $problem_lastid = $stmt->insert_id;
        if ($result)
        {
            // $stmt->close();
            for ($i = 0; $i < count($encoded_string); $i++)
            {
                $decoded_string = base64_decode($encoded_string[$i]);

                $path = '../image_api/images/'.$image_name[$i];

                $file = fopen($path, 'wb');

                $is_written = fwrite($file, $decoded_string);
                fclose($file);

                if ($is_written > 0)
                {
                    $stmt = $conn->prepare("INSERT INTO wo_images(image_name,path,uploaded_at) VALUES(?, ?, NOW())");
                    $stmt->bind_param("ss", $image_name[$i],$path);
                    $result = $stmt->execute();
                    $image_lastid = $stmt->insert_id;
                    if ($result)
                    {
                        //$stmt = $conn->query("INSERT INTO problem_images(problem_id, wo_images_id) VALUES(LAST_INSERT_ID(), )");
                        $stmt = $conn->prepare("INSERT INTO problem_images(problem_id, wo_images_id) VALUES(?,?)");
                        $ii = 'ii';
                        $stmt->bind_param($ii, $problem_lastid, $image_lastid);
                        $result = $stmt->execute();
                        if ($result) {
                        } else {
                            $response["error"] = TRUE;
                            $response["error_msg"] = "Failed adding work order.1";
                            $conn->rollback();
                            $stmt->close();
                        }
                    }
                    else
                    {
                        $response["error"] = TRUE;
                        $response["error_msg"] = "Failed adding work order.2";
                        $conn->rollback();
                    }
                    $stmt->close();
                }
            }
            if ($result)
            {
                $stmt = $conn->prepare("INSERT INTO work_order(created_date,problem_id, status_id) VALUES (NOW(),?,1)");
                $stmt->bind_param("i",$problem_lastid);
                $result = $stmt->execute();
                $work_order_lastid = $stmt->insert_id;

                if ($result)
                {
                    $stmt = $conn->prepare("INSERT INTO created(work_order_id, user_id, create_date) VALUES (?,$user_id,NOW())");
                    $stmt->bind_param("i",$work_order_lastid);
                    $result = $stmt->execute();
                    if ($result) {
                        $response["error"] = FALSE;
                        $response["success_msg"] = "Sucessfully added work order.3";
                    }
                }
            }
            else
            {
                $response["error"] = TRUE;
                $response["error_msg"] = "Failed adding work order.4";
                $conn->rollback();
                echo "Failed.";
            }

        }
        else
        {
            $response["error"] = TRUE;
            $response["error_msg"] = "Failed adding work order.5";
            $conn->rollback();
            $stmt->close();
        }
    }
    catch(mysqli_sql_exception $exception)
    {
        $conn->rollback();
        $stmt->close();
    }

    echo json_encode($response);

    /*
    // inserting to images table
    $query1 = mysql_query();
    $query2 = mysql_query();
    $query3 = mysql_query();

    // inserting to problem table
    $query4 = mysql_query();
    // inserting to problem_images
    $query5 = mysql_query();
    // inserting to work_order
    $query6 = mysql_query();
    // inserting to create

    if ($query1 && $query2 && $query3 && $query4 && $query5 && $query6) {
    	commit();
    	$sucess = true;
    } else {
    	rollback();
    	$success = false;
    }

    if ($success) {
    	echo "Success.";
    } else {
    	echo "Failed.";
    }




    function begin() {
    	mysql_query("BEGIN");
    }

    function commit() {
    	mysql_query("COMMIT");
    }

    function rollback() {
    	mysql_query("ROLLBACK");
    }*/
}
?>
