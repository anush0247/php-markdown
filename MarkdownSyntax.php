<?php
/*
 * MarkdownSyntax.php - A MediaWiki extension which adds support for Markdown syntax.
 * @author Simon Dorfman (Thomas Peri (http://www.tumuski.com) pretty much figured this whole thing out!)
 * @version 0.2
 * @copyright Copyright (C) 2007 Simon Dorfman
 * @license The MIT License - http://www.opensource.org/licenses/mit-license.php 
 * @addtogroup Extensions
 * -----------------------------------------------------------------------
 * Description:
 *     This is a MediaWiki (http://www.mediawiki.org/) extension which adds support
 *     for Markdown syntax.
 * Requirements:
 *     This extension was written and tested with MediaWiki 1.10.0.
 *     It depends upon PHP Markdown:
 *         http://www.michelf.com/projects/php-markdown/ (version 1.0.1f)
 * Installation:
 *     1. Create a folder in your $IP/extensions directory called MarkdownSyntax.
 *         Note: $IP is your MediaWiki install dir.
 *         You have something like this: $IP/extensions/MarkdownSyntax/
 *     2. Download Michel Fortin's PHP Markdown, unzip and look for the file markdown.php.
 *         Note: Don't download PHP Markdown Extra. Only PHP Markdown is supported. PHP Markdown Extra may be supported in a future release
 *     3. Drop markdown.php into $IP/extensions/MarkdownSyntax/
 *     4. Download MarkdownSyntax.php and drop it into $IP/extensions/MarkdownSyntax/ also.
 *     5. Enable the extension by adding this line to your LocalSettings.php:
 *            require_once( "{$IP}/extensions/MarkdownSyntax.php" );
 * Usage:
 *     See http://daringfireball.net/projects/markdown/syntax
 * Version Notes:
 *     version 0.2:
 *         Switched to ParserBeforeStrip hook.
 *         Hacked html links produced by markdown.php into mediawiki links.
 *     version 0.1:
 *         Initial release.
 * -----------------------------------------------------------------------
 * Copyright (c) 2007 Simon Dorfman
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal 
 * in the Software without restriction, including without limitation the rights to 
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of 
 * the Software, and to permit persons to whom the Software is furnished to do 
 * so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all 
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES 
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR 
 * OTHER DEALINGS IN THE SOFTWARE. 
 * -----------------------------------------------------------------------
 */
 
# Confirm MediaWiki environment
if (!defined('MEDIAWIKI')) die();

# Credits
$wgExtensionCredits['other'][] = array(
    'name'=>'MarkdownSyntax',
    'author'=>'Simon Dorfman &lt;simon@yamlike.com&gt;',
    'url'=>'https://www.mediawiki.org/wiki/Extension:MarkdownSyntax',
    'description'=>'Adds support for markdown syntax.',
    'version'=>'0.2'
);
 
# Attach Hook
$wgHooks['ParserBeforeStrip'][] = 'wfProcessMarkdownSyntax';
 
/**
 * Processes any Markdown sytnax in the text.
 * Usage: $wgHooks['ParserBeforeStrip'][] = 'wfProcessMarkdownSyntax';
 * @param Parser $parser Handle to the Parser object currently processing text.
 * @param String $text The text being processed.
 */
 
# includes Michel Fortin's PHP Markdown: http://www.michelf.com/projects/php-markdown/
require_once "Michelf/Markdown.inc.php" ;
#require_once( dirname( __FILE__ ) . '/markdown.php' );

function wfProcessMarkdownSyntax($parser, &$text) {

    # Perform Markdown syntax processing on provided $text from markdown.php line 43 function
	$text = Michelf\Markdown::defaultTransform($text);
	// <a href="http://example.com/" title="Title">an example</a>

	/* After running the text through this parser, mediawiki converts <a> tags to &lt...
		So, here we convert such links to mediawiki format links so they will be properly rendered. */
		
	/*
		pattern: <a href="(.+?)"( title="(.+?)")?>(.+?)</a>
		escaped quotes and slash: <a href=\"(.+?)\"( title=\"(.+?)\")?>(.+?)<\/a>
		regexed and quoted: "/<a href=\"(.+?)\"( title=\"(.+?)\")?>(.+?)<\/a>/i"
		split php end tag: "/<a href=\"(.+?)\"( title=\"(.+?)\")?".">(.+?)<\/a>/i"
	*/
	$pattern = "/<a href=\"(.+?)\"( title=\"(.+?)\")?".">(.+?)<\/a>/i";
	$replacement = '[$1 $4]';
	
	$text = preg_replace($pattern, $replacement, $text);
	return true;
}
