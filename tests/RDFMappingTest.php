<?php

namespace JSKOS\RDF;

use Symfony\Component\Yaml\Yaml;
use JSKOS\Concept;


class RDFMappingTest extends \PHPUnit\Framework\TestCase
{
    public function testMapping()
    {
        $examples = dirname(__FILE__).'/examples';
        $rules = Yaml::parse(file_get_contents("$examples/example.yaml"));
        $mapping = new RDFMapping($rules);

        $rdf = new \EasyRdf_Graph();
        $rdf->parseFile("$examples/example.ttl");

        $foo = 'http://example.org/concept/foo';
        $bar = 'http://example.org/concept/bar';
        $jskos = new Concept(['uri'=>$foo]);
        $resource = $rdf->resource($foo);
        $mapping->apply($resource, $jskos);

        $expect = new Concept([
            'uri' => $foo,
            'altLabel' => ['en' => ['FOO','FOOO']],
            'prefLabel' => ['en' => 'foo', 'de' =>'föö'],
            'broader' => [
                new Concept(['uri' => $bar])
            ]
        ]);
        $this->assertEquals($jskos, $expect);
    }
}
