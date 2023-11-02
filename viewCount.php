<?php 
    if (isset($_GET['id'])) {
        function __autoload($class_name) {
            $file = "$class_name.php";
            require_once $file;
        }
        $conn = DB::getConnection();
        $id = $_GET['id'];

        $sql = "UPDATE article SET views = views + 1 WHERE ArticleId = $id;";
        $conn->query($sql);
    }
?>