<!--

$Id$

    This file contains generally used macros which are useful for creating
    forms, such as for creating select boxes etc.
-->


<!--
    @param
-->
{%macro Form_standardOptions( $options , $selected=false , $params='' )%}
    {foreach( $options as $key=>$val )}
        <option value="{$key}" {$params}
            {if($key == $selected)}
<!--  for selecting multiple out of an array
            {if($aUser['id']==$selected || ( is_array($selected) && in_array($aUser['id'],$selected) ))}
-->
                selected="selected"
        >
        {$val}
        </option>

<!--
    @param      mixed   if true or any expression that evaluates to true
                        like <not ''> then it means yes
-->
{%macro Form_yesNoOptions($selected=false)%}
    { $options = array(1=>t('yes'),0=>t('no'))}
    {foreach( $options as $key=>$val )}
        <option value="{$key}"
            {if($key == $selected)}
                selected="selected"
        >
        {$val}
        </option>


<!--
    create an input field

    @param      array   this is the data array, that contains the data to show
    @param      string  the key to access the data inside the $data-array
    @param      string  additional parameters
-->
{%macro Form_input( &$data , $key , $params='' )%}
    <input name="newData[{$key}]" value="{$data[$key]}" {$params}>


<!--
    create a textarea

    @param      array   this is the data array, that contains the data to show
    @param      string  the key to access the data inside the $data-array
    @param      string  additional parameters for the textarea, such as rows and cols
-->
{%macro Form_textarea( &$data , $key , $param=' rows="4" cols="30"' )%}
    <textarea name="newData[{$key}]" {$param}>{$data[$key]}</textarea>
