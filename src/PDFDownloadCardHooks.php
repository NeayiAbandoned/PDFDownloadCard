<?php

class PDFDownloadCardHooks
{
	/**
	 * Register any render callbacks with the parser
	 */
	public static function onParserFirstCallInit( Parser $parser )
	{
		// Create a function hook associating the "pdfdownloadcard" magic word with renderPDFDownloadCard()
		$parser->setFunctionHook( 'pdfdownloadcard', [ self::class, 'renderPDFDownloadCard' ] );

		$title = Title::newFromText('FICHE_CEPAGE_MUSCARIS_B.pdf');

	}

	/**
	 * Render the output of {{#pdfdownloadcard:}}.
	 */
	public static function renderPDFDownloadCard( Parser $parser )
	{
		// The input parameters are wikitext with templates expanded.
		// The output should be wikitext too.
		$options = PDFDownloadCardHooks::extractOptions( array_slice( func_get_args(), 1 ) );

		$title = Title::newFromText('File:'.$options['file']);
		$bExists = $title->exists();

		if ($bExists)
		{
			$url1 = $title->getFullURL(); // the page

			$page = WikiPage::factory( $title );
			wfDebugLog( 'PDFDownloadCardHooks',  print_r($page, true ) );
			$file = $page->getFile();
			$FileUrl = $file->getCanonicalUrl(); // the file

			$thumbsURL = $file->getThumbURL();
			//"/neayi/wikipratiques/images/thumb/e/e8/FICHE_CEPAGE_MUSCARIS_B.pdf"
// <img class="card-img-top" src="'.$thumbsURL.'" alt="Card image cap">
			$output = '<div class="card mb-3" style="width: 18rem;">
			<div class="card-body">
			<h5 class="card-title">'.$options['title'].'</h5>
			<p class="card-text">'.$options['description'].'</p>
			<a href="'.$FileUrl.'" target="_blank" class="btn btn-primary">'.wfMessage( 'pdfcardextension-downloadfile' )->parse().'</a>
			</div>
			</div>';

			return [ $output, 'noparse' => false, 'isHTML' => true ];
		}
		else
			return "[[File:".$options['file']."]]";
	}

	/**
	 * Converts an array of values in form [0] => "name=value"
	 * into a real associative array in form [name] => value
	 * If no = is provided, true is assumed like this: [name] => true
	 *
	 * @param array string $options
	 * @return array $results
	 */
	private static function extractOptions( array $options )
	{
		$keys = array('file', 'title', 'description');
		$results = [];

		foreach ( $options as $k => $option )
		{
			$pair = array_map( 'trim', explode( '=', $option, 2 ) );

			if ( count( $pair ) === 2 )
				$results[ strtolower($pair[0]) ] = $pair[1];
			else if ( count( $pair ) === 1 )
				$results[ $keys[$k] ] = $pair[0];
		}

		return $results;
	}
}