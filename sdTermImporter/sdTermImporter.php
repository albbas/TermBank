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

    function getTopicClass($entry)
    {
        return $entry->topicClass["top"];
    }

    function getTopicClassLangString($top, $lang)
    {
        return $this->sdClass->xpath('//macro[@id="' . $top . '"]/label[@xml:lang="' . $lang . '"]/text()')[0];
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
        "|explanation=" . "\n" .
        "|more_info=" . "\n" .
        "|sources=" . "\n" .
        "|reviewed=" . $this->getQAChecked($entryref) . "\n" .
        "|no picture=No\n" .
        "}}\n";

        return $result;
    }

    function makeRelatedExpression($entry, $entryref)
    {
        $result = "{{Related expression\n" .
        "|language=" . $this->langArray[$this->getEntryRefLang($entryref)] . "\n" .
        "|expression=" . $this->getHead($entryref) . "\n" .
        "|in_header=No" . "\n" .
        "}}\n";

        return $result;
    }
}

?>
