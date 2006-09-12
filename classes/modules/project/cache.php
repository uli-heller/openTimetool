<?php

require_once $config->classPath.'/modules/project/treeDyn.php';

/**
*   this class handles the caching of the tree
*   all methods have to be called statically
* @version $Id$
*/
class modules_project_cache
{

    /**
    *   actually we only need this for setModified since it is called as a 
    *   static method too
    *
    */
    function getCacheDir()
    {
        global $config,$account;
    
        $accountName = '';
        if ($account->isAspVersion()) {
            $accountName = '/'.$account->getAccountName();
        }
        return $config->cacheDir.'/projectTree'.$accountName;
    }
    
    /**
    *   this takes care of setting the proper state for the cache
    *   to be able to check isCached() for a certain uswer
    *   it currently does it creating a file with the user-id in 
    *   the tmp-dir
    *
    */
    function setModified($userIds)
    {
        global $config,$user;
        
        // in admin mode the changes are relevant to at least all the admins
        // so we add all the admin's user-ids the userIds array, so all the admins get informed too
        if ($user->isAdmin()) {
            $_user = new modules_common(TABLE_USER);
            $_user->setWhere('isAdmin=1');
            $adminIds = $_user->getCol('id');
            $userIds = array_unique(array_merge($userIds,$adminIds));
        }
        
        $cacheDir = modules_project_cache::getCacheDir();
        $cacheDirArray = explode('/',substr($cacheDir,1));    // we remove the leading slash
        settype($userIds,'array');
        if (sizeof($userIds)) {
            $_tmpCacheDir = "";
            foreach ($cacheDirArray as $aDir) {
                $_tmpCacheDir = "$_tmpCacheDir/$aDir";
                if (!is_dir($_tmpCacheDir)) {
                    mkdir($_tmpCacheDir);
                }
            }
            foreach ($userIds as $aUserId) {
                touch($cacheDir."/$aUserId");
            }
        }
    }

    /**
    *   this calls setModified, but it takes a project ID and gets the user-ids 
    *   for the users that have something to do with this project
    *
    *   @param  integer the ID of a project
    */
    function setModifiedByProject($projectIds)
    {
        global $config,$user;
        
        settype($projectIds,'array');
        
        // get all the children of the project, so we get all the user-ID's 
        // of all the users that are really affected by the change
        // otherwise we would only get the users who are really a member of
        // this very project
        require_once $config->classPath.'/modules/project/treeDyn.php';
        $projectTreeDyn =& modules_project_treeDyn::getInstance();
        $_projectIds = $projectTreeDyn->getAllChildrenIds($projectIds[0]);
        if (sizeof($_projectIds)) {
            $projectIds = array_merge($projectIds,$_projectIds);
        }
        
        $tree = new modules_common(TABLE_PROJECTTREE);
        $tree->autoJoin(TABLE_PROJECTTREE2USER);
        $tree->setWhere("id IN (".implode(',',$projectIds).')');
        $tree->setGroup(TABLE_PROJECTTREE2USER.'.user_id');     // get every user-id only once
        $userIds = $tree->getCol(TABLE_PROJECTTREE2USER.'.user_id');
        // call setModified every time so it takes care of the admin-ids too
        // this way we dont have to handle the admin userIds special here, setModified() does it all
        modules_project_cache::setModified($userIds);
    }    
    
    /**
    *   if the project tree for this user has changed
    *   it returns true depending on the tplFile, since every tree
    *   has to be rebuild, projectTree, projectTreeAdmin, projectTreeTeam, projectTreeTeamAdmin, etc.
    *   see activity diagram
    *
    *   @param  int     the user's ID
    *   @param  string  this is the name by which we check if the data for this user-id 
    *                   was modified, if u have userId=3 and cacheName='foo'
    *                   we will check if '3_foo' does already exist
    *                   if this parameter is not given the basename of PHP_SELF is used
    *   @return boolean true if the data were modified
    */
    function wasModified($userId,$cacheName='')
    {
        global $config,$user;
    
//FIXXXME somehow we should also remove all the files in _cache some day
// but therefore we would need to know which files can exist, to check if all files are there
// and if we can remove them ... but that would mean we would need some inteliigent code at this point
        $cacheDir = modules_project_cache::getCacheDir();
        // for the admin the file fu will be the newest file which's name
        // is only a number (the user-id) 
        // this one will then be compared to fm and if it is newer we know
        // that something in the tree has changed and we need to update it for the admin
        if ($user->isAdmin() && is_dir($cacheDir)) {
            // read all the files from the cache dir
            $dp = opendir($cacheDir);
            $mtime = 0;
            while ($curFile=readdir($dp)) {
                // dismiss all those which's names are not *only* numbers
                $latestMtime = filemtime("$cacheDir/$curFile");
                if (is_file("$cacheDir/$curFile") && is_numeric($curFile) && $latestMtime>$mtime) {
                    $mtime = $latestMtime;
                    $fu = "$cacheDir/$curFile";
                }
            }
            closedir($dp);
        } else {
            //  what we do here is getting the mtime of the file with the user id
            //  and the mtime of the file which has the name md5(userId:tplFile) 
            //  if the first file is newer it was modified
            //  and since the mtime is 0 for a file which doesnt exist this is the
            //  only condition we need to check
            // get the mtime for the actual file (which has the user id as its name)
            $fu = $cacheDir."/$userId";
        }
        // 
//        $fm = $cacheDir.'/'.md5("$userId:$tplFile");
        $fm = "$cacheDir/{$userId}_".($cacheName?$cacheName:basename($_SERVER['PHP_SELF']));
        
        if (    (is_file($fu) && 
                    (!is_file($fm) || 
                        (is_file($fm) && filemtime($fu)>filemtime($fm))
                    )
                )
           ) {
            touch($fm);                
            return true;
        }
        return false;
    }
    
    /**
    *   Check if the tree needs to be rebuilt.
    *
    *   Returns true if the tree needs to be rebuilt, false otherwise.
    *
    *   @param  string  the template file name
    *   @param  object  an object of HTTP_Header_Cache
    */
    function needsRebuild($tplFile,&$httpCache)
    {
        global $userAuth,$tpl,$session;

        $userId = $userAuth->getData('id');
        $treeModified = modules_project_cache::wasModified($userId) || 
                        modules_project_cache::updateDueToProjectSpan($httpCache);

        $otherUser = true;
        if (@$session->temp->projectTreeJS->user_id==$userId) {
            $otherUser = false;
        } else {
            $session->temp->projectTreeJS->user_id = $userId;
        }    
        
        // this if-statememnt results from the activity diagram (projectTree::needsRebuild)
        // the fact that exitIfCached might stop execution here is a bit special but correct!
        if ($treeModified || 
                (!$treeModified && 
                    ($otherUser || (!$otherUser && !$httpCache->exitIfCached())) && 
                    // we need to keep the order of the checks as in the diagram
                    // since oour exitIfCached might quit it all here
                    !$tpl->isCached($tplFile)   
                )
            ) {
//print "DO REBUILD<br>";            
            // we need to set this header, since exitIfCached() might not be called
            // and only if it was called it would set this header, so to be sure we set it here
            // so the next HTTP-Request contains a 'if-modified-since'
// i am not sure if we need this, i just did that before i discovered 
// the bug that i didnt pass httpCache as reference ... check this one day
// FIXXXXME see the comment above, test it out
            $httpCache->setHeader('Last-Modified');
            return true;
        }
        return false;
    }
    
    /**
    *   Tell if an update needs to be done because a span of a project
    *   has expired or starts now.
    *
    *   This method was introduced to solve bug#0000109
    *   We check first if the tree has been updated today, if so then this check here
    *   doesnt need to be executed.
    *   Otherwise we check if any of the time span of a visible project
    *   has an effect on today, which means if any project starts today or
    *   if any project expires today. For the expiration we have to 
    *   add the 'close' span too, so the people can book on the project
    *   for those number of days!
    *
    */
    function updateDueToProjectSpan(&$httpCache)
    {
//FIXXXXME remove this AAANNNDDDD the <cache >
return false;    // i added this because the ccheck below doesnt work for some reason :-(
        // only do this check once a day and if the file was already modifed today
        // we dont need to check the project props anymore        
        if ($httpCache->isCached() &&
            $httpCache->getCacheStart()<mktime(0,0,0,date('m'),date('d'),date('Y'))) {
            // check the project properties, see comment above!!!
//FIXXXXXXME do implement this!!! 
// SELECT count(*) FROM `projectTree` WHERE startDate>1049101917 OR endDate+(close*24*60*60)<1049103917
// something like this, but also take care of the cacheStart, etc ...           
            return true;
        }
        return false;
    }
}

?>
