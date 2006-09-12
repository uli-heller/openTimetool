/////////////////////////////////////////////////////
//
//      name:       CLASSNAME
//      type:       final
//
//      created:    wolfram@kriesing.de
//
//      comment:    basic browser abstraction layer
//
//      revisions:
//                  21/01/2001 wolfram@kriesing.de
//                  - made resolverefernce return the proper thing for DOM too
//                    now no document.getElementById needs to be used anymore
//                    a direct call to resolveReference returns the right reference
//                    for each browser
//
//      revisions:
//      Revision 1.3  2001/02/21 19:41:26  cain
//      made makeLayer work for DOM
//
//      $Log: func.js,v $
//      Revision 1.1.2.1  2003/11/09 14:40:54  wk
//      *** empty log message ***
//
//      Revision 1.15  2003/10/17 05:43:14  cain
//      + CS issues
//
//      Revision 1.14  2003/03/16 01:55:41  cain
//      - make getWidth() work a little better
//      - make hideMagic and showMagic also work in mozilla
//
//      Revision 1.13  2003/02/09 03:19:24  cain
//      - added show and hideMagic
//
//      Revision 1.12  2003/02/07 03:43:22  cain
//      - added getScrollOffsetX/Y
//
//      Revision 1.11  2003/02/03 05:35:37  cain
//      - resolveRef does also handle objects, as well as the name or id of an object
//      - getWidth/Height now also handle static elements
//
//      Revision 1.10  2003/01/31 05:42:54  cain
//      - now you can also use references for any function
//
//      Revision 1.9  2002/09/29 08:58:30  cain
//      - made it basically work in Mozilla 1.2a
//
//      Revision 1.8  2002/09/21 19:09:27  cain
//      - added function setClass
//
//      Revision 1.7  2002/09/21 09:34:55  cain
//      - some reformatting and bugfix for opera
//
//      Revision 1.6  2002/04/30 03:41:24  cain
//      - use offsetHeight in dom browser, if style.height doesnt return anything
//
//      Revision 1.5  2002/04/27 07:27:50  cain
//      - made clipping also work in dom-browsers
//
//      Revision 1.4  2001/11/20 22:40:19  cain
//      - added some stuff for Mozilla in getSelection
//
//      Revision 1.3  2001/11/05 16:37:55  cain
//      - some modification for Konqueror :-)
//
//      Revision 1.2  2001/04/03 18:29:20  cain
//      - added the funtion getSelected
//      - commented out some stuff, that is needed for Mozilla, but not for NS4
//
//      Revision 1.1  2001/03/28 18:38:37  cain
//      no message
//
//
//
/////////////////////////////////////////////////////

/** 
* create a new layer
*      
* @version    2001/02/13
*
* @author     Wolfram Kriesing <wolfram@kriesing.de>
*
* @return     the layer name
*/
function makeLayer(name,x,y,width,height)
{
    if (is.ns4)
    {
        return new Layer(100);
    }

    if (is.ie4)
    {
        var code = '<span id="'+name+'" style="position:absolute; left:0px; top:0px; width:100px;"></div>';
        window.insertAdjacentHTML("beforeEnd", code);
        //lyr = parentElement.children[parentElement.children.length-1]
    }

    if (is.dom)
    {
        //var parentName;
        //var windowName;
//FIXXME handle this properly for name like 'div1.div2.div3' which is actually a bit stupid but hey ...
        nameSplitted = name.split(".");
        if( nameSplitted.length>1 )
        {
            parentName = nameSplitted[0];
            windowName = nameSplitted[1];
            parentLayer = resolveReference(parentName);
        }
        else
        {
            windowName = name;
            parentLayer = document.getElementsByTagName("body")[0]
        }

        lyr = document.createElement("SPAN");
        parentLayer.appendChild(lyr);
        lyr.name = windowName;
        lyr.id = windowName;
        lyr.style.position = "absolute";
        if( width )     lyr.style.width   = width;
        if( height )    lyr.style.height  = height;
        lyr.style.left    = x ? x : "10";
        lyr.style.top     = y ? y : "10";

        return windowName;

    /* neeeeeded 4 DOM
        try
        {
        document.body.appendChild(lyr);
        }
        catch(e)
        {
        alert("ERROR func.js: couldn't create SPAN "+name);
        }
    */
    }
} // end of function

/**
*
*   fills filler in the div name
*
*   @para   string  can be like this menu5.pageNumber means a div in a div
*                   the NS needs both divs to reference
*                   the inner div, the IE just uses pageNumber
*                   NS uses document.menu5.document.pageNumber
*/
function fillInnerHTML(name,filler)
{
    if(is.ie4 || is.dom)
    {
        // is just a hack of Mozilla, because there is no innerHTML in DOM
        resolveReference(name).innerHTML = filler;
//        document.getElementById(name).innerHTML = filler;
        // document[name].innerHTML = filler;   // this was only a test for an iframe
//    return; // this is ONLY because in IE 4+ is.dom is also true, but it doesnt work all the way yet
    }
    if(is.ns4)	// resolve the referencing
    {
        doc = resolveReference(name);
        doc.document.write(filler);
        doc.document.close();
    }
}

/////////////////////////////////////////////////////
//
//
//
function getInnerHTML(name)
{
    if(is.ie4 || is.dom)
    {
        return resolveReference(name).innerHTML;
    }
    if(is.ns4)
    {
        return "";  // there is no implementation of getInnerHTML for NS4
        //doc = resolveReference(name);
        // ???????
    }
}


function clip(name,cLeft,cTop,cRight,cBottom)
{
    if(is.ie4 || is.dom)
    {
        //document.all[name].style.clip='rect('+cTop+' '+cRight+' '+cBottom+' '+cLeft+')';
        resolveReference(name).style.clip='rect('+cTop+' '+cRight+' '+cBottom+' '+cLeft+')';
    }
    if(is.ns4)
    {
        doc = resolveReference(name);
        doc.clip.top = cTop;
        doc.clip.right = cRight;
        doc.clip.bottom = cBottom;
        doc.clip.left =  cLeft;
    }
}

///////////////////////////////////////////////
//
//
//
function setY(name,posTop) {setTop(name,posTop)}
function setTop(name,posTop)
{
	if(is.ie4 || is.dom)
  {
		resolveReference(name).style.top=posTop;
    return; // this is ONLY because in IE 4+ is.dom is also true, but it doesnt work all the way yet
  }
	if(is.ns4)
		resolveReference(name).top=posTop;
}


///////////////////////////////////////////////
//
//
//
function setX(name,posLeft) {setLeft(name,posLeft)}
function setLeft(name,posLeft)
{
    if (is.ie4 || is.dom) {
        resolveReference(name).style.left=posLeft;
        return; // this is ONLY because in IE 4+ is.dom is also true, but it doesnt work all the way yet
    }
    if (is.ns4) {
        resolveReference(name).left=posLeft;
    }
}

///////////////////////////////////////////////
//
//		
//
function setZ(name,zIndex)
{
	if(is.ie4 || is.dom)
  {
		resolveReference(name).style.zIndex=zIndex;
    return; // this is ONLY because in IE 4+ is.dom is also true, but it doesnt work all the way yet
  }
	if(is.ns4)
		resolveReference(name).zIndex=zIndex;
}

///////////////////////////////////////////////
//
//		
//
function setPos(name,posLeft,posTop)
{
  setLeft(name,posLeft);
  setTop(name,posTop);
}

///////////////////////////////////////////////
//
//		
//
function setHeight(name,sizeHeight)
{
	if(is.ie4 || is.dom)
  {
		resolveReference(name).style.height=sizeHeight;
    return; // this is ONLY because in IE 4+ is.dom is also true, but it doesnt work all the way yet
  }
	if(is.ns4)
		resolveReference(name).height=sizeHeight;
}

///////////////////////////////////////////////
//
//		
//
function setWidth(name,sizeWidth)
{
	if(is.ie4 || is.dom)
  {
		resolveReference(name).style.width=sizeWidth;
    return; // this is ONLY because in IE 4+ is.dom is also true, but it doesnt work all the way yet
  }
	if(is.ns4)
		resolveReference(name).width=sizeWidth;
}

///////////////////////////////////////////////
//
//		
//
function getHeight(name)
{
    if(is.ie4)
        return resolveReference(name).offsetHeight;
    if(is.ns4)
        return resolveReference(name).document.height;
    if(is.dom)
        // ????????  can only be read if it was set before, either by setHeight or in the layer-style
        //return parseInt(resolveReference(name).style.height  ?  resolveReference(name).style.height : 277);
        return parseInt(resolveReference(name).style.height  ?  resolveReference(name).style.height : resolveReference(name).offsetHeight );
}


///////////////////////////////////////////////
//
//
//
function getClipHeight(name)
{
    if(is.ie4 || is.dom)
    {
        // get the clip parameters in an array clip is like that: top right bottom left
        // aClips = document.all[name].style.clip.split(" ");
        aClips = resolveReference(name).style.clip.split(" ");
        // replace the characters to get a number because aClips[0] is "rect(0"
        aClips[0] = aClips[0].replace(/[^0-9]+/,"");
        // status = "0="+aClips[0]+"   2="+aClips[2];
        return parseInt(parseInt(aClips[2])-parseInt(aClips[0]));   // height = bottom - top
    }
    if(is.ns4)
        return resolveReference(name).clip.height;
}

///////////////////////////////////////////////
//
//		
//
function getWidth(name)
{
    if (is.ie) {
        return resolveReference(name).offsetWidth;
    }
    if (is.ns4) {
        return resolveReference(name).clip.width; // !!!! this is the clipping width, but not the actual width...
    }
        // NS doesnt have an property document.width, but therefore i cant use this
        // to read the width while i am changing the clipping width.... shit (i.e. loadingBar)
//		return resolveReference(name).document.width;
    if (is.dom) {
    // ????????  can only be read if it was set before, either by setWidth or in the layer-style
        _width = parseInt(resolveReference(name).style.width);
        if (!_width) {
            _width = parseInt(resolveReference(name).offsetWidth);
        }
        return _width?_width:0; // lets not return NaN, since that is not really useful
    }
    return 0;
}

/**
*
*
*/
function getX(name) {return getLeft(name)}
function getLeft(name)
{
    if (userAgent.dom) {
        // if the element is not positioned absolute the IE returns undefined, which is OK
        ret = parseInt(resolveReference(name).style.left);
    } else {
        if (userAgent.ie && userAgent.version<5) {
            ret = resolveReference(name).offsetLeft;
        }
        if (userAgent.ns4) {
            ret = resolveReference(name).left;
        }
    }
/*    try{leftPos=(document.getElementById(name)).style.left; }
    catch(exception)
    {leftPos="";}
    return leftPos;*/
    
    // if we have no x then we try if there is a static value, which
    // is for not absolute positioned elements, like normal HTML elements, without divs around them, etc.
    if (!ret) {
        ret = getXOfStatic(name);
    }

    return ret;
}

///////////////////////////////////////////////
//
//
//
function getY(name) {return getTop(name);}
function getTop(name)
{
    if (is.ie4) {
        ret = resolveReference(name).offsetTop;
    }
    if (is.ns4) {
        ret = resolveReference(name).top;
    }
    if (is.dom) {
        ret = parseInt(resolveReference(name).style.top);
    }
    if (!ret) {
        ret = getYOfStatic(name);
    }
    return ret;
}

///////////////////////////////////////////////
//
//
//
function getZ(name)
{
  return;
}


/**
*   we try to get the static position, which
*   is for not absolute positioned elements, like normal HTML elements, without divs around them, etc.
*/
function getXOfStatic(name)
{
    element = resolveReference(name);
    ret = element.offsetLeft;
    do {
//alert(element+" "+element.offsetParent);    
        element = element.offsetParent;
        ret += element.offsetLeft;
    } while(element.tagName.toLowerCase()!="body");

    return ret;
}

/**
*   we try to get the static position, which
*   is for not absolute positioned elements, like normal HTML elements, without divs around them, etc.
*/
function getYOfStatic(name)
{
    element = resolveReference(name);
    ret = element.offsetTop;
    do {
        element = element.offsetParent;
        ret += element.offsetTop;
    } while(element.tagName.toLowerCase()!="body");

    return ret;
}



///////////////////////////////////////////////
//
//
//
function show(name,doHideMagic)
{
    if(is.ie4) {
        resolveReference(name).style.visibility="visible";
        // resolveReference(name).style.display = "";  // only used, for the case, that a IE only uses display:none, as the example object.window.html does
        return; // this is ONLY because in IE 4+ is.dom is also true, but it doesnt work all the way yet
    }

    if (is.ns4) {
        resolveReference(name).visibility="visible";
    }

    if(is.dom) {// makes problems in IE, but does actually work too
        // if i remove the "return" 5 lines above, it should actually work 2 but
        // the IE wont let me drag objects then....strange
        resolveReference(name).style.display = "inline"; // according to the CSS-spec
        // actually only "display:none" should be used, but since
        // the IE does strange things, if i replace all the "visibility:hidden;" by "display:none"
        // i am using both here, so the NS6 shows it too
        resolveReference(name).style.visibility = "visible";
    }
    
    if (doHideMagic==true) {
        hideMagic(name);
    }
    
}

///////////////////////////////////////////////
//
//
//
function hide(name,doShowMagic)
{
    if(is.ie4 || is.op) { // it seems to be relevant for op5 i dont know for other operas!
        resolveReference(name).style.visibility="hidden";
        return; // this is ONLY because in IE 4+ is.dom is also true, but it doesnt work all the way yet
    }

    if(is.ns4) {
        resolveReference(name).visibility="hidden";
    }
    if(is.dom) {// works also for IE 5.x, but only with display:none
        resolveReference(name).style.visibility = "hidden";
//        resolveReference(name).style.display = "none"; well the above seems to work too :-)
    }
    if (doShowMagic==true) {
        showMagic(name);
    }
    
}

function isHidden(name)
{
    if(is.ie4 || is.op || is.dom) {
        return resolveReference(name).style.visibility=="hidden";
    }

    if(is.ns4) {
        return resolveReference(name).visibility=="hidden";
    }
}

///////////////////////////////////////////////
//
//		does the layer "which" exist?
//
function isLayer(which)
{
    if(is.dom && !is.ie4) // !!! actually it works in IE 4 too, but somehow not always
    {                     // maybe because of nested layers ?????
        if(resolveReference(which))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    // !!!!!!!! extend which, if it is like "layer1.layer2"
    if(resolveReference(which))
        return true;
    return false;
}

/////////////////////////////////////////////////////
//
//      sets the background color
//
function setBgColor(which,color)
{
	if(is.ie4)
        resolveReference(which).style.backgroundColor = color;
	if(is.ns4)
		resolveReference(which).bgColor = color;
}

/////////////////////////////////////////////////////
//
//      sets the body background color
//
function setBodyBgColor(color)
{
	if(is.ie4 || is.dom)
		document.body.style.backgroundColor = color;
	if(is.ns4)
		document.bgColor = color;
}

/////////////////////////////////////////////////////
//
//      gets the selected text
//
function getSelection()
{
/*  Mozilla 0.9.5 says document.getSelection is deprecated but window.getSelection brings an error and doesnt work, so we stay with document
    if (window.getSelection) // Mozilla 0.9.5, the proper way i figure
    {
        txt = window.getSelection();
        alert(txt);
    }
    else*/
    if (document.getSelection) txt = document.getSelection(); // NS
	else if (document.selection) txt = document.selection.createRange().text; // IE
	else return;

//	txt = txt.replace(new RegExp('([\\f\\n\\r\\t\\v ])+', 'g')," ");
  return txt;
}

function emptySelection()
{
  if (document.getSelection)
  {
  }
  else
  {
    document.selection.empty();
  }
}


/**
*
*/
function setClass( which , className )
{
    //element = document.getElementById(elementName)?document.getElementById(elementName):document.getElementsByName(elementName)[0];
    element = resolveReference(which);
    if( is.ie )
    {
        element.className = className;
    }
    else
    {
        element.setAttribute("class",className);
    }
}

/**
*   this function finds the next parent with the given tagName
*   this is very useful when you have an input field and want to add
*   a hidden-input field to the form
*
*
*/
function findParent(element,tagName)
{
}


function getScrollOffsetX()
{   
    return window.pageXOffset;
}

function getScrollOffsetY()
{
    return window.pageYOffset;
}


/**
*   hides <select> and <applet> objects (for IE only)
*   this is sometimes needed since the IE has problems showing a div on top of a 
*   select box, etc.
*   @author Tan Ling Wee, fuushikaden@yahoo.com
*/
var _excludeFromHideMagic = new Array();
function hideMagic(element)
{
    _excludeFromHideMagic = new Array();
    if (userAgent.ie || userAgent.moz) {
        overDiv = resolveReference(element); //i dont know why we shouldnt use this :-(
        //overDiv = element;
        tagNames = new Array('select','applet');
        for(a=0;a<tagNames.length;a++) {
            curElements = document.getElementsByTagName(tagNames[a]);
            for (i=0;i<curElements.length;i++) {
                obj = curElements[i];
                if (!obj || !obj.offsetParent) {
                    continue;
                }
                if (isHidden(obj)) {
                    _excludeFromHideMagic[i] = true;
                    continue;
                }
                objX = getX(obj);
                objY = getY(obj);
                objWidth = getWidth(obj);
                objHeight = getHeight(obj);

                x = getX(overDiv);
                y = getY(overDiv);
                width = getWidth(overDiv);
                height = getHeight(overDiv);

                if ((x+width ) <= objX );
                else if(( y + height ) <= objY );
                else if( y >= ( objY + objHeight ));
                else if( x >= ( objX + objWidth ));
                else {
                    hide(obj);
                }
                
            }
        }
    }
}


/**
*   shows <select> and <applet> objects (for IE only)
*
*   @author Tan Ling Wee, fuushikaden@yahoo.com
*   @see    hideMagic()
*/
function showMagic()
{
    if (userAgent.ie || userAgent.moz) {
        tagNames = new Array('select','applet');

        for(a=0;a<tagNames.length;a++) {
            curElements = document.getElementsByTagName(tagNames[a]);
            for (i=0;i<curElements.length;i++) {
                obj = curElements[i];
                if( !obj || !obj.offsetParent ) {
                    continue;
                }
                if (_excludeFromHideMagic[i]==true) {
                    continue;
                }
                show(obj,false);
                //obj.style.visibility = "";
            }
        }
    }
}




/////////////////////////////////////////////////////
//
//      this function constructs the proper reference
//	depending on a browser
//
//	input:	layer1.layerInsideLayer1
//	output:	depending on the browser type
//		IE:	document.all.layerInsideLayer1
//		NS:	document.layer1.document.layerInsideLayer1
//
function resolveReference(name)
{
    var a;

    if (typeof name !="string" && parseInt(name)!= name) {
        return name;
    }

    aRefs = name.split(".");
    if (is.dom) {
        // if something like this "win.subwin.sub1win" is passed to this function, it returns "sub1win"
        // what happens if windows in different levels are called the same, like win.win, which win is used?
        // or is that forbidden in DOM?

        // check if the element with this id is given, if not, try the name :-)
        if (doc = document.getElementById(aRefs[aRefs.length-1])) {
            return doc;
        } else {
            if (doc=document.getElementsByName(aRefs[aRefs.length-1])[0]) {
                return doc;
            } else {
                return false;
            }
        }
    }
    if(is.ie4) {
        return eval(document.all[aRefs[aRefs.length-1]]);
    }
    if(is.ns4) { // resolve the referencing
        refString = 'document["'+aRefs[0]+'"]';	// not to have the period before the first document
        for (a=1 ; a < aRefs.length ; a++) {
            refString += '.document["'+aRefs[a]+'"]';
        }
        return eval(refString);
    }
}


/////////////////////////////////////////////////////
//
//      opens a new window using JS-function open
//
function openNewWindow(src,name,paras,addParas)
{
    aParameters = new Array();
    aParameters = parseParameters(paras,new Array(",","="));
    if(addParas)	// any additional parameters?
    {
        // add additional parameters to paras
        if(addParas.match(/position:middle/))
        {
            newLeft = parseInt(env.screenWidth/2-aParameters['width']/2);
            newTop = parseInt(env.screenHeight/2-aParameters['height']/2);
//            paras = paras.concat( ",left="+newLeft+","+"top="+newTop);    Konqueror doesnt get that
            paras = paras + ",left="+newLeft+","+"top="+newTop;
        }

    }

    /////////////////////////////////
    //
    //  finally open the window
    //
    __newWins[__winNum] = open(src,name,paras);
    setTimeout("__newWins["+(__winNum++)+"].focus()",100); // this is used for the case, that the window is opened by a double click (some users do that)
}
__newWins = new Array();
__winNum = 0;

/////////////////////////////////////////////////////
//
//  parses a parameter list like: "width:100px; left:300px;" or "width=400,height=350"
//  and returns an array: aParameters["width"] = 100
//                        aParameters["left"] = 300
//
//  parameters: aSplitChar  - is an array, which contains the characters that devide the different
//                            parameters and their values
//                            using the example above, it would be
//                            aSplitChar[0] = ";"
//                            aSplitChar[1] = ":"
//
function parseParameters(paras,aSplitChar)
{
  var a;

  aParameters = new Array();
  aParas = paras.split(aSplitChar[0]);
  for( a=0 ; a<aParas.length ; a++ )
  {
    aSplit = aParas[a].split(aSplitChar[1]);
    aParameters[aSplit[0]] = parseInt(aSplit[1]);
  }
  return aParameters;
}
