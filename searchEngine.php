<?php
    class searchEngine {
        public static function getArticle($id) {
            $conn = DB::getConnection();
            $sql = "SELECT article.ArticleId, article.Title, article.SmallTitle, article.releaseDate, article.published, mainSection.Name 
            FROM article 
            LEFT JOIN mainSection ON article.MainSectionId=mainSection.MainSectionId
            WHERE article.ArticleId = $id;";
            $result = $conn->query($sql);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $releaseDate = "Not released yet.";
            If(!is_null($row['releaseDate'])) {
                $releaseDate = $row['releaseDate'];
            }
            return new article($row['ArticleId'], $row['Title'], $row['SmallTitle'], $releaseDate, $row['published'], $row['Name']);
        }
        public static function getArticlesAsArray($AuthorId, $editor) {
            $conn = DB::getConnection();
            $sql = '';
            if ($editor == 1) {
                $sql = "SELECT article.ArticleId, article.Title, article.SmallTitle, article.releaseDate, article.published, mainSection.Name 
                FROM article 
                LEFT JOIN mainSection ON article.MainSectionId=mainSection.MainSectionId 
                ORDER BY ArticleId DESC;";
            }
            else {
                $sql = "SELECT article.ArticleId, article.Title, article.SmallTitle, article.releaseDate, article.published, mainSection.Name 
                FROM article 
                LEFT JOIN mainSection ON article.MainSectionId=mainSection.MainSectionId 
                WHERE article.ArticleId 
                IN (SELECT author_article.ArticleId FROM author_article WHERE AuthorId = $AuthorId) 
                ORDER BY article.ArticleId DESC;";
            }

            $query = $conn->query($sql);
        
            $result = $query->fetchAll();

            $articleArray = array();

            foreach($result as $row) {
                $releaseDate = "Not released yet.";
                If(!is_null($row['releaseDate'])) {
                    $releaseDate = $row['releaseDate'];
                }
                array_push($articleArray, new article($row['ArticleId'], $row['Title'], $row['SmallTitle'], $releaseDate, $row['published'], $row['Name']));
            }
            return $articleArray;
        }
        public static function getImgFormats(){
            return array('jpg', 'jpeg', 'png', 'jfif', 'tiff', 'tif');
        }
        public static function checkImgFormat($imgName) {
            $fileExt = explode('.', $imgName);
            $fileActualExt = strtolower(end($fileExt));

            $allowed = self::getImgFormats();

            if (in_array($fileActualExt, $allowed)) {
                return true;
            }
            else {
                return false;
            }  
        }
        public static function getArtMainImage($id) {
            $fileTypes = self::getImgFormats();
            foreach ($fileTypes as $Type) {
                if (file_exists("articles/$id/MainImage.".$Type)) {
                    return "articles/$id/MainImage.".$Type;
                }
            }
            return false;
        }
        public static function deleteOtherImage($extension, $id) {
            $fileTypes = self::getImgFormats();
            foreach ($fileTypes as $Type) {
                if (file_exists("articles/$id/MainImage.".$Type) && $Type != $extension) {
                    unlink("articles/$id/MainImage.$Type");
                }
            }
        }
        public static function getArtDesc($id) {
            return file_get_contents('articles/'.$id.'/desc.txt');
        }
        public static function publish($id) {
            $conn = DB::getConnection();
            $sql = "SELECT F_publish($id);";
            $result = $conn->query($sql);
            $row = $result->fetchColumn();

            return '<p class=\'success\'>The article was succesfully published on '.$row.'.</p>';
        }
        public static function unpublish($id) {
            $conn = DB::getConnection();
            $sql = "UPDATE article SET published = 0 WHERE ArticleId = $id;";
            $conn->query($sql);
            return '<p class=\'success\'>The article was succesfully unpublished.</p>';
        }
        public static function deleteArt($id, $publish) {
            if ($publish == TRUE) {
                return '<p class=\'error\'>You can\'t delete published article. Unpublish it first.</p>';
            }
            $fileArr = scandir("articles/$id");
            foreach($fileArr AS $file) {
                if($file === '.' || $file === '..') {
                    continue;
                }
                if(unlink("articles/$id/$file") == false) {
                    return "<p class=\'error\'>Some file failed to be deleted. Article is likely corrupted. 
                    Contact administrator and tell him article number $id is corrupted</p>";
                }
            }
            rmdir ("articles/$id");
            $conn = DB::getConnection();
            $sql = "DELETE FROM article WHERE ArticleId = $id;";
            $conn->query($sql);
        }
        public static function getMainPageArticles() {
            $articleArray = array();

            $conn = DB::getConnection();        
            //                                  First two in array are world news

            $sql = "SELECT * FROM `article` WHERE published = 1 AND MainSectionId = 1 ORDER BY releaseDate DESC LIMIT 2;";
            $query = $conn->query($sql);
            $result = $query->fetchAll();

            foreach($result as $row) {      
                array_push($articleArray, new article($row['ArticleId'], $row['Title'], $row['SmallTitle'], 
                $row['releaseDate'], $row['published'], $row['MainSectionId'], $row['views']));
            }

            //                                  Then one latest analysis.

            $sql = "SELECT * FROM `article` WHERE published = 1 AND NOT MainSectionId = 1 ORDER BY releaseDate DESC LIMIT 10;";
            $query = $conn->query($sql);
            $result = $query->fetchAll();

            $diditwork = false;

            foreach ($result as $key => $row) { 
                if($row['MainSectionId'] == 2) {
                    array_push($articleArray, new article($row['ArticleId'], $row['Title'], $row['SmallTitle'], 
                    $row['releaseDate'], $row['published'], $row['MainSectionId'], $row['views']));
                    unset($result[$key]);
                    $diditwork = true;
                    break;
                }
            }

            if ($diditwork == false) {
                $sql = "SELECT * FROM `article` WHERE published = 1 AND MainSectionId = 2 ORDER BY releaseDate DESC LIMIT 1;";
                $query = $conn->query($sql);
                $res = $query->fetch();
                array_push($articleArray, new article($res['ArticleId'], $res['Title'], $res['SmallTitle'], 
                    $res['releaseDate'], $res['published'], $res['MainSectionId'], $res['views']));
            }

            //                                  Then three "reader's favourite"

            for ($i = 1; $i < 4; $i++){
                $mostViewedArticle = -1;
                $views = -1;
                foreach ($result as $key => $row){
                    if ($row['views'] > $views) {
                        $mostViewedArticle = $key;
                        $views = $row['views'];
                    }
                }

                array_push($articleArray, new article($result[$mostViewedArticle]['ArticleId'], $result[$mostViewedArticle]['Title'], $result[$mostViewedArticle]['SmallTitle'], 
                $result[$mostViewedArticle]['releaseDate'], $result[$mostViewedArticle]['published'], $result[$mostViewedArticle]['MainSectionId'], 
                $result[$mostViewedArticle]['views']));                

                unset($result[$mostViewedArticle]);
            }

            //                                  And finaly the rest.

            foreach($result as $row) {
                array_push($articleArray, new article($row['ArticleId'], $row['Title'], $row['SmallTitle'], 
                $row['releaseDate'], $row['published'], $row['MainSectionId'], $row['views']));
            }

            return $articleArray;

        }
        public static function searchSection($sectionName, $main) {
            $conn = DB::getConnection();
            
            if($main == 1) {
                $query = $conn->prepare("SELECT article.ArticleId, article.Title, article.SmallTitle, article.releaseDate, article.published, mainSection.Name 
                FROM article 
                LEFT JOIN mainSection ON article.MainSectionId=mainSection.MainSectionId 
                WHERE article.published = 1 AND mainSection.Name = :sectionName
                ORDER BY releaseDate DESC;");
    
                $query->bindParam("sectionName", $sectionName, PDO::PARAM_STR);
            
                $query->execute();

                $result = $query->fetchAll(\PDO::FETCH_ASSOC);
    
                $artArray = array();
    
                foreach ($result as $row) {
                    array_push($artArray, new article($row['ArticleId'], $row['Title'], $row['SmallTitle'], $row['releaseDate'], $row['published'], $row['Name']));
                }
    
                return $artArray;
            }
            else {
                $query = $conn->prepare("SELECT article.ArticleId, article.Title, article.SmallTitle, article.releaseDate, article.published, mainSection.Name
                FROM article 
                LEFT JOIN mainSection ON article.MainSectionId=mainSection.MainSectionId 
                LEFT JOIN section_article ON article.ArticleId=section_article.ArticleId 
                LEFT JOIN section ON section_article.SectionId=section.SectionId
                WHERE article.published = 1 AND section.name = :sectionName
                ORDER BY releaseDate DESC;");

                $query->bindParam("sectionName", $sectionName, PDO::PARAM_STR);
            
                $query->execute();

                $result = $query->fetchAll(\PDO::FETCH_ASSOC);

                $artArray = array();

                foreach ($result as $row) {
                    array_push($artArray, new article($row['ArticleId'], $row['Title'], $row['SmallTitle'], $row['releaseDate'], $row['published'], $row['Name']));
                }

                return $artArray;
            }
            
        }
        public static function searchText($text) {
            $conn = DB::getConnection();
            $text = '%'. $text .'%';

            $query = $conn->prepare("SELECT article.ArticleId, article.Title, article.SmallTitle, article.releaseDate, article.published, mainSection.Name 
            FROM article 
            LEFT JOIN mainSection ON article.MainSectionId=mainSection.MainSectionId 
            WHERE article.published = 1 AND (article.Title LIKE :textsearch OR article.SmallTitle LIKE :textsearchx)
            ORDER BY releaseDate DESC;");

            $query->bindParam("textsearch", $text, PDO::PARAM_STR);
            $query->bindParam("textsearchx", $text, PDO::PARAM_STR);
        
            $query->execute();

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);

            $artArray = array();

            foreach ($result as $row) {
                array_push($artArray, new article($row['ArticleId'], $row['Title'], $row['SmallTitle'], $row['releaseDate'], $row['published'], $row['Name']));
            }

            return $artArray;
        }
        public static function getMainSections() {
            $conn = DB::getConnection();
            $sql = "SELECT * FROM `mainSection`;";
            $query = $conn->query($sql);
            return $query->fetchAll();
        }
        public static function getSections() {
            $conn = DB::getConnection();
            $sql = "SELECT * FROM `section`;";
            $query = $conn->query($sql);
            return $query->fetchAll();
        }
    }









?>