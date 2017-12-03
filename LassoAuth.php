<?php

class LassoAuth {
  public static $auth = 'https://login.indieweb.org/';
  public static $wiki = 'https://indieweb.org/';
}

$wgAuthRemoteuserUserName = [
  $_SERVER['HTTP_REMOTE_USER']
];
$wgAuthRemoteuserRemoveAuthPagesAndLinks = false;
$wgAuthRemoteuserUserNameReplaceFilter = [
  '/^https?:\/\//'  => '',  # remove http prefix
  '/\/$/'           => '',  # remove trailing slash
  '/'               => ' ', # convert other slashes to spaces
];
$wgAuthRemoteuserUserNameBlacklistFilter = [
  '/commentpara\.de/',
  '/github\.io/',
  '/wordpress\.com/',
  '/blogspot\.com/',
  '/livejournal\.com/',
  '/indiewebcamp\.com/',
  '/indieweb\.org/',
  '/herokuapp\.com/',
  '/jsbin\.com/',
];
$wgAuthRemoteuserUserUrls = [
  'logout' => function($metadata) {
    return LassoAuth::$auth.'logout?url='.urlencode(LassoAuth::$wiki);
  }
];
$wgHooks['PersonalUrls'][] = function( array &$personal_urls, Title $title, SkinTemplate $skin ) {
  if(isset($personal_urls['login'])) {
    if(preg_match('/returnto=([^&]+)/', $personal_urls['login']['href'], $match)) {
      $page = str_replace('+','_',$match[1]);
    } else {
      $page = '';
    }
    $personal_urls['login']['href'] = LassoAuth::$auth.'login?url='.urlencode(LassoAuth::$wiki.$page);
  }
  if(isset($personal_urls['logout'])) {
    $personal_urls['logout']['text'] = 'Log Out';
  }
  return true;
};

