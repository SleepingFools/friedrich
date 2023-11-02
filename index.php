<DOCTYPE html>

    <head>
        <link rel="stylesheet" href="mainPageStyle.css">
        <?php
            function __autoload($class_name) {
            $file = "$class_name.php";
            require_once $file;
            }
        ?>
    </head>
    
    <body>
        <?php
            echo pageHeader::loadHeader();
            $MParts = searchEngine::getMainPageArticles();
            $ret = '<div class="rightPart">
            <h2 class="divisionName">Current World Events</h2>';
            $ret .= $MParts[0]->getSmallTeaser();
            $ret .= $MParts[1]->getSmallTeaser();
            $ret .= '</div>
            <div class="midBorder"></div>
            <div class="leftPart">
                <h2 class="divisionName">Latest Analysis</h2>';
            $ret .= $MParts[2]->getLargeTeaser();
            $ret .= '</div>
            <div class="bottomPart">
                <h2 class="divisionName">Reader\'s favourite</h2>
                <div class="FlexBox">';
            $ret .= $MParts[3]->getMediumTeaser();
            $ret .= $MParts[4]->getMediumTeaser();
            $ret .= $MParts[5]->getMediumTeaser();
            $ret .= '</div>
            </div>
            <div class="otherArticles">
                <h2 class="divisionName">Newest articles</h2>
                <div class="FlexBox">';
            $ret .= '';
            for ($i=6; $i < count($MParts); $i++) { 
                if(($i - 5) % 2 == 0 && $i != count($MParts) - 1) {
                    $ret .= $MParts[$i]->getMediumTeaser();
                    $ret .= '</div>
                    <div class="FlexBox">';
                }
                else {
                    $ret .= $MParts[$i]->getMediumTeaser();
                }
            }
            $ret .= '</div>
            </div>';

            echo $ret;

        ?>
    </body>