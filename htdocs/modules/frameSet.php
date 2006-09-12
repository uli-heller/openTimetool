<?php
    //
    //  $Log: frameSet.php,v $
    //  Revision 1.1.1.1  2002/07/22 09:37:37  wk
    //
    //
    //

    $session->layout = 'framedDefault';
    $layout->setLayout('framedDefault');

    $tpl->compile($layout->getContentTemplate());
    // and include the compiled main template
    include($tpl->compiledTemplate);

?>