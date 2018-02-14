<?php
//declare(strict_types=1);
require_once('./config.php.ini');
require_once('./mysql.class.php');
require_once('./ExMysql.class.php');
require_once('./ExSmsSender.php');
$_SERVER['SCRIPT_NAME']="index.php";

set_time_limit(0);
$db = new ExMysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME); // connecting to database

header('Content-Type: text/html; charset=utf-8');
/*
 * A static method that takes as a parameter a connection to the database 
 * connection and the page number to be displayed, outputting database 
 * records on a page-by-page basis.
 * returns nothing
 */
ExSmsSender::ShowByPages($db,$_GET['page']);
?>

<html>
<head>
    
    <title>Тестовое задание</title>
    <script src="ButtonClick.js"></script> 
</head>
<body>
 
<?php


/*
 * if the "delete" field is set in the superglobal array, then ...
 */
if (isset($_POST['delete']))
{
    /*
     * A static method that takes as a parameter a reference to the 
     * connection to the database. returns an SQL array containing all 
     * elements of the table
     */
    $result=ExSmsSender::getAll($db);
    
    foreach($result as $item){
        /*
         * select id of batabase items
         */
        $itemId=$item['ID'];
        /*
         * if a field with a database table entry ID is set in the superglobal 
         * array, then ...
         */
        if (isset($_POST["$itemId"]))
        {
            /*
             * A static method that takes as a parameter a reference to the 
             * connection to the database and the identifier of the database 
             * element that must be deleted. Deletes an element with the 
             * specified ID of their table.
             * Returns nothing
             */
            ExSmsSender::delete($db, $itemId);
        }
    }
}

/*
 * If the field with the addsms ID is set in the superglobal array, then ...
 */
if ($_REQUEST['addsms']) {
    /*
     * If the field with the TEXT ID is set in the superglobal array, then ...
     */
    if ($_POST['TEXT']){
        /*
         * A static method that accepts as a parameter a connection to the 
         * database and a string with the text of the SMS message. adds a 
         * message to the table
         * returns nothing
         */
        ExSmsSender::createNew($db, $_POST['TEXT']);
    }
    //if ($_REQUEST['save']) {
        //ExSmsSender::createNew($db, $_REQUEST['TEXT']);
    //}

    //echo '<form method="post">'
    //        . '<textarea name="TEXT" rows="5" cols="40"></textarea>'
    //        . '<br>'
    //        . '<input type="hidden" name="save" value="1">'
    //        . '</form>';
}
/*
 * randomly selects an SMS message and sends it to users 
 * (using the static Notify method)
 */
else {
    $file = './last.txt';

    if (is_file($file)) {
        $date = file_get_contents($file);

        $date = strtotime($date);

        if (time() - $date < 3600 * 24 * DAYS_COUNT) exit;
        if (date('G') < 9 || date('G') > 12) exit; // send sms from 9 to 12
        if (in_array(date('N'), array(6, 7))) exit; // send sms from monday to friday

        unlink($file);
    }

    $need = rand(0, 1);
    
    if ($need) {
        if (ExSmsSender::notify($db)) {
            file_put_contents($file, date('Y-m-d H:i:s'));
        }
    }
}
 
?>
  
    
    
    <form id="searchform" method="post"> 
    <div>  
        <textarea name="TEXT" id="TEXT" rows="5" cols="40"></textarea>
        <input type="submit" name="save" value="1"/>
        <input type="submit" name="addsms" id="addsms" value="Отправить"/>
    </div> 
    </form> 
    <div id="search_results"></div> 
</body>


</html>