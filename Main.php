<?php
require_once "Parser.php";
// Main execution
try {

    $startTime = microtime(true);
    $parser = new Parser();
    $options = getopt("", ["file:", "unique-combinations:"]);
    //if file not found in the required path if throw not found exception
    if (!isset($options['file'])) {
        throw new InvalidArgumentException("File path is required. Usage: php parser.php --file <file_path>");
    }
    //set output file directory
    $parser->setOutputFile('export/' . $options['unique-combinations']);
    // Parse the file
    $parser->parseFile($options['file']);
    $endTime = microtime(true);
    $elapsedTime = $endTime - $startTime;
    echo "Parsing completed successfully. {$elapsedTime}s\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
