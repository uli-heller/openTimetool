<?php
    //
    //  $Log: navigation.php,v $
    //
    //  Revision 1.29 $id 
    //  - added passord chaneg navigation
    //
    //  Revision 1.28.2.1  2003/03/17 16:23:53  wk
    //  - some bugfixes in the navi, it was showing project/team also for non-manager
    //
    //  Revision 1.28  2003/02/17 19:17:34  wk
    //  - just cosmetical
    //
    //  Revision 1.27  2003/02/14 15:41:39  wk
    //  - rearrange navi a bit and add new overview points
    //
    //  Revision 1.26  2003/02/13 16:19:50  wk
    //  - optimize navi stuff
    //
    //  Revision 1.25  2003/02/10 19:28:11  wk
    //  - moved project edit stuff out of the admin-area
    //
    //  Revision 1.24  2003/01/29 16:09:43  wk
    //  - show change account name point not in live mode
    //
    //  Revision 1.23  2003/01/28 10:57:47  wk
    //  - manual is a macro, so we can show the pdf-link behind
    //
    //  Revision 1.22  2002/12/02 16:35:48  wk
    //  - updated manual link
    //
    //  Revision 1.21  2002/11/30 18:40:07  wk
    //  - moved account menu above user
    //
    //  Revision 1.20  2002/11/30 13:06:13  wk
    //  - show account menu only when needed
    //  - rename manual link
    //
    //  Revision 1.19  2002/11/29 16:58:51  wk
    //  - added today-log and account items
    //
    //  Revision 1.18  2002/11/29 14:45:16  jv
    //  - change admin to admin mode  -
    //
    //  Revision 1.17  2002/11/29 08:49:59  jv
    //  - change placement for user logoin -
    //
    //  Revision 1.16  2002/11/22 20:14:19  wk
    //  - added help button
    //
    //  Revision 1.15  2002/11/19 20:02:31  wk
    //  - some rearrangements
    //
    //  Revision 1.14  2002/11/13 19:02:56  wk
    //  - changes due to the new admin mode
    //
    //  Revision 1.13  2002/10/31 17:49:05  wk
    //  - rename navi item
    //
    //  Revision 1.12  2002/10/28 11:21:51  wk
    //  - added project-members link
    //
    //  Revision 1.11  2002/10/24 14:15:32  wk
    //  - rename to be unique
    //
    //  Revision 1.10  2002/10/22 14:44:48  wk
    //  - use vp-navi
    //
    //  Revision 1.9  2002/09/11 15:51:19  wk
    //  - added multilog in navi
    //
    //  Revision 1.8  2002/08/29 13:32:10  wk
    //  - added holidays in navi
    //
    //  Revision 1.7  2002/08/26 09:09:42  wk
    //  - dont show graphical
    //
    //  Revision 1.6  2002/08/20 16:30:21  wk
    //  - added imprint and overview links
    //
    //  Revision 1.5  2002/08/14 16:19:33  wk
    //  - removed old navi-stuff
    //  - added Info e-mail sent tomenu item 'prices'
    //
    //  Revision 1.4  2002/07/25 11:56:15  wk
    //  - added graphMode to url
    //
    //  Revision 1.3  2002/07/25 10:11:46  wk
    //  - shrink navi
    //
    //  Revision 1.2  2002/07/24 17:11:12  wk
    //  - updated navi
    //
    //  Revision 1.1.1.1  2002/07/22 09:37:37  wk
    //
    //
    //

    $isLoggedIn =   $userAuth->isLoggedIn();
    $isAdmin =      $user->isAdmin();
    // only someone who is logged in can be a manager
    $isManager = false;		// AK to avoid initial warning ...
    if ($isLoggedIn) {
        require_once $config->classPath.'/modules/project/member.php';
        $isManager =    $projectMember->isManager();
    }
    
    $naviItems = array(
            array(  'name'  =>  'Logging',
                    'condition'=>   $isLoggedIn,
                    'children'=>array(
                        array(  'name'  =>  'Today-Log',
                                'url'   =>  '/modules/time/today.php'
                            ),
                        array(  'name'  =>  'Quick-Log',
                                'onClick'   =>  'openQuickLog(\''.$config->applPathPrefix.'/modules/time/quick.php\')',
                            ),
                        array(  'name'  =>  'Multi-Log',
                                'url'   =>  '/modules/time/multi.php'
                            ),
                        array(  'name'  =>  'Period-Log',
                                'url'   =>  '/modules/time/holiday.php'
                            )
                        )
                    ),

            array(  'name'  =>  'Overview',
                    'condition'=>   $isLoggedIn,
                    'children'=>array(
                        array(  'name'  =>  'by date',
                                'url'   =>  '/modules/time/index.php'
                            ),
                        array(  'name'  =>  'by week',
                                'url'   =>  '/modules/time/week.php'
                            ),
                        array(  'name'  =>  'by project',
                                'url'   =>  '/modules/time/project.php'
                            ),
                        array(  'name'  =>  'Export',
                                'url'   =>  '/modules/time/index.php'
                            ),
                        array(  'name'  =>  'Summary',
                                'condition'=>$config->hasFeature('price'),
                                'url'   =>  '/modules/time/summary.php'
                            )
                        )
                    ),
                            /*,
                        array(  'name'  =>  'Graphical',
                                'url'   =>  '/modules/time/index.php?graphMode=1'
                            )*/

            /*  let users see their projects, where they are members
                and of course also those where she is a manager
             */
            array(  'name'  =>  'Project',
                    'condition'=>   $isLoggedIn && ($isAdmin || $isManager),
                    'children'=>array(
                        array(  
                                'condition'=>$isAdmin,
                                'name'  =>  'edit',
                                'url'   =>  '/modules/project/index.php'
                            ),
                        array(  'name'  =>  'Team',
                                'condition'=>$isManager||$isAdmin,
                                'url'   =>  '/modules/project/member.php'
                            )
                        )
                ),

            array(  'name'  =>  'Admin mode',
                    'condition'=>   $user->canBeAdmin() && $isLoggedIn,
                    'children'=>array(
                        array(  'name'  =>  'activate',
                                'condition'=>!$isAdmin,
                                'url'   =>  '/modules/user/adminMode.php?adminModeOn=1'
                            ),
                        array(  'name'  =>  'deactivate',
                                'condition'=>$isAdmin,
                                'url'   =>  '/modules/user/adminMode.php'
                            ),
                        array(  'name'  =>  'Tasks',
                                'condition'=>$isAdmin,
                                'url'   =>  '/modules/task/index.php'
                            ),
                        array(  'name'  =>  'Prices',
                                'condition'=> $config->hasFeature('price') && $isAdmin ,
                                'url'   =>  '/modules/price/index.php'
                            ),
                        array(  'name'  =>  'Users',
                                'condition'=>$isAdmin,
                                'url'   =>  '/modules/user/index.php'
                            )
                        )
                ),                        
/*
            array(  'name'  =>  'Imprint',
                    'children'=>array(
                        array(  'name'  =>  'Thanks',
                                'url'   =>  '/modules/imprint/index.php'
                            )
                        )
                ),
*/                

            array(  'name'  =>  'Account name',
                    'condition'=>   $account->isAspVersion() && !$isLoggedIn && !$config->isLiveMode(),
                    'children'=>array(
                        array(  'name'  =>  'change',
                                'url'   =>  '/index.php?resetAccountName=1'
                            )
                        )
                ),

            array(  'name'  =>  'User',
                    'condition'=>   !($isLoggedIn || ( $account->isAspVersion() && !$account->getAccountName() )),
                    'children'=>array(
                        array(  'name'  =>  'Login',
                                'url'   =>  '/index.php'
                            )
                        )
                ),

            array(  'name'  =>  'Password',
                    'condition'=>   $isLoggedIn && !$account->isAspVersion() ,
                    'children'=>array(
                        array(  'name'  =>  'change',
                                'url'   =>  '/modules/user/password.php'
                            )
                        )
                ),


            array(  'name'  =>  'Help',
                    'children'=>array(
                        array(  'macro'  =>  '_manualLink'
                            )
                        )
                ),

            array(  'name'  =>  'Languages',
                    'children'=>array(
                        array(  'macro'  =>  '_chooseLanguage'
                            )
                        )
                    )


        );

    require_once('vp/Application/HTML/Navigation.php');
    $navigation = new vp_Application_HTML_Navigation( $naviItems , $config );
    $naviItems = $navigation->getAll();


    require_once($config->finalizePage);
?>
