//
//  $Log: textarea.js,v $
//  Revision 1.1.2.1  2003/11/09 14:40:54  wk
//  *** empty log message ***
//
//  Revision 1.1  2003/03/15 22:49:44  cain
//  - initial commit
//
//

/**
*   this is the constructor
*
*   @version    19/03/2003
*   @author     Wolfram Kriesing <wolfram@visionp.de>
*   @param      object  the form DOM-reference
*/
function class_form_textarea(ref)
{
    this._ref = ref;
    
    this.addLine = form_textarea_addLine;
    this.empty = form_textarea_empty;
    
    this.getLength = form_textarea_getLength;
    
    this.setRows = form_textarea_setRows;
    this.setCols = form_textarea_setCols;
}

/**
*   
*
*/
function form_textarea_addLine(line)
{
    if (this._ref.value.length && this._ref.value.substr(this._ref.value.length-2,2)!="\r\n") {
        this._ref.value += "\r\n";
    }
    this._ref.value += line;
}

/**
*   
*
*/
function form_textarea_empty()
{
    this._ref.value = "";
}

/**
*   get the length, which is the same as the number of elements
*
*/
function form_textarea_getLength()
{
    return this._ref.value.length;   
}


/**
*   
*
*/
function form_textarea_setRows(rows)
{
    this._ref.rows = rows;
}

/**
*   
*
*/
function form_textarea_setCols(cols)
{
    this._ref.cols = cols;
}
