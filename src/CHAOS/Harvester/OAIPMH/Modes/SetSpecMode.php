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
		
		assert(is_string($reference));
		
		/* @var $oaipmh \CHAOS\Harvester\OAIPMH\LoadableOAIPMHClient */
		$oaipmh = $this->_harvester->getExternalClient('oaipmh');
		
		foreach(explode(',', $reference) as $set) {
			$r = 1;
			$resumptionToken = null;
			
			$this->_harvester->info("Fetching references to all records belonging to the set '%s'.", $set);
			do {
				if($resumptionToken == null) {
					$response = $oaipmh->ListRecords($this->_metadataPrefix, null, null, null, $set);
				} else {
					$response = $oaipmh->ListRecords(null, $resumptionToken, null, null, null);
				}
					
				$records = $response->ListRecords->record;
				if(count($response->ListRecords->resumptionToken) == 0) {
					$total = count($records);
				} else {
					$total = $response->ListRecords->resumptionToken->attributes()->completeListSize;
				}
					
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
}