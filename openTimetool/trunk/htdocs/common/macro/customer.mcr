<!--
   $Log: customer.mcr,v $
   Revision 1.1.1.1  2002/07/22 09:37:37  wk


-->


<!--
   @param   array   all the customers to show
   @param   int     the selected customer's id
-->
{%macro customersAsOptions($customers,$selected=0)%}
    {foreach( $customers as $aCustomer )}
        <option value="{$aCustomer['id']}"
            {if( $aCustomer['id'] == $selected )}
                selected
        >
        {$aCustomer['name']}
        </option>