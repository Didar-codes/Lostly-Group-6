<?php

include("db.php");
include("lists.php");

$username = "";
$email = "";
$phone = "";
$division = "";
$district = "";
$password = "";
$confirm_password = "";
$message = "";
$message_type = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $division = $_POST["division"];
    $district = trim($_POST["district"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if(empty($username))
    {
        $message = "Enter Username";
        $message_type = "error";
    }
    else if(empty($email))
    {
        $message = "Enter Email";
        $message_type = "error";
    }
    else if(empty($phone))
    {
        $message = "Enter Phone Number";
        $message_type = "error";
    }
    else if(empty($division) || !in_array($division, $divisions))
    {
        $message = "Select Division";
        $message_type = "error";
    }
    else if(empty($district))
    {
        $message = "Enter District";
        $message_type = "error";
    }
    else if(empty($password))
    {
        $message = "Enter Password";
        $message_type = "error";
    }
    else if(strlen($password) < 6)
    {
        $message = "Password must be at least 6 characters";
        $message_type = "error";
    }
    else if(empty($confirm_password))
    {
        $message = "Confirm Your Password";
        $message_type = "error";
    }
    else if($password != $confirm_password)
    {
        $message = "Passwords do not match";
        $message_type = "error";
    }
    else
    {
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE Email=? OR Username=?");
        mysqli_stmt_bind_param($stmt, "ss", $email, $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) > 0)
        {
            $message = "Username or email already exists";
            $message_type = "error";
        }
        else
        {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn,
                "INSERT INTO users (Username, Email, Phone, Division, District, Password) VALUES (?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, "ssssss", $username, $email, $phone, $division, $district, $hashed_password);

            if(mysqli_stmt_execute($stmt))
            {
                $message = "Registration successful! You can now login.";
                $message_type = "success";
                $username = "";
                $email = "";
                $phone = "";
                $division = "";
                $district = "";
            }
            else
            {
                $message = "Registration failed";
                $message_type = "error";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Lostly - Registration</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body data-page="registration">

<header class="navbar">
    <a href="login.php" class="logo">Lostly</a>
    <nav>
        <a href="login.php">Login</a>
    </nav>
</header>

<main class="auth-container">
<div class="auth-box registration-box">

    <h1>Create Account</h1>
    <p class="subtitle">Join Lostly Bangladesh</p>

    <form method="post">

        <label>Username</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">

        <label>Phone Number</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>">

        <label>Division</label>
        <select name="division">
            <option value="">Select Division</option>
            <?php foreach($divisions as $div) { ?>
            <option value="<?php echo $div; ?>" <?php echo $division == $div ? "selected" : ""; ?>><?php echo $div; ?></option>
            <?php } ?>
        </select>

        <label>District</label>
        <input type="text" name="district" value="<?php echo htmlspecialchars($district); ?>">

        <label>Password</label>
        <input type="password" name="password">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password">

        <input type="submit" value="Create Account" class="btn">

    </form>

    <?php if($message != "") { ?>
    <p class="<?php echo $message_type; ?>-message"><?php echo htmlspecialchars($message); ?></p>
    <?php } ?>

    <p class="switch-link">
        Already have an account? <a href="login.php">Login</a>
    </p>

</div>
</main>

<script src="js/main.js"></script>
</body>
</html>
