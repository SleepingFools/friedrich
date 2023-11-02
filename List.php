<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href="List.css">



<?php
if (isset($_SESSION['AuthorId']) == false) {
    header('Location: login.php');
    exit();
}
function __autoload($class_name)
    {
        $file = "$class_name.php";
        require_once $file;
    }

//check for actions and maybe do them.
if (isset($_POST['actionName'])) {

    switch($_POST['actionName']) {
        case "imgUpload":
            $fileName = $_FILES['file']['name'];
            $fileTmpName = $_FILES['file']['tmp_name'];
            $fileSize = $_FILES['file']['size'];
            $fileError = $_FILES['file']['error'];
            $fileType = $_FILES['file']['type'];

            $fileExt = explode('.', $fileName);
            $fileActualExt = strtolower(end($fileExt));

            if (searchEngine::checkImgFormat($fileName)) {
                if ($fileError === 0) {
                    if ($fileSize < 5 * 1024 * 1024) {
                        $fileDestination = "articles/".$_POST['articleId']."/MainImage.".$fileActualExt;
                        move_uploaded_file($fileTmpName, $fileDestination);
                        searchEngine::deleteOtherImage($fileActualExt, $_POST['articleId']);
                        echo "<p class='success'>Upload successful. Image is saved.</p>";
                    }
                    else {
                        echo "<p class='error'>The image you uploaded was too big. Maximum size is 5mb. Image was not saved.</p>";
                    }
                }
                else {
                    echo "<p class='error'>Image upload failed. Image was not saved.</p>";
                }
            }
            else {
                echo "<p class='error'>You uploaded file of wrong format. Image was not saved.</p>";
            }
            break;
        case "publish":
            echo searchEngine::publish($_POST['articleId']);
            break;
        case "unpublish":
            echo searchEngine::unpublish($_POST['articleId']);
            break;
        case "delete": 
            echo searchEngine::deleteArt($_POST['articleId'], $_POST['published']);
            break;
        default:
            break;
    }

}
?>
</head>

<body>
<h1>Your articles:</h1>

<form action="editing.php">
    <input type="submit" value="Create new article" />
</form>
<?php 
//load page
$articles = searchEngine::getArticlesAsArray($_SESSION['AuthorId'], $_SESSION['editor']);
$ret = "<table class='artTable'>
    <tr><th>Title</th><th>Main Section</th><th>Published</th><th>Main Image</th><th>Actions</th></tr>";

foreach($articles as $article) {
    $ret .= $article->getArticleAsRow();
}
$ret .= "</table>";
echo $ret;
?>
</body>
</html>