//
//  $Log: common.js.tpl,v $
//  Revision 1.14  2003/02/10 16:16:52  wk
//  - CS
//  - size up the quickLog window
//
//  Revision 1.13  2003/01/13 18:10:25  wk
//  - use HTML_Template_Xipe tags in XML
//
//  Revision 1.12  2002/11/29 15:09:00  wk
//  - translate message
//
//

<HTML_Template_Xipe>
    <options>
        <delimiter begin="[[" end="]]"/>
    </options>
</HTML_Template_Xipe>


/**
*   show a confirm box before redirecting to the remove URL
*
*   @param  string      the URL to redirect to on positive confirm
*   @param  string      the message/question to show to the user
*/
function removeConfirm( url , message )
{
    rowId = url.replace(/^.*\?/,"").replace(/=/,"");
    markRow( rowId );

    if( !message )
        message = "[[$T_MSG_REMOVE_CONFIRM]]";

    //document.getElementById('rowId_'+id).style.border = '2px dashed red';
    if( confirm( message ) )
    {
        markRow( rowId , "red" );
        window.location = url;
    }
    else
    {
// FIXXME dont hard code the color here and in the markRow function
        markRow( rowId , "white" );
    }
}

/**
*   show a confirm box before redirecting to the remove URL
*
*   @param  string      the URL to redirect to on positive confirm
*   @param  string      the message/question to show to the user
*/
function removeConfirmAll( url , message )
{
    markRow( "all" );

    if( !message )
        message = "[[$T_MSG_REMOVE_CONFIRM]]";

    //document.getElementById('rowId_'+id).style.border = '2px dashed red';
    if( confirm( message ) )
    {
        markRow( "all" , "red" );
        window.location = url;
    }
    else
    {
// FIXXME dont hard code the color here and in the markRow function
        markRow( "all" , "white" );
    }
}

/**
*   highlight all the td's inside the given tr
*   @param  string  the name of the tr
*/
function markRow( name , color )
{
    tds = document.getElementById(name).getElementsByTagName("td");

    if( !color )
        color = "yellow";

    for( i=0 ; i< tds.length ; i++ )
    {
        tds[i].style.backgroundColor = color;
    }

}



function autoCorrectTime( formName , inputField )
{
    ref = document.forms[formName][inputField];
    time = ref.value;

    newTime = time;

    if( time.split(":").length < 2 )
    {
        // IE workaround, since IE cant get a char of a string using [] so date[0] is undefined
        // put all the stuff from time in an array named time, so the IE can also access it as an array
        // which for all the other browsers is no problem either :-)
        var tmpTime = time;
        var time = new Array();
        for( i=0 ; i<tmpTime.length ; i++ )
            time[i] = tmpTime.substring(i,i+1);

        newTime = 0;
        if( time.length == 1 )
        {
            newTime = "0"+time[0]+":00";
        }
        if( time.length == 2 )
        {
            if( parseInt(time[0]+time[1]) > 23 )
                newTime = "0"+time[0]+":0"+time[1];
            else
                newTime = time[0]+time[1]+":00";
        }
        if( time.length == 3 )
        {
            if( parseInt(time[0]+time[1]) > 23 )
            {
                if( parseInt(time[1]+time[2]) <60 )
                    newTime = "0"+time[0]+":"+time[1]+time[2];
                else           
                    newTime = 0;
            }
            else
            {
                newTime = time[0]+time[1]+":0"+time[2];
            }
        }
        if( time.length == 4 )
        {                            
            if( parseInt(time[0]+time[1]) < 23 && parseInt(time[2]+time[3]) < 60 )
            {
                newTime = time[0]+time[1]+":"+time[2]+time[3];
            }
            else
            {
                newTime = 0;
            }
        }
    }

    if( !newTime )
        newTime = "00:00";

    ref.value = newTime;
}

function autoCorrectDate( formName , inputField , setTodayIfEmpty )
{
    ref = document.forms[formName][inputField];
    date = ref.value;
               
    if( setTodayIfEmpty == true && date=="" )
    {
        month = new Date().getMonth()+1;
        year = new Date().getFullYear();
        day = new Date().getDate();
        ref.value = day+"."+month+"."+year;
        return;
    }

    // all those if's leave a lot of options that the date is not changed, so writing this date
    // in the newDate makes sure we get no bull.. in the input field :-) it helped me when i had
    // 2 input fields, newDate was not changed in the second one so the first one was copied again :-(
    // that is solved by this line
    newDate = date;

    month = new Date().getMonth()+1;
    year = new Date().getFullYear();

// FIXXME doesnt handle '5.8', '5.8.' '10502'
// FIXXME check the integrity of the date too!
// if we have January detect that 211 shall mean 21.1 not 2.11 !!! and so on!
    if( date.split(".").length == 1 )
    {
        // IE workaround, since IE cant get a char of a string using [] so date[0] is undefined
        // put all the stuff from date in an array named date, so the IE can also access it as an array
        // which for all the other browsers is no problem either :-)
        var tmpDate = date;
        var date = new Array();
        for( i=0 ; i<tmpDate.length ; i++ )
            date[i] = tmpDate.substring(i,i+1);

        if( date.length == 1 ) {
            newDate = "0"+date[0]+"."+month+"."+year;
        }
        if (date.length == 2) {
            if (date[0] > 3) {
                newDate = "0"+date[0]+".0"+date[1]+"."+year;
            } else {
                if (parseInt(date[0]+date[1]) < 32) {
                    newDate = date[0]+date[1]+"."+month+"."+year;
                } else {
                    date[2] = date[1];
                    date[1] = date[0];
                    date[0] = 0;
                }
            }
        }
        if( date.length == 3 ) {
            if (date[2] != 0) {
                newDate = date[0]+date[1]+".0"+date[2]+"."+year;
            } else {
                newDate = 0+date[0]+"."+date[1]+date[2]+"."+year;
            }
        }
        if (date.length == 4) {
            newDate = date[0]+date[1]+"."+date[2]+date[3]+"."+year;
        }
        if (date.length == 5) {
            newDate = date[0]+date[1]+".0"+date[2]+".20"+date[3]+date[4];
        }
        if (date.length == 6) {
            newDate = date[0]+date[1]+"."+date[2]+date[3]+".20"+date[4]+date[5];
        }
    } else {
        tmpDate = date.split(".");
        if (tmpDate.length==2) {
            if (parseInt(tmpDate[0]) < 32 && parseInt(tmpDate[1]) < 13) {
                newDate = tmpDate[0]+"."+tmpDate[1]+"."+year;
            }
        } else {
            if (tmpDate[2].length == 2) {
                newDate = tmpDate[0]+"."+tmpDate[1]+".20"+tmpDate[2];
            }
            if (tmpDate[2].length == 1) {
                newDate = tmpDate[0]+"."+tmpDate[1]+".200"+tmpDate[2];
            }
        }
    }
    ref.value = newDate;
}



function updateTime( formName , inputNameDate , inputNameTime )
{
    if( !formName )         formName = "editForm";
    if( !inputNameDate )    inputNameDate = "newData[timestamp_date]";
    if( !inputNameTime )    inputNameTime = "newData[timestamp_time]";

    // do only update if the user has not modified the date or time by hand
    if( lastDate != document.forms[formName][inputNameDate].value ||
        lastTime != document.forms[formName][inputNameTime].value )
    {
        return;
    }

    _updateTime( formName , inputNameDate , inputNameTime );

    lastDate = dateString;
    lastTime = timeString;
    window.setTimeout("updateTime()",5000);    // update every 5 seconds
}

/**
*   define an extra function, so we can call this method directly without
*   having the check(s) done and starting the interval,
*   called i.e. when the "now" button was pushed
*/
function _updateTime( formName , inputNameDate , inputNameTime )
{
    if( !formName )         formName = "editForm";
    if( !inputNameDate )    inputNameDate = "newData[timestamp_date]";
    if( !inputNameTime )    inputNameTime = "newData[timestamp_time]";

    var dateNow = new Date();
    dateString = dateNow.getDate()+'.'+(dateNow.getMonth()+1)+'.'+dateNow.getFullYear()
    minutes = dateNow.getMinutes();
    if (minutes<10) {
        minutes = "0"+minutes;
    }
    timeString = dateNow.getHours()+':'+minutes;
                                           
    if (document.forms[formName][inputNameDate]) {
        document.forms[formName][inputNameDate].value = dateString;
    }
    if (document.forms[formName][inputNameTime]) {
        document.forms[formName][inputNameTime].value = timeString;
    }
}

                           

/**
*
*
*/
function openHelpWindow( url )
{
    helpWin = openWindowOnce( url , "Help" , "left=0,top=100,width=550,height=600,scrollbars=yes" , "help");
    // jump right to the link, if the window is already opened we simply want the anchor to work :-)
    helpWin.location.href = url;
}



function openQuickLog( url )
{
    openWindowOnce( url , "Quick_Log" , "left=0,top=0,width=600,height=600,scrollbars=yes" , "quickLog");
}


var openedWins = new Array();
openedWins["help"] = false;
openedWins["quickLog"] = false;
openedWins["export"] = false;

function openWindowOnce( url , name , paras , openedWinIndex )
{
    if (!openedWins[openedWinIndex] || openedWins[openedWinIndex].closed) {
        openedWins[openedWinIndex] = openWindow( url , name , paras );
    }
    openedWins[openedWinIndex].focus();
    return openedWins[openedWinIndex];
}



function openWindow( url , name , paras )
{
    if (!paras) {
        paras = "left=0,top=0,width=500,height=400,scrollbars=yes";
    }

    return window.open( url , name , paras );
}

