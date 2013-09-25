<?php

/*
 * <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2013  Børre Gaup <albbas@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
include 'sdTermImporter.php';

    $dom = new SdTermImporter();

    $langs = array("eng", "fin", "lat", "nor", "sma", "sme", "smj", "smn", "sms", "swe");

    foreach ($langs as $lang) {
        print 'file:///home/boerre/gtsvn//words/terms/SD-terms/newsrc/terms-' . $lang . ".xml" . "\n";
        $dom->initSynonymUrl('file:///home/boerre/gtsvn//words/terms/SD-terms/newsrc/terms-' . $lang . ".xml", $lang);
    }

    print "initDom\n";
    $dom->initDom('file:///home/boerre/gtsvn//words/terms/SD-terms/newsrc/termcenter.xml');
    print "initSdClass\n";
    $dom->initSdClass('https://victorio.uit.no/langtech/branches/Risten_1-5-x/termdb/src/db-colls/classes/SD-class/SD-class.xml');

    $termcenter = new SimpleXMLElement($dom->getDom()->saveXML());

    $counter = 1;
    foreach ($termcenter->entry as $entry) {
        $counter++;
        try {
            $dom->makeConceptPageName($entry) . "\n";
            $dom->makeConceptPageContent($entry). "\n\n\n";
        } catch (Exception $e) {
            print "Exception: " . $e->getMessage() . "\n";
            print $entry->asXML() . "\n";
        }
    }
    print $counter . "\n";
?>
