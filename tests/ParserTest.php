<?php

namespace JSKOS\RDF;

use JSKOS\Concept;
use JSKOS\Set;
use JSKOS\Resource;
use EasyRdf_Graph;

/**
 * @covers JSKOS\RDF\Parser
 */
class ParserTest extends \PHPUnit\Framework\TestCase
{
    public function exampleProvider()
    {
        $files = glob(__DIR__."/examples/*.json");
        sort($files);
        return array_map(
            function($file) { return [substr($file,0,-5)]; },
            $files
        );
    }

    /**
     * @dataProvider exampleProvider
     */
    public function testMapping($file)
    {
        $json = file_get_contents("$file.json");
        if (substr($json, 0, 1) == '[') {
            $jskos = new Set(array_map(
                function ($member) {
                    $class = Resource::guessClassFromTypes($member['type'] ?? [])
                        ?? Concept::class;
                    return new $class($member);
                },
                json_decode($json, true)
            ));            
        } else {
            $class = Resource::guessClassFromTypes($json['type'] ?? [])
                    ?? Concept::class;
            $jskos = new $class(json_decode($json, true));
        }

		# error_log($jskos->json());

        $parser = new Parser();
        $rdf = $parser->parseJSKOS($jskos);
        $got = explode("\n", $rdf->serialise('ntriples'));
        sort($got);

        if (file_exists("$file.ttl")) {
            $expect = new EasyRdf_Graph( 
                'http://example.org/', file_get_contents("$file.ttl"), 'turtle'
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
