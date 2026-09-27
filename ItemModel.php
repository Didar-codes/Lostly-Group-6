<?php

class ItemModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function searchItems($search = "", $type = "", $division = "", $category = "")
    {
        $sql = "SELECT items.*, users.Username
                FROM items
                JOIN users ON items.UserID = users.ID
                WHERE items.Status='Active'";

        $params = [];
        $types = "";

        if($search != "")
        {
            $sql .= " AND (
                items.ItemName LIKE ?
                OR items.Category LIKE ?
                OR items.District LIKE ?
                OR items.Division LIKE ?
            )";

            $value = "%" . $search . "%";
            $params = [$value, $value, $value, $value];
            $types = "ssss";
        }

        if($type == "Lost" || $type == "Found")
        {
            $sql .= " AND items.Type = ?";
            $params[] = $type;
            $types .= "s";
        }

        if($division != "")
        {
            $sql .= " AND items.Division = ?";
            $params[] = $division;
            $types .= "s";
        }

        if($category != "")
        {
            $sql .= " AND items.Category = ?";
            $params[] = $category;
            $types .= "s";
        }

        $sql .= " ORDER BY items.CreatedAt DESC";

        $stmt = mysqli_prepare($this->conn, $sql);

        if(!$stmt)
        {
            return false;
        }

        if(!empty($params))
        {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }
}
?>
