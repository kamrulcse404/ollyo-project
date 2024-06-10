<?php

//edit_profile.php

include('database_connection.php');

if(isset($_POST['user_name']))
{
	if($_POST["user_new_password"] != '')
	{
		$query = "
		UPDATE user_details SET 
			user_name = ?, 
			user_email = ?, 
			user_password = ? 
			WHERE user_id = ?
		";
		$statement = $connect->prepare($query);
		$user_password_hashed = password_hash($_POST["user_new_password"], PASSWORD_DEFAULT);
		$statement->bind_param('sssi', $_POST["user_name"], $_POST["user_email"], $user_password_hashed, $_SESSION["user_id"]);
	}
	else
	{
		$query = "
		UPDATE user_details SET 
			user_name = ?, 
			user_email = ?
			WHERE user_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param('ssi', $_POST["user_name"], $_POST["user_email"], $_SESSION["user_id"]);
	}
	
	if ($statement->execute()) {
		echo '<div class="alert alert-success">Profile Edited</div>';
	} else {
		echo '<div class="alert alert-danger">Error editing profile</div>';
	}
}

?>
