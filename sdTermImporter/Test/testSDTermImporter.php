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

include '../sdTermImporter.php';

class TestSdTermImporter extends PHPUnit_Framework_TestCase
{
    public function testInitDom()
    {
        $resultDom = new SdTermImporter();
        $resultDom->initDom('termcenter.xml');

        $expectedDom = new DOMDocument();
        $expectedDom->load('result-termcenter.xml');

        $this->assertEquals($resultDom->getDom()->saveXML(), $expectedDom->saveXML());
    }

    public function testGetTopicClass()
    {
        $xmlstr = <<<XML
<entry id="6">
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="sme">
        <entry id="m&#xE1;n&#xE1;_biilastuollu\S">
            <common>
                <head pos="S">m&#xE1;n&#xE1; biilastuollu</head>
                <infl major="I" minor="g">stuollu - stuolu - stuoluide</infl>
                <orth status="main"/>
                <qa checked="true" when="20060106135554" who="risten"/>
            </common>
            <senses>
                <sense idref="6" status="main">
                    <topicClass botm="RN8120" mid="R8100" top="R"/>
                    <synonyms/>
                </sense>
            </senses>
        </entry>
    </entryref>
</entry>
XML;

        $dom = new SdTermImporter();

        $entry = new SimpleXMLElement($xmlstr);
        $result = $dom->getTopicClass($entry);
        $expectedResult = "R";

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetTopicClassLangString()
    {
        $xmlstr = <<<XML
<entry id="6">
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="sme">
        <entry id="m&#xE1;n&#xE1;_biilastuollu\S">
            <common>
                <head pos="S">m&#xE1;n&#xE1; biilastuollu</head>
                <infl major="I" minor="g">stuollu - stuolu - stuoluide</infl>
                <orth status="main"/>
                <qa checked="true" when="20060106135554" who="risten"/>
            </common>
            <senses>
                <sense idref="6" status="main">
                    <topicClass botm="RN8120" mid="R8100" top="R"/>
                    <synonyms/>
                </sense>
            </senses>
        </entry>
    </entryref>
</entry>
XML;

        $dom = new SdTermImporter();
        $dom->initSdClass('sd-class.xml');


        $entry = new SimpleXMLElement($xmlstr);
        $top = $dom->getTopicClass($entry);
        $result = $dom->getTopicClassLangString($top, "sme");

        $expectedResult = "Ekologiija ja biras";
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetHead()
    {
        $xmlstr = <<<XML
<entryref xml:lang="sme">
    <entry id="m&#xE1;n&#xE1;_biilastuollu\S">
        <common>
            <head pos="S">m&#xE1;n&#xE1; biilastuollu</head>
            <infl major="I" minor="g">stuollu - stuolu - stuoluide</infl>
            <orth status="main"/>
            <qa checked="true" when="20060106135554" who="risten"/>
        </common>
        <senses>
            <sense idref="6" status="main">
                <topicClass botm="RN8120" mid="R8100" top="R"/>
                <synonyms/>
            </sense>
        </senses>
        <changes>
            <change when="20050401134257" what="Converted from SQL" who="admin"/>
        </changes>
    </entry>
</entryref>
XML;

        $dom = new SdTermImporter();
        $entryref = new SimpleXMLElement($xmlstr);
        $result = $dom->getHead($entryref);
        $expectedResult = "máná biilastuollu";

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetQAChecked()
    {
        $xmlstr = <<<XML
<entryref xml:lang="sme">
    <entry id="m&#xE1;n&#xE1;_biilastuollu\S">
        <common>
            <head pos="S">m&#xE1;n&#xE1; biilastuollu</head>
            <infl major="I" minor="g">stuollu - stuolu - stuoluide</infl>
            <orth status="main"/>
            <qa checked="true" when="20060106135554" who="risten"/>
        </common>
        <senses>
            <sense idref="6" status="main">
                <topicClass botm="RN8120" mid="R8100" top="R"/>
                <synonyms/>
            </sense>
        </senses>
        <changes>
            <change when="20050401134257" what="Converted from SQL" who="admin"/>
        </changes>
    </entry>
</entryref>
XML;

        $dom = new SdTermImporter();
        $entryref = new SimpleXMLElement($xmlstr);
        $result = $dom->getQAChecked($entryref);
        $expectedResult = "Yes";

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetEntryRefLang()
    {
        $xmlstr = <<<XML
<entryref xml:lang="sme">
    <entry id="m&#xE1;n&#xE1;_biilastuollu\S">
        <common>
            <head pos="S">m&#xE1;n&#xE1; biilastuollu</head>
            <infl major="I" minor="g">stuollu - stuolu - stuoluide</infl>
            <orth status="main"/>
            <qa checked="true" when="20060106135554" who="risten"/>
        </common>
        <senses>
            <sense idref="6" status="main">
                <topicClass botm="RN8120" mid="R8100" top="R"/>
                <synonyms/>
            </sense>
        </senses>
        <changes>
            <change when="20050401134257" what="Converted from SQL" who="admin"/>
        </changes>
    </entry>
</entryref>
XML;

        $dom = new SdTermImporter();
        $entryref = new SimpleXMLElement($xmlstr);
        $result = $dom->getEntryRefLang($entryref);
        $expectedResult = "sme";

        $this->assertEquals($expectedResult, $result);
    }

    public function testMakePageName()
    {
        $xmlstr = <<<XML
<entry id="6">
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="sme">
        <entry id="m&#xE1;n&#xE1;_biilastuollu\S">
            <common>
                <head pos="S">m&#xE1;n&#xE1; biilastuollu</head>
                <infl major="I" minor="g">stuollu - stuolu - stuoluide</infl>
                <orth status="main"/>
                <qa checked="true" when="20060106135554" who="risten"/>
            </common>
            <senses>
                <sense idref="6" status="main">
                    <topicClass botm="RN8120" mid="R8100" top="R"/>
                    <synonyms/>
                </sense>
            </senses>
        </entry>
    </entryref>
</entry>
XML;

        $dom = new SdTermImporter();
        $dom->initSdClass('sd-class.xml');

        $entry = new SimpleXMLElement($xmlstr);
        $result = $dom->makePageName($entry, $entry->entryref[0]);
        $expectedResult = "Ekologiija ja biras:máná biilastuollu";

        $this->assertEquals($expectedResult, $result);
    }

    public function testMakeConcept()
    {
        $xmlstr = <<<XML
<entry id="6">
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="sme">
        <entry id="m&#xE1;n&#xE1;_biilastuollu\S">
            <common>
                <head pos="S">m&#xE1;n&#xE1; biilastuollu</head>
                <infl major="I" minor="g">stuollu - stuolu - stuoluide</infl>
                <orth status="main"/>
                <qa checked="true" when="20060106135554" who="risten"/>
            </common>
            <senses>
                <sense idref="6" status="main">
                    <topicClass botm="RN8120" mid="R8100" top="R"/>
                    <synonyms/>
                </sense>
            </senses>
        </entry>
    </entryref>
</entry>
XML;

        $expectedResult = <<<EOD
{{Concept
|language=se
|definition=máná biilastuollu
|explanation=
|more_info=
|sources=
|reviewed=Yes
|no picture=No
}}

EOD;

        $dom = new SdTermImporter();
        $dom->initSdClass('sd-class.xml');
        $entry = new SimpleXMLElement($xmlstr);

        $result = $dom->makeConcept($entry, $entry->entryref[0]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testMakeRelatedExpressionFromEntryRef()
    {
        $xmlstr = <<<XML
<entry id="6">
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="sme">
        <entry id="m&#xE1;n&#xE1;_biilastuollu\S">
            <common>
                <head pos="S">m&#xE1;n&#xE1; biilastuollu</head>
                <infl major="I" minor="g">stuollu - stuolu - stuoluide</infl>
                <orth status="main"/>
                <qa checked="true" when="20060106135554" who="risten"/>
            </common>
            <senses>
                <sense idref="6" status="main">
                    <topicClass botm="RN8120" mid="R8100" top="R"/>
                    <synonyms/>
                </sense>
            </senses>
        </entry>
    </entryref>
</entry>
XML;

        $expectedResult = <<<EOD
{{Related expression
|language=se
|expression=máná biilastuollu
|in_header=No
}}

EOD;

        $dom = new SdTermImporter();
        $dom->initSdClass('sd-class.xml');
        $entry = new SimpleXMLElement($xmlstr);

        $result = $dom->makeRelatedExpression($entry->entryref[0]);

        $this->assertEquals($expectedResult, $result);
    }
}

?>
