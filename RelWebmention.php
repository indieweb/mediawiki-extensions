<?php
$wgExtensionCredits['parserhook'][] = array(
  'name' => 'RelWebmention',
  'author' => 'Aaron Parecki',
  'description' => 'Adds <nowiki><link rel="webmention"></nowiki> tag to advertise a Webmention endpoint on every page',
  'url' => 'https://github.com/indieweb/mediawiki-extensions'
);

$wgHooks['OutputPageBeforeHTML'][] = function(OutputPage &$out, &$text) {
  global $wgWebmentionEndpoint, $wgPingbackEndpoint;
  
  $out->addHeadItem('webmention', '<link rel="webmention" href="' . $wgWebmentionEndpoint . '">'."\n");
  $out->addHeadItem('pingback', '<link rel="pingback" href="' . $wgPingbackEndpoint . '">'."\n");

  return true;
};
