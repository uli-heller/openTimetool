<!--
    $Log: Form.mcr,v $
    Revision 1.3  2003/03/11 12:57:55  wk
    *** empty log message ***

    Revision 1.7  2003/01/10 13:41:45  pp
    - removed new form elements added by cb
      (this are moved to the application 'bvt2002' itself)

    Revision 1.6  2002/12/19 16:00:06  cb
    - added new form elements

    Revision 1.5  2002/11/26 15:53:50  wk
    - use params

    Revision 1.4  2002/10/17 14:36:21  wk
    - added input and textarea

    Revision 1.3  2002/09/11 15:56:56  wk
    - started multiple-select

    Revision 1.2  2002/08/19 20:00:00  wk
    - renamed macros

    Revision 1.1  2002/07/26 20:46:33  wk
    - initial commit




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
                selected
        >
        {$val}
        </option>

<!--
    @param      mixed   if true or any expression that evaluates to true
                        like <not ''> then it means yes
-->
{%macro Form_yesNoOptions($selected=false)%}
    { $options = array(1=>'yes',0=>'no')}
    {foreach( $options as $key=>$val )}
        <option value="{$key}"
            {if($key == $selected)}
                selected
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

