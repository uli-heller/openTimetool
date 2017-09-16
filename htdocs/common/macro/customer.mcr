<!--

$Id$

-->

<!--
   @param   array   all the customers to show
   @param   int     the selected customer's id
-->
{%macro customersAsOptions($customers,$selected=0)%}
    {foreach( $customers as $aCustomer )}
        <option value="{$aCustomer['id']}"
            {if( $aCustomer['id'] == $selected )}
                selected="selected"
        >
        {$aCustomer['name']}
        </option>
