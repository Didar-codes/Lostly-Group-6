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

$edit_mode = false;
$item_id = 0;
$existing_image = "";

$type = (isset($_GET["type"]) && $_GET["type"] == "found") ? "Found" : "Lost";
$item_name = "";
$category = "";
$description = "";
$division = "";
$district = "";
$event_date = "";
$message = "";

// Load the existing item when editing
if(isset($_GET["id"]))
{
    $item_id = (int)$_GET["id"];

    $stmt = mysqli_prepare($conn, "SELECT * FROM items WHERE ID=? AND UserID=?");
    mysqli_stmt_bind_param($stmt, "ii", $item_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) == 1)
    {
        $item = mysqli_fetch_assoc($result);
        $edit_mode = true;
        $type = $item["Type"];
        $item_name = $item["ItemName"];
        $category = $item["Category"];
        $description = $item["Description"];
        $division = $item["Division"];
        $district = $item["District"];
        $event_date = $item["EventDate"];
        $existing_image = $item["ImagePath"];
    }
    else
    {
        header("Location: dashboard.php");
        exit();
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $type = ($_POST["type"] == "Found") ? "Found" : "Lost";
    $item_name = trim($_POST["item_name"]);
    $category = $_POST["category"];
    $description = trim($_POST["description"]);
    $division = $_POST["division"];
    $district = trim($_POST["district"]);
    $event_date = $_POST["event_date"];
    $item_id = isset($_POST["item_id"]) ? (int)$_POST["item_id"] : 0;
    $edit_mode = $item_id > 0;

    if(empty($item_name))
    {
        $message = "Enter the item name";
    }
    else if(empty($category) || !in_array($category, $categories))
    {
        $message = "Select a valid category";
    }
    else if(empty($division) || !in_array($division, $divisions))
    {
        $message = "Select a valid division";
    }
    else if(empty($district))
    {
        $message = "Enter a district";
    }
    else if(empty($event_date))
    {
        $message = "Select a date";
    }
    else
    {
        $image_path = $existing_image;

        if(isset($_FILES["item_image"]) && $_FILES["item_image"]["error"] == 0)
        {
            $allowed = ["jpg", "jpeg", "png", "webp"];
            $ext = strtolower(pathinfo($_FILES["item_image"]["name"], PATHINFO_EXTENSION));

            if(!in_array($ext, $allowed))
            {
                $message = "Image must be JPG, PNG, or WEBP";
            }
            else if($_FILES["item_image"]["size"] > 5 * 1024 * 1024)
            {
                $message = "Image must be smaller than 5MB";
            }
            else if(!getimagesize($_FILES["item_image"]["tmp_name"]))
            {
                $message = "That file isn't a valid image";
            }
            else
            {
                $upload_dir = __DIR__ . "/uploads/items/";

                if(!is_dir($upload_dir))
                {
                    mkdir($upload_dir, 0755, true);
                }

                $new_name = uniqid("item_", true) . "." . $ext;

                if(move_uploaded_file($_FILES["item_image"]["tmp_name"], $upload_dir . $new_name))
                {
                    $image_path = "uploads/items/" . $new_name;
                }
                else
                {
                    $message = "Image upload failed, please try again";
                }
            }
        }

        if($message == "")
        {
            if($edit_mode)
            {
                $stmt = mysqli_prepare($conn,
                    "UPDATE items SET Type=?, ItemName=?, Category=?, Description=?, Division=?, District=?, EventDate=?, ImagePath=? WHERE ID=? AND UserID=?");
                mysqli_stmt_bind_param($stmt, "ssssssssii", $type, $item_name, $category, $description, $division, $district, $event_date, $image_path, $item_id, $user_id);
            }
            else
            {
                $stmt = mysqli_prepare($conn,
                    "INSERT INTO items (UserID, Type, ItemName, Category, Description, Division, District, EventDate, ImagePath) VALUES (?,?,?,?,?,?,?,?,?)");
                mysqli_stmt_bind_param($stmt, "issssssss", $user_id, $type, $item_name, $category, $description, $division, $district, $event_date, $image_path);
            }

            mysqli_stmt_execute($stmt);

            header("Location: dashboard.php");
            exit();
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Lostly - <?php echo $edit_mode ? "Edit Item" : "Report Item"; ?></title>
<link rel="stylesheet" href="css/style.css">
</head>
<body data-page="report-item">

<header class="navbar">
    <a href="dashboard.php" class="logo">Lostly</a>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="browse.php">Browse</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="auth-container">
<div class="auth-box form-box">

    <h1><?php echo $edit_mode ? "Edit Item" : "Report an Item"; ?></h1>
    <p class="subtitle">
        <?php echo $type == "Lost" ? "Let others help you find it" : "Help return it to its owner"; ?>
    </p>

    <form method="post" enctype="multipart/form-data">

        <input type="hidden" name="item_id" value="<?php echo (int)$item_id; ?>">

        <label>Type</label>
        <select name="type">
            <option value="Lost" <?php echo $type == "Lost" ? "selected" : ""; ?>>Lost</option>
            <option value="Found" <?php echo $type == "Found" ? "selected" : ""; ?>>Found</option>
        </select>

        <label>Item Name</label>
        <input type="text" name="item_name" value="<?php echo htmlspecialchars($item_name); ?>">

        <label>Category</label>
        <select name="category">
            <option value="">Select Category</option>
            <?php foreach($categories as $cat) { ?>
            <option value="<?php echo $cat; ?>" <?php echo $category == $cat ? "selected" : ""; ?>><?php echo $cat; ?></option>
            <?php } ?>
        </select>

        <label>Description</label>
        <textarea name="description" rows="4" class="textarea-input"><?php echo htmlspecialchars($description); ?></textarea>

        <label>Division</label>
        <select name="division">
            <option value="">Select Division</option>
            <?php foreach($divisions as $div) { ?>
            <option value="<?php echo $div; ?>" <?php echo $division == $div ? "selected" : ""; ?>><?php echo $div; ?></option>
            <?php } ?>
        </select>

        <label>District</label>
        <input type="text" name="district" value="<?php echo htmlspecialchars($district); ?>">

        <label>Date <?php echo $type == "Lost" ? "Lost" : "Found"; ?></label>
        <input type="date" name="event_date" value="<?php echo htmlspecialchars($event_date); ?>">

        <label>Photo (optional)</label>
        <input type="file" name="item_image" accept="image/png, image/jpeg, image/webp">

        <?php if($existing_image != "") { ?>
        <img src="<?php echo htmlspecialchars($existing_image); ?>" alt="Current photo" class="current-photo-preview">
        <?php } ?>

        <input type="submit" value="<?php echo $edit_mode ? "Save Changes" : "Post Item"; ?>" class="btn">

    </form>

    <?php if($message != "") { ?>
    <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
    <?php } ?>

    <p class="switch-link">
        <a href="dashboard.php">Cancel and go back</a>
    </p>

</div>
</main>

<script src="js/main.js"></script>
</body>
</html>
