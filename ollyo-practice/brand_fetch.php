<?php

//brand_fetch.php

include('database_connection.php');

$query = '';

$output = array();
$query .= "
SELECT brand.brand_id, brand.brand_name, brand.brand_status, category.category_name 
FROM brand 
INNER JOIN category ON category.category_id = brand.category_id 
";

if(isset($_POST["search"]["value"]))
{
    $query .= 'WHERE brand.brand_name LIKE ? ';
    $query .= 'OR category.category_name LIKE ? ';
    $query .= 'OR brand.brand_status LIKE ? ';
}

if(isset($_POST["order"]))
{
    $query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
    $query .= 'ORDER BY brand.brand_id DESC ';
}

if($_POST["length"] != -1)
{
    $query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);

if(isset($_POST["search"]["value"]))
{
    $search = "%".$_POST["search"]["value"]."%";
    $statement->bind_param("sss", $search, $search, $search);
}

$statement->execute();

$result = $statement->get_result();

$data = array();

$filtered_rows = $result->num_rows;

while($row = $result->fetch_assoc())
{
    $status = '';
    if($row['brand_status'] == 'active')
    {
        $status = '<span class="label label-success">Active</span>';
    }
    else
    {
        $status = '<span class="label label-danger">Inactive</span>';
    }
    $sub_array = array();
    $sub_array[] = $row['brand_id'];
    $sub_array[] = $row['category_name'];
    $sub_array[] = $row['brand_name'];
    $sub_array[] = $status;
    $sub_array[] = '<button type="button" name="update" id="'.$row["brand_id"].'" class="btn btn-warning btn-xs update">Update</button>';
    $sub_array[] = '<button type="button" name="delete" id="'.$row["brand_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["brand_status"].'">Delete</button>';
    $data[] = $sub_array;
}

function get_total_all_records($connect)
{
    $statement = $connect->prepare('SELECT * FROM brand');
    $statement->execute();
    $result = $statement->get_result();
    return $result->num_rows;
}

$output = array(
    "draw"              =>  intval($_POST["draw"]),
    "recordsTotal"      =>  get_total_all_records($connect),
    "recordsFiltered"   =>  $filtered_rows,
    "data"              =>  $data
);

echo json_encode($output);
?>