<?php
    include 'config.php';
    date_default_timezone_set("Asia/Bangkok");
    $date = "all";
    if (isset($_GET['date'])) {
        $date = $_GET['date'];
    }
    if(strlen($date)==0)
        $date = "all";
   
    $conn = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);

    if (!$conn) {
        die("Database connection failed: " . mysqli_error($conn));
    }

    mysqli_set_charset($conn, "utf8");
    mysqli_select_db($conn, DB_DATABASE);

    if($date === "all")
        $query = "SELECT id,SessionID,sessionDate,dependentVariable,phraseNumber,language,phraseShown,phraseTyped,editdistance,typingTime,backspaces,IncorrNF,IncorrF,Correct,Fixed,round((char_length(phraseTyped)-1)/(typingTime/60000.0)) cpm, least(char_length(phraseTyped),char_length(phraseShown)) minLen, greatest(char_length(phraseTyped),char_length(phraseShown)) maxLen FROM experiment1 ORDER BY id desc";
    else
        $query = "SELECT id,SessionID,sessionDate,dependentVariable,phraseNumber,language,phraseShown,phraseTyped,editdistance,typingTime,backspaces,round((char_length(phraseTyped)-1)/(typingTime/60000.0)) cpm, least(char_length(phraseTyped),char_length(phraseShown)) minLen, greatest(char_length(phraseTyped),char_length(phraseShown)) maxLen  FROM experiment1 WHERE sessionDate LIKE '".$date."%' ORDER BY id desc";


    $result = mysqli_query($conn, $query);
    if ($result === false)
        die("Query failed: " . mysqli_error($conn));
   
   
    $tablePrefix = "<table id='data'><tr><th colspan='1'>Row ID</th><th colspan='1'>Session ID</th><th colspan='1'>Session date</th><th colspan='1'>Variable</th><th colspan='1'>Phrase#</th><th colspan='1'>Language</th><th colspan='1'>Shown phrase</th><th colspan='1'>Typed phrase</th><th colspan='1'>Error rate</th><th colspan='1'>Speed(cpm)</th><th colspan='1'>Backspace Count</th><th colspan='1'>Corrected Error Rate</th><th colspan='1'>Uncorrected Error rate</th><th colspan='1'>Total Error rate</th></tr>";
    $tableSuffix = "</table>";
    $tableBody="";


    while ($row = mysqli_fetch_array($result)) {

        $timeInMins = ($row[9]/60000.0);
        $backspacecount=$row[10];
        $cpm=0;
        $x=0;
        $y=0;
        $z=0;
        $minStrLen = $row[16];//min(mb_strlen($row[6]),mb_strlen($row[7]));
        $maxStrLen =  $row[17];//max(mb_strlen($row[6]),mb_strlen($row[7]));
        /*console.log("Strs:".$row[6].",".$row[7]);
        console.log($minStrLen);
        console.log($maxStrLen);*/
        $inf=$row[11];
        $ifc=$row[12];
        $corr=$row[13];
        $fix=$row[14];
        $cer=0;
        $ter=0;
        $uer=0;
        if($ifc!=0 || $inf!=0 || $corr!=0)
        {
        $x=($ifc)/($ifc+$inf+$corr);
        $cer=round($x*100,4);
        $y=($inf)/($ifc+$inf+$corr);
        $uer=round($y*100,4);
        $z=($ifc+$inf)/($ifc+$inf+$corr);
        $ter=round($z*100,4);
        }
            
        $cpm =  $row[15];
     
        //error rate
        if($row[8]>$minStrLen){

            $errorrate = 100;
        }
        else
            $errorrate = round(($row[8]/$maxStrLen )*100,2);
       
        $tableBody.="\n<tr>";
        $tableBody.="<td colspan='1'>" . $row[0]. "</td>";
        $tableBody.="<td colspan='1'>" . $row[1] . "</td>";
        $tableBody.="<td colspan='1'>" . $row[2] . "</td>";
        $tableBody.="<td colspan='1'>" . $row[3] . "</td>";
        $tableBody.="<td colspan='1'>" . $row[4] . "</td>";
        $tableBody.="<td colspan='1'>" . $row[5] . "</td>";
        $tableBody.="<td colspan='1'>" . $row[6] . "</td>";
        $tableBody.="<td colspan='1'>" . $row[7] . "</td>";
        $tableBody.="<td colspan='1'>" . $errorrate. "%</td>";
        //$tableBody.="<td colspan='1'>" . $row[8]. "</td>";
        //$tableBody.="<td colspan='1'>" . $row[9]. "</td>";
        $tableBody.="<td colspan='1'>" . $cpm. "</td>";
        $tableBody.="<td colspan='1'>" . $backspacecount. "</td>";
        $tableBody.="<td colspan='1'>" . $cer. "</td>";
        $tableBody.="<td colspan='1'>" . $uer. "</td>";
        $tableBody.="<td colspan='1'>" . $ter. "</td>";
        $tableBody.="</tr>\n";        
    }
    
    mysqli_close($conn);
    echo $tablePrefix.$tableBody.$tableSuffix;


    ?>



