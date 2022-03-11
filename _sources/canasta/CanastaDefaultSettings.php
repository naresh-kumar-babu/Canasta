<?php

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

function getCanastaLocalSettingsFilePath() {
	return getenv( 'MW_VOLUME' ) . '/config/LocalSettings.php';
}

if ( defined( 'MW_CONFIG_CALLBACK' ) ) {
	// Called from WebInstaller or similar entry point

	if ( !file_exists( getCanastaLocalSettingsFilePath() ) ) {
		// We don't define any variables and WebInstaller should decide that "$IP/LocalSettings.php" does not exist.
		return;
	}
}
// WebStart entry point

// Check that user's LocalSettings.php exists
$canastaLocalSettingsFilePath = getCanastaLocalSettingsFilePath();
if ( !is_readable( $canastaLocalSettingsFilePath ) ) {
	// Emulate that "$IP/LocalSettings.php" does not exist

	// Set MW_CONFIG_FILE for NoLocalSettings template work correctly
	define( "MW_CONFIG_FILE", $canastaLocalSettingsFilePath );

	// Do the same what function wfWebStartNoLocalSettings() does
	require_once "$IP/includes/NoLocalSettings.php";
	die();
}

// Canasta default settings below

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs
## (like /w/index.php/Page_title to /wiki/Page_title) please see:
## https://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath = "/w";
$wgScriptExtension = ".php";
$wgArticlePath = '/wiki/$1';
$wgStylePath = $wgScriptPath . '/skins';

## The URL path to static resources (images, scripts, etc.)
$wgResourceBasePath = $wgScriptPath;

# SyntaxHighlight_GeSHi
$wgPygmentizePath = '/usr/bin/pygmentize';

# We use job runner instead
$wgJobRunRate = 0;

# Docker specific setup
# see https://www.mediawiki.org/wiki/Manual:$wgCdnServersNoPurge
$wgUseCdn = true;
$wgCdnServersNoPurge = [];
$wgCdnServersNoPurge[] = '172.16.0.0/12';

# Include user defined LocalSettings.php file
require_once "$canastaLocalSettingsFilePath";

# Include all files in LocalSettings.php.d directory
foreach (glob(getenv( 'MW_VOLUME' ) . '/config/LocalSettings.php.d/*.php') as $filename) {
	require_once $filename;
}
