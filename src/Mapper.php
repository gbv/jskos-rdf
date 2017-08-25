<?php declare(strict_types = 1);

namespace JSKOS\RDF;

use JSKOS\Resource;

/**
 * Mapper maps RDF data to JSKOS based on a set of mapping rules.
 *
 * The rules define which RDF properties will be used to fill which JSKOS
 * fields. The rules further include two special keys:
 *
 * `_ns` provides a list of RDF prefixes and namespace. Namespaces are
 * applied *globally* for all mapping instances.
 *
 * `_defaultLanguage` defines the default language ("und" if not set).
 * This field can be also be used at individual mappings.
 *
 * RDF data must be provided as EasyRdf_Resource.
 *
 * @license LGPL
 * @author Jakob Voß
 */
class Mapper
{
    /**
     * The mapping rules.
     */
    protected $rules;

    /**
     * Default language for literals without language tag.
     */
    protected $defaultLanguage;

    /**
     * Allowed JSKOS class names
     */
    public static $JSKOSClasses = [
        'Concept',
        'ConceptScheme',
        'ConceptType',
        'ConceptBundle',
        'Concordance',
        'Mapping'
    ];

    /**
     * Create a new mapping based on rules.
     */
    public function __construct(array $rules = null)
    {
        if ($rules === null) {
            $rules = json_decode(file_get_contents(__DIR__."/rdf2jskos.json"), true);
        }

        if (isset($rules['_ns'])) {
            foreach ($rules['_ns'] as $prefix => $namespace) {
                # TODO: warn if prefix is already defined with different namespace!
                \EasyRdf_Namespace::set($prefix, $namespace);
            }
        }

        if (isset($rules['_defaultLanguage'])) {
            $this->defaultLanguage = $rules['_defaultLanguage'];
        } else {
            $this->defaultLanguage = 'und';
        }

        foreach ($rules as $field => $config) {
            if (substr($field, 0, 1) != '_') {
                $this->rules[$field] = $config;
            }
        }
    }

    /**
     * Apply mapping via extraction of data from an RDF resource and add
     * resulting data to a JSKOS Resource.
     *
     * @param EasyRdf_Resource $rdf
     * @param PrettyJsonSerializable $jskos
     */
    public function applyAtResource(\EasyRdf_Resource $rdf, Resource $jskos)
    {
        #error_log($rdf->getGraph()->dump('text'));
        #error_log(print_r($this->rules,1));

        foreach ($this->rules as $property => $mapping) {
            $type = $mapping['type'];
            if (isset($mapping['jskos']) && in_array($mapping['jskos'], static::$JSKOSClasses)) {
                $class = '\JSKOS\\'.$mapping['jskos'];
            } else {
                $class = null;
            }

            foreach ($mapping['properties'] as $rdfProperty) {
                if ($type == 'URI') {
                    foreach (static::getURIs($rdf, $rdfProperty) as $uri) {
                        if (isset($class)) {
                            $uri = new $class(['uri'=>$uri]);
                        }
                        if (isset($mapping['unique'])) {
                            $jskos->$property = $uri;
                        } else {
                            if (empty($jskos->$property)) {
                                $jskos->$property = [];
                            }
                            $jskos->$property[] = $uri;
                        }
                    }
                } elseif ($type == 'literal') {
                    foreach ($rdf->allLiterals($rdfProperty) as $literal) {
                        $value = static::cleanString($literal);
                        if (!isset($value)) {
                            continue;
                        }

                        if ($literal->getLang()) {
                            $language = $literal->getLang();
                        } elseif (isset($mapping['_defaultLanguage'])) {
                            $language = $mapping['_defaultLanguage'];
                        } else {
                            $language = $this->defaultLanguage;
                        }

                        $languageMap = isset($jskos->$property) ? $jskos->$property : [];

                        if (isset($mapping['unique'])) {
                            $languageMap[$language] = $value;
                        } else {
                            $list = $languageMap[$language] ?? [];
                            $list[] = $value;
                            $languageMap[$language] = $list;
                        }

                        $jskos->$property = $languageMap;
                    }
                } elseif ($type == 'plain') {
                    foreach ($rdf->allLiterals($rdfProperty) as $literal) {
                        $value = static::cleanString($literal);
                        if (!isset($value)) {
                            continue;
                        }

                        if (isset($mapping['pattern']) && !preg_match($mapping['pattern'], $value)) {
                            continue;
                        }
                        if (isset($mapping['unique'])) {
                            $jskos->$property = $value;
                        } else {
                            if (empty($jskos->$property)) {
                                $jskos->$property = [$value];
                            } else {
                                $jskos->$property[] = $value;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Clean up a string value by trimming whitespace and mapping empty strings to null.
     */
    public static function cleanString($string)
    {
        $string = trim((string)$string);
        return $string !== "" ? $string : null;
    }

    /**
     * Silently try to load RDF from an URL.
     * @return EasyRdf_Resource|null
     */
    public static function loadRDF($url, $uri = null, $format = null)
    {
        try {
            $rdf = \EasyRdf_Graph::newAndLoad($url, $format);
            return $rdf->resource($uri ? $uri : $url);
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * Get a list of RDF object URIs.
     */
    public static function getURIs(\EasyRDF_Resource $subject, $predicate, $pattern = null)
    {
        $uris = [];
        foreach ($subject->allResources($predicate) as $object) {
            if (!$object->isBNode()) {
                $object = $object->getUri();
                if (!$pattern or preg_match($pattern, $object)) {
                    $uris[] = $object;
                }
            }
        }
        return $uris;
    }
}
