//
//  $Log: projectTree.js.tpl,v $
//  Revision 1.8.2.4  2003/04/01 08:21:29  wk
//  - somehow the updateDueToProjectSpan() doesnt work properly :-(
//
//  Revision 1.8.2.3  2003/03/28 10:28:51  wk
//  - truncate project name when it gets too long, I extended getPathAsString for that
//
//  Revision 1.8.2.2  2003/03/19 19:39:07  wk
//  - make the multiSelect stuff work
//
//  Revision 1.8.2.1  2003/03/11 16:06:02  wk
//  - load js-files only as needed
//  - add locale to xml-config, even though it has no effect yet
//  - declare private var in js-class
//
//  Revision 1.8  2003/03/06 13:22:08  wk
//  - prevent JS error
//
//  Revision 1.7  2003/03/04 19:11:51  wk
//  - starting to add multiSelect
//
//  Revision 1.6  2003/02/17 19:15:45  wk
//  - some bugfixing
//
//  Revision 1.5  2003/02/13 16:15:20  wk
//  - fix positioning in IE
//  - and make it work in IE
//
//  Revision 1.4  2003/02/11 11:11:47  wk
//  - make preselect work properly
//  - add method select
//
//  Revision 1.3  2003/02/10 19:26:32  wk
//  - add method getPathAsString
//
//  Revision 1.2  2003/02/10 16:17:24  wk
//  - add a lot features
//
//  Revision 1.1  2003/02/05 19:01:21  wk
//  - initial revision

// locale-setting doesnt work due to a bug in 1.7.3
// remove caching for 1 day, when updateDueToProjectSpan() is implemented properly!!!
<HTML_Template_Xipe>
    <options>
        <delimiter begin="{{" end="}}"/>
        <cache>
            <time value="1" unit="day"/>
            <depends value="$cacheKey"/>
        </cache>
        <locale value=""/>
    </options>
</HTML_Template_Xipe>

/* flag to signal that the projecttree-info has been generated and loaded */
var projectTreeLoaded = false;

/**
*   the constructor of the projectTree object
*   @author     Wolfram Kriesing <wk@visionp.de>
*   @version    03/02/06
*/
function class_projectTree()
{
    // methods
    this.init = projectTree_init;
    this.hide = projectTree_hide;
    this.show = projectTree_show;
    this.checkForHide = projectTree_checkForHide;
    this.getPathAsString = projectTree_getPathAsString;

    this.select = projectTree_select;
    this.multiSelect = projectTree_multiSelect;
    this.unselect = projectTree_unselect;
    this.onClick = projectTree_onClick;
    
    this._updateTextarea = projectTree__updateTextarea;

    // properties
    // the id/name of the element that contains the path-string
    this._valueRef = "projectText";
    this._positioned = false;   // did we already position the div?
    // this is the last element, that was selected by the user
    // this is needed to show it to the user and unselect it when a new one is selected, but only
    // in single mode
    this._lastSelectedElement = null;

    // this is the name of the input field that shall be filled with the value
    // it might also be "newData[projectTree_ids][]" for passing multiple ids
    this._treeIdRef = "newData[projectTree_id]";

    this._divName = "projectTreeDiv";

    this._shown = false;    // is the tree visible?
    // this is the id of the event that we register for onclick to close the
    // opened div
    this._onClickHandler = null;
    
    // this is true when multiple nodes can be selected
    this._multiSelect = false;

    this._pathAsString = new Array();
    {{foreach($pathsAsString as $id=>$path)}}
        this._pathAsString[{{$id}}] = "{{echo str_replace('"','\\"',$path)}}";
        
    
}

/**
*   the constructor of the projectTree object
*
*   @author     Wolfram Kriesing <wk@visionp.de>
*   @version    03/02/06
*   @param      object  element reference
*   @param      boolean shall there be a div around the projectTree?
*/
function projectTree_init(divAround,treeIdRef,valueRef)
{
    if (treeIdRef!=null) {
        this._treeIdRef = treeIdRef;
        if (treeIdRef.substr(treeIdRef.length-2,2)=="[]") {
            this._multiSelect = true;
            hide(this._treeIdRef);  // in multiSelect this shall be invisibile, an IE has problems with it, so we do it explicitly here
        }
    }
    if (valueRef!=null) {
        this._valueRef = valueRef;
    }

    if (divAround || divAround==null) {
        divAround = true;
    } else {
        divAround = false;
    }

    treeView = "{{echo str_replace("\r",'',str_replace("\n",'',str_replace('"','\\"',preg_replace("~(\s{2})~",'',$treeMenu->toHtml()))))}}";

    if (divAround) {
        stylePos = "";
        if (!userAgent.ie) {
            x = getX(this._valueRef);
            y = getY(this._valueRef)+getHeight(this._valueRef)+2;
            stylePos = 'style="left:'+x+'px; top:'+y+'px"';
            this._positioned = true;
        }
//alert(x+" "+y);        

        treeView =  '<div id="'+this._divName+'" class="treeMenuDiv" '+stylePos+'>'+
//                    '<div style="overflow:auto; height:300px;">'+
                    treeView+
//                    '</div>'+
                    '</div>'
    }
    document.write(treeView);

    // if there is no div around it then we usually dont preselect anything, since there
    // is not button or alike
    if (divAround) {
        if (_projectTree_preSelect) {
            this.select(_projectTree_preSelect);
        }
        if (_projectTree_preMultiSelect!=null) {
            this.multiSelect(_projectTree_preMultiSelect);
            this._multiSelect = true;
        }
    }
}

/**
*
*
*   @author     Wolfram Kriesing <wk@visionp.de>
*   @version    03/02/06
*   @param      object  element reference
*   @param      integer the id of the node, needed for submission
*   @param      boolean shall there be a div around the projectTree?
*/
function projectTree_onClick(id,element)
{
    if (this._multiSelect) {
        // this is meant to set the "_selected" property of the element so the 
        // if below works properly, this is due to the projects that are selected
        // but since we have no ref to "element" we can not set the attribute when initializing
        selBox = new class_form_select(resolveReference(this._treeIdRef));
        if (selBox.getOptionByValue(id)) {
            element._selected=true;
        }
    
    } else {    // if its no multiselect then we unselect the previously selected element
        this.unselect(id,element);
    }
    
    if (element!=null) {
        if (!element._selected) {
            this.select(id,element);
        } else {
            this.unselect(id,element);
        }
    } else {
        // normally onClick selects a project, so do that here in case no element is given
        this.select(id);
    }
    if (!this._multiSelect) {
        this.hide();
    }
}


// if element is not given set the value in for the projectTree_id anyway
// this might be the case for a preselcted via _projectTree_preSelect
function projectTree_select(id,element)
{
    if (this._multiSelect) {
        //selBox = new class_form_select(this._treeIdRef);
        selBox = new class_form_select(resolveReference(this._treeIdRef));
        newData = new Array();
        newData["value"] = id;
        newData["text"] = id;
        selBox.addOption(newData);
        this._updateTextarea();
    } else {
        resolveReference(this._valueRef).value = this.getPathAsString(id,50);
        resolveReference(this._treeIdRef).value = id;
    }
    
    if (element!=null) {
        setClass(element,"treeMenuSelected");
        element._selected = true;
        this._lastSelectedElement = element;
// FIXXXME if the input field does not exist, create it!!!!
    }
}

/**
*   this updates the textarea which shows the project names
*   
*   read them from the select box, which is hidden and simply put all the
*   names in there, so the user sees the list of projects
*/
function projectTree__updateTextarea()
{
    selBox = new class_form_select(resolveReference(this._treeIdRef));
    selBox.selectAll(true);
    
    _projects = selBox.getOptions("value"); // get really only the values of each option
    _filler = "";
    for (i=0;i<_projects.length;i++) {
        _line = this.getPathAsString(parseInt(_projects[i]),40);
        _filler += _line+"<br>";
    }
    if (!_projects.length) {
        _filler = "all projects";
    }
    
    fillInnerHTML("projectText",_filler);
    
    setX(this._divName,getX(this._valueRef));
    setY(this._divName,getY(this._valueRef)+getHeight(this._valueRef)+2);
}

/**
*   select multiple projects
*
*   this method is actually only called once, when the tree is built
*   @param  array   the ids
*/
function projectTree_multiSelect(ids)
{   
    selBox = new class_form_select(resolveReference(this._treeIdRef));
    for (i=0;i<ids.length;i++) {
        newData = new Array();
        newData["text"] = ids[i];
        newData["value"] = ids[i];
        selBox.addOption(newData);        
//FIXXXME we should somehow select the selected elements here ... dont know how yet    
    }
    this._updateTextarea();    
}

/**
*
*
*   @author     Wolfram Kriesing <wk@visionp.de>
*   @version    03/02/06
*   @param      object  element reference, if not given
*                       the lastSelectedElement is used
*/
function projectTree_unselect(id,element)
{
    if (this._multiSelect) {
        selBox = new class_form_select(resolveReference(this._treeIdRef));
        selBox.removeOption(selBox.getIndexByValue(id));
        this._updateTextarea();
    }
    if (element==null && this._lastSelectedElement!=null) {
        element = this._lastSelectedElement;
    }

    if (element!=null) {
        setClass(element,"treeMenu");
        element._selected = false;
    }
}

/**
*   show the tree
*
*   @author     Wolfram Kriesing <wk@visionp.de>
*   @version    03/02/06
*/
function projectTree_show()
{
    if (!this._positioned) {
        setX(this._divName,getX(this._valueRef));
        setY(this._divName,getY(this._valueRef)+getHeight(this._valueRef)+2);
        this._positioned = true;
    }

    if (this._shown==true) {
        return;
    }

    //events.register("onkeypress","projectTree.hide();");
    if (projectTree._onClickHandler!=null) {
        events.unregister(projectTree._onClickHandler);
        projectTree._onClickHandler = null;
    }

    this._shown = true;
    show(this._divName,true);
    projectTree._onClickHandler = events.register("onclick","projectTree.checkForHide()",1);
}

/**
*   hide the project tree
*
*   @author     Wolfram Kriesing <wk@visionp.de>
*   @version    03/02/06
*/
function projectTree_hide()
{
    if (this._onClickHandler!=null) {
        events.unregister(this._onClickHandler);
        this._onClickHandler = null;
    }
    hide(this._divName,true);
    this._shown = false;
}

/**
*   this method calls hide() in case the mouse is not over the tree-view
*   
*   @see    hide()
*
*/
function projectTree_checkForHide()
{
    _left = getLeft(this._divName);
    _top = getTop(this._divName);
//FIXXXME
    _width = getWidth(this._divName);    // for some strange reason this fails, fix it some day
    _right = _left + (_width?_width:300);     // we assume 300 just to be kinda sure :-)
    _bottom = _top + getHeight(this._divName);

//alert("l:"+left+" r:"+right+" t:"+top+" b:"+bottom);
    if (!(mouse.x<_right && mouse.x>_left && mouse.y>_top && mouse.y<_bottom)) {
        this.hide();
    }    
}

/**
*   get the path as string, from the private array
*
*/
function projectTree_getPathAsString(id,maxLength)
{
    ret = this._pathAsString[id];
    if (maxLength!=null && ret.length>maxLength) {
        ret = "..."+ret.substr(ret.length-maxLength,maxLength);
    }
    return ret;
}

projectTree = new class_projectTree();

document.write(
    '<script src="{{$config->applPathPrefix}}/external/HTML_TreeMenu/TreeMenu.js"></script>'
    +"\r\n"+'<script src="{{$config->applPathPrefix}}/common/js/libs/js/classes/form/select.js"></script>'
    +"\r\n"+'<script src="{{$config->applPathPrefix}}/common/js/libs/js/classes/form/textarea.js"></script>'
    );

if (typeof(events)=='undefined') {    
    document.write(
        '<script src="{{$config->applPathPrefix}}/common/js/libs/js/classes/func.js"></script>'
        +"\r\n"+'<script src="{{$config->applPathPrefix}}/common/js/libs/js/classes/env.js"></script>'
        +"\r\n"+'<script src="{{$config->applPathPrefix}}/common/js/libs/js/classes/object.js"></script>'
        +"\r\n"+'<script src="{{$config->applPathPrefix}}/common/js/libs/js/classes/object/mouse.js"></script>'
        +"\r\n"+'<script src="{{$config->applPathPrefix}}/common/js/libs/js/classes/object/events.js"></script>'
        );
}

/* info has been written and loaded, the pages can init it */
projectTreeLoaded = true;
