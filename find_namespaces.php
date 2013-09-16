<?php

function print_all_namespaces($xml) {
    $counter = 1200;

    foreach ($xml->macro as $macro) {
        print "// " . $macro['id'] . "\n";
        foreach ($macro->label as $label) {
            $label = str_replace(" ", "_", $label);
            print "wfAddTermNamespace( " . $counter . ", '" . $label . "' );\n";
            $counter++;
            print "wfAddTermNamespace( " . $counter . ", '" . $label . "_talk' );\n";
            $counter++;
        }

    }
}

$xml = simplexml_load_file('https://victorio.uit.no/langtech/branches/Risten_1-5-x/termdb/src/db-colls/classes/SD-class/SD-class.xml');

print_all_namespaces($xml);

?>
