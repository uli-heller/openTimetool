<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Wolfram Kriesing <wolfram@kriesing.de>                      |
// +----------------------------------------------------------------------+
//
/**
*
*   $Log: TagLib.php,v $
*   Revision 1.3  2002/12/01 13:06:36  wk
*   *** empty log message ***
*
*   Revision 1.15  2002/11/10 11:39:06  mccain
*   - added filters for replacing SimpleTags which do the stuff in TagLib -syntax
*
*   Revision 1.14  2002/11/08 22:51:28  mccain
*   - removed 's' modifier in macro method, which is not needed anyway and caused problems with multiple appearances
*
*   Revision 1.13  2002/10/16 19:00:44  mccain
*   - include every macro only once, so i set a if-def kinda around it
*
*   Revision 1.12  2002/07/26 20:48:00  mccain
*   - made include work like include_once, corrected macro to work properly with it
*
*   Revision 1.11  2002/07/26 10:40:17  mccain
*   - search for a file to include also in the include path
*
*   Revision 1.10  2002/06/26 18:48:25  mccain
*   - remove unnecessary spaces
*
*   Revision 1.9  2002/06/21 20:52:53  mccain
*   - added macro filter
*
*   Revision 1.8  2002/06/10 18:43:39  mccain
*   - fix bug in applyHtmlEntities, was not greedy before
*
*   Revision 1.7  2002/05/21 23:03:33  mccain
*   - added a filter which invokes all pre filters
*      thanks to Alan Knowles' hint
*   - renamed the trimWords method to trimByWords and corrected the behaviour of trim, to work properly again (the $ sign was searched before, again)
*
*   Revision 1.6  2002/05/20 20:51:21  mccain
*   - added trimWords method
*
*   Revision 1.5  2002/05/13 12:14:26  mccain
*   - added tagLib filter for applyHtmlEntities
*
*   Revision 1.4  2002/03/04 19:05:15  mccain
*   - made files compatible to run on php4.1.1 with stricter php.ini settings
*
*   Revision 1.3  2002/02/07 22:45:55  mccain
*   - make the options stuff work
*
*   Revision 1.2  2002/02/07 22:03:46  mccain
*   - added informational comment
*
*   Revision 1.1.1.1  2002/02/07 21:52:23  mccain
*
*   ##### those are my local revisions, from before moving it to sourceforge :-) #####
*   ##### just kept for informational reasons, might be removed one day
*
*   Revision 1.10  2002/01/21 23:01:53  cain
*   - added license statement
*
*   Revision 1.9  2002/01/15 11:25:24  cain
*   - comment
*
*   Revision 1.8  2002/01/09 05:28:12  cain
*   - some phpdoc adjustment
*
*   Revision 1.7  2002/01/06 12:20:50  cain
*   - fixed bug, which let blocks only be used when they have the same indention before the copy tag
*
*   Revision 1.6  2001/12/21 14:52:20  cain
*   - make blocks useable in every template no matter if included via php or via tagLilb
*
*   Revision 1.5  2001/12/19 22:39:04  cain
*   - added block and include tag parsers
*
*   Revision 1.4  2001/12/19 14:51:50  cain
*   - added method includeFile
*   - make use of the delimiters
*
*   Revision 1.3  2001/12/18 15:55:38  cain
*   - added method trim
*   - finished 'repeat'
*
*   Revision 1.2  2001/12/18 00:12:56  cain
*   - comments
*
*   Revision 1.1  2001/12/15 14:03:07  cain
*   - this file will contain tagLib like filters
*
*
*
*/

require_once('SimpleTemplate/Options.php');

/**
*   this file is intended to realize stuff like this
*   - add custom tags, which are no PHP (but replaced by it)!!! therefore they go like this: {%...%} where { and } are the delimiters
*       {%repeat $x times%}, {%repeat $x times, loopname=$loopCounter%}, replace by a simple for loop, if
*       loopname is given use this as the loop varibale name
*
*       {%copy block x here%} this replaces a defined block which is somewhere in the file
*           {%block x%}
*       {%include directory/file.tpl %}  this might define different blocks, which can be copied by using the above tag {%copy ...%}
*
*       {%strip whitespaces%}
*       {%strip%}
*
*       {%trim $x after 20 characters and add '...'%}
*       {%trim $x 20 '...'%}
*
*       {%trim $x by words after 20 characters and add '...'%}
*       {%trim $x by words 20 '...'%}
*
*   @package    SimpleTemplate/Filter
*   @version    01/12/15
*/
class SimpleTemplate_Filter_TagLib extends SimpleTemplate_Options
{
// i need the method setOption, that's why i extend myPEAR_Common

    /**
    *   for passing values to the class, i.e. like the delimiters
    *   @access private
    *   @var    array   $options    the options for initializing the filter class
    */
    var $options = array(   'delimiter'     =>  array(),    // first value of the array is the begin delimiter, second the end delimiter
                            'templateDir'   =>  '' );       // we need the template dir for the include directive

// remove the constructor one day, i feel that passing the delimiters to this class makes it all somehow unclean
// but therefore we have to move addIfBeforeForeach too, since it depends on having the delimiters

    /**
    *   @var    array   all the files that get included
    */
    var $_includedFiles = array();

    /**
    *   @var    array   all the macros that are defined
    */
    var $_macros = array();


    /**
    *   actually i made a constructor only to pass the delimiters to this class
    *   at a definite point
    *
    *   @version    01/12/15
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      array   $options    need to be given, use the options from your tempalte class
    */
    function SimpleTemplate_Filter_TagLib($options=array())
    {
        if(sizeof($options))
            foreach( $options as $key=>$aOption )
                $this->setOption( $key , $aOption );
    }

    /**
    *   apply all filters available in this class
    *   thanks to hint from Alan Knowles
    *
    *   @version    02/05/22
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the actual input string, to which the filters will be applied
    *   @return     string  the resulting string
    */
    function allPrefilters( $input )
    {
        $input = $this->includeFile($input);
        $input = $this->block($input);
        // do block and include before other tags, so the other tags also work
        // when they were used in a block !!!
        $input = $this->macro($input);

        // do trim words before trim!! so trim doesnt catch the tag first :-)
        $input = $this->trimByWords($input);
        $input = $this->trim($input);
        $input = $this->repeat($input);

        $input = $this->applyHtmlEntites($input);

        $input = $this->loop($input);
        $input = $this->condition($input);
        $input = $this->end($input);

        return $input;
    }

    /**
    *   NOT IMPLEMENTED, AND I WONT
    *   removes spaces and new lines
    *   ACTUALLY i think this is unnecessary, simply use filters trimLines and optimizeHtml, this
    *   does everything, at least it works perfect for me
    *
    *   @version    01/12/15
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function strip( $input )
    {
        return $input;
    }

    /**
    *   {%repeat $x times%}, {%repeat $x times using $loopCounter%}, replace by a simple for loop, if
    *   a variable is given use this as the loop varibale name
    *
    *   @version    01/12/15
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function repeat( $input )
    {
        // find those repeats which dont have no variable that is given as the loop variable
        // we need to do this, since the next regExp needs this variable name, because
        // we can not use the $5 to check if it is given (down there in the second regExp)... bummer
        $counterName = '$_someUniqueVariableName';  // generate something here
        $input = preg_replace(  '/'.preg_quote($this->options['delimiter'][0]).
                                '%\s*repeat\s+([^\s%]+)([^\$]*)%'.preg_quote($this->options['delimiter'][1]).
                                '/',

                                //"PRE-REPEAT:<br>1='$1'<br>2='$2'<br>3='$3'<br>4='$4'<br>5='$5'<br>" , // for testing
                                $this->options['delimiter'][0].
                                "%repeat $1 $counterName%".
                                $this->options['delimiter'][1],

                                $input);

        $input = preg_replace(  '/\n(.*)'.          // save the indention in $1
                                preg_quote($this->options['delimiter'][0]).
                                '%\s*repeat\s+'.    // find begin delimiter repeat at least one space behind and variable spaces before
                                '([^\s]+)'.         // find everything until the next space, which is the count variable $2
                                '(([^\$%]*)?(\$[^\s]+)?)?'. // find the loop varibale name $5, a lot of stuff around it is optional (?)
                                                    // the variable name has to start with a $ and spaces are excluded, so we trim it too
                                '\s*%'.             // optional numbner of spaces before closing delimiter
                                preg_quote($this->options['delimiter'][1]).
                                '/',

                                "\n$1".$this->options['delimiter'][0].
                                "for($5=0;$5<$2;$5++)".
                                $this->options['delimiter'][1],

                                //"REPEAT:<br>1='$1'<br>2='$2'<br>3='$3'<br>4='$4'<br>5='$5'<br>6='$6'<br>" , // for testing
                                $input);  // replace unnecessary spaces, so the next regexp is shorter and easier

        return $input;
        /* TESTS

        { $xx->methodCall=7}
        { $x=1}
        { $x1=1}
        { $x2=1}
        { $x_y=1}
        { $variableName_howEver_Long_it_mig111htBe=1}
        { $x4=1}

        <!--{%repeat $x->methodCall($easyVar)%} this works too, but i am too lazy to declare a class here-->
        {%repeat $xx->methodCall%}
            repeat 1
        <br>
        {%repeat sizeof($x)%}
            repeat 2
        <br>
        {%repeat $x times%}
            repeat 3
        <br>
        {%repeat $x1 times $y1%}
            repeat 4
        <br>
        {%    repeat     $x2    times    $y2   %}
            repeat 5
        <br>
        {%repeat $x_y times using $y%}
            repeat 6
        <br>
        {%repeat $variableName_howEver_Long_it_mig111htBe times with $y3%}
            repeat 7
        <br>
        {%repeat sizeof($x4) times $y4%}
            repeat 8
        <br>
        */
    }

    /**
    *   trims strings after X characters and adds a given string, if given
    *   use as PRE filter
    *   @todo   the length can not be a variable yet, do this someday
    *
    *   tested with
    *   {$x1='What a long string'}<br>
    *   {$x2='I am here '}<br>
    *   {$that->fuck='He ho'}<br>
    *   <br><br>
    *   1. {%trim $x1 after 5 characters and add "JUST simple ..."%}<br>
    *   2. {%trim $x2 3 "REPLACE with this"%}<br>
    *   3. {%   trim    $that->fuck fucking off unitl it dies after no more than  200 characters ,kajdfa sdkjas dlkjas dfkjasdf lksjd fksjdf lksjdf lksjd flkj l reaplce with ""%}
    *   <br>
    *   4. {%trim $x2 3%}<br>
    *   5. {%trim $x2  after 3 letters %}<br>
    *   6. {%trim   $x2  3   %}<br>
    *   7. {%trim $x2 to the length of 3%}<br>
    *
    *   @version    01/12/18
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @param      string  this is an extra string which can be added behind trim, is used i.e. for "trim words"
    *   @return     string  the modified template
    */
    function trim( $input , $extra='' )
    {
        $exp =  $this->options['delimiter'][0].
                'echo ((strlen($1) > $2))?(substr($1,0,$2)."$4"):$1'.
                $this->options['delimiter'][1];
        if( $extra == 'by words' )
        {
            $exp =  $this->options['delimiter'][0].
                    'echo ((strlen($1) > $2))?(substr($1,0,(($2)-(strlen(strrchr(substr($1,0,$2),\' \')))))."$4"):$1'.
                    $this->options['delimiter'][1];

            $extra = '\s+by\s+words';
        }




        return preg_replace(    '/'.preg_quote($this->options['delimiter'][0]).
                                '%\s*trim\s+'.      // find at least one space behind and any number of spaces between % and trim
                                '([^\s]+)'.         // find all until the next space, that will be our variable name $1
                                $extra.
                                '[^\d]+'.           // find anything until a decimal number comes, at least one character
                                '(\d+)'.            // put the decimal number in $2
                                '(\s+.*"(.*)")?'.   // this is saucy, we only need the most inner pair of (),
                                                    // that will be our string we use to add at the end in case we trim it
                                                    // all those other () are only for making each block optional (?), esp. for test 5 to work
                                '\s*%'.preg_quote($this->options['delimiter'][1]).
                                                    // allow any kind of spaces before the end delimiter
                                '/i' ,              // search case insensitive

                                $exp,
                                //"TRIM:<br>1='$1'<br>2='$2'<br>3='$3'<br>4='$4'<br>5='$5'<br>" , // for testing

                                $input );
    }

    /**
    *   this trims strings but only after a space
    *   NOTE: be sure to put this filter before "trim"
    *
    *   @version    02/05/30
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @param      string  this is an extra string which can be added behind trim, is used i.e. for "trim words"
    *   @return     string  the modified template
    */
    function trimByWords( $input )
    {
        return $this->trim( $input , 'by words' );
    }

    /**
    *   {%include xxx.tagLib%}
    *
    *   @version    01/12/18
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function includeFile( $input )
    {
//print "<br>includeFile<br>";
//print_r($this->getOptions());
        $openDel = preg_quote($this->getOption('delimiter',0));
        $closeDel = preg_quote($this->getOption('delimiter',1));
        $_openDel = $this->getOption('delimiter',0);
        $_closeDel = $this->getOption('delimiter',1);

// FIXXXME discover all the functions that are used in the current file, so only those functions are pasted inside the code!!! //"
        if( preg_match_all( '/'.$openDel.'%\s*include\s+(.+)\s*%'.$closeDel.'/U' , $input , $includes ) )
        {
//print_r($includes);
            if(sizeof($includes[1]))
            foreach( $includes[1] as $index=>$aInclude )
            {
                // get the relative path to templateDir or absolute if given
// FIXXME unix specific!!!!
                if( $aInclude[0] != '/' )           // add trailing slash if missing
                    $_aInclude = '/'.$aInclude;
                $fileToInclude = $this->options['templateDir'].$_aInclude;

                // do only include a file that really exists, otherwise the tag also stays there, so the programmer removes it
                // do also search for the file in the include path, but as the second option only!
                if($content = @file($fileToInclude))
                    $contentFile = $fileToInclude;
                else
                    if( $content = @file($aInclude,true))
                        $contentFile = $aInclude;

                if( $content )
                {
                    // do only include the files content if we didnt include it yet
                    // just like 'include_once' only that it does it by default :-)
                    // this only works if we are only using one instance of the filter, which is not the case
                    // since every file might have different options, i.e. delimiters, so i changed 
                    // it to make a new instance for every file, which means this has almost no effect
                    if( !in_array($contentFile,$this->_includedFiles) )
                    {
//print "including: $contentFile<br>";
                        $this->_includedFiles[] = $contentFile;
                        // read the file
                        $fileContent = implode("\n",$content);
                         
                        // put an if around the entire macro file, so it wont even be parsed
                        // if it is already once in the code, this takes care of not multiple
                        // times defining functions (macros in this case)
                        // it also works if you compile multiple files with different instances of this filter
                        // since php checks the variable $___SimpleTemplate_TagLib_includedFile given here
                        $fileContent =  "$_openDel if(!\$___SimpleTemplate_TagLib_includedFile['$fileToInclude'])\\\{ $_closeDel".
                                        $fileContent.
                                        $_openDel." \$___SimpleTemplate_TagLib_includedFile['$fileToInclude']=true;\\\}".$_closeDel;
                    }
                    else
                    {
//print "already included: $contentFile<br>";
                        $fileContent = '';
                    }

                    // replace the string from $includes[0] with the file
                    $input = preg_replace( '/'.preg_quote($includes[0][$index],'/').'/' , $fileContent , $input );
                }
            }

        }
        return $input;
    }

    /**
    *   parses {%block xxx%} tags
    *   DEPRECATED, use macro instead!!!!
    *
    *   @version    01/12/18
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function block( $input )
    {
        // do somehow add the block-end tag first, use autoBraces, but needs modification first
        // for now you need to write the {%/block%} end tag
        $regExpToFindBlocks = '/{%\s*block\s+(.+)\s*%}.*{%\/\s*block\s*%}/Us';
        // retreive the block names only, since a block might contain another block
        // by not getting the block content here we can also put blocks in blocks...
        preg_match_all( $regExpToFindBlocks , $input , $blocks );

        if( sizeof($blocks[0]))
        {
            foreach( $blocks[1] as $index=>$aBlockName )
            {
                // we trim the block name here, so we only get the real block name
                // and we dont have to add this 'no spaces' in the regExp
                $realBlockName = trim($aBlockName);

                // !!!
                // get the block content now, because it might containes another copy-tag !!!
                // which was replaced by the according block, write it in $blockContent
                $blockRegExp = '/{%\s*block\s+'.$aBlockName.'\s*%}(.*){%\/\s*block\s*%}/Us';
                preg_match( $blockRegExp , $input , $blockContent );
                // and replace the block definitions with nothing
                $input = preg_replace( $blockRegExp , '' , $input );

                $this->blocks[$realBlockName] = $blockContent[1];

                // we need to get the number of spaces before each '{%copy' to maintain indention
                preg_match_all(  '/\n(\s*){%\s*copy\s+block\s+'.$realBlockName.'.*%}/' , $input , $copyTags );

                // now we need to go thru every '{%copy' tag that has to be replaced and get its indention
                // to keep it in front, this adds the indention that is given in the block too !!!

                if(sizeof($copyTags[0]))
                foreach( $copyTags[0] as $cpIndex=>$aCopyTag )
                {
                    $indentedBlockContent = preg_replace( '/\n/' , "\n".$copyTags[1][$cpIndex] , $blockContent[1] );
                    $input = preg_replace( '/'.$copyTags[0][$cpIndex].'/' , $indentedBlockContent , $input );
                }
            }
        }

        // go thru all blocks to replace copy-tags that are still left
        // in the first foreach we had only replaced copy tags which use blocks that
        // are defined in the same file
        if( isset($this->blocks) && sizeof($this->blocks) )
        foreach( $this->blocks as $realBlockName=>$blockContent )
        {
            // we need to get the number of spaces before each '{%copy' to maintain indention
            preg_match_all(  '/\n(\s*){%\s*copy\s+block\s+'.$realBlockName.'.*%}/' , $input , $copyTags );
            // now we need to go thru every '{%copy' tag that has to be replaced and get its indention
            // to keep it in front, this adds the indention that is given in the block too !!!
            if(sizeof($copyTags[0]))
            foreach( $copyTags[0] as $cpIndex=>$aCopyTag )
            {
                $indentedBlockContent = preg_replace( '/\n/' , "\n".$copyTags[1][$cpIndex] , $blockContent );
                $input = preg_replace( '/'.$copyTags[0][$cpIndex].'/' , $indentedBlockContent , $input );
            }
        }

        // we have replaced all that was to replace, remove {%copy-tags
        // that were not replaced by anything
        $input = preg_replace(  '/\n(\s*){%\s*copy\s+block\s+.*%}/' , '' , $input );

        return $input;

        /*
        tested with

        {% block x %}<br>
            hi i am your first block
            even a line break i
            contain
        {%/block %}

        {%block this_block%}
            this is this block INSERTED<br>
        {%/block %}

        1.<br>
        {%copy block this_block here %}
        <br><br>
        2.<br>
        {%    copy     block     x      here %}
        <br><br>
        3.<br>
        {%   copy     block    this_block%}
        <br><br>
        4.<br>
        {%copy block     this_block   %}
        <br><br>

        */
    }

    /**
    *   applies htmlentites to all the '{%$xxxx%}' strings, so the
    *   printout will always be valid html
    *
    *   @version    02/05/13
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified input
    */
    function applyHtmlEntites($input)
    {
        $input = preg_replace(  '/'.preg_quote($this->options['delimiter'][0]).
                                '%\$(.*)%'.preg_quote($this->options['delimiter'][1]).
                                '/U' ,
                                '<?=htmlentities($$1)?>' ,
                                $input );
        return $input;
    }

    /**
    *
    *
    *   @version    02/06/21
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified input"
    */
    function macro( $input )
    {
        $openDel = preg_quote($this->getOption('delimiter',0));
        $closeDel = preg_quote($this->getOption('delimiter',1));
        $_openDel = $this->getOption('delimiter',0);
        $_closeDel = $this->getOption('delimiter',1);

        // replace 'macro' with 'function'
        $regExp = '/'.$openDel.'%\s*(macro|function)\s+(.*)%'.$closeDel.'/Usi';
        $input = preg_replace( $regExp , $_openDel.'function $2'.$_closeDel , $input );

        // replace {%macroName()%} with {macroName()}
        $regExp = '/'.$openDel.'\s*function\s+(.*)\(.*\)\s*'.$closeDel.'/Usi';
        preg_match_all( $regExp , $input , $macroCalls );

        // merge the macros found now with the macros already found
        // do this because we might have some macros which are not defined in the current file
        // but we assume, that all the files that are being processed by the same instance of this filter
        // are merged to one big php-file, so the macro will be defined and available!
        $this->_macros = array_unique(array_merge($this->_macros,$macroCalls[1]));

        if( sizeof($this->_macros) )
        foreach( $this->_macros as $aMacroCall )
        {
            $regExp = '/'.$openDel.'%\s*'.trim($aMacroCall).'\s*(\(.*\))%'.$closeDel.'/Ui';
            $input = preg_replace( $regExp , $_openDel.$aMacroCall.'$1'.$_closeDel , $input );
        }

        return $input;
    }

    function loop( $input )
    {
        $input = $this->_replaceName( $input , 'while' );
        $input = $this->_replaceName( $input , 'for' );
        return $this->_replaceName( $input , 'foreach' );
    }

    function condition( $input )
    {
        return $this->_replaceName( $input , 'if' );
    }

    function _replaceName( $input , $name )
    {
        $openBrace = '{';
        if( $this->getOption('delimiter',0) == '{' )
            $openBrace = '\{';

        $input = preg_replace(  '/'.preg_quote($this->getOption('delimiter',0)).
                                '%\s*'.$name.'\s*\((.*)\)\s*%'.preg_quote($this->getOption('delimiter',1)).
                                '/Ui' ,
                                "<?php $name($1) $openBrace ?>" ,
                                $input );
        return $input;
    }

    function end( $input )
    {
        $closeBrace = '}';
        if( $this->getOption('delimiter',0) == '{' )
            $closeBrace = '\}';

        $input = preg_replace(  '/'.preg_quote($this->getOption('delimiter',0)).
                                '%\s*end\s*%'.preg_quote($this->getOption('delimiter',1)).
                                '/Umi' ,
                                "<?php $closeBrace ?>" ,
                                $input );
        return $input;
    }
}

?>
