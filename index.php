<?php

$host = "mysql-master";
$user = "root";
$password = "root123";
$database = "testing123";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}


// Create table
$conn->query("
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100)
)
");


// INSERT
if(isset($_POST['add'])){

    $name = $_POST['name'];
    $email = $_POST['email'];

    $sql = "INSERT INTO users(name,email)
            VALUES('$name','$email')";

    if($conn->query($sql)){
        echo "User Added Successfully <br>";
    }

}


// UPDATE
if(isset($_POST['update'])){

    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];


    $sql = "UPDATE users 
            SET name='$name', email='$email'
            WHERE id=$id";


    if($conn->query($sql)){
        echo "User Updated Successfully <br>";
    }

}



// DELETE
if(isset($_POST['delete'])){

    $id = $_POST['id'];

    $sql = "DELETE FROM users WHERE id=$id";


    if($conn->query($sql)){
        echo "User Deleted Successfully <br>";
    }

}



?>


<h2>PHP MySQL CRUD Test</h2>


<h3>Add User</h3>

<form method="post">

Name:
<input type="text" name="name">

Email:
<input type="text" name="email">

<button name="add">
Add
</button>

</form>



<hr>


<h3>Update User</h3>

<form method="post">

ID:
<input type="number" name="id">

New Name:
<input type="text" name="name">

New Email:
<input type="text" name="email">


<button name="update">
Update
</button>


</form>



<hr>


<h3>Delete User</h3>


<form method="post">

ID:
<input type="number" name="id">


<button name="delete">
Delete
</button>


</form>



<hr>



<h3>Users Data</h3>


<?php


$result = $conn->query("SELECT * FROM users");


while($row = $result->fetch_assoc()){


echo "

ID : ".$row['id']." <br>

Name : ".$row['name']." <br>

Email : ".$row['email']." 

<hr>

";


}


$conn->close();


?>
