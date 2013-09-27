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
class SdTermImporterException extends Exception
{
}

class SdTermImporter
{
    function __construct()
    {
        $this->langArray["eng"] = "en";
        $this->langArray["fin"] = "fi";
        $this->langArray["lat"] = "lat";
        $this->langArray["nor"] = "nb";
        $this->langArray["sma"] = "sma";
        $this->langArray["sme"] = "se";
        $this->langArray["smj"] = "smj";
        $this->langArray["smn"] = "smn";
        $this->langArray["sms"] = "sms";
        $this->langArray["swe"] = "sv";

        $this->posArray["A"] = "A";
        $this->posArray["ABBR"] = "N";
        $this->posArray["Adv"] = "Adv";
        $this->posArray["PP"] = "N";
        $this->posArray["Pron"] = "Pron";
        $this->posArray["S"] = "N";
        $this->posArray["V"] = "V";

        $this->langPriority = array("sme", "nor", "smj", "sma", "fin");
    }

    function getPos($origPos)
    {
        return $this->posArray[$origPos];
    }

    function initDom($url)
    {
        $this->dom = new DOMDocument();
        $this->dom->load($url);
        // substitute xincludes, takes forever and a day
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
        $top = $entry->topicClass["top"];

        if ($top != "Z") {
            return $top;
        } else {
            throw new SdTermImporterException("Dagligtale\n" . $entry->asXML());
        }
    }

    /*
     * Lang is hard coded to sme because that is what is used in
     * the saami termwiki
     */
    function getTopicClassLangString($top)
    {
        if (sizeof($this->sdClass->xpath('//macro[@id="' . $top . '"]/label[@xml:lang="sme"]/text()')) > 0 ) {
            $label = $this->sdClass->xpath('//macro[@id="' . $top . '"]/label[@xml:lang="sme"]/text()');

            return str_replace(" ", "_", trim((string) $label[0]));
        } else {
            throw new SdTermImporterException("No TopicClassLangString for: " . $top);
        }

    }

    function getHead($entryref)
    {
        if (sizeof($entryref->xpath('.//head/text()')) > 0) {
            $head = $entryref->xpath('.//head/text()');
            return trim((string) $head[0]);
        } else {
            throw new SdTermImporterException('head not found\n' . $entryref->asXML());
        }
    }

    function getEntryRefLang($entryref)
    {
        return trim((string) $entryref->attributes('xml', TRUE)->lang);
    }

    function getQAChecked($entryref)
    {
        if (sizeof($entryref->xpath('.//qa["checked"]')) > 0) {
            $qa = $entryref->xpath('.//qa["checked"]');
            if ((string) $qa[0]['checked'] === 'true') {
                return 'Yes';
            } else {
                return 'No';
            }
        } else {
            throw new SdTermImporterException('qa element not found\n' .
            $entryref->xpath("..")->asXML());
        }
    }

    /*
     * Most entries contain sme entryref, so sme is the first lang to search
     * for. Next comes nor, smj, sma, fin.
     * Look for entryrefs in this order.
     * The first entryref found fitting this order, is used to make the
     * concept page name
     */
    function getMainEntryref($entry)
    {
        foreach($this->langPriority as $lang) {
            $entryref = $entry->xpath('.//entryref[@xml:lang="' . $lang . '"]');
            if (sizeof($entryref) > 0) {
                return $entryref[0];
            }
        }

        throw new SdTermImporterException('None of the langs in langPriority are found');
    }

    /*
     * Always fetch the sami category string
     */
    function makeConceptPageName($entry)
    {
        $category = $this->getTopicClassLangString(
            $this->getTopicClass($entry),
            "sme");

        $head = $this->getHead($this->getMainEntryref($entry));

        return $category . ":" . $head;
    }

    function makeConcept($entry)
    {
        $result = "{{Concept\n";
        foreach ($entry->entryref as $entryref) {
            $lang = $this->langArray[$this->getEntryRefLang($entryref)];
            $result = $result .
            "|definition_" . $lang . "=" . $this->getHead($entryref) . "\n" .
            "|explanation_" . $lang . "=" . $this->findDef($entry, $this->getEntryRefLang($entryref)) . "\n" .
            "|more_info_" . $lang . "=" . "\n" .
            "|reviewed_" . $lang . "=" . $this->getQAChecked($entryref) . "\n";
        }

        $result = $result .
        "|sources=" . "\n" .
        "|category=" . "\n" .
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

    function makeRelatedExpressionFromSynonymEntry($synref, $lang)
    {
        $entry = $this->synArray[$lang]->xpath('//entry[@id="' . $synref . '"]');

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
            return trim( (string) $def[0]);
        } else {
            return "";
        }
    }

    function makeConceptPageContent($entry)
    {
        $result = "";
        $result = $result . $this->makeConcept($entry);

        foreach($entry->entryref as $entryref) {
            $lang = $this->getEntryRefLang($entryref);
            foreach ($this->findSynonyms($entry, $lang) as $synonym) {
                $result = $result . $this->makeRelatedExpressionFromSynonymEntry($synonym['synref'], $lang);
            }
        }

        foreach ($entry->entryref as $entryref) {
            $result = $result . $this->makeRelatedExpressionFromEntryRef($entryref);
        }

        return $result;
    }
}

?>
