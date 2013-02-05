<?php
namespace CHAOS\Harvester\OAIPMH\Modes;
use CHAOS\Harvester\Modes\SingleByReferenceMode;

class AllMode extends \CHAOS\Harvester\Modes\AllMode implements \CHAOS\Harvester\Loadable {
	
	/**
	 * The prefix to use when getting records from the service.
	 * @var string
	 */
	protected $_metadataPrefix;
	
	public function __construct($harvester, $name, $parameters) {
		parent::__construct($harvester, $name, $parameters);
		$this->_metadataPrefix = $parameters['metadataPrefix'];
		// TODO: Think about implementing the fetching of all metadata prefixes, if this isn't specified in the configuration.
	}

	public function execute() {
		throw new \BadMethodCallException("This method has not been implemented yet ...");
	}
}