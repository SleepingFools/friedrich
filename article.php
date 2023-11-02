<?php
    class article{
        private $ID;
        private $Title;
        private $SmallTitle;
        private $releaseDate;
        private $published;
        private $MainSection;

        public function __construct($ID, $Title, $SmallTitle, $releaseDate, $published, $MainSection) {
            $this->ID = $ID;
            $this->Title = $Title;
            $this->SmallTitle = $SmallTitle;
            $this->releaseDate = $releaseDate;
            $this->published = $published;
            $this->MainSection = $MainSection;
        } 
        public function loadFullArticle() {
            $ret = " ";

            $ret .= pageHeader::loadHeader();

            $ret .= $this->loadArticleHeader();
            $ret .= $this->loadArticleBody();

            $ret .= "<div id='endOfArticle'></div>";

            return $ret;
        }
        private function loadMainImage() {
            $imgSrc = searchEngine::getArtMainImage($this->ID);
            if($imgSrc == false) {
                return "";
            }
            else {
                return '<img src="'.$imgSrc.'" class="ArticleImage">';
            }
        }
        private function loadHeaderSidebar() {
            return '<div class="HeaderSidebar">
                <h4 class="MainSection">'.$this->MainSection.'</h4>
                <h4 class="SubSections">'.$this->getOtherSections().'</h4>
                <p class="date">'.$this->releaseDate.'</p>
            </div>';
        }
        private function loadArticleTop() {
            return '<div class="ArticleTop">
                <h1 class="ArticleTitle">
                    <span class="subheadline">'.$this->SmallTitle.'</span>
                    <br>
                    '.$this->Title.'
                </h1>
                <p class="ArticleDescription">'.searchEngine::getArtDesc($this->ID).'</p>
            </div>';
        }
        private function loadArticleHeader(){       //WHOLE TOP PART OF ARTICLE
            return '<div class="ArticleLeadImage">'.$this->loadMainImage().'</div>'.'<div class="ArticleHeader">'.$this->loadHeaderSidebar().$this->loadArticleTop().'</div>';
        }
        private function loadArticleSidebar(){
            return '<div class="ArticleSidebar">
                <p class="ArticleSidebarContent"></p>
            </div>';
        }
        private function loadContent(){ 
            $ret = '<div class="ArticleContent">';

            $ContentJson = file_get_contents("articles/".$this->ID."/content.txt");
            $DecodedContent = json_decode($ContentJson);
            $ShouldThereBeBigFirstLetter = TRUE;

            foreach ($DecodedContent->blocks as $block) {
                switch ($block->type) {
                    case 'paragraph':
                        if ($ShouldThereBeBigFirstLetter == TRUE) {
                            $ShouldThereBeBigFirstLetter = false;
                            $ret .= '<p class="ArticleFirstText">'.$block->data->text.'</p>';
                        }
                        else {
                            $ret .= '<p class="ArticleOtherText">'.$block->data->text.'</p>';
                        }
                        break;
                    case 'delimiter':
                        $ret .= '<p class="delimiter">***</p>';
                        break;
                    case 'header':
                        $ret .= '<h2 class="midContentHeader">'.$block->data->text.'</h2>';
                        break;
                    case 'image':
                        $ret .= '<figure><img src="'.$block->data->url.'" alt="Image not loaded." class="ArticleContentImage">
                        <figcaption>'.$block->data->caption.'</figcaption></figure>';
                        break;
                    case 'embed': 
                        if($block->data->service == 'youtube') {
                            $ret .= '<figure class="video"><div class="iframe-container"><iframe class="youtube" src="'.$block->data->embed.'"></iframe></div>
                            <figcaption>'.$block->data->caption.'</figcaption></figure>';
                        }
                        else {
                            $ret .= '<p class="error">I am sorry, but currently this site only supports youtube videos as embed.
                            If you would like to add something else to your article, then please contact the developer! 
                            If you\'re a reader and see this that means that something went wrong, or the author was too lazy to check how the article looks.</p>';
                        }
                        break;
                    default:
                        break;
                }
            }

            $ret .= '</div>';
            return $ret;
        }
        private function loadArticleBody(){         //WHOLE BODY PART OF ARTICLE
            return '<div class="ArticleBody">'.$this->loadArticleSidebar().$this->loadContent().'</div>';
        }
        private function getOtherSections() {
            $conn = DB::getConnection();
            $sql = "SELECT name FROM section WHERE SectionId IN (SELECT SectionId FROM section_article WHERE ArticleId = ".$this->ID.");";
            $result = $conn->query($sql);
            $len = $result->rowCount();
            $ret = " ";
            $i = 1;
            foreach ($result as $section) {
                if($i == 1) {
                    $ret .= $section["name"];
                }
                else if($i == $len) {
                    $ret .= " and ".$section["name"];
                }
                else {
                    $ret .= ", ".$section["name"];
                }
                $i++;
            }
            return $ret;
        }
        public function getArticleAsRow() {
            $ret = "<tr><td>".$this->Title."</td><td>".$this->MainSection."</td><td>".$this->releaseDate."</td><td>";

            // Upload Image
            $ret .= "<form action=\"\" method=\"post\" enctype=\"multipart/form-data\"> 
            <input name=\"actionName\" value=\"imgUpload\" type=\"hidden\">
            <input name=\"articleId\" value=\"".$this->ID."\" type=\"hidden\">
            <input type=\"file\" name=\"file\" accept=\".".implode(", ." , searchEngine::getImgFormats())."\">
            <button type=\"submit\">Upload main image</button>
            </form></td><td>";

            // View Article
            $ret .= "<form action=\"articles.php\" method=\"GET\">
            <input name=\"id\" value=\"".$this->ID."\" type=\"hidden\">
            <button type=\"submit\">View article</button>
            </form>";

            // Edit Article
            $ret .= "<form action=\"editing.php\" method=\"post\">
            <input name=\"articleId\" value=\"".$this->ID."\" type=\"hidden\">
            <button type=\"submit\">Edit article</button>
            </form>";

            //Delete Article
            $ret .= "<form action=\"\" method=\"post\">
            <input name=\"actionName\" value=\"delete\" type=\"hidden\">
            <input name=\"articleId\" value=\"".$this->ID."\" type=\"hidden\">
            <input name=\"published\" value=\"".$this->published."\" type=\"hidden\">
            <button type=\"submit\" onclick=\"return confirm('Are you sure?');\">Delete article</button>
            </form>";

            // Publish/Unpublish Article
            if ($_SESSION['editor'] == 1) {
                if($this->published == TRUE) {
                    $ret .= "<form action=\"\" method=\"post\">
                    <input name=\"actionName\" value=\"unpublish\" type=\"hidden\">
                    <input name=\"articleId\" value=\"".$this->ID."\" type=\"hidden\">
                    <button type=\"submit\" onclick=\"return confirm('Are you sure?');\">Unpublish</button>
                    </form>";
                }
                else {
                    $ret .= "<form action=\"\" method=\"post\">
                    <input name=\"actionName\" value=\"publish\" type=\"hidden\">
                    <input name=\"articleId\" value=\"".$this->ID."\" type=\"hidden\">
                    <button type=\"submit\" onclick=\"return confirm('Are you sure?');\">Publish</button>
                    </form>";
                }
            }
            return $ret."</td></tr>";
        }
        public function getLargeTeaser() {
            return '<div class="big">
            <a href="articles.php?id='.$this->ID.'">
                <div>
                    <h4 class="subTitle">'.$this->SmallTitle.'</h4>
                    <h1 class="headline">'.$this->Title.'</h1>
                    <p class="description">'.searchEngine::getArtDesc($this->ID).'</p>
                </div>
                <img src="'.searchEngine::getArtMainImage($this->ID).'" class="artImage">
            </a>
        </div>';
        }
        public function getMediumTeaser() {
            return '<div class="medium">
            <a href="articles.php?id='.$this->ID.'">
            <img src="'.searchEngine::getArtMainImage($this->ID).'" class="artImage">
            <h4 class="subTitle">'.$this->SmallTitle.'</h4>
            <h2 class="headline">'.$this->Title.'</h2>
            <p class="description">'.searchEngine::getArtDesc($this->ID).'</p>
            </a>
        </div>';
        }
        public function getSmallTeaser() {
            return '<div class="small">
            <a href="articles.php?id='.$this->ID.'">
            <h4 class="subTitle">'.$this->SmallTitle.'</h4>
            <h2 class="headline">'.$this->Title.'</h2>
            <p class="description">'.searchEngine::getArtDesc($this->ID).'</p>
        </a>
        </div>';
        }
        public function getPublished() {
            return $this->published;
        }
    }
?>