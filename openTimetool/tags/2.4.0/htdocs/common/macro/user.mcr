<!--

$Id$

-->

<!--
    @param   array   all the users to show
    @param   int     the selected user's id
-->
{%macro user_asOptions( $users , $selected=0 , $valueName='id' )%}
    {foreach( $users as $aUser )}
        <option value="{$aUser[$valueName]}"
            {if($aUser['id']==$selected || ( is_array($selected) && in_array($aUser['id'],$selected) ))}
                selected="selected"
        >
        {$aUser['surname']}, {$aUser['name']}
        </option>

<!--
    @deprecated
    @param   array   all the users to show
    @param   int     the selected user's id
-->
{%macro usersAsOptions( $users , $selected=0 , $valueName='id' )%}
    {%user_asOptions( $users , $selected , $valueName )%}
