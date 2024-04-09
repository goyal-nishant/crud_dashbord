<?php
	class Customers
	{
		private $servername = "localhost";
		private $username   = "root";
		private $password   = "";
		private $database   = "post";
		public  $con;
		// Database Connection 
		public function __construct() {
			$this->con = new mysqli($this->servername, $this->username, $this->password, $this->database);
			if ($this->con->connect_error) {
				die("Connection failed: " . $this->con->connect_error);
			}
		}
		
		// Insert customer data into customer table
		public function insertData($post)
		{
			// Validate input
			$name = isset($post['name']) ? trim($post['name']) : '';
			$email = isset($post['email']) ? trim($post['email']) : '';
			$username = isset($post['username']) ? trim($post['username']) : '';
			$password = isset($post['password']) ? $_POST['password'] : '';
		
			if (empty($name) || empty($email) || empty($username) || empty($password)) {
				return "All fields are required.";
			}
		
			// Validate email
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				return "Email is not valid";
			}
		
			// Check if email or username already exists
			$query = "SELECT * FROM customers WHERE email = ? OR username = ?";
			$statement = $this->con->prepare($query);
		
			if (!$statement || !$statement->bind_param("ss", $email, $username) || !$statement->execute()) {
				return "Error in checking existing user: " . $statement->error;
			}
		
			$result = $statement->get_result();
			if ($result->num_rows > 0) {
				$statement->close();
				return "User with this email or username already exists.";
			}
		
			$statement->close();
		
			// Hash password securely
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		
			// Insert the user data
			$query = "INSERT INTO customers(name, email, username, password) VALUES (?, ?, ?, ?)";
			$statement = $this->con->prepare($query);
		
			if (!$statement || !$statement->bind_param("ssss", $name, $email, $username, $hashed_password) || !$statement->execute()) {
				return "Error in inserting data: " . $statement->error;
			}
		
			$statement->close();
			return "User registered successfully.";
		}
		


		// Fetch customer records for show listing
		public function displayData()
		{

			$user = $_SESSION['user_name'];
			if($user){

			}
			else{
				header('location:login.php');
			}
		    $query = "SELECT * FROM customers";
		    $result = $this->con->query($query);
		if ($result->num_rows > 0) {
		    $data = array();
		    while ($row = $result->fetch_assoc()) {
		           $data[] = $row;
		    }
			 return $data;
		    }else{
			 echo "No found records";
		    }
		}
		// Fetch single data for edit from customer table
		public function displyaRecordById($id)
		{
			
		    $query = "SELECT * FROM customers WHERE id = '$id'";
		    $result = $this->con->query($query);
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			return $row;
		    }else{
			echo "Record not found";
		    }
		}
		// Update customer data into customer table
		public function updateRecord($postData)
		{
			
			$name = $this->con->real_escape_string($_POST['uname']);
			$email = $this->con->real_escape_string($_POST['uemail']);
			$username = $this->con->real_escape_string($_POST['upname']);
			$id = $this->con->real_escape_string($_POST['id']);

			

			// Validate email with regex
			if (!empty($email) && !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
				echo "Email is not valid";
				return;
			}
			

			if (!empty($id) && !empty($postData)) {
				// Prepare and execute the query
				$query = "UPDATE customers SET name = ?, email = ?, username = ? WHERE id = ?";
				$statement = $this->con->prepare($query);
				$statement->bind_param("sssi", $name, $email, $username, $id);

				if ($statement->execute()) {
					header("Location:index.php?");
				} else {
					echo "Registration update failed. Please try again!";
				}

				$statement->close();
			}
		}

		public function login($post) {
	session_start();
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        echo "Username and password are required.";
        return;
    }

    // Fetch user from database
    $query = "SELECT * FROM customers WHERE username = ?";
    $statement = $this->con->prepare($query);
    $statement->bind_param("s", $username);
    $statement->execute();
    $result = $statement->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
		echo "Stored Hashed Password: " . $user['password'];
    echo "User Provided Password: $password";
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_name'] = $username;
            header("Location: home.php"); // Redirect to dashboard or any other page
            exit;
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }

    $statement->close();
}

	
		
		// Delete customer data from customer table
	

public function deleteRecord($id) {

    $query = "DELETE FROM customers WHERE id = ?";
    $statement = $this->con->prepare($query);
    $statement->bind_param("i", $id);

    if ($statement->execute()) {
        echo "Record deleted successfully";
        header("Location: index.php?msg3=delete");
        exit();
    } else {
        echo "Error: " . $this->con->error;
    }

    $statement->close();
}	
	}
?>