<DOCTYPE html>

<head>
        <link rel="stylesheet" href="searchStyle.css">
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

            $artList = array();

            switch ($_GET['type']) {
                case 'text':
                    $artList = searchEngine::searchText($_GET['search']);
                    break;
                case 'main-section':
                    $artList = searchEngine::searchSection($_GET['search'], 1);
                    break;
                case 'Section':
                    $artList = searchEngine::searchSection($_GET['search'], 0);
                    break;
                default:
                    
                    break;
            }

            $ret = '<div class="results">
            <h1 class="searchName">'.ucfirst($_GET['search']).'</h1>
            <div class="FlexBox">';

            if(1 > count($artList)) {
                $ret .= '<img src="resources/idk.png" class="nothing">';
            }

            for ($i=1; $i < count($artList) + 1; $i++) { 
                if ($i % 3 == 0 && $i != count($artList) + 1) {
                    $ret .= $artList[$i - 1]->getMediumTeaser() . '</div>
                    <div class="FlexBox">';
                }
                else {
                    $ret .= $artList[$i - 1]->getMediumTeaser();
                }

                if ($i == count($artList) + 1) {
                    $ret .= '</div></div>';
                }
            }
            echo $ret;

        ?>
    </body>




</DOCTYPE>