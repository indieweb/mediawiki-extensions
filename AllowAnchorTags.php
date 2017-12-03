<?php
/**
 * AllowAnchorTags - allows inserting <a> anchor tags into wikitext
 */

// Extension credits that will show up on Special:Version
$wgExtensionCredits['parserhook'][] = array(
  'name' => 'AllowAnchorTags',
  'version' => '0.1',
  'author' => 'Aaron Parecki',
  'url' => 'https://github.com/indieweb/mediawiki-extensions',
  'description' => 'Allows inserting <nowiki><a></nowiki> anchor tags into wikitext',
);

$wgHooks['ParserFirstCallInit'][] = function(Parser &$parser) {
  $parser->setHook('a', function($text, array $args, Parser $parser, PPFrame $frame) {
    $text = $parser->recursiveTagParse($text, $frame);
    $href = $parser->recursivePreProcess($args['href'], $frame);

    $output = Linker::makeExternalLink( $href, $text, false, '', $args );
    unset( $args['href'] );

    return $output;
  });
  return true;
};

