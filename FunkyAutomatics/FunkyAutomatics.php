<?php
/**
 * FunkyAutomatics Extension
 *
 * Optional parameter: <var weight="3"> == 3x weight given
 *
 * @file
 * @version 0.1.6
 * @date 17 November 2010
 * @author McCouman 
 */
 
if( !defined( 'MEDIAWIKI' ) ) {
	die( "This is not a valid entry point to MediaWiki.\n" );
}
 
$wgHooks['ParserFirstCallInit'][] = 'wfRandomSelection';
 
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'FunkyAutomatics',
	'version' => '0.1.6',
	'author' => '[[User:McCouman|Michael McCouman jr.]]',
	'description' => '&#60;automatic&#62;&#60;var&#62; Diese Extension zeigt automatisch eine der ausgew&auml;hlten Parameter an.',
);
 
function wfRandomSelection( &$parser ) {
	$parser->setHook( 'automatic', 'renderAuto' );
	return true;
}
 
function renderAuto( $input, $argv, $parser ) {
	# caching
	$parser->disableCache();
 
	# parsering the options Tag "var"
	$len = preg_match_all(
		"/<var(?:(?:\\s[^>]*?)?\\sweight=[\"']?([^\\s>]+))?"
			. "(?:\\s[^>]*)?>([\\s\\S]*?)<\\/var>/",
		$input,
		$out
	);
	$r = 0;
	for( $i = 0; $i < $len; $i++ ) {
		if( strlen( $out[1][$i] ) == 0 ) {
			$out[1][$i] = 1;
		} else {
			$out[1][$i] = intval( $out[1][$i] );
		}
		$r += $out[1][$i];
	}
 
	# varoption at random
	if( $r <= 0 ) {
		return '';
	}
	$r = mt_rand( 1, $r );
	for( $i = 0; $i < $len; $i++ ) {
		$r -= $out[1][$i];
		if( $r <= 0 ) {
			$input = $out[2][$i];
			break;
		}
	}
 
	# Parse tags and return the variable
	return $parser->recursiveTagParse( $input );
}
