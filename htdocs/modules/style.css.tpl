/*
    $Log: style.css.tpl,v $
    Revision 1.25.2.3  2003/03/28 10:18:53  wk
    - add noStyle:hover

    Revision 1.25.2.2  2003/03/20 14:39:44  wk
    - style the treeMenuMultiText so it looks like a button

    Revision 1.25.2.1  2003/03/19 19:39:53  wk
    - added class for treeMenu

    Revision 1.25  2003/03/04 19:19:14  wk
    - add a.button for multiselect in tree view

    Revision 1.24  2003/02/17 19:17:44  wk
    - add overlay class

    Revision 1.23  2003/02/13 16:20:09  wk
    - treemenu design

    Revision 1.22  2003/02/10 19:28:27  wk
    - added treeMenu classes

    Revision 1.21  2003/01/13 18:13:33  wk
    - use HTML_Template_Xipe tags in XML

    Revision 1.20  2002/12/09 12:22:15  wk
    - changed the color of the button-border so it is also visible in IE

    Revision 1.19  2002/12/05 14:20:29  wk
    - set color for required class

    Revision 1.18  2002/11/30 18:40:19  wk
    - added layout* classes

    Revision 1.17  2002/11/29 14:45:00  jv
    - add paddings and class for buutons  -

    Revision 1.16  2002/11/28 10:31:07  wk
    - added status line

    Revision 1.15  2002/11/19 20:02:56  wk
    - remove margin from message box

    Revision 1.14  2002/11/11 18:02:25  wk
    - added message box classes

    Revision 1.13  2002/11/07 11:43:43  wk
    - commented out not properly working stuff

    Revision 1.12  2002/10/31 17:49:26  wk
    - highlight outlined table rows on mouseover

    Revision 1.11  2002/10/28 16:24:37  wk
    - use $styleSheet -class vars

    Revision 1.10  2002/10/24 14:15:59  wk
    - moved definitions to php file
    - added vp...-class

    Revision 1.9  2002/10/22 18:21:15  wk
    - removed font size, since we also use it in the project to highlight stuff - fix this

    Revision 1.8  2002/10/22 14:45:03  wk
    - use new css-stuff

    Revision 1.7  2002/08/30 18:45:45  wk
    - added class selected which is not in use yet

    Revision 1.6  2002/08/29 13:35:48  wk
    - made the h1 smaller

    Revision 1.5  2002/08/20 09:04:18  wk
    - added class smooth

    Revision 1.4  2002/08/05 18:55:38  wk
    *** empty log message ***

    Revision 1.3  2002/07/30 20:24:52  wk
    - added class outline

    Revision 1.2  2002/07/24 17:11:33  wk
    - added some comments

    Revision 1.1.1.1  2002/07/22 09:37:37  wk


*/

<HTML_Template_Xipe>
    <options>
        <delimiter begin="[[" end="]]"/>
    </options>
</HTML_Template_Xipe>


/*
*   define the font only ONCE for all elements that are relevant
*   this works like kind of inheritance ...
*/
body, td, input, textarea, font, a, b, i
{
    font-family : verdana, geneva, arial;
    font-size: [[$styleSheet->fontSize]];
}

body
{
    background-color: [[$styleSheet->mainColor]];
}

/*
*   table stuff
*/
table, td
{
    background-color: white;
}

td
{
    padding: 1 3 1 3;
} 

.layout, .layoutWithBgColor
{
    padding: 0px;
    margin: 0px;
}

.layoutWithBgColor
{
    background-color: [[$styleSheet->lighterColor]];
}

table.outline {
    background-color: [[$styleSheet->bgHighlightColor]];
}

/**
*   this class is for outlined divs that are positioned dynamically
*/
.outlineOverlay {
    border: 1px [[$styleSheet->bgHighlightColor1]] outset;
    background-color: [[$styleSheet->bgHighlightColor1]];
}

.outlineOverlay td,.outlineOverlay table {
    background-color: [[$styleSheet->bgHighlightColor]];
}


/* mozilla has a problem unfortunately, drop downs dont open, or simply close on mousemove if they are opened
table.outline tr:hover td
{
    background-color: [[$styleSheet->bgHighlightColor1]];
}
*/

.navi
{
    background-color: [[$styleSheet->lighterColor]];
}

/* this is a class for rows, which should be visible but kind of commented out
   used i.e. in the project-edit mask, to mark those projects, which have already expired
*/
.disabled
{
    color: lightgrey;
}

.header, th
{
    background-color: [[$styleSheet->bgHighlightColor]];
    padding: 1 3 1 3;
}

th
{
    font-size:  11px;
    font-weight: bold;
    text-align:left;
}

/* currently only used to highlight the currently selected time-entry for editing */
.backgroundHighlight
{
    background-color: [[$styleSheet->bgHighlightColor]];
}
/*
*   links
*/
a
{
    color: [[$styleSheet->mainColor]];
}

a.navi, a.naviSelected
{
    color: [[$styleSheet->fontColor]];
    text-decoration : none;
}

a:hover
{
    text-decoration : underline;
}

font
{
    color : [[$styleSheet->fontColor]];
}

.highlight
{
    color: [[$styleSheet->mainColor]];
    font-weight:bold;
}

li
{
    color: #8a8a8a;
}

h1
{
    font-size : 18px;
    font-weight : bold;
}

/* mostly used to make a link not have any style 
    '.noStyle:hover'  doesnt work :-(
*/
.noStyle, a.noStyle:hover
{
    background-color: transparent;
    color: [[$styleSheet->fontColor]];
    text-decoration: none;
}
        
.vpRedDotColor
{
    color: [[$styleSheet->vpRedDotColor]];
}

/*
*   used for the application messages and errors
*/
.warning{
    background-color: yellow;
    color:red;
    font-weight : bold;
}

.success{
    background-color: white;
    color:red;
    font-weight : bold;
}
         
table.message, th.message
{
    background-color: [[$styleSheet->darkerColor]];
    color: black;
    font-weight: bold;
}

td.message
{
    padding: 5px;
    margin: 0px;
}

.statusLine
{
    background-color: [[$styleSheet->lighterColor]];
    border: white 1px inset;
    padding: 0 5 0 5;
}


/* FIXXME this style needs to be used to highlight a selected row !!! */
.selected
{
    background-color: yellow;
}

img.button
{
    padding: 0 5 0 5;
    border: 0px;
}


.pageHeader
{
    font-weight: bold;
    color: white;
    background-color: [[$styleSheet->darkerColor]];
    border: white 1px outset;
    padding: 1 10 1 10;
}

.pageHeader:first-letter
{
    text-transform: uppercase;
}






/**
*
*   NAVIGATION
*
*   designing the navigation
*   each navi group is a table of its own
*
*/
table.navi
{
/*    border: lightgrey 1px solid;*/
}

table.navi th           /* each navi item has its own header */
{                       
    font-size: [[$styleSheet->fontSize]];
    font-weight:bold;
    background-color: [[$styleSheet->lighterColor]];
    text-align:left;
    padding: 0 3 0 3;
}

/*table.navi th:first-letter   make the first letter beautiful
{
    color: red;
    font-size: 120%;
    text-shadow: 3px 3px 5px blue;
    padding-right: 1px;
}                                 
*/
                     
a.navi
{
    background-color: [[$styleSheet->lighterColor]];
}

.naviTdWithLink           /* make some space around each td */
{
    background-color: [[$styleSheet->lighterColor]];
    padding: 1 10 1 10;
}

/* define the style for the td when the user goes over it with the mouse */
.naviTdWithLink:hover
{
    align: center;
    background-color: [[$styleSheet->darkerColor]];
/*    border: 1px lightgrey outset;
    padding: 2 2 2 4;     /* take back 1px from the padding since the border takes up 1px */
}

/* what happens if the user goes on the link which is in the td */
.naviTdWithLink:hover a
{
    border: 1px lightgrey outset;
    padding: 0;               /* this padding is one pixel less than the one for the links without hover, because the border adds 1px */
    text-decoration: none;
    background-color: [[$styleSheet->darkerColor]];
}

.naviTdWithLink a
{
    padding: 1 1 1 0;   /* pad with 0 on the left side, so the td's without links inside have the same padding */
}
/* change when going over the link */
.naviTdWithLink a:hover
{
    color: white;           /* just the color for the font when the mouse is on the link */
}

/* change when clicking the link */
.naviTdWithLink a:active
{
    border-style: inset;
}

.naviTdWithLink[selected], .naviTdWithLink[selected] a
{
    background-color: [[$styleSheet->darkerColor]];
}




/*
*
*   styling the input fields
*
*/

/* IE _always_ has Mozilla/4.0 (for any 4.x) in fornt, but Netscape i.e. Mozilla/4.79  */
[[ if(preg_match('/netscape\s*4\.|mozilla.4.[1-9]/i',$_SERVER['HTTP_USER_AGENT']) == false) ]]
    /* Netscape 4.7 screws up with this big time!!! */
    input, textarea, select
    {
        /*background-color: [[$styleSheet->lighterColor]];*/
        background-color: [[$styleSheet->bgHighlightColor1]];
        border: [[$styleSheet->mainColor]] 1px solid;
        margin: 3px;
    }

/* the class button is only for IE, because it doesn't work with the attribute selectors :-( */
input.button
[[ if(!preg_match('/msie/i',$_SERVER['HTTP_USER_AGENT'])) ]]
    ,input[type=submit], input[type=button]
{
    border: [[$styleSheet->lighterColor]] 1px outset;
}
/* this does not work in IE */
input.button:hover, input[type=submit]:hover, input[type=button]:hover
{
    color: [[$styleSheet->mainColor]];
    font-weight: normal;
}

a.button {
    color: [[$styleSheet->fontColor]];
    background-color: [[$styleSheet->bgHighlightColor1]];
    border: [[$styleSheet->lighterColor]] 1px outset;
    padding: 2px;
    cursor: pointer;
}

a.button:hover {
    color: [[$styleSheet->mainColor]];
    text-decoration: none;
}

#boldbutton {
	font-weight:bold;
}


.required
{
    border-color:[[$styleSheet->alertColor]];
}

/* for special styling of the warning for the input fields, if desired */
input.warning, select.warning
{
    border: red 2px dashed;
    background-color: [[$styleSheet->lighterColor]];
    color: [[$styleSheet->fontColor]];
    font-weight: normal;
}






/**
*
*   treeMenu
*
*/

.treeMenu, .treeMenuNotSelectable {          /* make some space around each td */
    /*dont use a bg-color, so we can use it in the div and directly on the page
    background-color: [[$styleSheet->bgHighlightColor1]]; */
    padding: 0px;
}

.treeMenuNotSelectable {
    color: [[$styleSheet->mainColor]];
    cursor: text;
}

/* define the style for the td when the user goes over it with the mouse */
.treeMenu:hover
{
    background-color: [[$styleSheet->invertedFontColor]];
    color: [[$styleSheet->mainColor]];
}

/* what happens if the user goes on the link which is in the td */
.treeMenu:hover span {
    cursor: pointer;
    text-decoration: none;
    background-color: [[$styleSheet->invertedFontColor]];
    color: [[$styleSheet->mainColor]];
}

.treeMenu span:hover {
    cursor: pointer;
    background-color: [[$styleSheet->mainColor]];
    color: [[$styleSheet->invertedFontColor]];  /* just the color for the font when the mouse is on the link */
}

.treeMenu[selected], .treeMenu[selected] span, .treeMenuSelected {
    background-color: [[$styleSheet->invertedFontColor]];
}

.treeMenuDisabled {
    color:  [[$styleSheet->darkerColor]];
}

/**
*   the layer that is used to show the projectTree
*/
.treeMenuDiv {
    background-color: [[$styleSheet->bgHighlightColor1]];
    position:absolute;
    left:0px;
    top:0px;
    visibility:hidden;
    border:1px [[$styleSheet->mainColor]] outset;
    padding: 10px;
/*    overflow:auto; this becomes ugly with the scroll bars :-( use some js, to prevent the horizontal scroll bar */
}

/**
*
*   .treeMenuMultiText
*   is for formatting the the projects which are shown for the
*   multi select
*
*/
.treeMenuMultiText {
    border:1px [[$styleSheet->mainColor]] outset;
    background-color: [[$styleSheet->bgHighlightColor1]];
    padding: 2px;
    margin: 2px;
    cursor: default;
}

/* this doesnt seem to work, not even in Mozilla without the "div" in front */
div.treeMenuMultiText:active {
    border-style: inset;    /*doesnt work, font-size or smthg else does work, but border not :-( */
}

div.treeMenuMultiText:hover {
    color: [[$styleSheet->mainColor]];
}

