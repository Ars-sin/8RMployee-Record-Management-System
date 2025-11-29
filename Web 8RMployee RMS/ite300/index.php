<?php include 'config.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>User List</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-image: url('cat.png'); /* Change to your image path */
        background-size: contain;      /* Cover whole screen */
        background-position: center; /* Center the image */
        background-repeat: no-repeat;
        background-attachment: fixed; /* So it doesn't scroll with the content */
        color: #fff; /* White text for better contrast */
        margin: 20px;
    }
    h2 {
        color: #fff;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
    }
    a {
        color: #ffd700;
        text-decoration: none;
        font-weight: bold;
    }
    a:hover {
        text-decoration: underline;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background-color: rgba(0,0,0,0.6); /* Semi-transparent black background for table */
        color: #fff;
        margin-top: 15px;
        border-radius: 8px;
        overflow: hidden;
    }
    th, td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }
    th {
        background-color: rgba(0,0,0,0.8);
    }
    tr:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }
</style>
</head>
<body>

<h2>User List</h2>
<a href="register.php">Add New User</a>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>

<?php  
$result = mysqli_query($conn, "SELECT * FROM users");
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['name']}</td>
        <td>{$row['email']}</td>
        <td>
            <a href='update.php?id={$row['id']}'>Edit</a> |
            <a href='delete.php?id={$row['id']}'>Delete</a>
        </td>
        </tr>";
}
?>
</table>

</body>
</html>
