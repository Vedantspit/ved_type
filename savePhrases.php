<?php

    include 'config.php';
    // $lastlogdate = date("m/d/y");
    $timestamp = time();
    $currentDate = gmdate('Y-m-d', $timestamp);
	$id = "";
	$dependentVariable="var1";

	//$tapsJSON='[{"tapSequenceNumber":1,"startTimestamp":1531976733779,"endTimestamp":1531976733895},{"tapSequenceNumber":2,"startTimestamp":1531976734286,"endTimestamp":1531976734413},{"tapSequenceNumber":3,"startTimestamp":1531976734455,"endTimestamp":1531976734571},{"tapSequenceNumber":4,"startTimestamp":1531976734612,"endTimestamp":1531976734714},{"tapSequenceNumber":5,"startTimestamp":1531976734759,"endTimestamp":1531976734891},{"tapSequenceNumber":6,"startTimestamp":1531976734918,"endTimestamp":1531976735028},{"tapSequenceNumber":7,"startTimestamp":1531976735060,"endTimestamp":1531976735183},{"tapSequenceNumber":8,"startTimestamp":1531976863014,"endTimestamp":1531976863098}]';

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $id=substr($id,0,5);
        echo "got the id ".$id;

    }else
    {
    	$id = time();
        echo "dint get the id  thus id is time ".$id;
    }
    if (isset($_POST['var'])) {
        $dependentVariable = $_POST['var'];
    }

    if (isset($_POST['phrases'])) {

        $tapsJSON = $_POST['phrases'];

    }else{
    	echo "No phrase logs found";
    	return;
    }
    if (isset($_POST['backspace'])) {

        $backspaces=$_POST['backspace'];
        echo "got backspace array back";
    }
    if (isset($_POST['corrected'])) {

        $corr=$_POST['corrected'];
        echo "got corrected array back";
    }
    if (isset($_POST['incorrfix'])) {

        $ifc=$_POST['incorrfix'];
        echo "got incorrfix array back";
    }
    if (isset($_POST['incorrnot'])) {

        $inf=$_POST['incorrnot'];
        echo "got incorrnot array back";
    }
    if (isset($_POST['fixes'])) {

        $fstroke=$_POST['fixes'];
        echo "got fixes array back";
    }
    


    $taps = json_decode($tapsJSON);
    $backpresses=json_decode($backspaces);
    $corr=json_decode($corr);
    $ifc=json_decode($ifc);
    $inf=json_decode($inf);
    $fstroke=json_decode($fstroke);
    
    echo $backpresses[0];


    //echo "\n ID:".$id." dependent variable:".$dependentVariable;
    //print_r($taps);
    $insert_query = "INSERT INTO experiment1(SessionID,SessionDate,dependentVariable,phraseNumber,language,phraseShown,phraseTyped,editdistance,typingTime, keystrokes,backspaces,IncorrNF,IncorrF,Correct,Fixed) VALUES ";
    $insert_body ="";

    for($i=0;$i<count($taps);$i++){

    	if(strlen($insert_body) >0)
    		$insert_body.= ",";
        if(!$corr[$i]==0)
        {
    	$insert_body.="('".$id."','".$currentDate."','".$dependentVariable."',".$taps[$i]->phraseSequenceNumber.",'".$taps[$i]->phraseLanguage."','".$taps[$i]->phraseShown."','".$taps[$i]->phraseTyped."',".$taps[$i]->editdistance.",".$taps[$i]->timeTaken.",'".$taps[$i]->ksLogs."',".$backpresses[$i].",".$inf[$i].",".$ifc[$i].",".$corr[$i].",".$fstroke[$i].")";
        }
   	}

    $conn = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);

    if (!$conn) {
        die("Database connection failed: " . mysqli_error());
    }

    mysqli_set_charset($conn, "utf8");
    mysqli_select_db($conn, DB_DATABASE);

    $result = mysqli_query($conn,$insert_query.$insert_body);

    if($result)
    	echo "Data saved successfully";
    else
    	die("Data could not be saved: " . mysqli_error($conn)." ".$insert_query.$insert_body);



?>