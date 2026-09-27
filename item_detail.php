<?php

session_start();

if(!isset($_SESSION["id"]))
{
    header("Location: login.php");
    exit();
}

include("db.php");

$user_id = $_SESSION["id"];
$item_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

// Mark-as-resolved is scoped to the owner's own item at the query level,
// so it can't be used to resolve someone else's report.
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_resolved"]))
{
    $stmt = mysqli_prepare($conn, "UPDATE items SET Status='Resolved' WHERE ID=? AND UserID=?");
    mysqli_stmt_bind_param($stmt, "ii", $item_id, $user_id);
    mysqli_stmt_execute($stmt);

    header("Location: item_detail.php?id=" . $item_id);
    exit();
}

$stmt = mysqli_prepare($conn,
    "SELECT items.*, users.Username, users.Email, users.Phone
     FROM items JOIN users ON items.UserID = users.ID
     WHERE items.ID = ?");
mysqli_stmt_bind_param($stmt, "i", $item_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) != 1)
{
    header("Location: browse.php");
    exit();
}

$item = mysqli_fetch_assoc($result);
$is_owner = ($item["UserID"] == $user_id);

?>
<!DOCTYPE html>
<html>
<head>
<title>Lostly - <?php echo htmlspecialchars($item["ItemName"]); ?></title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="navbar">
    <a href="dashboard.php" class="logo">Lostly</a>
    <nav>
        <a href="browse.php">Browse</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="dashboard-container">
<div class="dashboard-content"> 

<section class="user-section">

    <span class="badge badge-<?php echo strtolower($item["Type"]); ?>"><?php echo $item["Type"]; ?></span>
    <?php if($item["Status"] == "Resolved") { ?>
    <span class="badge badge-resolved">Resolved</span>
    <?php } ?>

    <h1><?php echo htmlspecialchars($item["ItemName"]); ?></h1>

    <?php if($item["ImagePath"]) { ?>
    <img src="<?php echo htmlspecialchars($item["ImagePath"]); ?>" alt="<?php echo htmlspecialchars($item["ItemName"]); ?>" class="detail-photo">
    <?php } ?>

    <div class="user-info">

        <div class="user-info-item">
            <span>Category</span>
            <strong><?php echo htmlspecialchars($item["Category"]); ?></strong>
        </div>

        <div class="user-info-item">
            <span>Date <?php echo $item["Type"] == "Lost" ? "Lost" : "Found"; ?></span>
            <strong><?php echo date("F j, Y", strtotime($item["EventDate"])); ?></strong>
        </div>

        <div class="user-info-item">
            <span>Location</span>
            <strong><?php echo htmlspecialchars($item["District"]); ?>, <?php echo htmlspecialchars($item["Division"]); ?></strong>
        </div>

        <div class="user-info-item">
            <span>Posted By</span>
            <strong><?php echo htmlspecialchars($item["Username"]); ?></strong>
        </div>

    </div>

    <?php if($item["Description"]) { ?>
    <p><?php echo nl2br(htmlspecialchars($item["Description"])); ?></p>
    <?php } ?>

    <?php if($is_owner) { ?>

    <div class="item-owner-actions">
        <a href="report_item.php?id=<?php echo (int)$item["ID"]; ?>" class="dashboard-btn">Edit</a>

        <?php if($item["Status"] == "Active") { ?>
        <form method="post" class="inline-form">
            <input type="hidden" name="mark_resolved" value="1">
            <input type="submit" value="Mark as Resolved" class="dashboard-btn">
        </form>
        <?php } ?>

        <a href="delete_item.php?id=<?php echo (int)$item["ID"]; ?>" class="dashboard-btn js-confirm-delete" data-confirm-message="Delete this report? This cannot be undone.">Delete</a>
    </div>

    <?php } else if($item["Status"] == "Active") { ?>

    <div class="contact-box">
        <h2>Found this item?</h2>
        <p>If you have found this lost item, contact the person who reported it.</p>

        <button type="button" class="dashboard-btn js-show-contact" data-target="contact-info">
            Show Contact Information
        </button>

        <div id="contact-info" class="contact-info" style="display:none;">
            <p><strong>Reporter:</strong> <?php echo htmlspecialchars($item["Username"]); ?></p>
            <p><strong>Phone:</strong> <a href="tel:<?php echo htmlspecialchars($item["Phone"]); ?>"><?php echo htmlspecialchars($item["Phone"]); ?></a></p>
            <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($item["Email"]); ?>"><?php echo htmlspecialchars($item["Email"]); ?></a></p>
        </div>
    </div>

    <?php } ?>

</section>

</div>
</main>

<script src="js/main.js"></script>
</body>
</html>
