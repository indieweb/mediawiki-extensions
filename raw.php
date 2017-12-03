<?php

$wgExtensionCredits['parserhook'][] = array(
  'name' => 'Raw HTML',
  'author' => 'Aaron Parecki',
  'version' => '0.1',
  'description' => 'Adds <nowiki><raw></nowiki> tag to include arbitrary html',
  'url' => 'https://github.com/indieweb/mediawiki-extensions'
);

$wgHooks['ParserFirstCallInit'][] = function( Parser &$parser ) {
  $parser->setHook("raw", function($input, $args) {
    return $input;
  });
  return true;
};
