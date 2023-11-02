<DOCTYPE html>

    <head>
        <script src="js.js">
        </script>
        <link rel="stylesheet" href="style.css">
        <?php
            function __autoload($class_name) {
            $file = "$class_name.php";
            require_once $file;
            }
        ?>
    </head>
    
    <body>
        <?php
        session_start();
        if (isset($_GET['id'])) {
            $art = searchEngine::getArticle($_GET['id']);

            If($art->getPublished() == 0 && isset($_SESSION['AuthorId']) == false) {
                header('Location: login.php');
                exit();
            }

            echo $art->loadFullArticle();
            if(isset($_SESSION['AuthorId']) == false) {     //if viewer isn't logged journalist it will add to article views.
                echo '<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>';
                echo '<script> var articleId = '.$_GET['id'].';</script>';
                echo '<script src="viewCount.js"></script>';
            }
        }
        else {
            echo "error idk what happened.";
        }
        ?>
    </body>