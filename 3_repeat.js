const phrasset = [];
for (let i = 0; i < 45; i++) {
  phrasset.push("p" + (i + 1));
}

console.log(phrasset);

function shuffleArray(sourceArray) {
  for (let i = 0; i < sourceArray.length - 1; i++) {
    const j = i + Math.floor(Math.random() * (sourceArray.length - i));
    const temp = sourceArray[j];
    sourceArray[j] = sourceArray[i];
    sourceArray[i] = temp;
  }
  return sourceArray;
}


var sessionPhrases = {};
var phraseCount = {};
function dividePhrases(numSessions,phrasesPerSession,repetitionFactor)
{
  
    
    for (let session = 1; session <= numSessions; session++) {
      const availablePhrases = phrasset.slice(); // Create a copy to avoid modifying the original array
      shuffleArray(availablePhrases);
      var arrph = []; // Reset arrph for each session
    
      for (let i = 0; i < phrasesPerSession; i++) {
        let selectedPhrase;
    
        do {
          selectedPhrase = availablePhrases.pop();
        } while (
          (phraseCount[selectedPhrase] || 0) >= repetitionFactor &&
          availablePhrases.length > 0
        );
    
        phraseCount[selectedPhrase] = (phraseCount[selectedPhrase] || 0) + 1;
        arrph.push(selectedPhrase);
      }
    
      sessionPhrases[session] = arrph;
      console.log("ARRAY OF SESSION " + session + " is  " + sessionPhrases[session]);
    }
return sessionPhrases;    
}
dividePhrases(20,10,5)
console.log("lenght of sessionphrase is " + Object.keys(sessionPhrases).length);

for (let p = 1; p <= 20; p++) {
  console.log(sessionPhrases[p]);
}

for (let i = 0; i < 45; i++) {
 
  console.log("count of phrase p" + (i + 1) + " is " + phraseCount["p" + (i + 1)]);
}
