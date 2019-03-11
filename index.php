
<?php

$str = !empty($_REQUEST['DATABASE_NAME']) ? $_REQUEST['DATABASE_NAME'] : '';
#$str = !empty($_post['DATABASE_NAME']) ? $_post['DATABASE_NAME'] : ''; #post 방식에서 DB명을 가져옴
#$str = $_POST['DATABASE_NAME'];
if ($str != null){ $result = "DB : {$str} ";
} else { $result = "DB명을 입력해주세요";
}
?>
<!DOCTYPE html>
<head>
 <html lang="ko">
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
 <title>DB Compare</title>
</head>

<body>
<div><?php echo $result; ?></div>
<!--
<style type="text/css" media="all">
        @import url("public/css/style.css");
    </style> 
-->
<form method="post" action="./index.php">
<input type="text" name="DATABASE_NAME" placeholder="입력">
<!--<form method="get" action="./environment.php">
<input type="text" name="DATABASE_NAME">
<!-- <input type="text" name="DATABASE_NAME_SECONDARY"> -->
 <input type="submit" value="확인"/>
</form>
</body>
</html>


<?php
require_once 'config.php';

try {
    if (!defined('FIRST_DSN')) throw new Exception('Check your config.php file and uncomment settings section for your database');
    if (!strpos(FIRST_DSN, '://')) throw new Exception('Wrong dsn format');

    $pdsn = explode('://', FIRST_DSN);
    define('DRIVER', $pdsn[0]);

    if (!file_exists(DRIVER_DIR . DRIVER . '.php')) throw new Exception('Driver ' . DRIVER . ' not found');


    define('FIRST_BASE_NAME', @end(explode('/', FIRST_DSN)));
    define('SECOND_BASE_NAME', @end(explode('/', SECOND_DSN)));

    // abstract class
    require_once DRIVER_DIR . 'abstract.php';
    require_once DRIVER_DIR . DRIVER . '.php';

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'tables';

    $additionalTableInfo = array();
    switch ($action) {
        case "tables":
            $tables = Driver::getInstance()->getCompareTables();
            $additionalTableInfo = Driver::getInstance()->getAdditionalTableInfo();
            break;
        case "views":
            $tables = Driver::getInstance()->getCompareViews();
            break;
        case "procedures":
            $tables = Driver::getInstance()->getCompareProcedures();
            break;
        case "functions":
            $tables = Driver::getInstance()->getCompareFunctions();
            break;
        case "indexes":
            $tables = Driver::getInstance()->getCompareKeys();
            break;
        case "triggers":
            $tables = Driver::getInstance()->getCompareTriggers();
            break;
        case "rows":
            $rows = Driver::getInstance()->getTableRows($_REQUEST['baseName'], $_REQUEST['tableName']);
            break;
    }


    $basesName = array(
        'fArray' => FIRST_BASE_NAME,
        'sArray' => SECOND_BASE_NAME
    );

    if ($action == 'rows') {
        require_once TEMPLATE_DIR . 'rows.php';
    } else {
        require_once TEMPLATE_DIR . 'compare.php';
    }

} catch (Exception $e) {
    include_once TEMPLATE_DIR . 'error.php';
}
