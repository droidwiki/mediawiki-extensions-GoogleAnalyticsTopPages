<?php
	class GoogleAnalyticsTopPages {
		public static function getData() {
			global $wgGATPServiceAccountName, $wgGATPKeyFileLocation, $wgGATPAppName,
				$wgGATPProfileId, $wgGATPInterval;

			// create a new Google_Client object
			$client = new Google_Client();
			// set app name
			$client->setApplicationName( $wgGATPAppName );
			// Create the needed Google Analytics service object
			$service = new Google_Service_Analytics( $client );

			// check, if the client is already authenticated
			if ( isset( $_SESSION['service_token'] ) ) {
				$client->setAccessToken( $_SESSION['service_token'] );
			}

			// load the certificate key file
			$key = file_get_contents( $wgGATPKeyFileLocation );
			// create the service account credentials
			$cred = new Google_Auth_AssertionCredentials(
				$wgGATPServiceAccountName,
				array( 'https://www.googleapis.com/auth/analytics.readonly' ),
				$key
			);
			// set the credentials
			$client->setAssertionCredentials( $cred );
			if ( $client->getAuth()->isAccessTokenExpired() ) {
				// authenticate the service account
				$client->getAuth()->refreshTokenWithAssertion( $cred );
			}
			// set the service_token to the session for future requests
			$_SESSION['service_token'] = $client->getAccessToken();

			// get the start and end time for the request
			$startTime = date( 'Y-m-d', time() - $wgGATPInterval * 86400 );
			$endTime = date( 'Y-m-d', time() );

			// do the request to Google Analytics
			$response = $service->data_ga->get(
				'ga:' . $wgGATPProfileId,
				$startTime,
				$endTime,
				'ga:pageviews,ga:exits',
				array(
					'dimensions' => 'ga:pagePath',
					'max-results' => 10,
					'sort' => '-ga:pageviews'
				)
			);

			// our response array
			$titles = array();
			foreach( $response['rows'] as $row ) {
				// try to get an actual title object of the returned pagePath
				$title = self::makeTitle( $row[0] );
				if ( $title ) {
					$titles[] = array(
						'page_title' => $title->getText(),
						'page_visitors' => $row[1]
					);
				}
			}

			return $titles;
		}

		public static function makeTitle( $text ) {
			global $wgArticlePath;

			// try to make a Title object without modifications
			$title = Title::newFromText( $text );
			if ( $title instanceof Title && $title->exists() ) {
				return $title;
			}

			// remove $wgArticle
			$article = str_replace( '$1', '', $wgArticlePath );
			$text = str_replace( $article, '', $text );
			$title = Title::newFromText( $text );
			if ( $title instanceof Title && $title->exists() ) {
				return $title;
			}

			// try parse_url and parse_str (maybe it's a index.php?title=Page construct)
			$url = parse_url( $text );
			if ( isset( $url['query'] ) ) {
				parse_str( $url['query'], $query );
				if ( isset( $query['title'] ) ) {
					$title = Title::newFromText( $query['title'] );
					if ( $title instanceof Title && $title->exists() ) {
						return $title;
					}
				}
			}

			return false;
		}
	}
