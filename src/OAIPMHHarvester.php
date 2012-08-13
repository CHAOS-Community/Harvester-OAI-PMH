<?php
/**
 * This harvester connects to a OAI-PMH compliant webservice and
 * copies information on items into a CHAOS service.
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify  
 * it under the terms of the GNU Lesser General Public License as published by  
 * the Free Software Foundation, either version 3 of the License, or  
 * (at your option) any later version.  
 *
 * @author     Kræn Hansen (Open Source Shift) for the danish broadcasting corporation, innovations.
 * @license    http://opensource.org/licenses/LGPL-3.0	GNU Lesser General Public License
 * @version    $Id:$
 * @link       https://github.com/CHAOS-Community/Harvester-OAI-PMH
 * @since      File available since Release 0.1
 */

require "bootstrap.php";
use oaipmh\OAIPMHClient;

/**
 * Main class of the OAI-PMH Harvester.
 *
 * @author     Kræn Hansen (Open Source Shift) for the danish broadcasting corporation, innovations.
 * @license    http://opensource.org/licenses/LGPL-3.0	GNU Lesser General Public License
 * @version    Release: @package_version@
 * @link       https://github.com/CHAOS-Community/Harvester-OAI-PMH
 * @since      Class available since Release 0.1
 */
class OAIPMHHarvester extends ACHAOSImporter {
	
	/**
	 * The client to use when communicating with the OAI-PMH service.
	 * @var unknown_type
	 */
	protected $_oaipmh;
	
	/**
	 * Constructor for the DFI Harvester
	 * @throws RuntimeException if the CHAOS services are unreachable or
	 * if the CHAOS credentials provided fails to authenticate the session.
	 */
	public function __construct($args) {
		// Adding configuration parameters
		/*
		$this->_CONFIGURATION_PARAMETERS["OAI_PMH_URL"] = "_OAIPMHUrl";
		*/
		// Adding xml generators.
		/*
		$this->_metadataGenerators[] = dfi\dka\DKAMetadataGenerator::instance();
		$this->_metadataGenerators[] = dfi\dka\DKA2MetadataGenerator::instance();
		$this->_metadataGenerators[] = dfi\dka\DFIMetadataGenerator::instance();
		*/
		// Adding file extractors.
		/*
		$this->_fileExtractors[] = dfi\DFIImageExtractor::instance();
		$this->_fileExtractors[] = dfi\DFIVideoExtractor::instance();
		*/
		
		parent::__construct($args);
		
		$this->OAIPMH_initialize();
	}
	
	function OAIPMH_initialize() {
		$_oaipmh = new OAIPMHClient();
	}
}

// Call the main method of the class.
DFIIntoDKAHarvester::main($_SERVER['argv']);