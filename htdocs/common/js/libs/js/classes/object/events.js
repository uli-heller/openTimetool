/////////////////////////////////////////////////////
//
//      name:       events
//      type:       final class
//
//      created:    2000/06/23, cain@nomenclatura.de
//      
//      comment:    this class shall enable the multiple use of one event
//                  like document.onmousedown
//                  all the events shall be registered to this file
//                  and when the event occurs this file shall run
//                  all the functions registered for this event
//                
//      how 2 use:  simply pass the function which shall be called 
//                  on a special event to this class by using event.onmousedown.add("function call");
//                  i guess
//                  
//                            
//      revisions:
//                  2000/06/23, cain@nomenclatura.de
//                  - initial revision        
//                  2000/07/09, cain
//                  - optimised the call of all the event functions, now there is one big string
//                    which is executed via eval when an event occurs, before it was a for-loop which 
//                    called an eval for each event's function, but it was no speed up visible - shit
//                  2000/12/06
//                  - replaced one try-catch clause, so events work also in IE4
//                  
//                  
//      $Log: events.js,v $
//      Revision 1.1.2.1  2003/11/09 14:40:54  wk
//      *** empty log message ***
//
//      Revision 1.4  2003/02/09 03:17:54  cain
//      - some IE issues and CS
//
//      Revision 1.3  2003/02/03 05:33:43  cain
//      - add dropCount to register method
//      - handle opera7
//      - some cleaning up
//
//      Revision 1.2  2001/06/18 22:07:30  cain
//      - tried to get it running in opera, but i just added some comments
//
//      Revision 1.1  2001/03/28 18:59:48  cain
//      no message
//
//                  
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
//
//      function
//      name:       constructor
//      created:    2000/06/23, cain@nomenclatura.de
//                              
//      comment:    
//                  
//      parameter:  
//
function class_events()
{
	/////////////////////////////////////////
	//
	//  P U B L I C
	//  NOT to be overwritten
	//
  this.register = events_register;
  this.unregister = events_unregister;
  
	/////////////////////////////////////////
	//
	//  P U B L I C
	//  may be overwritten
	//
  
	/////////////////////////////////////////
	//
	//	P R I V A T E 
	//	dont touch nothing from here
	//
  this.handlers = new Array();
  this.handle   = events_handle;
  
  /////////////////////////////////////////////////////
  //
  //      prevent from trouble when instanciating  
  //      this class many times
  //
	this.name = "events"+(class_events.count++);
	this.obj = this.name+"Object";
	eval(this.obj + "=this");
}
/////////////////////////////////////////////////////
//            I N H E R I T A N C E                //
//inherit everything from the class "class_object" //
class_events.prototype = new class_object();
//                                                 //
/////////////////////////////////////////////////////

/**
*   this function lets the user register event handler
*
*   @version    2000/06/23, cain@nomenclatura.de
*   @param      string  is the standard event name like: "onmousedown"
*   @param      string  the function that shall be called when the event occurs
*   @param      integer the number of events to be dismissed before the eventFunction is really called
*   @return     returns a handler ID which has to be given to
*               the unregister method so it finds the function
*               which shall be unregistered
*/
function events_register(eventName,eventFunction,dropCount)
{
    if (dropCount==null) {
        dropCount = 0;
    }

    eventName = eventName.toLowerCase();
    if( this.handlers[eventName] == null )          // does the array already exist?
    {
        this.handlers[eventName] = new Array();       // if not create it
        eval("this.handlers"+eventName+"=''");        // empty the function call string
    }

    var len = this.handlers[eventName].length;      // get the length of the current array
    this.handlers[eventName][len] = new Array(eventFunction,dropCount);  // and use the length which is equal to the last element+1 to put the next eventFunction at the end of the array

    eval("this.handlers"+eventName+"+='"+eventFunction+"; '");// add the new function to the string, which will be called via eval when the event fires

    // debug doesnt exist when this function gets called 4 the first time
    /*  try{
    debug.writeln(eval("this.handlers"+eventName));
    debug.writeln("registered : "+eventName+" ID:"+len);
    //  debug.writeln(" function: "+handler[len]);
    }catch(e){};*/
    return eventName+"|"+len;
}

/////////////////////////////////////////////////////
//
//      function
//      name:       events_register
//      created:    2000/06/23, cain@nomenclatura.de
//                              
//      comment:    this function lets the user register event
//                  handler
//
//      parameter:  eventHandlerId - the event handler that shall be unregistered
//                                   was returned by the register function
//
//      return value: returns a handler ID which has to be given to
//                    the unregister method so it finds the function
//                    which shall be unregistered
//
function events_unregister(eventHandler)
{                       
    eventHandler = eventHandler.toLowerCase();
  // only NS would support "pop"
//  this.curUsedResourcesList.pop();              // erases the last array element

  var eventName = eventHandler.split("|")[0];     // a handle for an event is always encoded like this: "eventname|number"
  var handlerIndex = eventHandler.split("|")[1];  // the handlerIndex is the number of the event, that shall be unregistered

//  debug.writeln("event:"+eventName+" hID:"+handlerIndex);
   
// !!!!!!!!!!!!!!!!!!!!!!!!! B U G
// the following wont work properly, if the handlerIndex is used, to unregister
// an event, but the following two lines change the order of the registered events
// then the handlerIndex will only be correct by chance
// i.e.: current list of events is 1|2|3|4  event 2 shall be erased 1|4|3 and if now the eventHandler  
// "eventname|4" is passed to this function unregister will fail, because there is no array index 4 anymore....!!!!!!!!!!!!!
  if( handlerIndex != (this.handlers[eventName].length-1) )  // is the event which shall be unregistered NOT the last one?
  {
    this.handlers[eventName][handlerIndex] = this.handlers[eventName][length-1];  // move the last handler in the gap
  }
  this.handlers[eventName] = this.handlers[eventName].slice(0,-1);                  // remove last handler

  // regenerate the string which will be eval-ed in the handle function
  // this string is simply a string which is set together out of all the event functions
  // that have to be handled and it is executed via the eval function
  eval("this.handlers"+eventName+"=''");          // make the string empty
  for( var c=0 ; c < this.handlers[eventName].length ; c++ )  // fill the string with all teh functions to the registered event
  {
    eval("this.handlers"+eventName+"+='"+this.handlers[eventName][c][0]+"; '");// add the new function to the string, which will be called via eval when the event fires
  }
}

/**
*   does handle all the events, that are registered
*
*   version 2000/06/23, cain@nomenclatura.de
*   @param  eventName - the event 2 handle
*/
function events_handle(eventName)
{
    var evalString = "";

    i = 0;
    try{
    for (var x in this.handlers[eventName]) {
        if (this.handlers[eventName][i][1]>0) {     // handle the dropCount, which doesnt call the event for dropCount times
            this.handlers[eventName][i][1]--;
        } else {
            evalString += this.handlers[eventName][i][0]+";";
        }
        i++;
    }
    } catch(error) {;}

//    evalString = eval("this.handlers"+eventName);

    if( evalString != null && evalString != "" ) {    // only if something to execute had been registered
        eval(eval(evalString));
    }
}


class_events.count = 0;
////////////////////////////////////// class end ///////////////////////////////////////////////////////




// i hope this is a bit faster then the function calls  
//var wrapOnMouseDown = new Function('events.handle("onmousedown");');
function wrapOnMouseMove(event)
{
    events.handle("onmousemove");
    if (is.ns) {
        mouse.getPos(event);                          // pass the event to the mouse method which gets the position, it needs it to use event.pageX and event.pageY
        document.routeEvent(event);                   // pass on the events so each node in the HTML document can retreive events too
    }
//  debug.writeln("onmousemove");
}

// the opera only recognizes a mousemove if there is any html below the mouse
//
function wrapOnMouseDown(e)
{
//  debug.writeln("onmousedown");
  if ((is.dom || is.ns) && !userAgent.ie) {    // IE doesnt know e.button
    if( e.button == 3 ) {
        events.handle("onrightmousedown");
    } else {
        events.handle("onmousedown");
    }
  } else {
        events.handle("onmousedown");
  }
  
  if( is.ns ) {        
    document.routeEvent(e);                       // pass on the events so each node in the HTML document can retreive events too
  }
}

function wrapOnMouseUp(e)
{
  events.handle("onmouseup");
  if( is.ns )
  {
    document.routeEvent(e);                       // pass on the events so each node in the HTML document can retreive events too
  }
}

function wrapOnClick(e)
{
    events.handle("onclick");
}

function wrapOnKeypress(e)
{
    events.handle("onkeypress");
}

events = new class_events;

//if(is.ns)// || is.dom)
if (document.captureEvents)
{
    document.captureEvents(Event.MOUSEDOWN | Event.MOUSEMOVE | Event.MOUSEUP | Event.MOUSECLICK);
}

// the mousedown event is handled in a different way for DOM
// but all the other events are registered my way
// opera doesnt know addEventListener, at least not yet
// opera seems to do things different every time i load the page
// once it works once it doesnt, the mouseup only works when no mousedown was caught
// right before and some other misterious stuff
if( userAgent.dom && !(userAgent.op && userAgent.version<7) && !userAgent.ie)
{
    /*  try !!!!!!!
    {
        if( mouse ) {}
    } catch(error) {
    //    alert("include mouse class before events class, to ensure proper working");
    }
    */

    // this function call needs the mouse class to be defined
    document.addEventListener("mousemove",mouse.getPos,false);  // needs to be called this way to provide the event in mouse.getPos, to retreive the pos
    document.addEventListener("mousedown",wrapOnMouseDown,false);
    document.addEventListener("mousemove",wrapOnMouseMove,false);
    document.addEventListener("mouseup",wrapOnMouseUp,false);
    document.addEventListener("click",wrapOnClick,false);
    document.addEventListener("keypress",wrapOnKeypress,false);
}
else
{
    // for some reason the events cant call an object's method directly
    // so i use a function inbetween which redirects to the object events
    document.onmousedown  = wrapOnMouseDown;
    document.onmousemove  = wrapOnMouseMove;
    document.onmouseup    = wrapOnMouseUp;
    document.onclick    = wrapOnClick;
    document.onkeypress = wrapOnKeypress;
    if( !is.ns )
    {
        // register this event here for the mouse class to retreive the current mouse position
        // at any time and to fill the property mouse.x and mouse.y
        events.register("onmousemove","mouse.getPos();");
    }
}
