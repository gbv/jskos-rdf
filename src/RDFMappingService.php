<?php

namespace JSKOS\RDF;

abstract class RDFMappingService extends \JSKOS\ConfiguredService {
    private $rdfMapping;

    public function configure(array $config) {
        parent::configure($config);
        $this->rdfMapping = new RDFMapping($config);
    }

    public function applyRDFMapping($rdf, $jskos) {
        $this->rdfMapping->apply($rdf, $jskos);
    }
}
