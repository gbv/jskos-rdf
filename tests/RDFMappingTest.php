<?php

namespace JSKOS\RDF;

use JSKOS\Concept;

/**
 * @covers JSKOS\RDF\RDFMapping
 */
class RDFMappingTest extends \PHPUnit\Framework\TestCase
{
    public static function jsonFile($file) {
        return json_decode(file_get_contents(__DIR__."/$file"), true);
    }

    public function testMapping()
    {
        $mapper = new RDFMapping(static::jsonFile('sampleRules.json'));

        $rdf = new \EasyRdf_Graph();
        $rdf->parseFile(__DIR__.'/sampleRDF.ttl');

        $jskos = new Concept(['uri'=>'http://example.org/c0']);
        $mapper->apply($rdf->resource($jskos->uri), $jskos);

        $expect = new Concept(static::jsonFile('sampleConcept.json'));
        foreach ($expect->broader as &$b) {
            $b = new Concept($b);
        }

        $this->assertEquals($expect, $jskos);
    }
}
