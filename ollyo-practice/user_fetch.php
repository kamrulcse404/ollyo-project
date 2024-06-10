<?php

// user_fetch.php

include('database_connection.php');

$output = array();
$query = "SELECT * FROM user_details WHERE user_type = 'user' ";

if(isset($_POST["search"]["value"])) {
    $search = $connect->real_escape_string($_POST["search"]["value"]);
    $query .= 'AND (user_email LIKE "%'.$search.'%" ';
    $query .= 'OR user_name LIKE "%'.$search.'%" ';
    $query .= 'OR user_status LIKE "%'.$search.'%") ';
}

$order_column = array('user_id', 'user_email', 'user_name', 'user_status');
$order_dir = isset($_POST["order"]) ? $_POST['order']['0']['dir'] : 'DESC';
$order_col = isset($_POST["order"]) ? $order_column[$_POST['order']['0']['column']] : 'user_id';

$query .= 'ORDER BY '.$order_col.' '.$order_dir.' ';

if($_POST["length"] != -1) {
    $query .= 'LIMIT ' . intval($_POST['start']) . ', ' . intval($_POST['length']);
}

$result = $connect->query($query);

$data = array();
$filtered_rows = $result->num_rows;

while($row = $result->fetch_assoc()) {
    $status = '';
    if($row["user_status"] == 'Active') {
        $status = '<span class="label label-success">Active</span>';
    } else {
        $status = '<span class="label label-danger">Inactive</span>';
    }
    $sub_array = array();
    $sub_array[] = $row['user_id'];
    $sub_array[] = $row['user_email'];
    $sub_array[] = $row['user_name'];
    $sub_array[] = $status;
    $sub_array[] = '<button type="button" name="update" id="'.$row["user_id"].'" class="btn btn-warning btn-xs update">Update</button>';
    $sub_array[] = '<button type="button" name="delete" id="'.$row["user_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["user_status"].'">Delete</button>';
    $data[] = $sub_array;
}

$total_records = get_total_all_records($connect);

$output = array(
    "draw"             => intval($_POST["draw"]),
    "recordsTotal"     => intval($total_records),
    "recordsFiltered"  => intval($filtered_rows),
    "data"             => $data
);

echo json_encode($output);

function get_total_all_records($connect) {
    $query = "SELECT * FROM user_details WHERE user_type='user'";
    $result = $connect->query($query);
    return $result->num_rows;
}

?>
