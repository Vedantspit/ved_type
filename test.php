<?php



?>



<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>
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
				<div id="toType"></div>
				<br/>
				<input type="text" onkeyup="calculate()" id="tt" onKeyPress="return disableEnterKey(event)" autocomplete="off" autofocus />

			</form>
				<!-- <br/> -->
				<div style="display:inline-block;width:100%;height:30px;padding:0;margin:0;align:right;">
				<p id="cpm">SPEED: </p>
				<p id="error">ERROR: </p>
				<p id="bcc">BACKSPACE COUNT : </p>
                 <p id="display"></p>
                <p id="inotfixed"></p>
			    <p id="infixed"></p>
			    <p id="corrected"></p>
			    <p id="fixes"></p>
					<div style="display:none" class="label2">Speed: <span style="display:none" id="cpm"> </span></div>
					<div style="display:none" class="label2">Error: <span style="display:none" id="error"> </span></div>
					 <!-- <label id="counter" class="label2 rightAlign">1/5</label>  -->
                     <button type="button" onclick="calcCorrected()" enabled="false" id="ckey">find correct keystrokes</button>
			   <button type="button" onclick="calcIncorrectNotFixed()" enabled="false" id="infkey">find INF keystrokes</button>
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

<script type="text/javascript">
    var xl=0;
         var startTimestamp=-1;
         var endTimestamp=-1;
         var ksLog = "";
         var count;
         var currsesh=0;
		 var previousInput='';
         var toTypeArray;
         var toTypeArrayLang = ["hn","hn","hn","hn","hn","en","en","en","en","en"]; 
         var toTypeSequenceArray = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14];
         var toTypeArrayIndex=0;
         var dependentVariable="";
         var uCode = -1;
         var sCode = -1;
         var oldText="";
         var session = 0;
         var nmph=-1;
         var backspaceCount = 0;
		 var backarray=[];
		 var infarray=[]
		 var farray=[]
		 var ifcarray=[]
		 var corrarray=[]
         var trainingWords;
         var practiceWords;
         var practiceSession;
         var mainTask1; 
         var mainTask2;
		 var ifc=0;
		 var fix=0;
		 var inf=0;
		 var corr=0;
         toTypeSequenceArray = myshuffle(toTypeSequenceArray);
         console.log("hi")
         
         
         //var tapLog="";
         var tapLogsArray=[];
         var d;
         console.log("okay");
         
         function onLoad(){
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
         function syncToServer()
			{
				console.log("I AM IN THE SYNC TO SERVER FUNCTION ");
           
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
                console.log("LENGTH OF ALL PHRASES ARRAY" + tapLogsArray.length);
				var params ="id="+uCode+"_"+session+"_"+uniqueIdentifier+"&var="+dependentVariable+"&phrases=["+tapLogsArray+"]"+"&backspace=["+backarray+"]"+"&corrected=["+corrarray+"]"+"&incorrfix=["+ifcarray+"]"+"&incorrnot=["+infarray+"]"+"&fixes=["+farray+"]";
				xmlhttp.open("POST",url,true);
				xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xmlhttp.send(params);

			}
         function loadPhrases()
		 {
           console.log("im in load phrases function")
         	var xmlhttp;
             
             if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
                 xmlhttp = new XMLHttpRequest();
             }
             else {// code for IE6, IE5
                 xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
             }
         
         	xmlhttp.onreadystatechange = function() {
         
         	    if (this.readyState == 4 && this.status == 200) {
         	    	console.log("back");
         	    	
         	        var responseText = this.responseText;
         	        //console.log("Response:"+responseText);
         	        var phrasesArray = responseText.split(";");
         	        //console.log(phrasesArray);
         	        trainingWords = phrasesArray[0].split(",");
                    console.log(trainingWords);
         		
         		
         		    trainingWords = myshuffle(trainingWords);
                     console.log(trainingWords);
         
         	    }
                else if(this.readyState == 4 && this.status != 200)
                {
         	    	console.log("Something went wrong");
         	    	document.getElementById("container").innerHTML = '<span class="spanWhite">Server went away. Data not saved. Error code:'+this.status+'.</span><br/><button type="button" onclick="syncToServer()">Try again</button>';
         	    }
         	};
         	//console.log("making request");
         	var url = "loadPhrases.php";		
         	var params ="id="+uCode+"_"+session;		
         	xmlhttp.open("POST",url,true);
         	xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
         	xmlhttp.send(params);
         	
         
         }
         function myshuffle(sourceArray) 
         {
          for (var i = 0; i < sourceArray.length - 1; i++) 
          {
              var j = i + Math.floor(Math.random() * (sourceArray.length - i));
         
              var temp = sourceArray[j];
              sourceArray[j] = sourceArray[i];
              sourceArray[i] = temp;
          }
          return sourceArray;
         }
         function saveDependentVariable(dVariable){
         	count=0;
         
         	dependentVariable = dVariable;
         	uCode = document.getElementById("ucode").value;
            nmph=document.getElementById("nmph").value;
			session = document.getElementById("session").value;
			console.log("no. of phrases are "+nmph+"no. of sessions are "+session+"and participant id is "+uCode);
         	//console.log(uCode+","+sCode);
         
         	if(navigator.onLine == true){
         
         		//loadPhrases();
         
         		//dependentVariable = dVariable;
         		//document.getElementById("variable1").style.display = "none";
         		//document.getElementById("variable2").style.display = "none";
         		document.getElementById("buttoncontainer").style.display = "none";
         		document.getElementById("container").style.display = "inline-block";
         
                if(currsesh<=session)
                {
         		
          
         		toTypeArray = getRandomElementsFromArray(trainingWords,nmph);
                 document.getElementById("toType").innerHTML = toTypeArray[0];
                //  toTypeArray=myshuffle(toTypeArray);
                }
         
         	}else{
         
         		document.getElementById("buttoncontainer").innerHTML= "Please check your internet connection";
         	}
         
         
         }
         function showNextPhrase()
         {
           
          
			// calcCorrected();
            if(toTypeArrayIndex <toTypeArray.length)
            {
			infarray.push(inf);
			farray.push(fix);
			ifcarray.push(ifc);
			corrarray.push(corr);
            xl++;

  

			backarray.push(backspaceCount);
            }
			for(i=0;i<backarray.length;i++)
			{
				console.log("backspace array of phrases["+(i+1)+"] "+backarray[i]);
			}
			for(i=0;i<infarray.length;i++)
			{
				console.log("INF array of phrases["+(i+1)+"] "+infarray[i]);
			}
			for(i=0;i<ifcarray.length;i++)
			{
				console.log("IF array of phrases["+(i+1)+"] "+ifcarray[i]);
			}
			for(i=0;i<farray.length;i++)
			{
				console.log("Fixes array of phrases["+(i+1)+"] "+farray[i]);
			}
			for(i=0;i<corrarray.length;i++)
			{
				console.log("Corrected C array of phrases["+(i+1)+"] "+corrarray[i]);
			}
			previousInput='';
			resetBackspaceCount();
			resetinf();
			ifc=0;
			fix=0;
            count=0;
         	toTypeArrayIndex++;
         	var x = document.getElementById("myAudio");
         	var accuracy = "";
         	var ext = "wav";
         	var toType = "";
         	var typed = "";
            console.log("TO TYPE ARRAY LENGTH IS "+toTypeArray.length)
         	var phrase = {};
             if (toTypeArrayIndex > toTypeArray.length) 
             {
                console.log("Current session is complete SESSION "+(currsesh+1));
        // Current session is complete
        // document.getElementById("display").innerHTML = '<span class="span2">Done.</span>';

        // Reset variables for the next session
        // if(toTypeArrayIndex-1>=toTypeArray.length)
        // {
            document.getElementById("tt").innerHTML = "";
        
        
        toTypeArrayIndex = 0;
        currsesh++;

        // Load new phrases for the next session
        if (currsesh<session) 
        {
            document.getElementById("tt").innerHTML = "";
            toTypeArray = getRandomElementsFromArray(trainingWords,nmph);
            document.getElementById("toType").innerHTML = toTypeArray[0];
        } 
        else
         {
            // All sessions are complete
            document.getElementById("container").innerHTML = '<span class="span2">All sessions are complete.</span>';
            console.log("Calling sync to server ");
            syncToServer();
 
        }
    }
     else {
        // Continue with the next phrase in the current session
        phrase["phraseSequenceNumber"] = xl;
        toType = toTypeArray[toTypeArrayIndex-1];
        phrase["phraseShown"] = toType;
         
         		//phrase["phraseLanguage"] = toTypeArrayLang[toTypeSequenceArray[toTypeArrayIndex-1]];
         		phrase["phraseLanguage"] = "hn";
         		phrase["phraseTyped"] = document.getElementById("tt").value;					
         		phrase["editdistance"] = getED(phrase["phraseTyped"],phrase["phraseShown"]);
         		phrase["timeTaken"] = endTimestamp-startTimestamp;
         		phrase["ksLogs"] = ksLog;
         
         		accuracy = getStarRating(getErrorRate(phrase["phraseShown"],phrase["phraseTyped"]));
         
         		//console.log(phrase);
         		//console.log("Tap string:"+JSON.stringify(phrase));
         
         		tapLogsArray.push(JSON.stringify(phrase));
         		
         		//cleanup
         		document.getElementById("cpm").innerHTML = "yah";
         		document.getElementById("error").innerHTML = " ";
         		document.getElementById("tt").value = "";
         		ksLog = "";
         		oldText = "";
         		
         		x.src = "fb/"+accuracy+"."+ext;
         		//console.log(x.src);
         		x.play();
                 document.getElementById("tt").innerHTML = "";
         		/*x.load();
         		fetch(x.src)
         		    .then(response => response.blob())
         		    .then(blob => {
         		      x.srcObject = blob;
         		      return x.play();
         		    })
         		    .then(_ => {
         		      // Video playback started ;)
         		    })
         		    .catch(e => {
         		      // Video playback failed ;(
         		    })
         */
        if((toTypeArrayIndex>=toTypeArray.length) && (currsesh==session))  
        {
            document.getElementById("tt").innerHTML = "";
        //  document.getElementById("container").innerHTML = '<span class="span2">Done.</span>';
         console.log("Calling sync to server")
         syncToServer();
 
     }else
     {
        if(currsesh==session)
         {
            document.getElementById("go").innerHTML = "Done";
         }
 
         if((toTypeArrayIndex+1)<=toTypeArray.length ){
 
            document.getElementById("tt").innerHTML = "";
            //  document.getElementById("go").innerHTML = "Done";
         }
         
        console.log("HOA HOA");
        
         //update word / phrase
         if(toTypeArrayIndex >= toTypeArray.length )
         {
            document.getElementById("toType").innerHTML = "CURRENT SESSION IS COMPLETE !! SESSION "+(currsesh+1)+" + is complete <br>press next to proceed"

           
         }
       else
       {
         document.getElementById("toType").innerHTML = toTypeArray[toTypeArrayIndex];
       }
             
         console.log(document.getElementById("toType").innerHTML);
 
         //cleanup
         document.getElementById("cpm").innerHTML = " ";
         document.getElementById("error").innerHTML = " ";
         document.getElementById("tt").value = "";
         ksLog = "";
         oldText = "";
 
         startTimestamp = -1;
         endTimestamp = -1;
 
 
     }
         
     
        
        // ... (remaining code)
    }
         
        //  		phrase["phraseSequenceNumber"] = toTypeArrayIndex;
        //         toType = toTypeArray[toTypeArrayIndex-1];
         		
       
         
        //  		phrase["phraseShown"] = toType;
         
        //  		//phrase["phraseLanguage"] = toTypeArrayLang[toTypeSequenceArray[toTypeArrayIndex-1]];
        //  		phrase["phraseLanguage"] = "hn";
        //  		phrase["phraseTyped"] = document.getElementById("tt").value;					
        //  		phrase["editdistance"] = getED(phrase["phraseTyped"],phrase["phraseShown"]);
        //  		phrase["timeTaken"] = endTimestamp-startTimestamp;
        //  		phrase["ksLogs"] = ksLog;
         
        //  		accuracy = getStarRating(getErrorRate(phrase["phraseShown"],phrase["phraseTyped"]));
         
        //  		//console.log(phrase);
        //  		//console.log("Tap string:"+JSON.stringify(phrase));
         
        //  		tapLogsArray.push(JSON.stringify(phrase));
         		
        //  		//cleanup
        //  		document.getElementById("cpm").innerHTML = "yah";
        //  		document.getElementById("error").innerHTML = " ";
        //  		document.getElementById("tt").value = "";
        //  		ksLog = "";
        //  		oldText = "";
         		
        //  		x.src = "fb/"+accuracy+"."+ext;
        //  		//console.log(x.src);
        //  		x.play();
        //  		/*x.load();
        //  		fetch(x.src)
        //  		    .then(response => response.blob())
        //  		    .then(blob => {
        //  		      x.srcObject = blob;
        //  		      return x.play();
        //  		    })
        //  		    .then(_ => {
        //  		      // Video playback started ;)
        //  		    })
        //  		    .catch(e => {
        //  		      // Video playback failed ;(
        //  		    })
        //  */
         
         
        //  	if(toTypeArrayIndex>=toTypeArray.length){
         
        //  		document.getElementById("container").innerHTML = '<span class="span2">Done.</span>';
        //  		console.log("Calling sync to server")
        //  		syncToServer();
         
        //  	}else{
         
        //  		if((toTypeArrayIndex+1)==toTypeArray.length){
         
        //  			document.getElementById("go").innerHTML = "Done";
        //  		}
         	
        //  		//update word / phrase
        //  		document.getElementById("toType").innerHTML = toTypeArray[toTypeArrayIndex];
         	
         			
        //  		console.log(document.getElementById("toType").innerHTML);
         
        //  		//cleanup
        //  		document.getElementById("cpm").innerHTML = " ";
        //  		document.getElementById("error").innerHTML = " ";
        //  		document.getElementById("tt").value = "";
        //  		ksLog = "";
        //  		oldText = "";
         
        //  		startTimestamp = -1;
        //  		endTimestamp = -1;
         
         
        //  	}
         
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
         const key = event.key;
     
        if (key === "Backspace" || key === "Delete")
          {
            backspaceCount++;
                   
        }
        
         console.log("Backspace Count:", backspaceCount);
        return backspaceCount;
     
     }
     function resetBackspaceCount() 
     {
                backspaceCount = 0;
    }
    function resetinf()
    {
        inf=0;
        corr=0;
        ifc=0;

    }
     
     function calculate(){       // function which calls CPM and get error rate functions respectively
         
     
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
        // document.getElementById("corrected").innerHTML ="Corrected " +calcCorrected(toType,textSoFar)+" keystrokes";
        // document.getElementById("inotfixed").innerHTML ="INF "+calcIncorrectNotFixed(toType,textSoFar)+" keystrokes";
        // document.getElementById("infixed").innerHTML ="IF" +calcIncorrectFixed()+" keystrokes";
         
         document.getElementById("bcc").innerHTML =backSpaceCount()+" presses";
     
         document.getElementById("cpm").innerHTML = getCPM() + " cpm";
                  console.log("this is the calculated CPM --> " +getCPM());
     
         //document.getElementById("error").innerHTML = getErrorRate() + " %";
         document.getElementById("error").innerHTML = getErrorRate(toType,textSoFar) + " %";
        console.log("this is the calculated error rate  --> " +getErrorRate(toType,textSoFar));
        // document.getElementById("corrected").innerHTML ="Correct" +calcCorrected(toType,textSoFar)+" keystrokes";
        // document.getElementById("inotfixed").innerHTML ="INF "+ calcIncorrectNotFixed(toType.trim(),textSoFar)+" keystrokes";
         calcIncorrectFixed();
         calcFixes();
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
         //str = cleanUp(str);
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
         //str = cleanUp(str);
     
         var ed = damerauLevenshteinDistance(str,toType);
     
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
    function getRandomElementsFromArray(array, numElements) //function for getting x no. of phrases from phraseset
	{
            // Shuffle the array
            const shuffledArray = array.slice().sort(() => Math.random() - 0.5);
    
            // Slice the first numElements from the shuffled array
            return shuffledArray.slice(0, numElements);
    }
    function calcIncorrectNotFixed()   //function for calculating INF (Incorrect Not Fixed) value that is mistyped characters either extra or missing      
		 {       
			inf=0;
			var totype = document.getElementById("toType").innerHTML;
			totype=totype.trim();
         	console.log("VEDANT HERE totype:"+totype);
         	var textSoFar = document.getElementById("tt").value;
            console.log("VEDANT HERE text so far :"+textSoFar);
                      
         	if(textSoFar.length==0)
         		return;
            var arr1=totype.split('');
			var arr2=textSoFar.split('');
			console.log("in inf function totype split "+arr1);
			console.log("in inf function textSoFar split "+arr2);

			arr1.forEach(function(item, index)
			{
			    if(!arr2.includes(item))
			    {
                    console.log(item);
			        inf++;
			    }
			})
			console.log("INF MISSING CHARACTERS COUNT " + inf);
			arr2.forEach(function(item, index)
			{
			    if(!arr1.includes(item))
			    {
                    console.log(item);
			        inf++;
			    }
			})
			console.log("INF EXTRA CHARACTERS COUNT " + inf);
			//document.getElementById("inotfixed").innerHTML ="INF "+ inf+" keystrokes";
            return inf;
		 }
		 function calcIncorrectFixed()                 //function for calculating IF(Incorrect Fixec) values the values which are mistyped characters which are fixed
		 {	

            //  document.addEventListener('keyup', function(event) {
             const key = event.key.toLowerCase();
  
               if (key === 'backspace') {
			       handleBackspace();
               } else if (key.length === 1) {
           handleKeyPress(key);
               }
            //  });
	  
	     }

      function handleKeyPress(key) //function required by IF fucntion
	  {
        previousInput += key;
        console.log('Key pressed:', key);
        }

        function handleBackspace() //function required by IF fucntion
       {
         console.log("I M IN HANDLE BACKSPACE FUNCTION FOR IF")
        const deletedChar = previousInput.slice(-1);
        
        previousInput = previousInput.slice(0, -1);
       
         console.log('Backspace pressed. Deleted character ->>:' + deletedChar);
         ifc++;
      
	     console.log("TOTAL IF KEYSTROKES ARE "+ifc);
		 document.getElementById("infixed").innerHTML ="IF "+ ifc+" keystrokes";
	   }
		 
		 function calcCorrected(toType,textSoFar)     //Function for calculating C value (correct characters typed)
		 {
			corr=0;
			var totype = document.getElementById("toType").innerHTML;
			totype=totype.trim();
         	console.log("IN CORRECTED KEYSTOKES FUNCTION VEDANT HERE totype:"+totype);
         	var textSoFar = document.getElementById("tt").value;
            console.log("VEDANT HERE text so far :"+textSoFar);
            var arr1=totype.split('');
			var arr2=textSoFar.split('');
			console.log(arr1);
			console.log(arr2);

			arr1.forEach(function(item, index)
			{
			    if(arr2.includes(item))
			    {
			        corr++;
			    }
			})
			console.log("CORRECTED KEYSTROKES ARE " +corr);
			return corr;
		 }
		 function calcFixes()                     //function for calculating F value (Fixes calue comprising of backspace and left cursor key presses)
		 {
			if (event.key === 'ArrowLeft'|| event.key === 'Backspace') {
                fix++;
                console.log('FIX KEY STROKES ARE ', fix);
            }
			document.getElementById("fixes").innerHTML ="FIX "+ fix+" keystrokes";
		 }
</script>
</html>