<!--

$Id$

-->


<!--
*   this macro prints all the Tree items passed to it
*
*   @version    2002/06/04
*   @access     public
*   @param      array   this is the result from calling Memory_Tree->getNode()
*   @param      boolean shows the root dir by default,
*                       NOTE: be careful not to pass the first parameter by reference
*                       if you set this para to false, since the first element will
*                       be removed!!!
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
-->
{%macro Tree_asOptions($elements,$selected=0,$showRoot=true)%}
    {if($showRoot==false)}
        {array_shift($elements)}
    {foreach( $elements as $aElement )}
        <option value="{$aElement['id']}"
            {if( $selected && ($selected==$aElement['id'] || ( is_array($selected) && in_array($aElement['id'],$selected) )))}
                selected="selected"
        >
        {%repeat $aElement['level'] times%}
            &middot;
        {$aElement['name']}
        </option>

<!--
    @deprecated
-->
{%macro treeAsOptions($elements,$selected=0,$showRoot=true)%}
    {%Tree_asOptions($elements,$selected,$showRoot)%}
