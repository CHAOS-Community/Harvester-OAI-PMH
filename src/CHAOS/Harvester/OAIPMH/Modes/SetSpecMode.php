<?php
namespace CHAOS\Harvester\OAIPMH\Modes;
class SetSpecMode extends \CHAOS\Harvester\Modes\SetByReferenceMode implements \CHAOS\Harvester\Loadable {
	
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
		$this->_harvester->debug(__CLASS__." is executing.");
		
		/* @var $oaipmh \CHAOS\Harvester\OAIPMH\LoadableOAIPMHClient */
		$oaipmh = $this->_harvester->getExternalClient('oaipmh');
		
		$r = 1;
		$resumptionToken = null;
		
		$this->_harvester->info("Fetching references to all movieclips.");
		do {
			if($resumptionToken == null) {
				$response = $oaipmh->ListRecords($this->_metadataPrefix, null, null, null, $reference);
			} else {
				$response = $oaipmh->ListRecords(null, $resumptionToken, null, null, null);
			}
			
			$total = $response->ListRecords->resumptionToken->attributes()->completeListSize;
			$records = $response->ListRecords->record;
			
			$this->_harvester->info("Found %u records.", $total);
		
			foreach($records as $record) {
				printf("[#%u/%u] ", $r++, $total);
				$recordShadow = null;
				try {
					$recordShadow = $this->_harvester->process('record', $record);
				} catch(\Exception $e) {
					$this->_harvester->registerProcessingException($e, $record, $recordShadow);
				}
				print("\n");
			}
			
			$resumptionToken = strval($response->ListRecords->resumptionToken);
		} while($r < $total);
	}
}