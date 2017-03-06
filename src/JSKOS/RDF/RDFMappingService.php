<?php

namespace JSKOS\RDF;

class RDFMappingService extends \JSKOS\ConfiguredService {
    private $rdfMapping;

    public function __construct() {
        parent::__construct(); // provides config and uriSpace
        $this->rdfMapping = new RDFMapping($this->config);
    }

    public function applyRDFMapping($rdf, $jskos) {
        $this->rdfMapping->apply($rdf, $jskos);
    }
}
