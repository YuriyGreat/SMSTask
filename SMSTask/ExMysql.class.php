<?php
require_once('./mysql.class.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExMysql
 * 
 * the class extends the base class. override obsolete methods and adds new 
 * functionality
 *
 * @author yuriy
 */
class ExMysql extends mysql{
    //put your code here
    
    private $link;
    /*
     * the constructor was overwritten by the updated regulations of versions 
     * 7.0 and higher
     */
    public function construct($host, $port, $user, $password, $database)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->dbName = $database;
        $this->Connect();
    }
    /*
     * return link fot database connection
     */
    public function GetLink(){
        return $link;
    }
    /*
     * connect to datadase
     * returns link to database connection
     */
    public function Connect(){
        $this->link=mysqli_connect($this->host,$this->user,$this->password,$this->dbName)
                or die("error ".mysqli_error($this->link));
      
        return $this->link;
    }
    /*
     * close database connection
     */
    public function Close($link): void{
        mysqli_close($link);
    }
    /*
     * select and return all items from database
     */
    public function SelectAll(String $table){
        $query1="SELECT * FROM ".$table;
	$result = mysqli_query($this->link, $query1) or die("Ошибка " . mysqli_error($this->link));
        return $result;
    }
    /*
     * runs query, that give as parameter. return result or false
     */
    public function SelectOne(String $query){
     if ($result = mysqli_query($this->link, $query)) {   
        //if ($result = mysql_query($query, $this->dbh)) {
            $rec = mysqli_fetch_array($result);
           
            return $rec;
        } else {
            
            $this->Error($query);
        }
        return false;
    }
    /*
     * insert in table users values
     */
    public function SQLInsert(String $table, String $values): void{
        $query = "INSERT INTO $table (TEXT) VALUES('$values')";
       //$this->Insert($table, $values);
        $result = mysqli_query($this->link, $query) or die("Ошибка " . mysqli_error($this->link));
    }
    /*
     * update datadase item from the table by id
     */
    public function SQLUpdate(String $table, int $id, String $ndx = "ID"):void{
       $query = "UPDATE $table SET IS_SENDED=1 WHERE ID = $id ";
       $result = mysqli_query($this->link, $query) or die("Ошибка " . mysqli_error($this->link));
    }
    /*
     * select and return items from database from $list position
     */
    public function SelectByCount(String $table, int $count, int $list){
        $query1="SELECT * FROM ".$table;
	//$result = mysqli_query($this->link, $query1) or die("Ошибка " . mysqli_error($this->link));
        $result = mysqli_query($this->link, "SELECT * FROM ".$table." LIMIT $count OFFSET $list") or die("Ошибка " . mysqli_error($this->link));
        return $result;
    }
    /*
     * return count of items in database
     */
    public function GetItemsCount(String $table):Int{
        $query1="SELECT * FROM ".$table;
	$result = mysqli_query($this->link, $query1) or die("Ошибка " . mysqli_error($this->link));
        
        $count = 0;
        foreach ($result as $value) {
            $count++;
        }
        return $count;
    }
    /*
     * delete item from database by id
     */
    public function Delete(String $table, String $id):void{
        $query= "DELETE FROM $table WHERE ID = '$id'";
        mysqli_query($this->link, $query); 
    }
    /*
     * print any sql query errors
     */
    public function Error(String $query = ""):bool{
        echo mysqli_errno() . ": " . mysqli_error() . "<br>$query";
        //echo mysql_errno() . ": " . mysql_error() . "<br>$query";
        return 1;
    }
}
