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
</entry>
XML;

        $dom = new SdTermImporter();
        $dom->initSdClass('sd-class.xml');


        $entry = new SimpleXMLElement($xmlstr);
        $top = $dom->getTopicClass($entry);
        $result = $dom->getTopicClassLangString($top);

        $expectedResult = "Ekologiija_ja_biras";
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetTopicClassLangStringWithSpaceAtEndOfString()
    {
        $xmlstr = <<<XML
<entry id="6">
    <topicClass top="T" mid="R8100" botm="RN8120"/>
</entry>
XML;

        $dom = new SdTermImporter();
        $dom->initSdClass('sd-class.xml');


        $entry = new SimpleXMLElement($xmlstr);
        $top = $dom->getTopicClass($entry);
        $result = $dom->getTopicClassLangString($top);

        $expectedResult = "Guolástus";
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetHead()
    {
        $xmlstr = <<<XML
<entryref xml:lang="sme">
    <entry id="máná_biilastuollu\S">
        <common>
            <head pos="S">máná biilastuollu</head>
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
    <entry id="máná_biilastuollu\S">
        <common>
            <head pos="S">máná biilastuollu</head>
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
    <entry id="máná_biilastuollu\S">
        <common>
            <head pos="S">máná biilastuollu</head>
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

    public function testMakeConceptPageName()
    {
        $xmlstr = <<<XML
<entry id="6">
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="sme">
        <entry id="máná_biilastuollu\S">
            <common>
                <head pos="S">máná biilastuollu</head>
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
        $result = $dom->makeConceptPageName($entry);
        $expectedResult = "Ekologiija_ja_biras:máná biilastuollu";

        $this->assertEquals($expectedResult, $result);
    }

    public function testMakeConcept()
    {
        $xmlstr = <<<XML
<entry id="6">
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="sme">
        <entry id="máná_biilastuollu\S">
            <common>
                <head pos="S">máná biilastuollu</head>
                <infl major="I" minor="g">stuollu - stuolu - stuoluide</infl>
                <orth status="main"/>
                <qa checked="true" when="20060106135554" who="risten"/>
            </common>
            <senses>
                <sense idref="6" status="main">
                    <topicClass botm="RN8120" mid="R8100" top="R"/>
                    <def>abcde</def>
                    <synonyms/>
                </sense>
            </senses>
        </entry>
    </entryref>
    <entryref xml:lang="nor">
        <entry id="barnesete\S">
            <common>
                <head pos="S">barnesete</head>
                <orth status="main"/>
                <qa checked="true" when="20050629145006" who="ingam"/>
            </common>
            <senses>
                <sense idref="6" status="main">
                    <topicClass botm="RN8120" mid="R8100" top="R"/>
                    <synonyms/>
                </sense>
            </senses>
            <changes>
                <change when="20050401134301" what="Converted from SQL" who="admin"/>
            </changes>
        </entry>
    </entryref>
</entry>
XML;

        $expectedResult = <<<EOD
{{Concept
|definition_se=máná biilastuollu
|explanation_se=abcde
|more_info_se=
|reviewed_se=Yes
|definition_nb=barnesete
|explanation_nb=
|more_info_nb=
|reviewed_nb=Yes
|sources=
|category=
|no picture=No
}}

EOD;

        $dom = new SdTermImporter();
        $dom->initSdClass('sd-class.xml');
        $entry = new SimpleXMLElement($xmlstr);

        $result = $dom->makeConcept($entry);

        $this->assertEquals($expectedResult, $result);
    }

    public function testMakeRelatedExpressionFromEntryRef()
    {
        $xmlstr = <<<XML
<entryref xml:lang="sme">
    <entry id="máná_biilastuollu\S">
        <common>
            <head pos="S">máná biilastuollu</head>
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
        $entryref = new SimpleXMLElement($xmlstr);

        $result = $dom->makeRelatedExpressionFromEntryRef($entryref);

        $this->assertEquals($expectedResult, $result);
    }

    public function testFindSynonyms()
    {
        $xmlstr = <<<XML
<entry id="6">
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="sme">
        <entry id="máná_biilastuollu\S">
            <common>
                <head pos="S">máná biilastuollu</head>
                <infl major="I" minor="g">stuollu - stuolu - stuoluide</infl>
                <orth status="main"/>
                <qa checked="true" when="20060106135554" who="risten"/>
            </common>
            <senses>
                <sense idref="6" status="main">
                    <topicClass botm="RN8120" mid="R8100" top="R"/>
                    <synonyms>
                        <synonym synref="máksimuš\S"/>
                        <synonym synref="čujuhus\S"/>
                    </synonyms>
                </sense>
            </senses>
        </entry>
    </entryref>
    <entryref xml:lang="nor">
        <entry>
            <senses>
                <sense idref="6">
                    <synonyms>
                        <synonym synref="abba\S"/>
                    </synonyms>
                </sense>
            </senses>
        </entry>
    </entryref>
</entry>
XML;

        $expectedResult = array(new SimpleXMLElement('<synonym synref="máksimuš\S"/>'), new SimpleXMLElement('<synonym synref="čujuhus\S"/>'));

        $dom = new SdTermImporter();
        $entry = new SimpleXMLElement($xmlstr);

        $result = $dom->findSynonyms($entry, "sme");

        $this->assertEquals($expectedResult, $result);
    }

    function testMakeRelatedExpressionFromSynonymEntry() {
        $synonym = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<terminology id="SD-terms" last-update="20120123141936" xml:lang="sme">
    <entry id="máksimuš\S">
        <common>
            <head pos="S">máksimuš</head>
            <orth status="main"/>
            <qa checked="false"/>
        </common>
        <senses>
            <sense idref="19034" mainref="máksámuš\S" status="syn">
                <topicClass botm="" mid="D0000" top="D"/>
            </sense>
        </senses>
        <changes>
            <change what="Added entry by inclusion from 'máksámuš\S'" when="20061107123635" who="ingam"/>
        </changes>
    </entry>
        <entry id="máksineiseváldi\S">
        <common>
            <head pos="S">máksineiseváldi</head>
            <orth status="main"/>
            <qa checked="false"/>
        </common>
        <senses>
            <sense idref="19035" status="main">
                <topicClass botm="" mid="A0000" top="A"/>
            </sense>
        </senses>
        <changes>
            <change what="Created the entry" when="20061107123822" who="ingam"/>
        </changes>
    </entry>
</terminology>
XML;

        $expectedResult = <<<EOD
{{Related expression
|language=se
|expression=máksimuš
|in_header=No
}}

EOD;
        $dom = new SdTermImporter();
        $dom->initSdClass('sd-class.xml');
        $dom->initSynonym($synonym, "sme");

        $result = $dom->makeRelatedExpressionFromSynonymEntry("máksimuš\S", "sme");

        $this->assertEquals($expectedResult, $result);
    }

    public function testFindDefWhenDefExists()
    {
        $xmlstr = <<<XML
<entry id="6">
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="sme">
        <entry id="máná_biilastuollu\S">
            <senses>
                <sense idref="6" status="main">
                    <topicClass botm="RN8120" mid="R8100" top="R"/>
                    <def>abcde</def>
                </sense>
            </senses>
        </entry>
    </entryref>
    <entryref xml:lang="nor">
        <entry>
            <senses>
                <sense idref="6">
                    <def>fghij</def>
                </sense>
            </senses>
        </entry>
    </entryref>
</entry>
XML;

        $expectedResult = 'abcde';

        $dom = new SdTermImporter();
        $entry = new SimpleXMLElement($xmlstr);

        $result = $dom->findDef($entry, "sme");

        $this->assertEquals($expectedResult, $result);
    }

    public function testFindDefWhenDefDoesNotExist()
    {
        $xmlstr = <<<XML
<entry id="6">
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="sme">
        <entry id="máná_biilastuollu\S">
            <senses>
                <sense idref="6" status="main">
                    <topicClass botm="RN8120" mid="R8100" top="R"/>
                </sense>
            </senses>
        </entry>
    </entryref>
    <entryref xml:lang="nor">
        <entry>
            <senses>
                <sense idref="6">
                    <def>fghij</def>
                </sense>
            </senses>
        </entry>
    </entryref>
</entry>
XML;

        $expectedResult = '';

        $dom = new SdTermImporter();
        $entry = new SimpleXMLElement($xmlstr);

        $result = $dom->findDef($entry, "sme");

        $this->assertEquals($expectedResult, $result);
    }

    public function testMakeConceptPageContent()
    {

        $xmlstr = <<<XML
<entry id="6">
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="sme">
        <entry id="máná_biilastuollu\S">
            <common>
                <head pos="S">máná biilastuollu</head>
                <infl major="I" minor="g">stuollu - stuolu - stuoluide</infl>
                <orth status="main"/>
                <qa checked="true" when="20060106135554" who="risten"/>
            </common>
            <senses>
                <sense idref="6" status="main">
                    <topicClass botm="RN8120" mid="R8100" top="R"/>
                    <def>abcde</def>
                    <synonyms>
                        <synonym synref="máksimuš\S"/>
                    </synonyms>
                </sense>
            </senses>
        </entry>
    </entryref>
</entry>
XML;

        $synonym = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<terminology id="SD-terms" last-update="20120123141936" xml:lang="sme">
    <entry id="máksimuš\S">
        <common>
            <head pos="S">máksimuš</head>
            <orth status="main"/>
            <qa checked="false"/>
        </common>
        <senses>
            <sense idref="19034" mainref="máksámuš\S" status="syn">
                <topicClass botm="" mid="D0000" top="D"/>
            </sense>
        </senses>
        <changes>
            <change what="Added entry by inclusion from 'máksámuš\S'" when="20061107123635" who="ingam"/>
        </changes>
    </entry>
        <entry id="máksineiseváldi\S">
        <common>
            <head pos="S">máksineiseváldi</head>
            <orth status="main"/>
            <qa checked="false"/>
        </common>
        <senses>
            <sense idref="19035" status="main">
                <topicClass botm="" mid="A0000" top="A"/>
            </sense>
        </senses>
        <changes>
            <change what="Created the entry" when="20061107123822" who="ingam"/>
        </changes>
    </entry>
</terminology>
XML;

        $expectedResult = <<<EOF
{{Concept
|definition_se=máná biilastuollu
|explanation_se=abcde
|more_info_se=
|reviewed_se=Yes
|sources=
|category=
|no picture=No
}}
{{Related expression
|language=se
|expression=máksimuš
|in_header=No
}}
{{Related expression
|language=se
|expression=máná biilastuollu
|in_header=No
}}

EOF;

        $dom = new SdTermImporter();
        $dom->initSdClass('sd-class.xml');
        $dom->initSynonym($synonym, "sme");

        $entry = new SimpleXMLElement($xmlstr);

        $result = $dom->makeConceptPageContent($entry);

        $this->assertEquals($expectedResult, $result);
    }

    public function testPos()
    {
        $origPos = array('A', 'ABBR', 'Adv', 'PP', 'Pron', 'S', 'V');
        $expectedResult = array('A', 'N', 'Adv', 'N', 'Pron', 'N', 'V');

        $dom = new SdTermImporter();
        $result = array();
        foreach ($origPos as $pos) {
            $result[] = $dom->getPos($pos);
        }

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetMainEntryRefSme()
    {
        $xmlstr = <<<XML
<entry>
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="sme">
    </entryref>
    <entryref xml:lang="nor">
    </entryref>
</entry>
XML;

        $dom = new SdTermImporter();
        $dom->initSdClass('sd-class.xml');

        $entry = new SimpleXMLElement($xmlstr);
        $result = $dom->getMainEntryref($entry);
        $expectedResult = $entry->xpath('.//entryref[@xml:lang="sme"]');;

        $this->assertEquals($expectedResult[0], $result);
    }

    public function testGetMainEntryRefNor()
    {
        $xmlstr = <<<XML
<entry>
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="fin">
    </entryref>
    <entryref xml:lang="nor">
    </entryref>
</entry>
XML;

        $dom = new SdTermImporter();
        $dom->initSdClass('sd-class.xml');

        $entry = new SimpleXMLElement($xmlstr);
        $result = $dom->getMainEntryref($entry);
        $expectedResult = $entry->xpath('.//entryref[@xml:lang="nor"]');;

        $this->assertEquals($expectedResult[0], $result);
    }

    public function testGetMainEntryRefEmpty()
    {
        $xmlstr = <<<XML
<entry>
    <topicClass top="R" mid="R8100" botm="RN8120"/>
    <entryref xml:lang="">
    </entryref>
</entry>
XML;

        $dom = new SdTermImporter();
        $dom->initSdClass('sd-class.xml');

        $entry = new SimpleXMLElement($xmlstr);
        $this->setExpectedException('SdTermImporterException');
        $result = $dom->getMainEntryref($entry);

     }

}
?>
