<?php
namespace CHAOS\Harvester\OAIPMH;
class LoadableOAIPMHClient extends \oaipmh\OAIPMHClient implements \CHAOS\Harvester\IExternalClient {
	/**
	 * A reference to the harvester.
	 * @var \CHAOS\Harvester\ChaosHarvester
	 */
	protected $_harvester;
	
	/**
	 * Constructs a new DFIClient for communication with the Danish Film Institute open API.
	 * @param string $baseURL
	 */
	public function __construct($harvester, $name, $parameters = array()) {
		parent::__construct($parameters['baseURL'], $harvester->hasOption('debug'));
		$this->_harvester = $harvester;
		$this->_harvester->debug("Created a client for OAI-PMH endpoint: %s", $this->_baseURL);
	}
	
	public function debug($message) {
		$this->_harvester->debug($message);
	}
	
	public function sanityCheck() {
		$response = parent::Identify();
		$this->_harvester->info("Connected to the OAI-PMH endpoint named '%s' administrated by %s.", $response->Identify->repositoryName, $response->Identify->adminEmail);
		return true;
	}
	
	protected function request($requiredArguments, $optionalArguments = array()) {
		timed();
		$response = parent::request($requiredArguments, $optionalArguments);
		timed('oaipmh');
		return $response;
	}
}