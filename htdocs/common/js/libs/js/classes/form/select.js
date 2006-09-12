//
//  $Log: select.js,v $
//  Revision 1.1.2.1  2003/11/09 14:40:54  wk
//  *** empty log message ***
//
//  Revision 1.4  2003/10/17 05:42:17  cain
//  + getSelectedValue
//
//  Revision 1.3  2003/10/11 07:44:30  cain
//  + setSelected
//
//  Revision 1.2  2003/03/16 01:54:53  cain
//  - added quite some functionality
//
//

/**
*   this is the constructor
*
*   @version    21/02/2003
*   @author     Wolfram Kriesing <wolfram@visionp.de>
*   @param      object  the form DOM-reference
*/
function class_form_select(ref)
{
    this._ref = ref;
    
    this.addOption = form_select_addOption;
    this.removeOption = form_select_removeOption;
    this.removeAllOptions = form_select_removeAllOptions;
    
    this.getLength = form_select_getLength;
    this.getOptions = form_select_getOptions;
    this.getOptionByValue = form_select_getOptionByValue;
    this.getIndexByValue = form_select_getIndexByValue;
    this.getSelectedValue = form_select_getSelectedValue;
    
    this.selectAll = form_select_selectAll;
    this.setSelected = form_select_setSelected;
    
    this.setSize = form_select_setSize;
}

/**
*   data is an array, with 'text' 'value' 
*
*/
function form_select_addOption(data)
{
    selBoxLen = this.getLength();
    this._ref.options[selBoxLen] = new Option();
    // loop through all the other data, in case there are more set
    // we dont pass any value before since it is not really necessary!
    for (var key in data) {
        this._ref.options[selBoxLen][key] = data[key];
    }
}

/**
*   
*
*/
function form_select_removeOption(index)
{
/*    selBoxLen = this.getLength();
    this._ref.options[selBoxLen] = new Option();
    // loop through all the other data, in case there are more set
    // we dont pass any value before since it is not really necessary!
    for (var key in data) {
        this._ref.options[selBoxLen][key] = data[key];
    }
*/    
    this._ref.options[index] = null;
}

/**
*   this removes all the options from the select box
*
*/
function form_select_removeAllOptions()
{
    for (i=0;i<this._ref.options.length;i++) {
        this.removeOption(i);
    }
}

/**
*   get the length, which is the same as the number of elements
*
*/
function form_select_getLength()
{
    return this._ref.options.length;   
}

function form_select_getOptions(what)
{
    if (what==null) {
        return this._ref.options;
    } else {
        ret = new Array();
        for (i=0;i<this._ref.options.length;i++) {
           ret[i] = this._ref.options[i][what];
        }
    }
    return ret;
}

function form_select_getIndexByValue(value)
{
    for (i=0;i<this._ref.options.length;i++) {
        if (this._ref.options[i]["value"]==value) {
            return i;
        }
    }   
    return false;
}

function form_select_getOptionByValue(value)
{
    _index = this.getIndexByValue(value);
    if (_index!==false) {       // the index might be 0, so we have to check false explicitly
        return this._ref.options[_index]
    }
    return false;
}

/**
*   set the size of the select box, if no param given it auto sizes to the number of elements
*
*   @see    getLength()
*   @param  int     the size to be set, if not given getLength() will be used
*   @return void
*/
function form_select_setSize(size)
{
    if (size==null) {
        size = this.getLength();
    }
    this._ref.size = size;
}

/**
*   selects all the options of this select box, optionally makes it multiple 
*
*   if you pass the parameter true then the attribute "multiple"
*   will be set, so that selecting all options is also possible
*
*   @param  boolean     true to set attribute "multiple"
*/
function form_select_selectAll(setMultiple)
{
    if (setMultiple) {
        this._ref.multiple = "multiple";
    }
    
    for (i=0;i<this._ref.options.length;i++) {
        this._ref.options[i].selected = true;
    }    
}


/**
* Select the given value(s)
*
* @param mixed either a single value[, or an array of values]
* @todo implement handling value being an array
*/
function form_select_setSelected(value)
{
    for (i=0;i<this._ref.options.length;i++) {
        if (this._ref.options[i].value == value) {
            this._ref.options[i].selected = true;
            break;
        }
    }
}

/**
* Get the currently selected value, from the select box.
* 
*
* @todo This does not work for multiples yet!
* @return mixed the value of the selected option
*/
function form_select_getSelectedValue()
{
    for (i=0;i<this._ref.options.length;i++) {
        if (this._ref.options[i].selected) {
            return this._ref.options[i].value;
        }
    }    
    return false;
}
