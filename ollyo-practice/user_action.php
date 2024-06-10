<?php

// user_action.php

include('database_connection.php');

if(isset($_POST['btn_action'])) {
    if($_POST['btn_action'] == 'Add') {
        $query = "
        INSERT INTO user_details (user_email, user_password, user_name, user_type, user_status) 
        VALUES (?, ?, ?, ?, ?)
        ";    
        $statement = $connect->prepare($query);
        $user_email = $_POST["user_email"];
        $user_password = password_hash($_POST["user_password"], PASSWORD_DEFAULT);
        $user_name = $_POST["user_name"];
        $user_type = 'user';
        $user_status = 'active';
        $statement->bind_param('sssss', $user_email, $user_password, $user_name, $user_type, $user_status);
        $statement->execute();
        echo 'New User Added';
    }
    elseif($_POST['btn_action'] == 'fetch_single') {
        $query = "
        SELECT * FROM user_details WHERE user_id = ?
        ";
        $statement = $connect->prepare($query);
        $user_id = $_POST["user_id"];
        $statement->bind_param('i', $user_id);
        $statement->execute();
        $result = $statement->get_result();
        $row = $result->fetch_assoc();
        echo json_encode($row);
    }
    elseif($_POST['btn_action'] == 'Edit') {
        $query = ($_POST['user_password'] != '') ? "
            UPDATE user_details 
            SET 
                user_name = ?, 
                user_email = ?,
                user_password = ?
            WHERE user_id = ?
        " : "
            UPDATE user_details 
            SET 
                user_name = ?, 
                user_email = ?
            WHERE user_id = ?
        ";
        $statement = $connect->prepare($query);
        $user_name = $_POST["user_name"];
        $user_email = $_POST["user_email"];
        $user_id = $_POST["user_id"];
        if($_POST['user_password'] != '') {
            $hashed_password = password_hash($_POST["user_password"], PASSWORD_DEFAULT);
            $statement->bind_param('sssi', $user_name, $user_email, $hashed_password, $user_id);
        } else {
            $statement->bind_param('ssi', $user_name, $user_email, $user_id);
        }
        $statement->execute();
        echo 'User Details Edited';
    }
    elseif($_POST['btn_action'] == 'delete') {
        $status = ($_POST['status'] == 'Active') ? 'Inactive' : 'Active';
        $query = "
        UPDATE user_details 
        SET user_status = ? 
        WHERE user_id = ?
        ";
        $statement = $connect->prepare($query);
        $user_id = $_POST["user_id"];
        $statement->bind_param('si', $status, $user_id);
        $statement->execute();
        echo 'User Status change to ' . $status;
    }
}

?>
