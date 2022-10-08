<?php

class LassoAuth {
  public static $auth = 'https://sso.indieweb.org/';
  public static $wiki = 'https://indieweb.org/';
}

$wgAuthRemoteuserUserName = [
  $_SERVER['REMOTE_USER']
];
$wgAuthRemoteuserRemoveAuthPagesAndLinks = false;
$wgAuthRemoteuserUserNameReplaceFilter = [
  '/^https?:\/\//'  => '',  # remove http prefix
  '/\/$/'           => '',  # remove trailing slash
  '/'               => ' ', # convert other slashes to spaces
];


#$wgAuthRemoteuserUserNameBlacklistFilter = [
$iwBlockedDomains = [
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

// Recreated this hook from the Auth_Remoteuser extension in order to add a custom error message
$wgHooks['AuthRemoteuserFilterUserName'][] = function ( &$username ) use ( $iwBlockedDomains ) {
  foreach ( $iwBlockedDomains as $pattern ) {
    # If $pattern is no regex, create one from it.
    if ( @preg_match( $pattern, null ) === false ) {
      $pattern = str_replace( '\\', '\\\\', $pattern );
      $pattern = str_replace( '/', '\\/', $pattern );
      $pattern = "/$pattern/";
    }
    if ( preg_match( $pattern, $username ) ) {
      return "This domain name is not allowed as a wiki username. See <a href=\"https://sso.indieweb.org/logout?url=https%3A%2F%2Findieweb.org%2Fblocked_subdomains\">blocked subdomains</a> for more info.<br><br><a href=\"https://sso.indieweb.org/logout?url=https%3A%2F%2Findieweb.org%2F\">Try Again</a> <iframe src=\"https://sso.indieweb.org/logout?url=https%3A%2F%2Findieweb.org%2F\" style=\"display:none;\"></iframe>";
    }
  }
};


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

