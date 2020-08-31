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

  if($_SERVER['REQUEST_URI'] == '/Special:RecentChanges') {
    $out->addHeadItem('websub', '<link rel="hub" href="https://switchboard.p3k.io/">'."\n".'<link rel="self" href="https://indieweb.org/Special:RecentChanges">');
  }
  
  $ga = <<<EOF
<script src="https://boar.indieweb.org/script.js" site="ZPHFRYAA" defer></script>
EOF;
  $out->addHeadItem('googleanalytics', $ga);

  return true;
};
