<?php

session_start();

if(!isset($_SESSION["id"]))
{
    header("Location: login.php");
    exit();
}

include("db.php");
include("lists.php");
include("ItemController.php");

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$type = isset($_GET["type"]) ? $_GET["type"] : "";
$division = isset($_GET["division"]) ? $_GET["division"] : "";
$category = isset($_GET["category"]) ? $_GET["category"] : "";

if(!in_array($division, $divisions))
{
    $division = "";
}

if(!in_array($category, $categories))
{
    $category = "";
}

$controller = new ItemController($conn);
$result = $controller->browse($search, $type, $division, $category);

?>
<!DOCTYPE html>
<html>
<head>
<title>Lostly - Browse Items</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="navbar">
    <a href="dashboard.php" class="logo">Lostly</a>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="browse-container">

    <section class="browse-hero">
        <p class="small-title">LOSTLY COMMUNITY</p>
        <h1>Browse Lost &amp; Found</h1>
        <p>Search for an item, check reports from your area, and connect with the person who posted it.</p>
    </section>

    <form method="get" class="filter-bar">
        <input type="text" name="search" placeholder="Search item, category or location..." value="<?php echo htmlspecialchars($search); ?>">

        <select name="type">
            <option value="">All Types</option>
            <option value="Lost" <?php echo $type == "Lost" ? "selected" : ""; ?>>Lost</option>
            <option value="Found" <?php echo $type == "Found" ? "selected" : ""; ?>>Found</option>
        </select>

        <select name="division">
            <option value="">All Divisions</option>
            <?php foreach($divisions as $div) { ?>
            <option value="<?php echo htmlspecialchars($div); ?>" <?php echo $division == $div ? "selected" : ""; ?>><?php echo htmlspecialchars($div); ?></option>
            <?php } ?>
        </select>

        <select name="category">
            <option value="">All Categories</option>
            <?php foreach($categories as $cat) { ?>
            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category == $cat ? "selected" : ""; ?>><?php echo htmlspecialchars($cat); ?></option>
            <?php } ?>
        </select>

        <input type="submit" value="Search" class="btn">
        <a href="browse.php" class="btn-secondary">Reset</a>
    </form>

    <?php if($result === false || mysqli_num_rows($result) == 0) { ?>

        <div class="no-results">
            <h2>No items found</h2>
            <p>Try a different keyword or remove one of the filters.</p>
        </div>

    <?php } else { ?>

        <div class="browse-summary">
            <span><?php echo mysqli_num_rows($result); ?> active report<?php echo mysqli_num_rows($result) == 1 ? "" : "s"; ?></span>
        </div>

        <div class="item-grid">

            <?php while($item = mysqli_fetch_assoc($result)) { ?>

                <a href="item_detail.php?id=<?php echo (int)$item["ID"]; ?>" class="item-card">

                    <?php if(!empty($item["ImagePath"])) { ?>
                        <img src="<?php echo htmlspecialchars($item["ImagePath"]); ?>" alt="<?php echo htmlspecialchars($item["ItemName"]); ?>">
                    <?php } else { ?>
                        <div class="item-card-placeholder">No photo</div>
                    <?php } ?>

                    <div class="item-card-body">
                        <div class="item-card-top">
                            <span class="badge badge-<?php echo strtolower($item["Type"]); ?>"><?php echo htmlspecialchars($item["Type"]); ?></span>
                        </div>

                        <h2><?php echo htmlspecialchars($item["ItemName"]); ?></h2>

                        <p><?php echo htmlspecialchars($item["Category"]); ?></p>

                        <p class="item-card-location">
                            <?php echo htmlspecialchars($item["District"]); ?>, <?php echo htmlspecialchars($item["Division"]); ?>
                        </p>

                        <p class="item-card-date">
                            <?php echo date("M j, Y", strtotime($item["EventDate"])); ?>
                        </p>

                        <span class="view-link">View details →</span>
                    </div>

                </a>

            <?php } ?>

        </div>

    <?php } ?>

</main>

</body>
</html>
