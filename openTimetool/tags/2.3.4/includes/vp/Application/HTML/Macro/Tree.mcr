<!--
    $Log: Tree.mcr,v $
    Revision 1.3  2003/03/11 12:57:55  wk
    *** empty log message ***

    Revision 1.6  2002/11/19 19:53:49  wk
    - use middot as seperator now

    Revision 1.5  2002/08/21 17:11:53  wk
    - fixed bug in deprecated macro :-(

    Revision 1.4  2002/08/21 15:48:24  wk
    - use proper macro naming

    Revision 1.3  2002/08/19 19:59:07  wk
    - enable multiple preselected elements

    Revision 1.2  2002/07/12 08:18:55  wk
    - added selected element

    Revision 1.1  2002/07/05 17:59:12  wk
    - initial commit

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
                selected
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

