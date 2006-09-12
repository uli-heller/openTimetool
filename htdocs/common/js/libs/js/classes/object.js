/////////////////////////////////////////////////////
//
//      name:     object
//      type:     abstract class
//      created:  2000-05-02, cain
//      
//      comment:  initializing an object of this class 
//                without building additional functionality 
//                around it doesn't make sence
//                                            
//      revisions:
//                2000 / 05 / 02
//                - initial revision
//
//      $Log: object.js,v $
//      Revision 1.1.2.1  2003/11/09 14:40:54  wk
//      *** empty log message ***
//
//      Revision 1.1  2001/03/28 18:38:37  cain
//      no message
//
//
//

/////////////////////////////////////////////////////
//
//      constructor
//
/** 
* constructor
*      
* @version    2000/10/xx
*                              
* @author     Wolfram Kriesing <wolfram@kriesing.de>
*                  
* @param      
* @param      
* @return     
* 
*/
function class_object()
{
  this.LEFT = 0;
  this.RIGHT = 1;
  this.TOP = 2;
  this.BOTTOM = 3; 
                         
  this.createAll = object_createAll;
  this.createConstructor = object_createConstructor;  
  this.createMethods = object_createMethods;
  this.createParentMethods = object_createParentMethods;
  this.createEvents = object_createEvents;
  this.createProperties = object_createProperties;

  this.captureLocalEvent = object_captureLocalEvent;
  
  this.libsPath = "../../";
}

/** 
* creates all the events, methods and so on
*      
* @version    2001/01/17
*                              
* @author     Wolfram Kriesing <wolfram@kriesing.de>
*                  
* @param      
* @param      
* @return     
* 
*/
function object_createAll()
{                   
  this.createConstructor();
  this.createMethods();
  this.createParentMethods();
  this.createProperties();
  this.createEvents();
} // end of function
                           
/** 
* 
*      
* @version    2001/01/16
*                              
* @author     Wolfram Kriesing <wolfram@kriesing.de>
*                  
* @param      
* @param      
* @return     
* 
*/
function object_createEvents()
{                                      
  for( i=0 ; i < this.eventList.length ; i++ )
  {            
    eval("this."+this.eventList[i][0]+"  = new Function();");
  }
} // end of function

/** 
* 
*      
* @version    2000/10/16
*                              
* @author     Wolfram Kriesing <wolfram@kriesing.de>
*                  
* @param      
* @param      
* @return     
* 
*/
function object_createMethods()
{                                      
  for( i=0 ; i < this.methodList.length ; i++ )
  {            
    eval("this."+this.methodList[i][0]+" = "+this.classname+"_"+this.methodList[i][0]);
  }
} // end of function

/** 
* 
*      
* @version    2000/10/
*                              
* @author     Wolfram Kriesing <wolfram@kriesing.de>
*                  
* @param      
* @param      
* @return     
* 
*/
function object_createParentMethods()
{
  for( i=0 ; i < this.parentMethodList.length ; i++ )
  {            
    eval("this."+this.classname+"_"+this.parentMethodList[i][0]+" = "+this.classname+"_"+this.parentMethodList[i][0]);
  }
} // end of function

                            
/** 
* 
*      
* @version    2000/10/
*                              
* @author     Wolfram Kriesing <wolfram@kriesing.de>
*                  
* @param      
* @param      
* @return     
* 
*/
function object_createConstructor()
{                                    
  eval("this."+this.classname+" = class_"+this.classname);
} // end of function

/** 
* 
*      
* @version    2001/01/17
*                              
* @author     Wolfram Kriesing <wolfram@kriesing.de>
*                  
* @param      property list
* @return     
* 
*/
function object_createProperties()
{                                    
  for( i=0 ; i < this.propertyList.length ; i++ )
  {      
//    eval("this." + aPropertyList[i][0] + " = " + aPropertyList[i][1] );  // set the default value defined for this property
    eval("this."+ this.propertyList[i][0] +" = this.propertyList[i][1]" );  // set the default value defined for this property
  }
} // end of function                            
                            
/** 
* captures a local event, local means only events for the specified layer
*      
* @version    2001/01/21
*                              
* @author     Wolfram Kriesing <wolfram@kriesing.de>
*                  
* @param      string layer name
* @return     
* 
*/
function object_captureLocalEvent(event,functionToCall,layerName)
{                 
  localeLayerName = layerName || this.winName;  
  if( is.ns && !is.dom)
  {        
    if( event == "onmousedown" )  resolveReference(localeLayerName).captureEvents(Event.MOUSEDOWN);
    if( event == "onmouseup" )  resolveReference(localeLayerName).captureEvents(Event.MOUSEUP);
    if( event == "onmousemove" )  resolveReference(localeLayerNamerName).captureEvents(Event.MOUSEMOVE);
  }
  var a = new Function(functionToCall);
  eval("resolveReference('"+localeLayerName+"')."+event+" = "+a);
  
//  eval("resolveReference('"+layer+"')."+eventName+" = "+functionToCall);  
  
} // end of function                            

object = new class_object();