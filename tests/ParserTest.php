<?php

namespace JSKOS\RDF;

use JSKOS\Concept;
use EasyRdf_Graph;

/**
 * @covers JSKOS\RDF\Parser
 */
class ParserTest extends \PHPUnit\Framework\TestCase
{
    public function exampleProvider()
    {
        return [
            ['example1'],
            ['example2'],
        ];
    }

    /**
     * @dataProvider exampleProvider
     */
    public function testMapping($name)
    {
        $jskos = json_decode(file_get_contents(__DIR__."/$name.json"), true);
        $jskos = new Concept($jskos);

		# error_log($jskos->json());

        $parser = new Parser();
        $rdf = $parser->parseJSKOS($jskos);
        $got = explode("\n", $rdf->serialise('ntriples'));
        sort($got);

        $rdffile = __DIR__."/$name.ttl";
        if (file_exists($rdffile)) {
            $expect = new EasyRdf_Graph( 
                'http://example.org/', file_get_contents($rdffile), 'turtle'
            );
            $expect = explode("\n", $expect->serialise('ntriples'));
            sort($expect);
            $this->assertEquals($expect, $got);
        } else {
            error_log($rdf->serialise('turtle'));
        }

		# TODO: test round-tripping
    }
} 
