<?php
    include 'config.php';
    date_default_timezone_set("Asia/Bangkok");
    $rowid = "aaaaa";//$date = "all";
    if (isset($_GET['rowid'])) {
        $rowid = $_GET['rowid'];
    }
    if(strlen($rowid)==0)
        $rowid = 1;

    $conn = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);

    if (!$conn) {
        die("Database connection failed: " . mysqli_error($conn));
    }

    mysqli_set_charset($conn, "utf8");
    mysqli_select_db($conn, DB_DATABASE);

   /* if($date === "all")
        $query = "SELECT id,sessionID,sessionDate,dependentVariable,phraseNumber,language,phraseShown,phraseTyped,editdistance,typingTime,round((char_length(phraseTyped)-1)/(typingTime/60000.0)) cpm, least(char_length(phraseTyped),char_length(phraseShown)) minLen, greatest(char_length(phraseTyped),char_length(phraseShown)) maxLen FROM experiment1 ORDER BY id desc";
    else*/
    //$query = "SELECT id,sessionID,sessionDate,dependentVariable,phraseNumber,language,phraseShown,phraseTyped,editdistance,typingTime,round((char_length(phraseTyped)-1)/(typingTime/60000.0)) cpm, least(char_length(phraseTyped),char_length(phraseShown)) minLen, greatest(char_length(phraseTyped),char_length(phraseShown)) maxLen FROM experiment1 WHERE sessionID substr(sessionID,1,5) in('PF29B','P4P29','PB4FF','29PP2','D28CD','422P2','2818H','8C21C','B2F24','FFFF4','4F229','HCHDH','2B2P4','11HHH','CDCHC','H1DHD','1HDC1','1H111','22DH1','2FFPF','H21D1','BFBBF','HC8HC','P4BPB' ) ORDER BY id desc";
        $query = "SELECT id,sessionID,sessionDate,dependentVariable,phraseNumber,phraseShown,phraseTyped,editdistance,typingTime,round((char_length(phraseTyped)-1)/(typingTime/60000.0)) cpm, least(char_length(phraseTyped),char_length(phraseShown)) minLen, greatest(char_length(phraseTyped),char_length(phraseShown)) maxLen FROM experiment1 WHERE substr(sessionID,1,5) in ('PF29B','P4P29','PB4FF','29PP2','D28CD','422P2','2818H','8C21C','B2F24','FFFF4','4F229','HCHDH','2B2P4','11HHH','CDCHC','H1DHD','1HDC1','1H111','22DH1','2FFPF','H21D1','BFBBF','HC8HC','P4BPB','9424B','P9BP4','1821D','P2P2P') AND id > $rowid ORDER BY id desc";
    //echo $query;
//SELECT id,sessionID,sessionDate,dependentVariable,phraseNumber,language,phraseShown,phraseTyped,editdistance,typingTime,round((char_length(phraseTyped)-1)/(typingTime/60000.0)) cpm, least(char_length(phraseTyped),char_length(phraseShown)) minLen, greatest(char_length(phraseTyped),char_length(phraseShown)) maxLen FROM experiment1 WHERE sessionID LIKE "%_p%_%" AND substr(sessionID,1,5) in('PF29B','P4P29','PB4FF','29PP2','D28CD','422P2','2818H','8C21C','B2F24','FFFF4','4F229','HCHDH','2B2P4','11HHH','CDCHC','H1DHD','1HDC1','1H111','22DH1','2FFPF','H21D1','BFBBF','HC8HC','P4BPB' ) ORDER BY id desc

    $result = mysqli_query($conn, $query);
    if ($result === false)
        die("Query failed: " . mysqli_error($conn).$query);
   
   
    $tablePrefix = "<table><tr><th colspan='1'>Row ID</th><th colspan='1'>Session ID</th><th colspan='1'>Session date</th><th colspan='1'>Variable</th><th colspan='1'>Phrase#</th><th colspan='1'>Shown phrase</th><th colspan='1'>Typed phrase</th><th colspan='1'>ED</th><th colspan='1'>Typing time</th><th colspan='1'>cpm</th><th colspan='1'>Min len</th><th colspan='1'>Max len</th></tr>";
    $tableSuffix = "</table>";
    $tableBody="";


    while ($row = mysqli_fetch_array($result)) {

        $timeInMins = ($row[9]/60000.0);
        $cpm=0;
        $minStrLen = $row[11];//min(mb_strlen($row[6]),mb_strlen($row[7]));
        $maxStrLen =  $row[12];//max(mb_strlen($row[6]),mb_strlen($row[7]));
        /*console.log("Strs:".$row[6].",".$row[7]);
        console.log($minStrLen);
        console.log($maxStrLen);*/
        

        //cpm
        /*if($timeInMins>0)
            $cpm = round(((mb_strlen($row[7])/3)-1)/$timeInMins,0);*/
            
        $cpm =  $row[10];

        //ED
        if($row[8]>$minStrLen)
            $errorrate = 100;
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
        /*$tableBody.="<td colspan='1'>" . $errorrate. "%</td>";*/
        $tableBody.="<td colspan='1'>" . $row[8]. "</td>";
        $tableBody.="<td colspan='1'>" . $row[9]. "</td>";
        $tableBody.="<td colspan='1'>" . $row[10]. "</td>";
        $tableBody.="<td colspan='1'>" . $row[11]. "</td>";
        /*$tableBody.="<td colspan='1'>" . $row[12]. "</td>";
        $tableBody.="<td colspan='1'>" . $row[13]. "</td>";*/

        /*$tableBody.="<td colspan='1'>" . $cpm. "</td>";*/
        $tableBody.="</tr>\n";        
    }
    
    mysqli_close($conn);
    echo $tablePrefix.$tableBody.$tableSuffix;


    ?>



