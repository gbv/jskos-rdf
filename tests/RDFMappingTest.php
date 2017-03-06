<?php

namespace JSKOS\RDF;

use JSKOS\Concept;

class RDFMappingTest extends \PHPUnit\Framework\TestCase
{
    public static function yamlFile($file) {
        return \Symfony\Component\Yaml\Yaml::parse(file_get_contents(__DIR__."/$file"));
    }

    public function testMapping()
    {
        $mapper = new RDFMapping(static::yamlFile('sampleRules.yaml'));

        $rdf = new \EasyRdf_Graph();
        $rdf->parseFile(__DIR__.'/sampleRDF.ttl');

        $jskos = new Concept(['uri'=>'http://example.org/c0']);
        $mapper->apply($rdf->resource($jskos->uri), $jskos);

        $expect = new Concept(static::yamlFile('sampleConcept.yaml'));
        foreach ($expect->broader as &$b) {
            $b = new Concept($b);
        }

        $this->assertEquals($expect, $jskos);
    }
}
