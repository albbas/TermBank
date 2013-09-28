<?php

/*
 * Import SD-terms to Termwiki
 * Copyright (C) 2013  Børre Gaup <borre.gaup@uit.no>
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

// Standard boilerplate to define $IP
if ( getenv( 'MW_INSTALL_PATH' ) !== false ) {
        $IP = getenv( 'MW_INSTALL_PATH' );
} else {
        $dir = dirname( __FILE__ ); $IP = "$dir/../../..";
}
require_once( "$IP/maintenance/Maintenance.php" );

class TBImportExternalDatabase extends Maintenance {

    public function __construct() {
        parent::__construct();
        $this->mDescription = '...';
    }

    public function execute() {

        $dom = new SdTermImporter();

        $langs = array("eng", "fin", "lat", "nor", "sma", "sme", "smj", "smn", "sms", "swe");

        foreach ($langs as $lang) {
            print 'terms-' . $lang . ".xml" . "\n";
            $dom->initSynonymUrl('terms-' . $lang . ".xml", $lang);
        }

        print "initDom\n";
        $dom->initDom('termcenter.xml');
        print "initSdClass\n";
        $dom->initSdClass('https://victorio.uit.no/langtech/branches/Risten_1-5-x/termdb/src/db-colls/classes/SD-class/SD-class.xml');

        $termcenter = new SimpleXMLElement($dom->getDom()->saveXML());

        $counter = 1;
        foreach ($termcenter->entry as $entry) {
            $counter++;
            try {
                $namespace =
                    $dom->getTopicClassLangString(
                        $dom->getTopicClass($entry),
                        "sme");

                global $wgContLang;
                $namespaceId = $wgContLang->getNsIndex($namespace);

                $title = Title::makeTitleSafe(
                            $namespaceId,
                            $dom->getHead($dom->getMainEntryref($entry)));
                $content = $dom->makeConceptPageContent($entry);
                $this->insert($title, $content);
            } catch (Exception $e) {
                print "Exception: " . $e->getMessage() . "\n";
                print $entry->asXML() . "\n";
            }
        }
        print $counter . "\n";
    }

    protected function insert(Title $title, $content)
    {
        $user = User::newFromName('SD-term importer', false);
        $page = new WikiPage($title);
        $page->doEdit($content, 'SD-term importer', 0, false, $user);
    }
}

$maintClass = 'TBImportExternalDatabase';
require_once( DO_MAINTENANCE );
?>
