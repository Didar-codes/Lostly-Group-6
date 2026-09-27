<?php

include_once("ItemModel.php");

class ItemController
{
    private $model;

    public function __construct($conn)
    {
        $this->model = new ItemModel($conn);
    }

    public function browse($search, $type, $division, $category)
    {
        return $this->model->searchItems($search, $type, $division, $category);
    }
}
?>
