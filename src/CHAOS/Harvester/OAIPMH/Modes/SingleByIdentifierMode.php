<?php
namespace CHAOS\Harvester\OAIPMH\Modes;
use CHAOS\Harvester\Modes\SingleByReferenceMode;

class SingleByIdentifierMode extends \CHAOS\Harvester\Modes\SingleByReferenceMode implements \CHAOS\Harvester\Loadable {
	
	/**
	 * The prefix to use when getting records from the service.
	 * @var string
	 */
	protected $_metadataPrefix;
	
	public function __construct($harvester, $name, $parameters) {
		parent::__construct($harvester, $name, $parameters);
		$this->_metadataPrefix = $parameters['metadataPrefix'];
	}

	public function execute($reference) {
		assert(is_string($reference));
		
		/* @var $oaipmh \CHAOS\Harvester\OAIPMH\LoadableOAIPMHClient */
		$oaipmh = $this->_harvester->getExternalClient('oaipmh');
		
		$this->_harvester->info("Fetching record by metadata prefix '%s' and identifier '%s'.", $this->_metadataPrefix, $reference);
		
		$response = $oaipmh->GetRecord($reference, $this->_metadataPrefix);
		
		$record = $response->GetRecord->record;
		
		try {
			$recordShadow = $this->_harvester->process('record', $record);
		} catch(\Exception $e) {
			$this->_harvester->registerProcessingException($e, $record, $recordShadow);
		}
	}
}