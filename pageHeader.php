<?php
    class pageHeader{
        public static function loadHeader() {
            $ret = '<link rel="stylesheet" href="header.css"><script src="js.js"></script><header>
            <ul class="firstUl">
                <li><a href="index.php"><img src="resources/FriedrichSmall.png" alt="logo"></a></li>
                <li><a href="index.php">News</a></li>
                <li><a href="articles.php?id=1">About</a></li>
                <li><a href="#" onclick="dropdownFunction(\'sectionDropdown\')">Sections<img src="resources/arrow down.png" class="TextImage" id="sectionArrow"></a></li>
                <li style="float:right"> 
                    <a href="#" onclick="dropdownFunction(\'searchDropdown\')">
                        <img src="resources/lupa.png" class="TextImage">Search<img src="resources/arrow down.png" class="TextImage" id="searchArrow">
                    </a>
                </li>
            </ul>
            <div id="searchDropdown" class="searchDropdownContent">
                <ul>
                    <li class="dropdownSearch">
                        <form action="search.php" method="GET">     
                            <input name="type" value="text" type="hidden">
                            <input type="text" name="search" id="searchText" rows="1" cols="100"><button type="submit" class="searchButton"><img src="resources/lupa.png" width="15px"></button>
                        </form>
                    </li>
                </ul>
            </div>
            <div id="sectionDropdown" class="sectionDropdownContent">
                <Ul>
                    <li class="sectionUl">
                        <ul>
                            <li>
                                <div class="division">Main</div>
                            </li>';
            $mainSections = searchEngine::getMainSections();
            $numberOfRows = 0;
            foreach($mainSections as $mainSection){
                $numberOfRows++;
                $ret .= '<li><a href="search.php?type=main-section&search='.urlencode($mainSection['Name']).'">'.$mainSection['Name'].'</a></li>';
            }

            $ret .= '</ul>
            </li>
            <li class="sectionUl">
                <ul class="border">
                    <li>
                        <div class="division">Regions and specifics</div>
                    </li>';
            
            $subSections = searchEngine::getSections();

            for ($i=0; $i < count($subSections); $i++) { 
                if ($i % $numberOfRows == 0 && $i != 0 && $i != count($subSections)) {
                    $ret .= '</ul>
                    </li>
                    <li class="sectionUl">
                        <ul>
                            <li>
                                <div class="division">&nbsp</div>
                            </li>
                            <li><a href="search.php?type=Section&search='.urlencode($subSections[$i]['name']).'">'.$subSections[$i]['name'].'</a></li>';
                }
                else {
                    $ret .= '<li><a href="search.php?type=Section&search='.urlencode($subSections[$i]['name']).'">'.$subSections[$i]['name'].'</a></li>';
                }
            }

            $ret .= '</ul>
            </li>
            </Ul>
            </div>

            </header>';
            
            return $ret;
        }
    }
?>