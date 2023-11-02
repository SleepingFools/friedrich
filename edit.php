<?php
    session_start();
    if (isset($_SESSION['AuthorId']) == false) {
        header('Location: login.php');
        exit();
    }
    function __autoload($class_name)
    {
        $file = "$class_name.php";
        require_once $file;
    }

    if($_POST['ArtId'] != "false") {
        
        $ArtId = $_POST['ArtId'];
        $title = isset($_POST['title']) ? $_POST['title'] : "nothing here";
        $subtitle = isset($_POST['subtitle']) ? $_POST['subtitle'] : "nothing here";
        $description = isset($_POST['description']) ? $_POST['description'] : "nothing here";
        $content = isset($_POST['content']) ? $_POST['content'] : "nothing here";
        $mainSection = isset($_POST['mainSection']) ? $_POST['mainSection'] : 1;

        $conn = DB::getConnection();

        $query = $conn->prepare("UPDATE article 
        SET Title = :title, SmallTitle = :subtitle, MainSectionId = :mainSection
        WHERE ArticleId = $ArtId;");
        $query->bindParam("title", $title, PDO::PARAM_STR);
        $query->bindParam("subtitle", $subtitle, PDO::PARAM_STR);
        $query->bindParam("mainSection", $mainSection, PDO::PARAM_INT);
        $query->execute();

        $myfile = fopen("articles/$ArtId/content.txt", "w") or die("Unable to open file!");
        fwrite($myfile, $content);
        fclose($myfile);

        $myfile = fopen("articles/$ArtId/desc.txt", "w") or die("Unable to open file!");
        fwrite($myfile, $description);
        fclose($myfile);

        $sql = "SELECT SectionId FROM section_article WHERE ArticleId = $ArtId;"; //Subsections
        $result = $conn->query($sql);
        $SubSecRow = $result->fetchAll(PDO::FETCH_COLUMN);

        $subsections = $_POST['subSection'];

        foreach($SubSecRow as $subsec) {
            if(in_array($subsec, $subsections) == false){
                $sql = "DELETE FROM section_article WHERE SectionId = $subsec AND ArticleId = $ArtId;";
                $conn->query($sql);
            }
        }

        foreach($subsections AS $subsec) {
            if(in_array($subsec, $SubSecRow) == false){
                $sql = "INSERT INTO section_article (SectionId, ArticleId) VALUES ($subsec, $ArtId);";
                $conn->query($sql);
            }
        }
        
        echo 'false';

    } 
    else {
        
    $title = isset($_POST['title']) ? $_POST['title'] : "nothing here";
    $subtitle = isset($_POST['subtitle']) ? $_POST['subtitle'] : "nothing here";
    $description = isset($_POST['description']) ? $_POST['description'] : "nothing here";
    $content = isset($_POST['content']) ? $_POST['content'] : "nothing here";
    $mainSection = isset($_POST['mainSection']) ? $_POST['mainSection'] : 1;

    $conn = DB::getConnection();

    $query = $conn->prepare("INSERT INTO article (Title, SmallTitle, published, MainSectionId, views) VALUES (:title, :subtitle, 0, :mainSection, 0);");
    $query->bindParam("title", $title, PDO::PARAM_STR);
    $query->bindParam("subtitle", $subtitle, PDO::PARAM_STR);
    $query->bindParam("mainSection", $mainSection, PDO::PARAM_INT);
    $query->execute();
    $articleId = $conn->lastInsertId();

    mkdir("articles/$articleId");

    $myfile = fopen("articles/$articleId/content.txt", "w") or die("Unable to open file!");
    fwrite($myfile, $content);
    fclose($myfile);

    $myfile = fopen("articles/$articleId/desc.txt", "w") or die("Unable to open file!");
    fwrite($myfile, $description);
    fclose($myfile);

    if (isset($_POST['subSection'])) {
        $subsections = $_POST['subSection'];
        $sql = "INSERT INTO section_article (SectionId, ArticleId) VALUES";
        foreach($subsections as $section) {
            $sql .= " ($section, $articleId),";
        }
        $sql = substr($sql, 0, -1) . ";";
        $conn->query($sql);
    }

    $sql = "INSERT INTO author_article (AuthorId, ArticleId) VALUES (" . $_SESSION['AuthorId'] . ", " . $articleId . ")";
    $conn->query($sql);
    
    echo $articleId;
}
?>
