/*

$Id$

*/

<HTML_Template_Xipe>
    <options>
        <delimiter begin="[[" end="]]"/>
    </options>
</HTML_Template_Xipe>


html
{
    overflow-y: scroll;
}

/*
*   define the font only ONCE for all elements that are relevant
*   this works like kind of inheritance ...
*/
body, td, input, textarea, font, a, b, i, select
{
    font-family: verdana, geneva, arial;
    font-size: [[$styleSheet->fontSize]];
}

body
{
    background-color: [[$styleSheet->mainColor]];
    margin: 20px;
    padding: 0;
}

body.projectMember nobr.level1 > span
{
    padding-left: 5px;
}
body.projectMember nobr.level1 > span > *
{
    vertical-align: middle;
}
body.projectMember nobr.level2 > span
{
    display: inline-block;
    padding-left: 5px;
    white-space: normal;
    width: 90%;
}

/*
*   table stuff
*/
table, td, div.table, div.td
{
    background-color: white;
}

td, div.td
{
    padding: 1px 3px;
} 

.layout, .layoutWithBgColor
{
    padding: 0;
    margin: 0;
}

.layoutWithBgColor, div.td.layoutWithBgColor
{
    background-color: [[$styleSheet->lighterColor]];
}

table.outline
{
    background-color: [[$styleSheet->bgHighlightColor]];
}

table.poutline
{
    margin-left: -7px;  /* SX Correct the alignment of filter buttons */
}


/**
*   this class is for outlined divs that are positioned dynamically
*/
.outlineOverlay
{
    border: 1px [[$styleSheet->bgHighlightColor1]] outset;
    background-color: [[$styleSheet->bgHighlightColor1]];
}

.outlineOverlay td,.outlineOverlay table
{
    background-color: [[$styleSheet->bgHighlightColor]];
}


table.outline > tbody tr:hover td
{
    background-color: [[$styleSheet->bgHighlightColor1]];
}


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

.header, th, div.th
{
    background-color: [[$styleSheet->bgHighlightColor]];
    padding: 1px 3px;
}

th, div.th
{
    font-size: [[$styleSheet->fontSize]];
    font-weight: bold;
    text-align: left;
}

/* currently only used to highlight the currently selected time-entry for editing */
.backgroundHighlight
{
    background-color: [[$styleSheet->bgHighlightColor]];
}


/* table => div replacement */
div.table
{
    display: table;
/*    border-collapse: collapse;*/
}
div.tr
{
    display: table-row;
}
div.td, div.th
{
    display: table-cell;
    text-align: center;
    vertical-align: middle;
}


/*
*   links
*/
a
{
    color: [[$styleSheet->mainColor]];
    text-decoration: none;
}

a.navi, a.naviSelected
{
    color: [[$styleSheet->fontColor]];
    text-decoration: none;
}

a:hover
{
    text-decoration: underline;
}

span, font
{
    color: [[$styleSheet->fontColor]];
}

.highlight
{
    color: [[$styleSheet->mainColor]];
    font-weight: bold;
}

li
{
    color: #8a8a8a;
}

h1
{
    font-size: 18px;
    font-weight: bold;
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
.warning
{
    background-color: yellow;
    color: red;
    font-weight: bold;
}

.success
{
    background-color: white;
    color: red;
    font-weight: bold;
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
    margin: 0;
}

div.table.statusLine
{
    margin: 5px;
    width: 99%;
}
div.table.statusLine > div.tr > div.td
{
    background-color: [[$styleSheet->lighterColor]];
    border: white 1px inset;
    padding: 0 5px;
}
div.table.statusLine.head > div.tr > div.td
{
    white-space: nowrap;
}


/* FIXXME this style needs to be used to highlight a selected row !!! */
.selected
{
    background-color: yellow;
}

img
{
    border: 0;
}
img.button
{
    padding: 0 5px;
}


.pageHeader
{
    font-weight: bold;
    color: white;
    background-color: [[$styleSheet->darkerColor]];
    border: white 1px outset;
    padding: 1px 10px;
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
    font-weight: bold;
    background-color: [[$styleSheet->lighterColor]];
    text-align: left;
    padding: 0 3px;
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
    padding: 1px 10px;
}

/* define the style for the td when the user goes over it with the mouse */
.naviTdWithLink:hover
{
    align: center;
    background-color: [[$styleSheet->darkerColor]];
/*    border: 1px lightgrey outset;
    padding: 2px 2px 2px 4px;     /* take back 1px from the padding since the border takes up 1px */
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
    padding: 1px 1px 1px 0;   /* pad with 0 on the left side, so the td's without links inside have the same padding */
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

a.button
{
    color: [[$styleSheet->fontColor]];
    background-color: [[$styleSheet->bgHighlightColor1]];
    border: [[$styleSheet->lighterColor]] 1px outset;
    padding: 2px;
    cursor: pointer;
}

a.button:hover
{
    color: [[$styleSheet->mainColor]];
    text-decoration: none;
}

#boldbutton
{
    font-weight: bold;
}


.required
{
    border-color: [[$styleSheet->alertColor]];
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

.treeMenu, .treeMenuNotSelectable
{   /* make some space around each td */
    /*dont use a bg-color, so we can use it in the div and directly on the page
    background-color: [[$styleSheet->bgHighlightColor1]]; */
    padding: 0;
}

.treeMenuNotSelectable
{
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
.treeMenu:hover span
{
    cursor: pointer;
    text-decoration: none;
    background-color: [[$styleSheet->invertedFontColor]];
    color: [[$styleSheet->mainColor]];
}

.treeMenu span:hover
{
    cursor: pointer;
    background-color: [[$styleSheet->mainColor]];
    color: [[$styleSheet->invertedFontColor]];  /* just the color for the font when the mouse is on the link */
}

.treeMenu[selected], .treeMenu[selected] span, .treeMenuSelected
{
    background-color: [[$styleSheet->invertedFontColor]];
}

.treeMenuDisabled
{
    color: [[$styleSheet->darkerColor]];
}

/**
*   the layer that is used to show the projectTree
*/
.treeMenuDiv
{
    background-color: [[$styleSheet->bgHighlightColor1]];
    position: absolute;
    left: 0;
    top: 0;
    visibility: hidden;
    border: 1px [[$styleSheet->mainColor]] outset;
    padding: 10px;
/*    overflow: auto; this becomes ugly with the scroll bars :-( use some js, to prevent the horizontal scroll bar */
}

/**
*
*   .treeMenuMultiText
*   is for formatting the the projects which are shown for the
*   multi select
*
*/
.treeMenuMultiText
{
    border: 1px [[$styleSheet->mainColor]] outset;
    background-color: [[$styleSheet->bgHighlightColor1]];
    padding: 2px;
    margin: 2px;
    cursor: default;
}

/* this doesnt seem to work, not even in Mozilla without the "div" in front */
div.treeMenuMultiText:active
{
    border-style: inset;    /*doesnt work, font-size or smthg else does work, but border not :-( */
}

div.treeMenuMultiText:hover
{
    color: [[$styleSheet->mainColor]];
}
