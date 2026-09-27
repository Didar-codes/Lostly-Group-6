<?php

session_start();

if(!isset($_SESSION["id"]))
{
    header("Location: login.php");
    exit();
}

include("db.php");

$user_id = $_SESSION["id"];

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE ID=?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 1)
{
    $user = mysqli_fetch_assoc($result);
}
else
{
    session_destroy();
    header("Location: login.php");
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT * FROM items WHERE UserID=? ORDER BY CreatedAt DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$my_items = mysqli_stmt_get_result($stmt);

?>
<!DOCTYPE html>
<html>
<head>
<title>Lostly - Dashboard</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="navbar">
    <a href="dashboard.php" class="logo">Lostly</a>
<nav>

    <a href="dashboard.php">Dashboard</a>

    <a href="browse.php">Browse</a>

    <a href="logout.php">Logout</a>
</nav>
</header>

<main class="dashboard-container">
<div class="dashboard-content">

    <section class="dashboard-welcome">
        <p class="small-title">LOSTLY DASHBOARD</p>
        <h1>Welcome back, <?php echo htmlspecialchars($user["Username"]); ?> 👋</h1>
        <p>What would you like to do today?</p>
    </section>

    <section class="dashboard-actions">

        <div class="action-card">
            <div class="action-icon">+</div>
            <h2>Report Lost Item</h2>
            <p>Lost something? Create a notice so others can help you find it.</p>
            <a href="report_item.php?type=lost" class="dashboard-btn">Report Lost Item</a>
        </div>

        <div class="action-card">
            <div class="action-icon">✓</div>
            <h2>Report Found Item</h2>
            <p>Found something? Post it on Lostly and help return it to its owner.</p>
            <a href="report_item.php?type=found" class="dashboard-btn">Report Found Item</a>
        </div>

    </section>

    <section class="user-section">
        <div class="section-header">
            <h2>Your Information</h2>
            <a href="edit_profile.php" class="edit-profile-link">Edit</a>
        </div>
        <div class="user-info">

            <div class="user-info-item">
                <span>Username</span>
                <strong><?php echo htmlspecialchars($user["Username"]); ?></strong>
            </div>

            <div class="user-info-item">
                <span>Email</span>
                <strong><?php echo htmlspecialchars($user["Email"]); ?></strong>
            </div>

            <div class="user-info-item">
                <span>Phone</span>
                <strong><?php echo htmlspecialchars($user["Phone"]); ?></strong>
            </div>

            <div class="user-info-item">
                <span>Location</span>
                <strong><?php echo htmlspecialchars($user["District"]); ?>, <?php echo htmlspecialchars($user["Division"]); ?></strong>
            </div>

        </div>
    </section>

    <section class="user-section">
        <div class="section-header">
            <h2>My Reports</h2>
        </div>

        <?php if(mysqli_num_rows($my_items) == 0) { ?>

        <p>You haven't reported any items yet.</p>

        <?php } else { ?>

        <div class="report-list">

            <?php while($item = mysqli_fetch_assoc($my_items)) { ?>

            <div class="report-row">

                <div class="report-row-info">
                    <span class="badge badge-<?php echo strtolower($item["Type"]); ?>"><?php echo $item["Type"]; ?></span>
                    <?php if($item["Status"] == "Resolved") { ?>
                    <span class="badge badge-resolved">Resolved</span>
                    <?php } ?>
                    <strong><?php echo htmlspecialchars($item["ItemName"]); ?></strong>
                    <span class="report-row-meta"><?php echo htmlspecialchars($item["Category"]); ?> &middot; <?php echo date("M j, Y", strtotime($item["EventDate"])); ?></span>
                </div>

                <div class="report-row-actions">
                    <a href="item_detail.php?id=<?php echo (int)$item["ID"]; ?>">View</a>
                    <a href="report_item.php?id=<?php echo (int)$item["ID"]; ?>">Edit</a>
                    <a href="delete_item.php?id=<?php echo (int)$item["ID"]; ?>" class="js-confirm-delete" data-confirm-message="Delete this report? This cannot be undone.">Delete</a>
                </div>

            </div>

            <?php } ?>

        </div>

        <?php } ?>

    </section>

    <section class="coming-section">
        <h2>Lost &amp; Found</h2>
        <p>Browse lost and found items from your area.</p>
        <a href="browse.php" class="dashboard-btn">Browse Items</a>
    </section>

</div>
</main>

<script src="js/main.js"></script>
</body>
</html>
