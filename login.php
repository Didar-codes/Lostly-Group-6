<?php

session_start();

include("db.php");

$username = "";
$password = "";
$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = $_POST["username"];
    $password = $_POST["password"];

    if(empty($username))
    {
        $message = "Enter Username";
    }
    else if(empty($password))
    {
        $message = "Enter Password";
    }
    else
    {
        $sql = "SELECT * FROM users WHERE Username=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) == 1)
        {
            $row = mysqli_fetch_assoc($result);

            if(password_verify($password, $row["Password"]))
            {
                $_SESSION["id"] = $row["ID"];
                $_SESSION["username"] = $row["Username"];

                header("Location: dashboard.php");
                exit();
            }
            else
            {
                $message = "Invalid Username or Password";
            }
        }
        else
        {
            $message = "Invalid Username or Password";
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Lostly - Login</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body data-page="login">

<header class="navbar">
    <a href="login.php" class="logo">Lostly</a>
    <nav>
        <a href="registration.php">Register</a>
    </nav>
</header>

<main class="auth-container">
<div class="auth-box">

    <h1>Welcome Back</h1>
    <p class="subtitle">Login to Lostly</p>

    <form method="post">

        <label>Username</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">

        <label>Password</label>
        <input type="password" name="password">

        <input type="submit" value="Login" class="btn">

    </form>

    <?php if($message != "") { ?>
    <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
    <?php } ?>

    <p class="switch-link">
        Don't have an account? <a href="registration.php">Create an account</a>
    </p>

</div>
</main>

<script src="js/main.js"></script>
</body>
</html>
