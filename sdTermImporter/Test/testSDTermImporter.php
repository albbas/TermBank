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

class TestSdTermImporter extends PHPUnit_Framework_TestCase
{
    public function testTestXinclude()
    {
        $resultDom = new SdTermImporter('termcenter.xml');

        $expectedDom = new DOMDocument();
        $expectedDom->load('result-termcenter.xml');

        $this->assertEquals($resultDom->getDom()->saveXML(), $expectedDom->saveXML());
    }

    public function testGetTopicClass()
    {
        $dom = new SdTermImporter('termcenter.xml');
        $result = $dom->getTopicClass("6");
        $expectedResult = "R";

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetTopicClassLangString()
    {
        $dom = new SdTermImporter('termcenter.xml');
        $dom->initSdClass('sd-class.xml');
        $top = $dom->getTopicClass("6");
        $result = $dom->getTopicClassLangString($top, "sme");

        $expectedResult = "Ekologiija ja biras";
        $this->assertEquals($expectedResult, $result);
    }

    public function testGet
}

class SdTermImporter
{
    function __construct($url)
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

    function getTopicClass($entryid)
    {
        $xml = new SimpleXMLElement($this->dom->saveXML());

        return $xml->xpath('//entry[@id="' . $entryid . '"]/topicClass["top"]')[0]['top'];
    }

    function getTopicClassLangString($top, $lang)
    {
        return $this->sdClass->xpath('//macro[@id="' . $top . '"]/label[@xml:lang="' . $lang . '"]/text()')[0];
    }
}

?>
