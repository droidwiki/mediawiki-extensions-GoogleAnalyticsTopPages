<?php
	/**
	GoogleAnalyticsTopPages License
	Copyright (c) 2014 Florian Schmidt

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
	*/

	if ( !defined( 'MEDIAWIKI' ) ) {
		die( 'This is an extension for Mediawiki and can not run standalone.' );
	}

	$wgExtensionCredits['specialpage'][] = array(
		'path' => __FILE__,
		'name' => 'GoogleAnayltcisTopPages',
		'author' => 'Florian Schmidt',
		'url' => 'https://www.mediawiki.org/wiki/Extension:GoogleAnalyticsTopPages',
		'description' => 'Provides a top pages list with the use of Google Analytics data.',
		'version'  => '0.0.1',
		'license-name' => "MIT",
	);

	$dir = __DIR__;

	// Autoload Classes
	$wgAutoloadClasses[ 'GoogleAnalyticsTopPages' ] =
		$dir . '/includes/GoogleAnalyticsTopPages.body.php';
	$wgAutoloadClasses[ 'GoogleAnalyticsTopPagesHooks' ] =
		$dir . '/includes/GoogleAnalyticsTopPages.hooks.php';
	$wgAutoloadClasses[ 'SpecialGoogleAnalyticsTopPages' ] =
		$dir . '/includes/specials/SpecialGoogleAnalyticsTopPages.php';
	$wgAutoloadClasses[ 'ApiGooglePageStatsUpdate' ] =
		$dir . '/includes/api/ApiGooglePageStatsUpdate.php';

	// Special Page
	$wgSpecialPageGroups[ 'GoogleAnalyticsTopPages' ] = 'other';
	$wgSpecialPages[ 'GoogleAnalyticsTopPages' ] = 'SpecialGoogleAnalyticsTopPages';

	// API Modules
	$wgAPIModules['googlepagestatsupdate'] = 'ApiGooglePageStatsUpdate';

	// Hooks
	$wgHooks['LoadExtensionSchemaUpdates'][] =
		'GoogleAnalyticsTopPagesHooks::onLoadExtensionSchemaUpdates';
	$wgHooks['ParserFirstCallInit'][] = 'GoogleAnalyticsTopPagesHooks::onParserFirstCallInit';

	// Configuration variables

	/**
	 * Service account name of the service account to use for api requests (to Google Analytics API).
	 * Can be found in Google Developer console.
	 *
	 * @var string
	 */
	$wgGATPServiceAccountName = '';

	/**
	 * Absolute path to certificate file to access the service account. Can be downloaded
	 * from Google Developer console. Be sure to put the certificate somewhere, where only
	 * you can access it! ({wikiroot}/ isn't a good idea!)
	 *
	 * @var string
	 */
	$wgGATPKeyFileLocation = '';

	/**
	 * Optional name of this api app.
	 *
	 * @var string
	 */
	$wgGATPAppName = "googleanalyticstoppages";

	/**
	 * Google Analytics profile id to get the data from. Your service account need to have at
	 * least read access to access the data. The profile ID isn't the UA-XXXX string!
	 *
	 * @var string
	 */
	$wgGATPProfileId = '';

	/**
	 * Interval in days (from today) to get visitor data.
	 *
	 * @var integer
	 */
	$wgGATPInterval = 30;

	/**
	 * To protect the API against anonymous calls, you can set this to true. You will need to
	 * transmit $wgSecretKey in your request to authenticate the call als valid.
	 *
	 * @var boolean
	 */
	$wgGATPProtectAPI = true;
