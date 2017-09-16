<!--

$Id$

    This file contains generally used macros which are useful for editing data
    in a form. This includes some formatting, what it's assuming is that
    the layout is created by using tables as follows:
    +-----------+-----------------+
    | name      | <input field>   |
    +-----------+-----------------+
    so if you are requesting to get a 'edit-cell' it will look like the one
    shown above.
    I know this limits the design but since we are using this for v:p we
    can live with that I have decided :-).

    The final goal is to create a set of class(es) and macros which can be used
    easily to create a form which lets a user edit data of any kind. Since those
    data are almost always coming from a db we are specializing in being compatible
    to the vp_DB_Common, which means i.e. columns are referenced by their name
    as the array-key.

    @author     Wolfram Kriesing <wk@visionp.de>
-->

<!--
    show a headline, use the th tag for it

    @param  string  the headline to show
-->
{%macro EditData_headline( $headline )%}
    <tr>
        <th colspan="2" nowrap="nowrap">{$headline}</th>
    </tr>

<!--
    create a table-row with one or many input fields
    NOTE: when giving multiple values, the keys have to be the same for each value
    for $keys, $keyNames and $params, an example call would be
        \{%EditData_input( $data ,   array('zip','city') ,
                                    array('Zip-Code','City') ,
                                    array('','size="20"') )%\}   - NOTE this here! the key 0 has to have a value!!

    @param      array   this is the data array, that contains the data to show
    @param      mixed   string  the key to access the data inside the $data-array
                        array   multiple keys
    @param      mixed   string  in case its given its shown as the description for this input field
                        array   multiple descriptions
    @param      mixed   string  parameters that will be added to the input field
                        array   multiple params for each input field one string

-->
{%macro EditData_input( &$data , $keys , $keyNames=array() , $params=array() )%}

    { settype($keys,'array')}
    { settype($keyNames,'array')}
    { settype($params,'array')}

    { $descriptions=array()}
    {foreach( $keys as $index=>$aKey )}
        { $descriptions[$index] = @$keyNames[$index]?$keyNames[$index]:$aKey} <!-- AK : @ -->

    {foreach( $keys as $index=>$aKey )}
        {if( strpos($descriptions[$index],'*')!==false && !strpos(@$params[$index],'class='))}  <!-- AK : @ -->
            { @$params[$index] .= ' class="required"' }
        {else}                                                <!-- AK : notices -->
            { @$params[$index] .= '' }                         <!-- AK : notices -->

    { $description = implode(', ',$descriptions)} <!-- so the translator can also translate it -->
    <tr>
        <td nowrap="nowrap">
            {$description}<br>
        </td>
        <td nowrap="nowrap">
            {foreach( $keys as $index=>$aKey )}
                { $ddata = !empty($data[$aKey])?$data[$aKey]:''}    <!-- AK : notices -->
                <input name="newData[{$aKey}]" value="{$ddata}" {$params[$index]}>  <!-- AK : notices -->
        </td>
    </tr>

<!--
    create a table-row with an input field for a url, and show the additional test-button

    @param      array   this is the data array, that contains the data to show
    @param      string  the key to access the data inside the $data-array
    @param      string  in case its given its shown as the description for this input field
-->
{%macro EditData_inputUrl( &$data , $key , $keyName='' , $params='' )%}
    <tr>
        <td nowrap="nowrap">{$keyName?$keyName:$key}</td>
        <td nowrap="nowrap">
            <input name="newData[{$key}]" value="{$data[$key]}" {$params}>
            <input type="button" onclick="window.open(this.form['newData[{$key}]'].value)" value="test" class="button">
        </td>
    </tr>


<!--
    create a table-row with an file-input field
    NOTE: we are not using the newData-array here, since php doesnt accept uploaded files
    in an array variable :-(

    @param      array   this is the data array, that contains the data to show
    @param      string  the key to access the data inside the $data-array
    @param      string  in case its given its shown as the description for this input field
-->
{%macro EditData_inputFile( &$data , $key , $keyName='' , $params='' )%}
    <tr>
        <td nowrap="nowrap">{$keyName?$keyName:$key}</td>
        <td nowrap="nowrap">
            <input type="file" name="{$key}" value="{$data[$key]}" {$params}>
        </td>
    </tr>

<!--
    create a table-row with an password-input field

    @param      array   this is the data array, that contains the data to show
    @param      string  the key to access the data inside the $data-array
    @param      string  in case its given its shown as the description for this input field
-->
{%macro EditData_password( $key , $keyName='' , $params='' )%}

    { $description = $keyName?$keyName:$key}

    {if( strpos($description,'*')!==false && !strpos($params,'class='))}
        { $params .= ' class="required"' }

    <tr>
        <td nowrap="nowrap">{$description}</td>
        <td nowrap="nowrap">
            <input type="password" name="newData[{$key}]" {$params}>
        </td>
    </tr>


<!--
    create a table-row with an checkbox field

    @param      array   this is the data array, that contains the data to show
    @param      string  the key to access the data inside the $data-array
    @param      string  in case its given its shown as the description for this input field
-->
{%macro EditData_checkbox( &$data , $key , $keyName='' , $params='' )%}

    { $description = $keyName?$keyName:$key}

    {if( strpos($description,'*')!==false && !strpos($params,'class='))}
        { $params .= ' class="required"' }

    <tr>
        <td nowrap="nowrap">{$description}</td>
        <td nowrap="nowrap">
            <input type="checkbox" name="newData[{$key}]" value="1" {$data[$key]?'checked="checked"':''}  {$params}>
        </td>
    </tr>


<!--
    create a table-row with an radio field

    @param      array   this is the data array, that contains the data to show
    @param      string  the key to access the data inside the $data-array
    @param      string  in case its given its shown as the description for this input field
-->


{%macro EditData_radio( &$data , $key , $keyName='' , $params='' )%}

    { $description = $keyName?$keyName:$key}

    {if( strpos($description,'*')!==false && !strpos($params,'class='))}
        { $params .= ' class="required"' }

    <tr>
        <td nowrap="nowrap">{$description}</td>
        <td nowrap="nowrap">
        links&nbsp;<input type="radio" name="newData[{$key}]" value="links">&nbsp;rechts&nbsp;
        <input type="radio" name="newData[{$key}]" value="rechts">

        </td>
    </tr>


<!--
    create a table-row with an textarea,
    dont use Form_textarea so the user doesnt need to include that one too

    @param      array   this is the data array, that contains the data to show
    @param      string  the key to access the data inside the $data-array
    @param      string  in case its given its shown as the description for this input field
    @param      string  additional parameters for the textarea, such as rows and cols
-->
{%macro EditData_textarea( &$data , $key , $keyName='' , $param=' rows="4" cols="30"' )%}
    { $text = $keyName?$keyName:$key}   <!-- we do it this way, so the translation also translates it, i think the if in there is still to complicated for the translation -->
    <tr>
        <td nowrap="nowrap" valign="top">{$text}</td>
        <td nowrap="nowrap">
            <textarea name="newData[{$key}]" {$param}>{%$data[$key]%}</textarea>
        </td>
    </tr>



{%macro EditData__standardOptions( $options , $selected=false , $params='' )%}
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
    create a table-row with a drop down

    @param
    @param
    @param

-->

{%macro EditData_select( $data , $key , $keyName='' , $selected=false )%}
    <tr>
        <td>{$keyName?$keyName:$key}</td>
        <td>
            <select name="newData[{$key}]">
                {%Form_standardOptions( $data , $selected )%}
            </select>
        </td>
    </tr>



<!--

                                
    @param
-->
{%macro EditData_saveButton( $showSaveAsNew=true  , $params='class="button"' )%}
    <tr>
        <td nowrap="nowrap">&nbsp;</td>
        <td align="middle" nowrap="nowrap">
            <input type="submit" name="action_save" value="Save" {$params}>
            {if($showSaveAsNew)}
                <input type="submit" name="action_saveAsNew" value="Save as new" {$params}>
        </td>
    </tr>


<!--


    @param
-->
{%macro EditData_spaceRow()%}
    <!-- some space between the data and the save buttons -->
    <tr>
        <td colspan="2"><br></td>
    </tr>



<!--
    creates a complete table with a form around it for the given fields
    using all the EditData-macros
    this can be used to create very simple input/edit forms

    @param  string  headline
    @param  array   this is the data array, that contains the data to show
    @param  array   names of the keys for the fields to show, if
                    a index has a key it will be used as the EditData_* macro name
    @param  string  teh additional parameters that will be added to each field
-->
{%macro EditData_all( $headline , &$data , $keys , $params )%}
    <form method="post" action="{$_SERVER['PHP_SELF']}" name="editForm">
        <input type="hidden" name="newData[id]" value="{$data['id']}">
        <table>
            <thead>
              {%EditData_headline($headline)%}
            </thead>

            <tfoot>
              {%EditData_saveButton()%}
            </tfoot>

            <tbody>
            {foreach( $keys as $key=>$val )}
                {if( is_numeric($key) )}
                    {%EditData_input($data,$val,'',$params)%}
                {else}
                    { $macroName = 'EditData_'.$key}
                    <!-- FIXXXME add params here!!! -->
                    { $macroName($data,'url')}
            </tbody>
        </table>
    </form>
