<?php

// include './index.php'
?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf8"/>
		<link rel="stylesheet" href="index.css">

		
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">		
		<link href="https://fonts.googleapis.com/css?family=Open+Sans|Open+Sans+Condensed:300|Roboto" rel="stylesheet"> 
		<link href='https://fonts.googleapis.com/css?family=Ek+Mukta:300' rel='stylesheet' type='text/css'>
		<script src="editdistance.js"></script>

		<script type="text/javascript">

			var startTimestamp=-1;
			var endTimestamp=-1;
			var currentsesh=0;
			var ksLog = "";
            var count;
			var toTypeArray;
			var toTypeArrayLang = ["hn","hn","hn","hn","hn","en","en","en","en","en"]; 
			var toTypeSequenceArray = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14];
			var toTypeArrayIndex=0;
			var dependentVariable="";
			var uCode = -1;
			var session = -1;
			var oldText="";
			var nmph=-1;
			var ind=0;

			var trainingWords;
			var practiceWords;
			var practiceSession;
			var mainTask1; 
			var mainTask2;
			toTypeSequenceArray = shuffle(toTypeSequenceArray);
            console.log("hi")
			

			//var tapLog="";
			var tapLogsArray=[];
			var d;
			console.log("okay");

			function onLoad()
			{
				console.log("load");

				
				//document.getElementById("toType").innerHTML = toTypeArray[toTypeSequenceArray[0]];

				d = new Date();

				uniqueIdentifier = d.getTime();
				var randomNumber = Math.floor(Math.random() * 42);
				uniqueIdentifier=uniqueIdentifier+""+randomNumber;

				document.getElementById("tt").addEventListener("input",logg);
				
				loadPhrases();
				//return;

			}

			function logg(){

				var txt = document.getElementById("tt").value;
				//document.getElementById("counter").text += "#"+ txt;
				var diff = "";


				if(txt.length > oldText.length){

					message = "A";
					diff = txt.substring(oldText.length);

				}
				else{
					message = "B";
					diff = oldText.substring(txt.length);
				}

				console.log("new Txt len:"+txt.length + ", old text length:" + oldText.length);

				
				ksLog += "#"+message+";"+txt+";"+diff;
				console.log(ksLog);
				//console.log(message);
				//document.getElementById("counter").innerHTML = ksLog;
				oldText = txt;
			}

			function syncToServer(){
               
				var xmlhttp;
			    
			    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			        xmlhttp = new XMLHttpRequest();
			    }
			    else {// code for IE6, IE5
			        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			    }

				xmlhttp.onreadystatechange = function() {

				    if (this.readyState == 4 && this.status == 200) {
				    	
				        var responseText = this.responseText;
				        console.log("Response:"+responseText);

						
				        if(responseText !== "Data saved successfully")
				        	document.getElementById("container").innerHTML = '<span class="spanWhite">Server could not save data. Please try again.'+responseText+'</span><br/><button style="background:orange;" type="button" onclick="syncToServer()">Try again</button>';
				        else
				        	document.getElementById("container").innerHTML = '<span class="span2">Data saved.</span>'
				    }else if(this.readyState == 4 && this.status != 200){

				    	document.getElementById("container").innerHTML = '<span class="spanWhite">Server went away. Data not saved. Error code:'+this.status+'.</span><br/><button style="background:orange;" type="button" onclick="syncToServer()">Try again</button>';

				    }
				};
				var url = "savePhrases.php";
				var params ="id="+uCode+"_"+session+"_"+uniqueIdentifier+"&var="+dependentVariable+"&phrases=["+tapLogsArray+"]";
				xmlhttp.open("POST",url,true);
				xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xmlhttp.send(params);

			}
			function sendUserCode()
			{
				console.log("in the send user code functions ");
				var xmlhttp;
			    
			    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			        xmlhttp = new XMLHttpRequest();
			    }
			    else {// code for IE6, IE5
			        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			    }

				// xmlhttp.onreadystatechange = function() 
				// {

				//     if (this.readyState == 4 && this.status == 200) {
				    	
				//         var responseText = this.responseText;
				//         console.log("Response:"+responseText);

						
				//         if(responseText !== "aaah Data saved successfully")
				//         	document.getElementById("container").innerHTML = '<span class="spanWhite">Server could not save data. Please try again.'+responseText+'</span><br/><button style="background:orange;" type="button" onclick="syncToServer()">Try again</button>';
				//         else
				//         	document.getElementById("container").innerHTML = '<span class="span2">Data saved.</span>'
				//     }else if(this.readyState == 4 && this.status != 200){

				//     	document.getElementById("container").innerHTML = '<span class="spanWhite">Server went away. Data not saved. Error code:'+this.status+'.</span><br/><button style="background:orange;" type="button" onclick="syncToServer()">Try again</button>';

				//     }
				// };
				var url = "moderator.php";
				var params ="uCode="+uCode;
				xmlhttp.open("POST",url,true);
				xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xmlhttp.send(params);
			}

            function loadPhrases() {
    var xmlhttp;

    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log("back");

            var responseText = this.responseText;
            var phrasesArray = responseText.split(";");
            
            trainingWords = phrasesArray[0].split(",");
            document.getElementById("nowords").innerHTML = "Total No. of training words are " + trainingWords.length;
            practiceWords = phrasesArray[1].split(",");
            practiceSession = phrasesArray[2].split(",");
            mainTask1 = phrasesArray[3].split(",");
            mainTask2 = phrasesArray[4].split(",");
            
            // Shuffle and limit phrases for each session
            trainingWords = myshuffle(trainingWords);
            practiceWords = myshuffle(practiceWords);
            practiceSession = myshuffle(practiceSession);
            mainTask1 = myshuffle(mainTask1);
            mainTask2 = myshuffle(mainTask2);

            // Limit phrases for each session based on nmph
            var startIdx = toTypeArrayIndex * nmph;
            var endIdx = startIdx + nmph;
            toTypeArray = trainingWords.slice(startIdx, endIdx);

            // Display the first phrase
            document.getElementById("toType").innerHTML = toTypeArray[0];

            console.log("array with " + nmph + " phrases: " + toTypeArray);
            currentsesh++;
        } else if (this.readyState == 4 && this.status != 200) {
            console.log("Something went wrong");
            document.getElementById("container").innerHTML = '<span class="spanWhite">Server went away. Data not saved. Error code:' + this.status + '.</span><br/><button type="button" onclick="syncToServer()">Try again</button>';
        }
    };

    var url = "loadPhrases.php";
    var params = "id=" + uCode + "_" + session;
    xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(params);
}
			trainingWords = shuffle(trainingWords);
        


function myshuffle(sourceArray) {
    for (var i = 0; i < sourceArray.length - 1; i++) {
        var j = i + Math.floor(Math.random() * (sourceArray.length - i));

        var temp = sourceArray[j];
        sourceArray[j] = sourceArray[i];
        sourceArray[i] = temp;
    }
    return sourceArray;
}
            function getRandomElementsFromArray(array, numElements) 
			{
            // Shuffle the array
            const shuffledArray = array.slice().sort(() => Math.random() - 0.5);
    
            // Slice the first numElements from the shuffled array
            return shuffledArray.slice(0, numElements);
            }
			// function saveDependentVariable(dVariable){
			// 	count=0;
            //     ind=0;
            //     cases=[]
			// 	dependentVariable = dVariable;
			// 	nmph=document.getElementById("nmph").value;
			// 	uCode = document.getElementById("ucode").value;
			// 	session = document.getElementById("session").value;
			// 	console.log("no. of phrases are "+nmph+"no. of sessions are "+session+"and participant id is "+uCode);
            
			// 	//console.log(uCode+","+sCode);

			// 	if(navigator.onLine == true){

			// 		//loadPhrases();

			// 		//dependentVariable = dVariable;
			// 		//document.getElementById("variable1").style.display = "none";
			// 		//document.getElementById("variable2").style.display = "none";
			// 		document.getElementById("buttoncontainer").style.display = "none";
			// 		document.getElementById("container").style.display = "inline-block";
                    
			// 		if(currentsesh!=session)
			// 		{
			// 			document.getElementById("sessno").innerHTML = "SESSION "+(ind+1);
						
			// 			trainingWords=getRandomElementsFromArray(trainingWords,nmph);
			// 			document.getElementById("toType").innerHTML = trainingWords[0];
			// 			toTypeArray=trainingWords.slice(1);
			// 			console.log("array with "+nmph-1 + "phrases" + toTypeArray);
			// 		    currentsesh++;
			// 		}
                    
					

			// 	}else{

			// 		document.getElementById("buttoncontainer").innerHTML= "Please check your internet connection";
			// 	}


			// }
			function saveDependentVariable(dVariable) {
    count = 0;
    ind = 0;
    cases = [];
    dependentVariable = dVariable;
    nmph = document.getElementById("nmph").value;
    uCode = document.getElementById("ucode").value;
    session = document.getElementById("session").value;
    console.log("no. of phrases are " + nmph + " no. of sessions are " + session + " and participant id is " + uCode);

    // Check if it's a new session
    if (currentsesh != session) {
        document.getElementById("sessno").innerHTML = "SESSION " + (ind + 1);

        // Shuffle and select phrases for the new session
        if (toTypeSequenceArray.length > 0) {
            trainingWords = shuffle(trainingWords);
            toTypeArray = getRandomElementsFromArray(trainingWords, nmph);
            toTypeArrayIndex = 0;
            document.getElementById("toType").innerHTML = toTypeArray[toTypeArrayIndex];
        }

        currentsesh++;
    } else {
        // Check if there are more phrases in the current session
        if (toTypeArrayIndex < toTypeArray.length) {
            document.getElementById("toType").innerHTML = toTypeArray[toTypeArrayIndex];
        } else {
            // If no more phrases, display "Done"
            document.getElementById("container").innerHTML = '<span class="span2">Done.</span>';
            console.log("Calling sync to server");
            syncToServer();
        }
    }
}

            function showNextPhrase() {
    count = 0;
    toTypeArrayIndex++;

    // Check if there are more phrases in the current session
    if (toTypeArrayIndex < toTypeArray.length) {
        var toType = toTypeArray[toTypeArrayIndex];
        document.getElementById("toType").innerHTML = toType;

        // Cleanup
        document.getElementById("cpm").innerHTML = " ";
        document.getElementById("error").innerHTML = " ";
        document.getElementById("tt").value = "";
        ksLog = "";
        oldText = "";

        startTimestamp = -1;
        endTimestamp = -1;

        // Update session number
        document.getElementById("sessno").innerHTML = "SESSION " + (ind + 1);
    } else {
        // If there are no more phrases, display "Done" message
        document.getElementById("container").innerHTML = '<span class="span2">Done.</span>';
        console.log("Calling sync to server");
        syncToServer();
    }
    ind++;
}



			
			function cleanUp(str){

				while(str.search("  ") >0){
					//str = str.replace("sss","s");
					str = str.replace("  "," ");

				}

				str = str.replace("ज़","ज़");

				
				return str.toLowerCase();
			}
			function backSpaceCount()
			{
				
		    let el = document.getElementById('tt'); 
		    el.addEventListener('keydown', function (event) {
			
			const key = event.key;
			if (key === "Backspace" || key === "Delete") {
				count++;
			}
			

            console.log("no. of times backspace was pressed is "+count);
			return count;
		}); 

           
			}

			function calculate(){
				

				toType = document.getElementById("toType").innerHTML;//toTypeArray[toTypeSequenceArray[toTypeArrayIndex]];
				console.log("VEDANT HERE calc:"+toType);
				var textSoFar = document.getElementById("tt").value;
                console.log("VEDANT HERE text so far :"+textSoFar);
                
				if(textSoFar.length==0)
					return;

				if(startTimestamp<0){

					startTimer();

				}
				updateTimestamp();

				/*var str=textSoFar;
				str = str.trim();
				str = cleanUp(str);*/
				document.getElementById("bcc").innerHTML =backSpaceCount()+" presses";

				document.getElementById("cpm").innerHTML = getCPM() + " cpm";
                console.log("this is the calculated CPM --> " +getCPM());

				//document.getElementById("error").innerHTML = getErrorRate() + " %";
				document.getElementById("error").innerHTML = getErrorRate(toType,textSoFar) + " %";
	            console.log("this is the calculated error rate  --> " +getErrorRate(toType,textSoFar));
				/*var timeTaken = (endTimestamp - startTimestamp)/60000.0;

				if(timeTaken>0){
					var cpm=Math.round((str.length-1)/timeTaken);
					var maxStringLength = Math.max(str.length,toType.length);
					var minStringLength = Math.min(str.length,toType.length);

					var ed = damerauLevenshteinDistance(toType,str);
					if(ed>minStringLength)
						var errorrate = 100;
					else
						var errorrate = Math.round((ed/maxStringLength)*100.0,2);
					
					document.getElementById("cpm").innerHTML = cpm + " cpm";
					document.getElementById("error").innerHTML = errorrate + " %";
					//document.getElementById("adj").innerHTML = str;

				}else{

					document.getElementById("cpm").innerHTML = 0;
				}*/
			}
			function damerauLevenshteinDistance(seq1,seq2)
			{ 
				
				var len1=seq1.length;
                 var len2=seq2.length;
    var i, j;
    var dist;
    var ic, dc, rc;
    var last, old, column;

    var weighter={
        insert:function(c) { return 1.; },
        delete:function(c) { return 0.5; },
        replace:function(c, d) { return 0.3; }
    };

    /* don't swap the sequences, or this is gonna be painful */
    if (len1 == 0 || len2 == 0) {
        dist = 0;
        while (len1)
            dist += weighter.delete(seq1[--len1]);
        while (len2)
            dist += weighter.insert(seq2[--len2]);
        return dist;
    }

    column = []; // malloc((len2 + 1) * sizeof(double));
    //if (!column) return -1;

    column[0] = 0;
    for (j = 1; j <= len2; ++j)
        column[j] = column[j - 1] + weighter.insert(seq2[j - 1]);

    for (i = 1; i <= len1; ++i) {
        last = column[0]; /* m[i-1][0] */
        column[0] += weighter.delete(seq1[i - 1]); /* m[i][0] */
        for (j = 1; j <= len2; ++j) {
            old = column[j];
            if (seq1[i - 1] == seq2[j - 1]) {
                column[j] = last; /* m[i-1][j-1] */
            } else {
                ic = column[j - 1] + weighter.insert(seq2[j - 1]);      /* m[i][j-1] */
                dc = column[j] + weighter.delete(seq1[i - 1]);          /* m[i-1][j] */
                rc = last + weighter.replace(seq1[i - 1], seq2[j - 1]); /* m[i-1][j-1] */
                column[j] = ic < dc ? ic : (dc < rc ? dc : rc);
            }
            last = old;
        }
    }

    dist = column[len2];
    return dist;
}
			function getCPM(){

				var textSoFar = document.getElementById("tt").value;
				var str=textSoFar;
				str = str.trim();
				str = cleanUp(str);
				var cpm=0;
	
				var timeTaken = (endTimestamp - startTimestamp)/60000.0;

				if(timeTaken>0)
					cpm=Math.round((str.length-1)/timeTaken);
				else
					cpm=0;

				return cpm;
			}

			function getErrorRate(toType, textSoFar){

				//toType = toTypeArray[toTypeSequenceArray[toTypeArrayIndex]];
				if(toType.length>0)
					toType = toType.trim();
				console.log("err:"+toType+", "+textSoFar);

				//var textSoFar = document.getElementById("tt").value;
				var str=textSoFar;
				str = str.trim();
				str = cleanUp(str);
				var maxStringLength = Math.max(str.length,toType.length);
				var minStringLength = Math.min(str.length,toType.length);
				var errorrate;

			var ed = damerauLevenshteinDistance(toType,str);
				if(ed>minStringLength)
					errorrate = 100;
				else
					errorrate = Math.round((ed/maxStringLength)*100.0,2);

				return errorrate;

			}

			function getStarRating(correctnessRatio){

				var starsVal=0;

				if( correctnessRatio == 0){

					starsVal = "*****";
					return "five";

		
				}else if(correctnessRatio > 0 && correctnessRatio <= 20){

					starsVal = "****";
					return "four";

				}else if(correctnessRatio > 20 && correctnessRatio <= 40){

					starsVal = "***";
					return "three";

				}else if(correctnessRatio > 40 && correctnessRatio <= 60){

					starsVal = "**";
					return "two";

				}else{

					starsVal = "*";
					return "one";
				}

			}

			function getED(textSoFar,toType) {

				// body...
				//toType = toTypeArray[toTypeSequenceArray[index]];
				toType = toType.trim();
				//var textSoFar = document.getElementById("tt").value;
				var str=textSoFar;
				str = str.trim();
				str = cleanUp(str);

				var ed = damerauLevenshteinDistance(toType,str);

				return ed;

			}

			function startTimer(){
				//console.log("start timer");
				startTimestamp = (new Date()).getTime() ;
				//console.log("StartTS: " + ";" +startTimestamp);
			}
			function updateTimestamp(){
				//console.log("update timer");
				endTimestamp = (new Date()).getTime();
				//console.log("EndTS: " + ";" +endTimestamp);
			}		


			function shuffle(arra1) {
    			var ctr = arra1.length, temp, index;

				// While there are elements in the array
				    while (ctr > 0) {
				// Pick a random index
				        index = Math.floor(Math.random() * ctr);
				// Decrease ctr by 1
				        ctr--;
				// And swap the last element with it
				        temp = arra1[ctr];
				        arra1[ctr] = arra1[index];
				        arra1[index] = temp;
				    }
				    return arra1;
				}

	/*$(document).on("keypress", 'form', function (e) {
	    var code = e.keyCode || e.which;
	    if (code == 13) {
	        e.preventDefault();
	        showNextPhrase();
	        return false;
	    }
	});	*/	

	function disableEnterKey(e){ 
		 
		//create a variable to hold the number of the key that was pressed      
		var key; 

		 
		    //if the users browser is internet explorer 
		    if(window.event){ 
		      
		    //store the key code (Key number) of the pressed key 
		    key = window.event.keyCode; 
		      
		    //otherwise, it is firefox 
		    } else { 
		      
		    //store the key code (Key number) of the pressed key 
		    key = e.which;      
		    } 
		    
		    //console.log(key);
		    //if key 13 is pressed (the enter key) 
		    if(key == 13){ 
		      
		      showNextPhrase();
		    //do nothing 
		    return false; 
		      
		    //otherwise 
		    } else { 
		      
		    //continue as normal (allow the key press for keys other than "enter") 
		    return true; 
		    } 
		      
		//and don't forget to close the function     
		} 				
			
		</script>

	</head>

	<body onload="onLoad()">
		<div>
			<div id="buttoncontainer">
				
                <p id="nowords" ></p>
				<fieldset id="study_info">
    <legend>Study Data</legend>
    <label>Participant ID</label><input type="text" id="ucode"><br />
    <label>No. of Session</label><input type="text" id="session"><br />
    <label>No. of Phrases</label><input type="text" id="nmph"><br />
             Select keyboard	
            <br/>
            <button type="button" onclick="saveDependentVariable('swarachakra')" id="variable1">स्वरचक्र हिंदी</button>
            <button type="button" onclick="saveDependentVariable('google')" id="variable2">Google Indic (Hindi)</button>
</fieldset>
			</div>
			<h1 id="sessno"></h1>
		<div id="container" class="container">
			<form autocomplete="off">
				<label id="toType"></label>
				<br/>
				<input type="text" onkeyup="calculate()" id="tt" onKeyPress="return disableEnterKey(event)" autocomplete="off" autofocus />

			</form>
				<!-- <br/> -->
				<div style="display:inline-block;width:100%;height:30px;padding:0;margin:0;align:right;">
				<p id="cpm">SPEED: </p>
				<p id="error">ERROR: </p>
				<p id="bcc">BACKSPACE COUNT : </p>
				
					<label style="display:none" class="label2">Speed: <span style="display:none" id="cpm"> </span></label>
					<label style="display:none" class="label2">Error: <span style="display:none" id="error"> </span></label>
					 <!-- <label id="counter" class="label2 rightAlign">1/5</label>  -->
					<button type="button" onclick="showNextPhrase()" enabled="false" id="go">Next</button>
				</div>
					<br>
				
				<br/>
				<audio id="myAudio">
				  <source src="" type="audio/wav">
				  <!-- <source src="horse.mp3" type="audio/mpeg"> -->
				  Your browser does not support the audio element.
				</audio>
			
		</div>
	</div>

	</body>
</html>