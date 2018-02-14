<?php
require_once('./SmsSender.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExSmsSender
 * 
 * This class extends the base class SmsSender, overrides some methods of the 
 * base class and adds a new functionality. class methods are static and 
 * represent a way to interact with the class MySql class
 *
 * @author yuriy
 */


class ExSmsSender extends SmsSender{
    
    /*
     * A static method that takes a link to connect to the database. returns 
     * all records stored in the database table
     */
    public static function getAll($db){
       $res = $db->SelectAll(self::TABLE);
       return $res;
    }
    /*
     * test xtatic method that shows all items in database
     * returns nothing
     */
    public static function showAll ($db):void{
       
        $res = $db->SelectAll(self::TABLE);
        foreach ($res as $item) {
            echo $item["TEXT"].'<br>';
        }
        echo $db->GetItemsCount(self::TABLE);
        
    }
    /*
     * this method overrides method from base class. now it doesn't convert data
     * to array format
     * returns nothing
     */
    public static function createNew($db,String $text):void{
        //$insert = array('TEXT' => self::clearText($text));
        $db->SQLInsert(self::TABLE, $text);
        
    }
    /*
    * A static method that takes as a parameter a connection to the database 
    * connection and the page number to be displayed, outputting database 
    * records on a page-by-page basis.
    * returns nothing
    */
    public static function ShowByPages($db,?int $pageNumber):void{
        $quantity=3;
        $limit=3;

        if (isset($pageNumber)){
            $page= $pageNumber;
        }
        else{
            $page = 0;
        }
        if(!is_numeric($page)) $page=1;
        if ($page<1) $page=1;
        $num = $db->GetItemsCount(ExSmsSender::TABLE);
        $pages = $num/$quantity;
        $pages = ceil($pages);
        $pages++; 
        if ($page>$pages) $page = 1;
        echo '<strong >Страница  ' . $page .'</strong><br /><br />'; 
        if (!isset($list)) $list=0;
        $list=--$page*$quantity;
        $result = $db->SelectByCount(ExSmsSender::TABLE,$quantity,$list);
        $num_result = mysqli_num_rows($result);
        echo '<form id="deleteForm" action="index.php" method="post">';
        echo "<table><tr><th></th><th>text</th><th>is sended</th><th> </th></tr>";
        for ($i = 0; $i<$num_result; $i++) {
            $row = mysqli_fetch_array($result);
            //echo '<div><strong>' . $row["TEXT"] . '</strong><br />';
            echo "<tr><td><input type = 'checkbox'  name='".$row['ID']."'  id = '".$row['ID']."'/><label for=\"cb1\"></label></td>";
            echo "<td>".$row['TEXT']."</td>"."<td>".$row['IS_SENDED']."<td>"."</td>";
        }
        echo "</table>";
        echo '<input type="submit" id="delete" name="delete" value="delete">';
        echo '</form>';
        echo '<br>';
        echo 'Страницы: ';
        if ($page>=1) {
            echo '<a href="' . 'index.php' . '?page=1"><<</a> &nbsp; ';
            echo '<a href="' . 'index.php' . '?page=' . $page . 
            '">< </a> &nbsp; ';
        }

        $thisPage = $page+1;

        $start = $thisPage-$limit;

        $end = $thisPage+$limit;

        for ($j = 1; $j<$pages; $j++) {

            if ($j>=$start && $j<=$end) {

                if ($j==($page+1)) echo '<a href="' . 'index.php' . 
                '?page=' . $j . '"><strong style="color: #df0000">' . $j . 
                '</strong></a> &nbsp; ';

                else echo '<a href="' . 'index.php' . '?page=' . 
                $j . '">' . $j . '</a> &nbsp; ';
            }
        }

        if ($j>$page && ($page+2)<$j) {

            echo '<a href="' . 'index.php' . '?page=' . ($page+2) . 
            '"> ></a> &nbsp; ';

            echo '<a href="' . 'index.php' . '?page=' . ($j-1) . 
            '">>></a> &nbsp; ';
        }
    }
    
    
    /*
     * A static method that takes a link to connect to the database. Sends sms 
     * messages to users and notifies about it.
     * returns boolean
     */
    public static function notify($db): bool{
        $sql = 'SELECT * FROM ' . self::TABLE . ' WHERE IS_SENDED = 0 ORDER BY RAND()';
        $exists = $db->SelectOne($sql);

        
        if (!$exists) {
            self::sendEmail('SMS NOT FOUND');
            return false;
        }
        self::sendSms($exists['TEXT']);
        self::sendEmail($exists['TEXT']);

        $exists['IS_SENDED'] = 1;
        $db->SQLUpdate(self::TABLE, $exists['ID']);

        return true;
    }
    /*
     * A static method that takes a link to connect to the database and index 
     * the record. removes from the database an entry with the specified 
     * identifier
     * returns nothing
     */
    public static function delete($db,int $index):void {
        $db->Delete(self::TABLE, $index);
    }
    
    
}
