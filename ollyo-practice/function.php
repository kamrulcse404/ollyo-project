<?php
//function.php

// function fill_category_list($connect)
// {
// 	$query = "
// 	SELECT * FROM category 
// 	WHERE category_status = 'active' 
// 	ORDER BY category_name ASC
// 	";
// 	$statement = $connect->prepare($query);
// 	$statement->execute();
// 	$result = $statement->fetchAll();
// 	$output = '';
// 	foreach ($result as $row) {
// 		$output .= '<option value="' . $row["category_id"] . '">' . $row["category_name"] . '</option>';
// 	}
// 	return $output;
// }

// solve 
function fill_category_list($connect)
{
    $query = "SELECT * FROM category WHERE category_status = 'active' ORDER BY category_name ASC";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->get_result();
    $output = '';
    while ($row = $result->fetch_assoc()) {
        $output .= '<option value="' . $row["category_id"] . '">' . $row["category_name"] . '</option>';
    }
    return $output;
}


// function fill_brand_list($connect, $category_id)
// {
// 	$query = "SELECT * FROM brand 
// 	WHERE brand_status = 'active' 
// 	AND category_id = '" . $category_id . "'
// 	ORDER BY brand_name ASC";
// 	$statement = $connect->prepare($query);
// 	$statement->execute();
// 	$result = $statement->fetchAll();
// 	$output = '<option value="">Select Brand</option>';
// 	foreach ($result as $row) {
// 		$output .= '<option value="' . $row["brand_id"] . '">' . $row["brand_name"] . '</option>';
// 	}
// 	return $output;
// }

// solve 
function fill_brand_list($connect, $category_id)
{
	$output = '<option value="">Select Brand</option>';

	// Use prepared statements to prevent SQL injection
	$query = "SELECT brand_id, brand_name FROM brand WHERE brand_status = 'active' AND category_id = ?";
	$statement = $connect->prepare($query);
	$statement->bind_param("i", $category_id); // 'i' denotes integer parameter type
	$statement->execute();
	$result = $statement->get_result();

	// Generate options for brand list
	while ($row = $result->fetch_assoc()) {
		$output .= '<option value="' . $row["brand_id"] . '">' . $row["brand_name"] . '</option>';
	}

	return $output;
}



// function get_user_name($connect, $user_id)
// {
// 	$query = "
// 	SELECT user_name FROM user_details WHERE user_id = '" . $user_id . "'
// 	";
// 	$statement = $connect->prepare($query);
// 	$statement->execute();
// 	$result = $statement->fetchAll();
// 	foreach ($result as $row) {
// 		return $row['user_name'];
// 	}
// }

// solve 
function get_user_name($connect, $user_id)
{
	$query = "SELECT user_name FROM user_details WHERE user_id = ?";
	$statement = $connect->prepare($query);
	$statement->bind_param("i", $user_id); // 'i' denotes integer parameter type
	$statement->execute();
	$result = $statement->get_result();

	// Fetch and return the user name
	if ($row = $result->fetch_assoc()) {
		return $row['user_name'];
	} else {
		return null; // Return null if no user is found with the provided ID
	}
}


// function fill_product_list($connect)
// {
// 	$query = "
// 	SELECT * FROM product 
// 	WHERE product_status = 'active' 
// 	ORDER BY product_name ASC
// 	";
// 	$statement = $connect->prepare($query);
// 	$statement->execute();
// 	$result = $statement->fetchAll();
// 	$output = '';
// 	foreach ($result as $row) {
// 		$output .= '<option value="' . $row["product_id"] . '">' . $row["product_name"] . '</option>';
// 	}
// 	return $output;
// }

// solve 
function fill_product_list($connect)
{
	$output = '';

	// Use prepared statements to prevent SQL injection
	$query = "SELECT product_id, product_name FROM product WHERE product_status = 'active' ORDER BY product_name ASC";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->get_result();

	// Fetch product list
	while ($row = $result->fetch_assoc()) {
		$output .= '<option value="' . $row["product_id"] . '">' . $row["product_name"] . '</option>';
	}

	return $output;
}


// function fetch_product_details($product_id, $connect)
// {
// 	$query = "
// 	SELECT * FROM product 
// 	WHERE product_id = '" . $product_id . "'";
// 	$statement = $connect->prepare($query);
// 	$statement->execute();
// 	$result = $statement->fetchAll();
// 	foreach ($result as $row) {
// 		$output['product_name'] = $row["product_name"];
// 		$output['quantity'] = $row["product_quantity"];
// 		$output['price'] = $row['product_base_price'];
// 		$output['tax'] = $row['product_tax'];
// 	}
// 	return $output;
// }

// solve 
function fetch_product_details($product_id, $connect)
{
	$output = array(); // Initialize output array

	// Use prepared statements to prevent SQL injection
	$query = "SELECT * FROM product WHERE product_id = ?";
	$statement = $connect->prepare($query);
	$statement->bind_param("i", $product_id);
	$statement->execute();
	$result = $statement->get_result();

	// Fetch product details
	if ($row = $result->fetch_assoc()) {
		$output['product_name'] = $row["product_name"];
		$output['quantity'] = $row["product_quantity"];
		$output['price'] = $row['product_base_price'];
		$output['tax'] = $row['product_tax'];
	}

	return $output;
}



// function available_product_quantity($connect, $product_id)
// {
// 	$product_data = fetch_product_details($product_id, $connect);
// 	$query = "
// 	SELECT 	inventory_order_product.quantity FROM inventory_order_product 
// 	INNER JOIN inventory_order ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
// 	WHERE inventory_order_product.product_id = '" . $product_id . "' AND
// 	inventory_order.inventory_order_status = 'active'
// 	";
// 	$statement = $connect->prepare($query);
// 	$statement->execute();
// 	$result = $statement->fetchAll();
// 	$total = 0;
// 	foreach ($result as $row) {
// 		$total = $total + $row['quantity'];
// 	}
// 	$available_quantity = intval($product_data['quantity']) - intval($total);
// 	if ($available_quantity == 0) {
// 		$update_query = "
// 		UPDATE product SET 
// 		product_status = 'inactive' 
// 		WHERE product_id = '" . $product_id . "'
// 		";
// 		$statement = $connect->prepare($update_query);
// 		$statement->execute();
// 	}
// 	return $available_quantity;
// }

// solve 
function available_product_quantity($connect, $product_id)
{
	// Fetch product details using a prepared statement
	$query = "SELECT quantity FROM product WHERE product_id = ?";
	$statement = $connect->prepare($query);
	$statement->bind_param("i", $product_id);
	$statement->execute();
	$result = $statement->get_result();
	$product_data = $result->fetch_assoc();

	// Calculate available quantity
	$query = "SELECT SUM(quantity) AS total_quantity 
              FROM inventory_order_product 
              INNER JOIN inventory_order ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
              WHERE inventory_order_product.product_id = ? AND inventory_order.inventory_order_status = 'active'";
	$statement = $connect->prepare($query);
	$statement->bind_param("i", $product_id);
	$statement->execute();
	$result = $statement->get_result();
	$row = $result->fetch_assoc();
	$total = $row['total_quantity'];
	$available_quantity = intval($product_data['quantity']) - intval($total);

	// If available quantity is 0, update product status to inactive
	if ($available_quantity == 0) {
		$update_query = "UPDATE product SET product_status = 'inactive' WHERE product_id = ?";
		$statement = $connect->prepare($update_query);
		$statement->bind_param("i", $product_id);
		$statement->execute();
	}

	return $available_quantity;
}


// solve 
function count_total_user($connect)
{
	$query = "SELECT * FROM user_details WHERE user_status = 'active'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$statement->store_result();
	return $statement->num_rows;
}

// solve 
function count_total_category($connect)
{
	$query = "SELECT * FROM category WHERE category_status='active'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$statement->store_result();
	return $statement->num_rows;
}

// solve 
function count_total_brand($connect)
{
	$query = "SELECT * FROM brand WHERE brand_status='active'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$statement->store_result();
	return $statement->num_rows;
}


// solve 
function count_total_product($connect)
{
	$query = "SELECT * FROM product WHERE product_status='active'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$statement->store_result();
	return $statement->num_rows;
}

// solve 
function count_total_order_value($connect)
{
	$query = "SELECT SUM(inventory_order_total) AS total_order_value FROM inventory_order WHERE inventory_order_status='active'";

	if ($_SESSION['type'] == 'user') {
		$query .= ' AND user_id = "' . $_SESSION["user_id"] . '"';
	}

	$statement = $connect->prepare($query);
	$statement->execute();

	$result = $statement->get_result()->fetch_all(MYSQLI_ASSOC);

	foreach ($result as $row) {
		return number_format($row['total_order_value'], 2);
	}
}


// solve 
function count_total_cash_order_value($connect)
{
	$query = "
        SELECT sum(inventory_order_total) as total_order_value 
        FROM inventory_order 
        WHERE payment_status = 'cash' 
        AND inventory_order_status='active'
    ";
	if ($_SESSION['type'] == 'user') {
		$query .= ' AND user_id = "' . $_SESSION["user_id"] . '"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->get_result(); // Get the result set
	$row = $result->fetch_assoc(); // Fetch the result row
	if ($row) {
		return number_format($row['total_order_value'], 2);
	} else {
		return 0; // Return 0 if no result found
	}
}


// solve 
function count_total_credit_order_value($connect)
{

	$query = "SELECT SUM(inventory_order_total) as total_order_value FROM inventory_order WHERE payment_status = 'credit' AND inventory_order_status = 'active'";
	if ($_SESSION['type'] == 'user') {
		$query .= " AND user_id = ?";
	}

	$statement = $connect->prepare($query);
	if ($_SESSION['type'] == 'user') {
		$statement->bind_param('i', $_SESSION['user_id']);
	}
	$statement->execute();
	$result = $statement->get_result();
	$row = $result->fetch_assoc();
	return number_format($row['total_order_value'], 2);
}


// solve 
function get_user_wise_total_order($connect)
{
	// SQL query to fetch the data
	$query = '
    SELECT 
        SUM(inventory_order.inventory_order_total) as order_total, 
        SUM(CASE WHEN inventory_order.payment_status = "cash" THEN inventory_order.inventory_order_total ELSE 0 END) AS cash_order_total, 
        SUM(CASE WHEN inventory_order.payment_status = "credit" THEN inventory_order.inventory_order_total ELSE 0 END) AS credit_order_total, 
        user_details.user_name 
    FROM inventory_order 
    INNER JOIN user_details ON user_details.user_id = inventory_order.user_id 
    WHERE inventory_order.inventory_order_status = "active" 
    GROUP BY inventory_order.user_id';

	// Prepare the statement
	$statement = $connect->prepare($query);
	$statement->execute();

	// Get the result
	$result = $statement->get_result();

	// Initialize the output HTML
	$output = '
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <tr>
                <th>User Name</th>
                <th>Total Order Value</th>
                <th>Total Cash Order</th>
                <th>Total Credit Order</th>
            </tr>';

	// Initialize totals
	$total_order = 0;
	$total_cash_order = 0;
	$total_credit_order = 0;

	// Fetch data and generate the output rows
	while ($row = $result->fetch_assoc()) {
		$output .= '
        <tr>
            <td>' . htmlspecialchars($row['user_name']) . '</td>
            <td align="right">$ ' . number_format($row["order_total"], 2) . '</td>
            <td align="right">$ ' . number_format($row["cash_order_total"], 2) . '</td>
            <td align="right">$ ' . number_format($row["credit_order_total"], 2) . '</td>
        </tr>';

		$total_order += $row["order_total"];
		$total_cash_order += $row["cash_order_total"];
		$total_credit_order += $row["credit_order_total"];
	}

	// Add the totals row
	$output .= '
    <tr>
        <td align="right"><b>Total</b></td>
        <td align="right"><b>$ ' . number_format($total_order, 2) . '</b></td>
        <td align="right"><b>$ ' . number_format($total_cash_order, 2) . '</b></td>
        <td align="right"><b>$ ' . number_format($total_credit_order, 2) . '</b></td>
    </tr></table></div>';

	// Return the generated HTML
	return $output;
}
