<!--

$Id$

-->

<!--
    @param   array   all the tasks to show
    @param   int     the selected task's id
-->
{%macro task_asOptions($tasks,$selected=0)%}
    {foreach( $tasks as $aTask )}
        <option value="{$aTask['id']}"
            {if($aTask['id']==$selected || ( is_array($selected) && in_array($aTask['id'],$selected) ))}
                selected
        >
        {$aTask['name']}
        </option>

<!--
    @deprecated
    @param   array   all the tasks to show
    @param   int     the selected task's id
-->
{%macro tasksAsOptions($tasks,$selected=0)%}
    {%task_asOptions($tasks,$selected)%}



<!--
    shows a table row with a drop down box to select a task

    @param  array   the result of $task->getAll()
    @param  int     the id of the selected task
    @param  string  the name used for the select box
-->
{%macro task_row(&$allTasks,$selectedTask=0,$name='newData[task_id]')%}
    <tr>
        <td>Task</td>
        <td>
            <select name="{$name}">
                {%task_asOptions($allTasks,$selectedTask)%}
            </select>
        </td>
    </tr>


<!--
    shows a table row with a drop down box to select a task
    Only one column as we use it in mobile log only

    @param  array   the result of $task->getAll()
    @param  int     the id of the selected task
    @param  string  the name used for the select box
-->
{%macro task_row_mobile(&$allTasks,$selectedTask=0,$name='newData[task_id]')%}
    <tr>
        <td>Task
        <br>
            <select name="{$name}">
                {%task_asOptions($allTasks,$selectedTask)%}
            </select>
        </td>
    </tr>
