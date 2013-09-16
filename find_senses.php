<?php

// $xmlstr = <<<XML
// <terminology id="SD-terms" last-update="20120123141936" xml:lang="sme">
//     <entry id="AV-rájus\S">
//         <common>
//             <head pos="S">AV-rájus</head>
//             <infl major="III" minor="i">rájus - rádjosii - rádjosiidda</infl>
//             <orth status="main"/>
//             <qa checked="false"/>
//         </common>
//         <senses>
//             <sense idref="13054" status="main">
//                 <topicClass top="A" mid="A4500" botm="AN4500"/>
//                 <synonyms>
//                     <synonym synref="AV-latnja\S"/>
//                     <synonym synref="AV-rádju\S"/>
//                 </synonyms>
//             </sense>
//         </senses>
//         <changes>
//             <change when="20050401134257" what="Converted from SQL" who="admin"/>
//         </changes>
//     </entry>
//     <entry id="biepmohas\S">
//         <common>
//             <head pos="S">biepmohas</head>
//             <infl major="II" minor="c">biepmohas - biepmohassii - biepmohasaide</infl>
//             <orth status="main"/>
//             <qa checked="false"/>
//         </common>
//         <senses>
//             <sense idref="9153" status="syn" mainref="biebmománná\S">
//                 <topicClass top="A" mid="A2000" botm="AN2010"/>
//             </sense>
//         </senses>
//         <changes>
//             <change when="20050401134257" what="Converted from SQL" who="admin"/>
//         </changes>
//     </entry>
// </terminology>
// XML;

function find_synonyms($xml) {
    foreach ($xml->entry as $entry) {
        if (sizeof($entry->xpath('.//synonym')) > 1) {
            print $entry['id'] . "\n";
            foreach ($entry->xpath('.//synonym') as $synonym) {
                print "\t" . $synonym['synref'] . "\n";
            }
        }
    }
}

function find_senses($xml) {
    foreach ($xml->entry as $entry) {
        if (sizeof($entry->xpath('.//sense')) > 1) {
            print "sence: " . $entry['id'] . sizeof($entry->xpath('.//sense')) . "\n";
        }
    }
}

$names = array('https://victorio.uit.no/langtech/trunk/words/terms/SD-terms/src/terms-sme.xml', 'https://victorio.uit.no/langtech/trunk/words/terms/SD-terms/src/terms-nor.xml', 'https://victorio.uit.no/langtech/trunk/words/terms/SD-terms/src/terms-fin.xml', 'https://victorio.uit.no/langtech/trunk/words/terms/SD-terms/src/terms-eng.xml');

foreach ($names as $name) {
//     print $name . "\n";
    $xml = simplexml_load_file($name);

//     $xml = new SimpleXMLElement($xmlstr);
    find_senses($xml);
//     find_synonyms($xml);
}

?>
