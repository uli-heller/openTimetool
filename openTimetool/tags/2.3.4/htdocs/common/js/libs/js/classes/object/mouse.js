/////////////////////////////////////////////////////
//
//      name:       mouse
//      type:       final class
//
//      created:    2000/05/25, cain@nomenclatura.de
//      
//      comment:    gets the current mouse position
//                  and calls the function callOnMouseMove
//                  overwrite it by: callOnMouseMove = new Function(...);
//                
//      how 2 use:  simply include it and mouse.x and mouse.y provide the 
//                  mouse position
//                  
//                  
//                            
//      revisions:
//                  2000 / 05 / 24, cain@nomenclatura.de
//                  - changed the mouseX to get the position relative to the window
//                    and not the element as before, by using clientX and clientY
//                    !!! for the NS still needs to be done
//                  2000/06/24
//                  - the event docuemnt.onmousemove is not caught in here anymore,
//                    now the class events is used for that and the mousemove gets registered 
//                    by default                             
//                  - mouseX is deprecated - mouse.x is the new way
//                  
//                  
//      $Log: mouse.js,v $
//      Revision 1.1.2.1  2003/11/09 14:40:54  wk
//      *** empty log message ***
//
//      Revision 1.5  2003/02/09 03:18:10  cain
//      - fix IE problems
//
//      Revision 1.4  2003/02/03 05:34:06  cain
//      - handle opera7 better
//      - CS issues
//
//      Revision 1.3  2001/06/18 22:07:56  cain
//      - added opera specific handling
//
//      Revision 1.2  2001/04/19 23:13:39  cain
//      - removed the position that was shown always in the status bar
//
//      Revision 1.1  2001/03/28 18:59:48  cain
//      no message
//
//                  
/////////////////////////////////////////////////////

var mouseX, mouseY = 0;

/////////////////////////////////////////////////////
//
//      function
//      name:       constructor
//      created:    2000/05/02, cain@nomenclatura.de
//                              
//      comment:    
//                  
//      parameter:  
//
function class_mouse()
{
	/////////////////////////////////////////
	//
	//  P U B L I C
	//  NOT to be overwritten
	//
  this.x = 0;                                     // the mouse position
  this.y = 0;
  this.getPos = mouse_getPos;
  
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
  
  /////////////////////////////////////////////////////
  //
  //      prevent from trouble when instanciating  
  //      this class many times
  //
	this.name = "mouse"+(class_mouse.count++);
	this.obj = this.name+"Object";
	eval(this.obj + "=this");
}
/////////////////////////////////////////////////////
//            I N H E R I T A N C E                //
//inherit everything from the class "class_object" //
class_mouse.prototype = new class_object();
//                                                 //
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
//
//      function
//      name:       mouse_getPos
//      created:    2000/.... , cain@nomenclatura.de
//                              
//      comment:    get the current mouse position
//                  mouseX and mouseY are deprecated !!!
//  
//      parameter:
//
function mouse_getPos(curEvent)
{                               
    if (is.ns) {
        this.x = mouseX = curEvent.pageX;     //!!!!!!!!!!!!! correct this
        this.y = mouseY = curEvent.pageY;
    }

    if (is.ie) {
        this.x = mouseX = window.event.clientX;
        this.y = mouseY = window.event.clientY;
    } else {
        // since opera doenst do it the dom way we got a check it seperatly
        if(is.op && is.version<7) {
            this.x = mouseX = curEvent.x+pageXOffset; // the pageXOffset is from the ix-magazine drag/drop example
            this.y = mouseY = curEvent.y+pageYOffset;
    //alert("got it "+this.x+"  y="+this.y );
        } else if(is.dom) {
            this.x = mouseX = curEvent.clientX;
            this.y = mouseY = curEvent.clientY;
            mouse.x = this.x;     // !!!!!!!!!!!! i dont know why i have to do this
            mouse.y = this.y;     // !!!!!!!!!!!! if i dont do it mouse.x and mouse.y stay 0, but it shouldnt be this way
        }
    }

//  status = this.x+" "+this.y;
//  return true; // ix 10/00 says should be returned
}

class_mouse.count = 0;

////////////////////////////////////// class end ///////////////////////////////////////////////////////

mouse = new class_mouse;
