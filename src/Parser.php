<?php declare(strict_types = 1);

namespace JSKOS\RDF;

use JSKOS\PrettyJsonSerializable;
use JSKOS\Set;
use ML\JsonLD\JsonLD;
use ML\JsonLD\NQuads;
use ML\IRI\IRI;
use ML\JsonLD\LanguageTaggedString;
use EasyRdf_Graph;

/**
 * EasyRdf parser to support JSON-LD to RDF conversion.
 *
 * Bridges PHP packages EasyRDF and json-ld.
 */
class Parser extends \EasyRdf_Parser
{
    protected $context;

    # TODO: move to jskos-php package
    private function jsonLDSerialize($jskos, $context, int $typed=0) {
        $data = $jskos->jsonLDSerialize($context);
        if ($typed && !count($data['type'] ?? [])) {
            $class = get_class($jskos);
            if (defined("$class::TYPES")) {
                $data['type'] = [$class::TYPES[0]];
            }
        }
        return $data;
    }

    /**
     * Parse JSKOS Resources to RDF.
     */
    public function parseJSKOS(PrettyJsonSerializable $jskos): EasyRdf_Graph
    {
        if (!$this->context) {
            $this->context = json_decode(file_get_contents(__DIR__.'/jskos-context.json'));
        }
        
        # TODO: put into JSKOS core package?
        if ($jskos instanceof Set) {
            $data = $jskos->map(
                function ($m) {
                    return json_encode($this->jsonLDSerialize($m,'', 1));
                }
            );
            $json = '{"@graph":['. implode(',', $data)."]}";
        } else { # TODO: throw on unparsable JSKOS
            $data = $this->jsonLDSerialize($jskos, '', 1);
            $json = json_encode($data);
        }

        $rdf = JsonLD::toRdf($json, ['expandContext' => $this->context]);
        # TODO: catch (\ML\JsonLD\Exception\JsonLdException $e) 

        $graph = new EasyRdf_Graph();
        $this->parse($graph, $rdf);

        return $graph;
    }

    /**
     * Parse Quads as returned by JsonLD.
     */
    public function parse($graph, $data, $format = 'jsonld', $baseUri = NULL) 
    {
        parent::checkParseParams($graph, $data, $format, $baseUri);

        foreach ($data as $quad) {
            $subject = (string)$quad->getSubject();

            if ('_:' === substr($subject, 0, 2)) {
                $subject = $this->remapBnode($subject);
            }

            $predicate = (string)$quad->getProperty();

            if ($quad->getObject() instanceof IRI) {
                $object = [
                    'type' => 'uri',
                    'value' => (string)$quad->getObject()
                ];

                if ('_:' === substr($object['value'], 0, 2)) {
                    $object = [
                        'type' => 'bnode',
                        'value' => $this->remapBnode($object['value'])
                    ];
                }
            } else {
                $object = [
                    'type' => 'literal',
                    'value' => $quad->getObject()->getValue()
                ];

                if ($quad->getObject() instanceof LanguageTaggedString) {
                    $object['lang'] = $quad->getObject()->getLanguage();
                } else {
                    $object['datatype'] = $quad->getObject()->getType();
                }
            }

            $this->addTriple($subject, $predicate, $object);
        }
    }
}
