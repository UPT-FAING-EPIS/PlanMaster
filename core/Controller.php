<?php

class Controller
{
    public function view($view, $data = [])
    {
        extract($data);
        require_once "../app/views/$view.php";
    }
    public function model($model)
    {
        require_once "../app/models/$model.php";
        require_once '../core/Model.php';

        $conn = new Model();

        return new $model($conn->getConnection());
    }
}