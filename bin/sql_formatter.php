#!/usr/bin/php
<?php
if(php_sapi_name() !== "cli") {
	echo "<p>Run this php script from the command line to see CLI syntax highlighting and formatting.  It support Unix pipes or command line argument style.</p>";
	echo "<pre><code>php bin/sql_formatter.php \"SELECT * FROM MyTable WHERE (id>5 AND \\`name\\` LIKE \\&quot;testing\\&quot;);\"</code></pre>";
	echo "<pre><code>echo \"SELECT * FROM MyTable WHERE (id>5 AND \\`name\\` LIKE \\&quot;testing\\&quot;);\" | php examples/cli.php</code></pre>";
	exit;
}

if(isset($argv[1])) {
	$sql = $argv[1];
}
else {
	$sql = stream_get_contents(fopen("php://stdin", "r"));
}

require_once(__DIR__.'/../lib/SqlFormatter.php');

/**
 * Returns true if the stream supports colorization.
 *
 * Colorization is disabled if not supported by the stream:
 *
 *  -  Windows without Ansicon and ConEmu
 *  -  non tty consoles
 *
 * @return bool    true if the stream supports colorization, false otherwise
 * @link https://github.com/symfony/symfony/blob/v2.4.6/src/Symfony/Component/Console/Output/StreamOutput.php#L97
 */
function hasColorSupport() {
	// @codeCoverageIgnoreStart
	if (DIRECTORY_SEPARATOR == '\\') {
		return false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI');
	}

	return function_exists('posix_isatty') && @posix_isatty(STDOUT);
	// @codeCoverageIgnoreEnd
}

$highlight = hasColorSupport();

echo SqlFormatter::format($sql, $highlight);
