/*
    $Log: popcalendar.css.tpl,v $
    Revision 1.3  2003/02/10 16:00:50  wk
    - just some changes in the color

    Revision 1.2  2003/01/13 18:09:25  wk
    - use HTML_Template_Xipe tags in XML
    - some cleaning up

    Revision 1.1  2002/10/28 16:22:55  wk
    - initial commit

*/

<HTML_Template_Xipe>
    <options>
        <delimiter begin="[[" end="]]"/>
    </options>
</HTML_Template_Xipe>


/* Default attributes of table container for entire calendar */
.table-style {  
    padding: 4px;
    border-width: 1px;
    border-style: outset;
    border-color: [[$styleSheet->lighterColor]];
    background-color: [[$styleSheet->bgHighlightColor1]];
}

/* Default attributes of DIV containing table container for entire calendar.
 * You probably don't want to alter this style.
 */
.div-style {
    z-index: +999;
    position: absolute;
    visibility: hidden;
}

/* Default attributes used in calendar title (month and year columns).*/
.title-style {
    padding: 2px;
    background-color: [[$styleSheet->bgHighlightColor1]];
}

/* Default attributes used in calendar title background.*/
.title-background-style {
    background-color: [[$styleSheet->bgHighlightColor1]];
}

/* Normal appearance of controls in calendar title. */
/* Note: The right, left and down icons are images, which must be edited if you need to change them. */
.title-control-normal-style {
    border-style: solid;
    border-width: 1;
    border-color: [[$styleSheet->darkerColor]];
    cursor: pointer;
}

/* Moused-over (selected) appearance of controls in calendar title. */
.title-control-select-style {
	border-style: solid;
	border-width: 1;
	border-color: [[$styleSheet->mainColor]];
	cursor: pointer;
}

/* Default attributes of drop down lists (month and year). */
.dropdown-style {
	border-width: 1px;
	border-style: outset;
	border-color: [[$styleSheet->lighterColor]];
	background-color: [[$styleSheet->lighterColor]];
    color: [[$styleSheet->fontColor]];
}

/* Default attributes selected (mouse-over) item in drop down lists (month and year). */
.dropdown-select-style {
	background-color: [[$styleSheet->mainColor]];
    color: [[$styleSheet->invertedFontColor]];
}

/* Default attributes unselected (mouse-off) item in drop down lists (month and year). */
.dropdown-normal-style {
	background-color: [[$styleSheet->bgHighlightColor1]];
}

/* Default attributes of calendar body (weekday titles and numbers). */
.body-style {
    padding: 5px;
    background-color: [[$styleSheet->bgHighlightColor1]];
    font-size: 10px;
}

/* Attributes of current day in calendar body. */
.current-day-style {
	color: [[$styleSheet->mainColor]];
	font-weight: bold;
	text-decoration: none;
}

/* Attributes of end-of-week days (Sundays) in calendar body. */
.end-of-weekday-style {
	color: [[$styleSheet->darkerColor]];
	text-decoration: none;
}

/* Attributes of all other days in calendar body. */
.normal-day-style {
	color: black;
	text-decoration: none;
}

/* Attributes of border around selected day in calendar body. */
.selected-day-style {
	border-style: solid;
	border-width: 1px; 
	border-color: #a0a0a0;
}

/* Default attributes of designated holidays. */
.holiday-style {
    background-color: [[$styleSheet->mainColor]];
    color: [[$styleSheet->invertedFontColor]];
}

/* Attributes of today display at bottom on calendar */
.today-style {
	padding: 0px;
	color: black;
    background-color: [[$styleSheet->bgHighlightColor1]];
	text-align: center;
	text-decoration: none;
}

/* Attributes of week number division (divider.gif) */
.weeknumber-div-style {
	background-color: #d0d0d0; 
	padding: 0px;
}
