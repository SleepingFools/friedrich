<DOCTYPE html>

    <head>
        <link rel="stylesheet" href="editor.css">
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/paragraph@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/simple-image@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>

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
        ?>
    </head>

    <body>
        <?php
        if (isset($_POST['articleId'])) {
            $conn = DB::getConnection();
            $sql = "SELECT * FROM article WHERE ArticleId = " . $_POST['articleId'] . ";"; //Main Sections
            $result = $conn->query($sql);
            $artRow = $result->fetch(PDO::FETCH_ASSOC);
            $ret = '<h1>Article Editor</h1>
            <form id="articleEditor">
            <div class="editorBody">
                <h2>Title:</h2> <textarea class="title" id="title" name="title">' . $artRow['Title'] . '</textarea>
                <h2>Subtitle:</h2> <textarea class="subtitle" id="subtitle" name="subtitle">' . $artRow['SmallTitle'] . '</textarea>
                <h2>Description:</h2> <textarea class="description" id="description" name="description">' . searchEngine::getArtDesc($_POST['articleId']) . '</textarea>
                <input name="ArtId" id="IdOfArticle" value="'.$_POST['articleId'].'" type="hidden">
                
                <div class="tags">
                    <h2>Sections:</h2>

                    <table style="width:100%">';
            $count = 0;     //How many columns there are

            $sql = "SELECT * FROM mainSection;"; //Main Sections
            $result = $conn->query($sql);
            $ret .= "<tr><th>Main sections:</th>";
            foreach ($result as $row) {
                $count++;
                $ret .= "<td><input type=\"radio\" 
                        id=\"" . $row["Name"] . "\" 
                        name=\"mainSection\" 
                        value=\"" . $row["MainSectionId"] . "\"";

                if ($row["MainSectionId"] == $artRow['MainSectionId']) {
                    $ret .= " checked ";
                }

                $ret .= ">
                        <label for=\"" . $row["Name"] . "\">" . $row["Name"] . "</label><br></td>";
            }
            $ret .= "</tr>";

            $sql = "SELECT SectionId FROM section_article WHERE ArticleId = " . $_POST['articleId'] . ";"; //Subsections
            $result = $conn->query($sql);
            $SubSecRow = $result->fetchAll(PDO::FETCH_COLUMN);

            $sql = "SELECT * FROM section;";     
            $result = $conn->query($sql);
            $columns = 0;   //columns of subsections
            $ret .= "<tr><th>Sections:</th>";
            foreach ($result as $row) {
                $columns++;

                $ret .= "<td><input type=\"checkbox\" 
                        id=\"" . $row["name"] . "\" 
                        name=\"subSection[]\" 
                        value=\"" . $row["SectionId"] . "\"";
                
                if (in_array($row["SectionId"], $SubSecRow)) {
                    $ret .= " checked ";
                }
                
                $ret .= ">
                        <label for=\"" . $row["name"] . "\">" . $row["name"] . "</label><br></td>";

                if ($columns == $count) {
                    $columns = 0;
                    $ret .= "</tr><tr><th></th>";
                }
            }
            $ret .= "</tr>
            </table>

                </div>

                <h2>Contents:</h2>
                <div class=\"container\">
                    <div id=\"editorjs\"></div>
                </div>

                <p><input type=\"submit\" value=\"Save Article\" /></p>
                <h1>*****</h1>";
            
            $ret .= '<script> var artContent = '.json_encode(file_get_contents('articles/'.$_POST['articleId'].'/content.txt')).';</script>';    

            $ret .= '<script src="./editor.js"></script>
            </div>
            </form>';
        } 
        else {
            $ret = '<h1>Article Editor</h1>
            <form id="articleEditor">
                <div class="editorBody">
                    <h2>Title:</h2> <textarea class="title" id="title" name="title"></textarea>
                    <h2>Subtitle:</h2> <textarea class="subtitle" id="subtitle" name="subtitle"></textarea>
                    <h2>Description:</h2> <textarea class="description" id="description" name="description"></textarea>
                    <input name="ArtId" id="IdOfArticle" value="false" type="hidden">
    
                    <div class="tags">
                        <h2>Sections:</h2>
    
                        <table style="width:100%">';
                        $count = 0;     //How many columns there are
                        $conn = DB::getConnection();

                        $sql = "SELECT * FROM mainSection;"; //Main Sections
                        $result = $conn->query($sql);
                        $ret .= "<tr><th>Main sections:</th>";
                        foreach ($result as $row) {
                            $count++;
                            $ret .= "<td><input type=\"radio\" 
                            id=\"" . $row["Name"] . "\" 
                            name=\"mainSection\" 
                            value=\"" . $row["MainSectionId"] . "\"";

                            if ($count == 1) {
                                $ret .= " checked ";
                            }

                            $ret .= ">
                            <label for=\"" . $row["Name"] . "\">" . $row["Name"] . "</label><br></td>";
                        }
                        $ret .= "</tr>";

                        $sql = "SELECT * FROM section;";     //Subsections
                        $result = $conn->query($sql);
                        $columns = 0;   //columns of subsections
                        $ret .= "<tr><th>Sections:</th>";
                        foreach ($result as $row) {
                            $columns++;

                            $ret .= "<td><input type=\"checkbox\" 
                            id=\"" . $row["name"] . "\" 
                            name=\"subSection[]\" 
                            value=\"" . $row["SectionId"] . "\">
                            <label for=\"" . $row["name"] . "\">" . $row["name"] . "</label><br></td>";

                            if ($columns == $count) {
                                $columns = 0;
                                $ret .= "</tr><tr><th></th>";
                            }
                        }
                        $ret .= "</tr>";
                        $ret .= '</table>

                        </div>
        
                        <h2>Contents:</h2>
                        <div class="container">
                            <div id="editorjs"></div>
                        </div>
        
                        <p><input type="submit" value="Save Article" /></p>
                        <h1>*****</h1>
                        <script src="./editor.js"></script>
                    </div>
                </form>';
        }
        echo $ret;
        ?>
    </body>