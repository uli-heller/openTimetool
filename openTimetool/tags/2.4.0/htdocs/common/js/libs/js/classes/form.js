//
//  $Log: form.js,v $
//  Revision 1.1.2.1  2003/11/09 14:40:54  wk
//  *** empty log message ***
//
//  Revision 1.1  2003/03/07 03:50:22  cain
//  - initial commit
//
//

/**
*   this is the constructor
*
*   @version    20/02/2003
*   @author     Wolfram Kriesing <wolfram@visionp.de>
*   @param      object  the form DOM-reference
*/
function class_form(formObject)
{
    // methods
    this.select2select = form_select2select;
    this.selectAll = form_selectAll;
    
    // properties
    this._form = formObject;
}

/**
*   this is the constructor
*
*   @version    20/02/2003
*   @author     Wolfram Kriesing <wolfram@visionp.de>
*   @param      object  the source select box
*   @param      object  the destination select box
*   @param      boolean shall the value be remove from the source box?
*/
function form_select2select(fromBox,toBox,doRemove) 
{
    if (doRemove==null) {
        doRemove = false;
    }

    if (this._form) {
        from = this._form[fromBox];
        to = this._form[toBox];
    } else {
        from = fromBox;
        to = toBox;
    }
    
    fromLen = from.length ;
    for ( i=0; i<fromLen ; i++) {
        if (from.options[i].selected == true ) {
            toLen = to.length;
            to.options[toLen] = new Option(from.options[i].text);
            to.options[toLen].value = from.options[i].value;
        }
    }

    if (doRemove) {
        for ( i = (fromLen -1); i>=0; i--) {
            if (from.options[i].selected == true ) {
                from.options[i] = null;
            }
        }
    }
}

function form_selectAll(selectBox)
{
    if (this._form) {
        selectBox = this._form[selectBox];
    }
    
    selectBoxLen = selectBox.length ;
    for ( i=0; i<selectBoxLen ; i++) {
        selectBox.options[i].selected = true;
    }    
}

