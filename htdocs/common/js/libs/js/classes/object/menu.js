/////////////////////////////////////////////////////
//
//      name:     menu
//      type:     abstract class
//      
//      comment:  initializing an object of this class 
//                without building additional functionality 
//                around it doesn't make sence
//                                            
//                  
//      $Log: menu.js,v $
//      Revision 1.1.2.1  2003/11/09 14:40:54  wk
//      *** empty log message ***
//
//      Revision 1.1  2001/03/28 18:59:48  cain
//      no message
//
//                  
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////
//
//      constructor
//
function class_menu(layerPrefix)
{
	/////////////////////////////////////////
	//
	//  P U B L I C
	//  NOT to be overwritten
	//
  this.CLOSEMODE_DEFAULT = 0;
  this.CLOSEMODE_MANUAL = 1;  // close the menu by calling close-method

	/////////////////////////////////////////
	//
	//  P U B L I C
	//  may be overwritten
	//
	this.speed=10;		                              // how fast, does the menu open (in pixel)
  this.closeMode = this.CLOSEMODE_DEFAULT;

	// the name prefix to use for the layer 
	//	i.e.: <div id="menu1"...             <div id="myMenu1"...
	//				xxx.layerPrefix = menu;				 xxx.layerPrefix = myMenu;
  this.layerPrefix = !layerPrefix?"dropDownMenu":layerPrefix;
  // number of menus
  this.numMenus = 0;
  // search all the layers which belong to this menu
  while(isLayer(this.layerPrefix+this.numMenus++)); 
  if(this.numMenus) this.numMenus--;  // decrease the number if it is > 0 (to correct the last while)

  
  // offsets for positioning the menu, if it is not supposed to all the way on the edge of the page (as normally)
  // can also be used, for some correction
  this.offsetX   = 0;                             // a general offset for the entire menu ...
  this.offsetY   = 0;                             // ... this way it can also be somewhere on the screen (under another layer...)

	this.onOpened = new Function();
	this.onClosed = new Function();

  /////////////////////////////////////////////////////
  //
  //      prevent from trouble when instanciating  
  //      this class many times
  //
	this.name = "menu"+(class_menu.count++);
	this.obj = this.name+"Object";
	eval(this.obj + "=this");
  
}
/////////////////////////////////////////////////////
//
//      inherit everything from the class "class_object"
//                     
class_menu.prototype = new class_object();

class_menu.count = 0;