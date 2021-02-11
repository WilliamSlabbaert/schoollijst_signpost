<?php
include_once 'readonly-conn.php';

function createByodOrdersDump(){
    global $dbname;
    global $conn;

    if($result = $conn->query("SHOW FULL TABLES WHERE Table_Type != 'VIEW';")){
        $tableNames = $result->fetch_all();
        $createQuery = "";
        
      
        foreach($tableNames as $tmp){
            $path = "C:/Users/william.slabbaert/Downloads/dump_{$tmp[0]}.sql";
            $insertQuery = "INSERT INTO `{$dbname}`.`{$tmp[0]}` (";

            /*-------------------------------------*/
            // Setting up dump for table's
            /*-------------------------------------*/

            if($create = $conn->query("SHOW CREATE TABLE `{$tmp[0]}`")){
               $table = $create->fetch_all();
               foreach($table as $cr){
                   $CreateQuery = substr_replace($cr[1]," `{$dbname}`.",12,0);
                   $createQuery.="DROP TABLE IF EXISTS `{$dbname}`.`{$cr[0]}`;\n ".$CreateQuery.";\n\n";
               }
            }

            /*-------------------------------------*/
            // Setting up dump for items
            /*-------------------------------------*/

            if($Items = $conn->query("DESCRIBE `{$dbname}`.`{$tmp[0]}`")){
                $rows = $Items->fetch_all();
                foreach($rows as $row)
                    $insertQuery .= $row[0].",";
                $ntemp = substr_replace($insertQuery,") VALUES (",-1);
                $insertQuery = $ntemp;
            }
            file_put_contents($path, "");
            
            /*-------------------------------------*/
            // Write items 
            /*-------------------------------------*/ 

            if($Items = $conn->query("SELECT * FROM `{$dbname}`.`{$tmp[0]}`;")){
                $file = fopen($path, 'w');
                $rows = $Items->fetch_all();
                foreach($rows as $row){
                    $temp = "";
                    $temp2 = $insertQuery;
                    for($i = 0; $i< count($row);$i++ ){
                        if($row[$i] === null or empty($row[$i]))
                            $temp2.="NULL,";
                        else
                            $temp2.=$row[$i].",";
                        $temp = substr_replace($temp2,");\n\n",-1);
                    }
                    fwrite($file,  $temp. "\n\n");
                }
            }
        }
        /*-------------------------------------*/
        // Write table construction 
        /*-------------------------------------*/
        file_put_contents("C:/Users/william.slabbaert/Downloads/dump_tables.sql", $createQuery);
    }

}
createByodOrdersDump();

?>