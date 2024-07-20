<?php

class Customers
{
    private $servername = "localhost";
    private $username   = "root";
    private $password   = "";
    private $database   = "cat_post";
    public $con;

    // Database Connection 
    public function __construct()
    {
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

        if (!$statement || !$statement->bind_param("ssss", $name, $email, $username, $password) || !$statement->execute()) {
            return "Error in inserting data: " . $statement->error;
        }

        $statement->close();
        return "User registered successfully.";
    }

    // Fetch customer records for show listing
    public function displayData()
    {
        session_start();
        $user = $_SESSION['user_name'];
        if (!$user) {
            header('location:login.php');
            exit();
        }

        $query = "SELECT * FROM customers";
        $result = $this->con->query($query);
        if ($result->num_rows > 0) {
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        } else {
            return "No records found";
        }
    }

    // Fetch single data for edit from customer table
    public function displayRecordById($id)
    {
        $query = "SELECT * FROM customers WHERE id = ?";
        $statement = $this->con->prepare($query);
        $statement->bind_param("i", $id);
        $statement->execute();
        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return "Record not found";
        }
    }

    // Update customer data into customer table
    public function updateRecord($postData)
    {
        $name = isset($postData['uname']) ? $this->con->real_escape_string($postData['uname']) : '';
        $email = isset($postData['uemail']) ? $this->con->real_escape_string($postData['uemail']) : '';
        $username = isset($postData['upname']) ? $this->con->real_escape_string($postData['upname']) : '';
        $id = isset($postData['id']) ? $this->con->real_escape_string($postData['id']) : '';

        // Validate email with regex
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Email is not valid";
        }

        if (!empty($id) && !empty($postData)) {
            // Prepare and execute the query
            $query = "UPDATE customers SET name = ?, email = ?, username = ? WHERE id = ?";
            $statement = $this->con->prepare($query);
            $statement->bind_param("sssi", $name, $email, $username, $id);

            if ($statement->execute()) {
                header("Location:index.php?");
                exit();
            } else {
                return "Registration update failed. Please try again!";
            }
        }
    }

	public function login($post)
{
    error_reporting(E_ALL); 
    ini_set('display_errors', 1);
    session_start();
    
    $username = isset($post['username']) ? trim($post['username']) : '';
    $password = isset($post['password']) ? $post['password'] : '';

    if (empty($username) || empty($password)) {
        return "Username and password are required.";
    }

	$sql = "SELECT * FROM customers WHERE username = '$username'";
	$result = mysqli_query($this->con,$sql);
	$total = mysqli_num_rows($result);
	echo $total;

    if($total>0){
		$data = mysqli_fetch_assoc($result);
        if($username == $data['username'] || $password == $data['password']){
		    $_SESSION['user_name'] = $username;
			header('location:home.php');
		}
	}
    // Fetch user from database
    // $query = "SELECT * FROM customers WHERE username = ?";
    // $statement = $this->con->prepare($query);
    // $statement->bind_param("s", $username);
    // $statement->execute();
    // $result = $statement->get_result();

    // if ($result->num_rows == 1) {
    //     $user = $result->fetch_assoc();
    //     if ($password === $user['password']) { // Compare plaintext passwords
    //         // Set session variables
    //         $_SESSION['user_name'] = $username;
    //         session_regenerate_id(true); // Regenerate session ID for security
    //         header("Location: home.php"); // Redirect to dashboard or any other page
    //         exit; // Exit to prevent further code execution
    //     } else {
    //         return "Invalid password.";
    //     }
    // } else {
    //     return "User not found.";
    // }

    // $statement->close();
}

    // Delete customer data from customer table
    public function deleteRecord($id)
    {
        $query = "DELETE FROM customers WHERE id = ?";
        $statement = $this->con->prepare($query);
        $statement->bind_param("i", $id);

        if ($statement->execute()) {
            return "Record deleted successfully";
            header("Location: index.php?msg3=delete");
            exit();
        } else {
            return "Error: " . $this->con->error;
        }

        $statement->close();
    }
}

?>
