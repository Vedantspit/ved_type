const phrasset=[];
for(i=0;i<45;i++)
{
  phrasset.push("p"+(i+1));
}
console.log(phrasset);
function shuffleArray(sourceArray) 
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



const numSessions = 20;
const phrasesPerSession = 10;
const repetitionFactor = 5;

var sessionPhrases = {};
var phraseCount = {};
var arrph=[];

for (let session = 1; session <= numSessions; session++) 
{
  const availablePhrases = phrasset.slice(); // Create a copy to avoid modifying the original array
  shuffleArray(availablePhrases);

  for (let i = 0; i < phrasesPerSession; i++) 
  {

    let selectedPhrase;

    do 
    {
      selectedPhrase = availablePhrases.pop();
    } while ((phraseCount[selectedPhrase] || 0) >= repetitionFactor && availablePhrases.length > 0);

    console.log("arrph array for session "+session+" is "+arrph);
    phraseCount[selectedPhrase] = (phraseCount[selectedPhrase] || 0) + 1;
    arrph.push(selectedPhrase);
  }
  
  sessionPhrases[session]=arrph;
  console.log("ARRAY OF SESSION "+session+" is  "+sessionPhrases[session]);
  arrph.length=0;
}
console.log("lenght of sessionphrase is "+sessionPhrases.length);
for(let p=1;p<=numSessions;p++)
{
    console.log(sessionPhrases[p]);
}
for(i=0;i<45;i++)  //phrase count of each phrase 
{

    console.log(sessionPhrases[i]);
    console.log("count of phrase p("+(i+1)+") is "+phraseCount["p"+(i+1)]);
}