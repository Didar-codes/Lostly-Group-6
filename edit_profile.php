<?php

session_start();

if(!isset($_SESSION["id"]))
{
    header("Location: login.php");
    exit();
}

include("db.php");
include("lists.php");

$user_id = $_SESSION["id"];

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE ID=?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$username = $user["Username"];
$email = $user["Email"];
$phone = $user["Phone"];
$division = $user["Division"];
$district = $user["District"];
$message = "";
$message_type = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $division = $_POST["division"];
    $district = trim($_POST["district"]);
    $new_password = $_POST["new_password"];
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
        $message = "Select a valid Division";
        $message_type = "error";
    }
    else if(empty($district))
    {
        $message = "Enter District";
        $message_type = "error";
    }
    else if($new_password != "" && strlen($new_password) < 6)
    {
        $message = "New password must be at least 6 characters";
        $message_type = "error";
    }
    else if($new_password != $confirm_password)
    {
        $message = "New passwords do not match";
        $message_type = "error";
    }
    else
    {
        $stmt = mysqli_prepare($conn, "SELECT ID FROM users WHERE (Username=? OR Email=?) AND ID!=?");
        mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $user_id);
        mysqli_stmt_execute($stmt);
        $check_result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($check_result) > 0)
        {
            $message = "Username or email already taken";
            $message_type = "error";
        }
        else
        {
            if($new_password != "")
            {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "UPDATE users SET Username=?, Email=?, Phone=?, Division=?, District=?, Password=? WHERE ID=?");
                mysqli_stmt_bind_param($stmt, "ssssssi", $username, $email, $phone, $division, $district, $hashed, $user_id);
            }
            else
            {
                $stmt = mysqli_prepare($conn, "UPDATE users SET Username=?, Email=?, Phone=?, Division=?, District=? WHERE ID=?");
                mysqli_stmt_bind_param($stmt, "sssssi", $username, $email, $phone, $division, $district, $user_id);
            }

            mysqli_stmt_execute($stmt);
            $_SESSION["username"] = $username;
            $message = "Profile updated successfully";
            $message_type = "success";
        }
    }
}

$initial = strtoupper(substr($username, 0, 1));

?>
<!DOCTYPE html>
<html>
<head>
<title>Lostly - Edit Profile</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body data-page="edit-profile">

<header class="navbar">
    <a href="dashboard.php" class="logo">Lostly</a>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="browse.php">Browse</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="profile-page">
    <div class="profile-shell">

        <div class="profile-heading">
            <p class="small-title">ACCOUNT SETTINGS</p>
            <h1>Edit Profile</h1>
            <p>Keep your contact and location details up to date so other users can reach you when needed.</p>
        </div>

        <div class="profile-card">

            <div class="profile-card-header">

                <div class="profile-avatar">
                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                </div>

                <h2><?php echo htmlspecialchars($username); ?></h2>

                <p>Manage your Lostly account information</p>

            </div>

            <form method="post" class="profile-form">

                <div class="profile-grid">

                    <div class="profile-field">
                        <label>Username</label>
                        <input type="text" name="username"
                               value="<?php echo htmlspecialchars($username); ?>">
                    </div>

                    <div class="profile-field">
                        <label>Email</label>
                        <input type="email" name="email"
                               value="<?php echo htmlspecialchars($email); ?>">
                    </div>

                    <div class="profile-field">
                        <label>Phone Number</label>
                        <input type="text" name="phone"
                               value="<?php echo htmlspecialchars($phone); ?>">
                    </div>

                    <div class="profile-field">
                        <label>Division</label>
                        <select name="division">
                            <?php foreach($divisions as $div) { ?>
                                <option value="<?php echo $div; ?>"
                                    <?php echo $division == $div ? "selected" : ""; ?>>
                                    <?php echo $div; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="profile-field full">
                        <label>District</label>
                        <input type="text" name="district"
                               value="<?php echo htmlspecialchars($district); ?>">
                    </div>

                    <div class="profile-field">
                        <label>New Password</label>
                        <input type="password" name="new_password"
                               placeholder="Leave blank to keep current">
                    </div>

                    <div class="profile-field">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password"
                               placeholder="Repeat new password">
                    </div>

                </div>

                <div class="profile-actions">
                    <a href="dashboard.php" class="btn-secondary">Cancel</a>
                    <input type="submit" value="Save Changes" class="btn">
                </div>

            </form>

            <?php if($message != "") { ?>
                <p class="profile-message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php } ?>

        </div>

    </div>
</main>

<script src="js/main.js"></script>
</body>
</html>
