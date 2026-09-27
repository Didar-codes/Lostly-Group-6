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

if($item_id > 0)
{
    $stmt = mysqli_prepare($conn, "SELECT ImagePath FROM items WHERE ID=? AND UserID=?");
    mysqli_stmt_bind_param($stmt, "ii", $item_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($result))
    {
        if($row["ImagePath"] && file_exists(__DIR__ . "/" . $row["ImagePath"]))
        {
            unlink(__DIR__ . "/" . $row["ImagePath"]);
        }

        $stmt = mysqli_prepare($conn, "DELETE FROM items WHERE ID=? AND UserID=?");
        mysqli_stmt_bind_param($stmt, "ii", $item_id, $user_id);
        mysqli_stmt_execute($stmt);
    }
}

header("Location: dashboard.php");
exit();

?>
