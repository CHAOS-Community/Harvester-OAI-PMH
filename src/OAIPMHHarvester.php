<?php
/**
 * This harvester connects to a OAI-PMH compliant webservice and
 * copies information on items into a Chaos service.
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
class OAIPMHHarvester extends AChaosImporter {
	
	/**
	 * The client to use when communicating with the OAI-PMH service.
	 * @var OAIPMHClient
	 */
	protected $_oaipmh;
	
	/**
	 * The base url of the external OAI-PMH compliant webservice.
	 * @var string
	 */
	protected $_OAIPMHBaseUrl;
	
	protected $_metadataFormats;
	
	/**
	 * Constructor for the DFI Harvester
	 * @throws RuntimeException if the Chaos services are unreachable or
	 * if the Chaos credentials provided fails to authenticate the session.
	 */
	public function __construct($args) {
		// Adding configuration parameters
		$this->_CONFIGURATION_PARAMETERS["OAIPMH_BASE_URL"] = "_OAIPMHBaseUrl";
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
		$this->_oaipmh = new OAIPMHClient($this->_OAIPMHBaseUrl);
		// Sanity check
		$identifyResponse = $this->_oaipmh->Identify();
		printf("Repository is '%s' (OAI-PMH version %s)\n", $identifyResponse->Identify->repositoryName, $identifyResponse->Identify->protocolVersion);
		
		$this->_metadataFormats = array();
		$listMetadataFormatsResponse = $this->_oaipmh->ListMetadataFormats();
		foreach($listMetadataFormatsResponse->ListMetadataFormats->metadataFormat as $metadataFormat) {
			//printf("This repository has metadata: %s\n", $metadataFormat->metadataPrefix);
			$this->_metadataFormats[] = $metadataFormat;
		}
	}
	
	protected function fetchRange($start, $count) {
		$metadataPrefix = strval($this->_metadataFormats[0]->metadataPrefix);
		$result = array();
		$offset = -1;
		$resumptionToken = null;
		do {
			$response = $this->_oaipmh->ListIdentifiers($resumptionToken === null ? $metadataPrefix : null, $resumptionToken);
			$resumptionToken = strval($response->ListIdentifiers->resumptionToken);
			foreach($response->ListIdentifiers->header as $header) {
				$identifier = strval($header->identifier);
				$offset++;
				
				if($offset < $start) {
					continue;
				} elseif($offset >= $start && $offset < $start + $count) {
					$result[] = $identifier;
				} else {
					break 2;
				}
			}
		} while($resumptionToken != null);
		return $result;
	}
	
	protected function fetchSingle($reference) {
		$metadataPrefix = strval($this->_metadataFormats[0]->metadataPrefix);
		$response = $this->_oaipmh->GetRecord($reference, $metadataPrefix);
		if($response === false || empty($response->GetRecord->record)) {
			throw new RuntimeException("Unexpected response from the OAIPMH service.");
		} else {
			var_dump($response);
			exit;
			return $response;
		}
	}
	
	protected function externalObjectToString($externalObject) {
		//var_dump($externalObject);
	}
	
	protected function getOrCreateObject($externalObject) {
		
	}
	
	protected function initializeExtras(&$extras) {
		// Nothing to do here.
	}
	
	protected function shouldBeSkipped($externalObject) {
		return false;
	}
	
	protected function generateChaosQuery($externalObject) {
		return "";
	}
	
	protected function getChaosObjectTypeID() {
		return false;
	}
	
	public function getExternalClient() {
		return $this->_oaipmh;
	}
}

// Call the main method of the class.
OAIPMHHarvester::main($_SERVER['argv']);
