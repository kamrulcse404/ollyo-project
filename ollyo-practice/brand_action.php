<?php

//brand_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO brand (category_id, brand_name) 
		VALUES (?, ?)
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("is", $_POST["category_id"], $_POST["brand_name"]);
		if($statement->execute())
		{
			echo 'Brand Name Added';
		}
	}

	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM brand WHERE brand_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("i", $_POST["brand_id"]);
		$statement->execute();
		$result = $statement->get_result();
		$output = $result->fetch_assoc();
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'Edit')
	{
		$query = "
		UPDATE brand SET 
		category_id = ?, 
		brand_name = ? 
		WHERE brand_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("isi", $_POST["category_id"], $_POST["brand_name"], $_POST["brand_id"]);
		if($statement->execute())
		{
			echo 'Brand Name Edited';
		}
	}

	if($_POST['btn_action'] == 'delete')
	{
		$status = ($_POST['status'] == 'active') ? 'inactive' : 'active';
		$query = "
		UPDATE brand 
		SET brand_status = ? 
		WHERE brand_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("si", $status, $_POST["brand_id"]);
		if($statement->execute())
		{
			echo 'Brand status changed to ' . $status;
		}
	}
}

?>
