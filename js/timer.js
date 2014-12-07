var questionTimeDiv;
function setTime(totalSeconds, questionTimeDiv)
{
	questionTimeDiv = questionTimeDiv;
	++totalSeconds;
	var sec = pad(totalSeconds % 60);
	var min = pad(parseInt(totalSeconds / 60));
	questionTimeDiv.innerHTML = "Time:" + min + ":" + sec;
}
function pad(val)
{
	var valString = val + "";
	if (valString.length < 2)
	{
		return "0" + valString;
	}
	else
	{
		return valString;
	}
}
