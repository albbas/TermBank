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

class SdTermImporter
{
    function __construct()
    {
        $this->langArray["sme"] = "se";
        $this->langArray["fin"] = "fi";
        $this->langArray["nor"] = "nb";
        $this->langArray["swe"] = "sv";
    }

    function initDom($url)
    {
        $this->dom = new DOMDocument();
        $this->dom->load($url);
        // substitute xincludes
        $this->dom->xinclude();

    }

    function getDom()
    {
        return $this->dom;
    }

    function initSdClass($url)
    {
        $this->sdClass = simplexml_load_file($url);
    }

    function initSynonym($xmlstr, $lang)
    {
        $this->synArray[$lang] = new SimpleXMLElement($xmlstr);
    }

    function initSynonymUrl($url, $lang)
    {
        $this->synArray[$lang] = simplexml_load_file($url);
    }

    function getTopicClass($entry)
    {
        return $entry->topicClass["top"];
    }

    /*
     * Lang is hard coded to sme because that is what is used in
     * the saami termwiki
     */
    function getTopicClassLangString($top)
    {
        return (string) $this->sdClass->xpath('//macro[@id="' . $top . '"]/label[@xml:lang="sme"]/text()')[0];
    }

    function getHead($entryref)
    {
        return $entryref->xpath('.//head/text()')[0];
    }

    function getEntryRefLang($entryref)
    {
        return (string) $entryref->attributes('xml', TRUE)->lang;
    }

    function getQAChecked($entryref)
    {
        if ((string) $entryref->xpath('.//qa["checked"]')[0]['checked'] === 'true') {
            return 'Yes';
        } else {
            return 'No';
        }
    }

    function makePageName($entry, $entryref)
    {
        return $this->getTopicClassLangString(
            $this->getTopicClass($entry),
            $this->getEntryRefLang($entryref)
            ) . ":" . $this->getHead($entryref);
    }

    function makeConcept($entry, $entryref)
    {
        $result = "{{Concept\n" .
        "|language=" . $this->langArray[$this->getEntryRefLang($entryref)] . "\n" .
        "|definition=" . $this->getHead($entryref) . "\n" .
        "|explanation=" . $this->findDef($entry, $this->getEntryRefLang($entryref)) . "\n" .
        "|more_info=" . "\n" .
        "|sources=" . "\n" .
        "|reviewed=" . $this->getQAChecked($entryref) . "\n" .
        "|no picture=No\n" .
        "}}\n";

        return $result;
    }

    function makeRelatedExpressionFromEntryRef($entryref)
    {
        $result = "{{Related expression\n" .
        "|language=" . $this->langArray[$this->getEntryRefLang($entryref)] . "\n" .
        "|expression=" . $this->getHead($entryref) . "\n" .
        "|in_header=No" . "\n" .
        "}}\n";

        return $result;
    }

    /*
     * entry is an entry from termcenter
     * returns an array of synonym elements
     * from an entryref with lang
     * from a sense with idref identical to the entry id
     */
    function findSynonyms($entry, $lang)
    {
        $id = $entry['id'];
        $entryref = $entry->xpath('.//entryref[@xml:lang="' . $lang . '"]');

        return $entryref[0]->xpath('.//sense[@idref="' . $id . '"]//synonym');
    }

    function makeRelatedExpressionFromSynonymEntry($synref, $lang, $xml)
    {
        $entry = $xml->xpath('//entry[@id="' . $synref . '"]');

        if (count($entry) === 1) {
            $result = "{{Related expression\n" .
            "|language=" . $this->langArray[$lang] . "\n" .
            "|expression=" . $this->getHead($entry[0]) . "\n" .
            "|in_header=No" . "\n" .
            "}}\n";

            return $result;
        }
    }

    function findDef($entry, $lang)
    {
        $id = $entry['id'];
        $entryref = $entry->xpath('.//entryref[@xml:lang="' . $lang . '"]');
        $def = $entryref[0]->xpath('.//sense[@idref="' . $id . '"]/def/text()');
        if ($def) {
            return $def[0];
        } else {
            return "";
        }
    }

    function makePageContent($entry, $entryref)
    {
        $result = "";
        $result = $result . $this->makeConcept($entry, $entryref);
        $lang = $this->getEntryRefLang($entryref);
        foreach ($this->findSynonyms($entry, $lang) as $synonym) {
            $result = $result . $this->makeRelatedExpressionFromSynonymEntry($synonym['synref'], $lang, $this->synArray[$lang]);
        }
        foreach ($entry->xpath('./entryref') as $eref) {
            $result = $result . $this->makeRelatedExpressionFromEntryRef($eref);
        }

        return $result;
    }
}

?>
