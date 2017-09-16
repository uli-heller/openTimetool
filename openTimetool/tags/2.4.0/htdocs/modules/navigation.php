<?php
/**
 * 
 * $Id$
 * 
 */

$isLoggedIn = $userAuth->isLoggedIn();
$isAdmin = $user->isAdmin();
// only someone who is logged in can be a manager
// AK to avoid initial warning ...
$isManager = false;
if ($isLoggedIn) {
    require_once $config->classPath . '/modules/project/member.php';
    $isManager = $projectMember->isManager();
}

$naviItems = array(
    array(
        'name'      => 'Logging',
        'condition' => $isLoggedIn,
        'children'  => array(
            array(
                'name' => 'Today-Log',
                'url'  => '/modules/time/today.php',
            ),
            array(
                'name'    => 'Quick-Log',
                'onClick' => 'openQuickLog(\'' .
                    $config->applPathPrefix . '/modules/time/quick.php\')',
            ),
            array(
                'name' => 'Multi-Log',
                'url'  => '/modules/time/multi.php',
            ),
            array(
                'name' => 'Period-Log',
                'url'  => '/modules/time/holiday.php',
            ),
        )
    ),
    array(
        'name'      => 'Overview',
        'condition' => $isLoggedIn,
        'children'  => array(
            array(
                'name' => 'by date',
                'url'  => '/modules/time/index.php',
            ),
            array(
                'name' => 'by week',
                'url'  => '/modules/time/week.php',
            ),
            array(
                'name' => 'by project',
                'url'  => '/modules/time/project.php',
            ),
            array(
                'name' => 'Export',
                'url'  => '/modules/time/index.php',
            ),
            array(
                'name'      => 'Summary',
                'condition' => $config->hasFeature('price'),
                'url'       => '/modules/time/summary.php',
            ),
        )
    ),
/*
    array(
        'name' => 'Graphical',
        'url'  => '/modules/time/index.php?graphMode=1',
    ),
 */
    /*  let users see their projects, where they are members
        and of course also those where she is a manager
     */
    array(  
        'name'      => 'Project',
        'condition' => $isLoggedIn && ($isAdmin || $isManager),
        'children'  => array(
            array(  
                'name'      => 'edit',
                'condition' => $isAdmin,
                'url'       => '/modules/project/index.php',
            ),
            array(
                'name'      => 'Team',
                'condition' => $isManager||$isAdmin,
                'url'       => '/modules/project/member.php',
            )
        )
    ),
    array(  
        'name'      => 'Admin mode',
        'condition' => $user->canBeAdmin() && $isLoggedIn,
        'children'  => array(
            array(
                'name'      => 'activate',
                'condition' => !$isAdmin,
                'url'       => '/modules/user/adminMode.php?adminModeOn=1',
            ),
            array(  
                'name'      => 'deactivate',
                'condition' => $isAdmin,
                'url'       => '/modules/user/adminMode.php',
            ),
            array(  
                'name'      => 'Tasks',
                'condition' => $isAdmin,
                'url'       => '/modules/task/index.php',
            ),
            array(  
                'name'      => 'Prices',
                'condition' => $config->hasFeature('price') && $isAdmin ,
                'url'       => '/modules/price/index.php',
            ),
            array(  
                'name'      => 'Users',
                'condition' => $isAdmin,
                'url'       => '/modules/user/index.php',
            )
        )
    ),                        
/*
    array(
        'name'     => 'Imprint',
        'children' => array(
            array(
                'name' => 'Thanks',
                'url'  => '/modules/imprint/index.php',
            )
        )
    ),
*/                
    array(  
        'name'      => 'Account name',
        'condition' => $account->isAspVersion() && !$isLoggedIn && !$config->isLiveMode(),
        'children'  => array(
            array(
                'name' => 'change',
                'url'  => '/index.php?resetAccountName=1',
            )
        )
    ),
    array(
        'name'      => 'User',
        'condition' => !($isLoggedIn || ($account->isAspVersion() && !$account->getAccountName())),
        'children'  => array(
            array(
                'name' => 'Login',
                'url'  => '/index.php',
            )
        )
    ),
    array(
        'name'      => 'Password',
        'condition' => $isLoggedIn && !$account->isAspVersion(),
        'children'  => array(
            array(
                'name' => 'change',
                'url'  => '/modules/user/password.php',
            )
        )
    ),
    array(
        'name' => 'Help',
        'children' => array(
            array('macro' => '_manualLink'),
        )
    ),
    array(
        'name' => 'Languages',
        'children' => array(
            array('macro' => '_chooseLanguage'),
        )
    ),
);

require_once 'vp/Application/HTML/Navigation.php';
$navigation = new vp_Application_HTML_Navigation($naviItems, $config);
$naviItems = $navigation->getAll();

require_once $config->finalizePage;
