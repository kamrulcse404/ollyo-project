<?php

// category_fetch.php

include('database_connection.php');

$query = "SELECT * FROM category ";
$searchValue = '';

if(isset($_POST["search"]["value"])) {
    $searchValue = $connect->real_escape_string($_POST["search"]["value"]);
    $query .= 'WHERE category_name LIKE "%'.$searchValue.'%" ';
    $query .= 'OR category_status LIKE "%'.$searchValue.'%" ';
}

$order_column = array('category_id', 'category_name', 'category_status');
$order_dir = isset($_POST["order"]) ? $_POST['order']['0']['dir'] : 'DESC';
$order_col = isset($_POST["order"]) ? $order_column[$_POST['order']['0']['column']] : 'category_id';

$query .= 'ORDER BY '.$order_col.' '.$order_dir.' ';

if($_POST["length"] != -1) {
    $query .= 'LIMIT ' . intval($_POST['start']) . ', ' . intval($_POST['length']);
}

$result = $connect->query($query);

$data = array();
$filtered_rows = $result->num_rows;

while($row = $result->fetch_assoc()) {
    $status = '';
    if($row['category_status'] == 'active') {
        $status = '<span class="label label-success">Active</span>';
    } else {
        $status = '<span class="label label-danger">Inactive</span>';
    }
    $sub_array = array();
    $sub_array[] = $row['category_id'];
    $sub_array[] = $row['category_name'];
    $sub_array[] = $status;
    $sub_array[] = '<button type="button" name="update" id="'.$row["category_id"].'" class="btn btn-warning btn-xs update">Update</button>';
    $sub_array[] = '<button type="button" name="delete" id="'.$row["category_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["category_status"].'">Delete</button>';
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
    $query = "SELECT * FROM category";
    $result = $connect->query($query);
    return $result->num_rows;
}

?>
