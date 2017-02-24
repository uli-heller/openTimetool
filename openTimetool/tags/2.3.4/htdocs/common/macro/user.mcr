<!--
   $Log: user.mcr,v $
   Revision 1.4  2002/11/13 18:59:49  wk
   - proper naming of the macros

   Revision 1.3  2002/10/28 11:19:17  wk
   - show "surname, name" now and enhanced the macro to use a user defined option value

   Revision 1.2  2002/07/30 20:22:54  wk
   - allow multi selects

   Revision 1.1.1.1  2002/07/22 09:37:37  wk


-->


<!--
    @param   array   all the users to show
    @param   int     the selected user's id
-->
{%macro user_asOptions( $users , $selected=0 , $valueName='id' )%}
    {foreach( $users as $aUser )}
        <option value="{$aUser[$valueName]}"
            {if($aUser['id']==$selected || ( is_array($selected) && in_array($aUser['id'],$selected) ))}
                selected
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
