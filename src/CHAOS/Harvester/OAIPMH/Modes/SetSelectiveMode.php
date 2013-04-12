<?php
namespace CHAOS\Harvester\OAIPMH\Modes;
class SetSelectiveMode extends \CHAOS\Harvester\Modes\SetByReferenceMode implements \CHAOS\Harvester\Loadable {
	
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
		
		$reference = explode(';', $reference);
		$from = null;
		$until = null;
		
		$sets = $reference[0];
		if(count($reference) > 1) {
			$from = $reference[1];
		}
		if(count($reference) > 2) {
			$until = $reference[2];
		}
		
		/* @var $oaipmh \CHAOS\Harvester\OAIPMH\LoadableOAIPMHClient */
		$oaipmh = $this->_harvester->getExternalClient('oaipmh');
		
		foreach(explode(',', $sets) as $set) {
			$r = 1;
			$resumptionToken = null;
			
			$this->_harvester->info("Fetching references to all records belonging to the set '%s'.", $set);
			do {
				if($resumptionToken == null) {
					$response = $oaipmh->ListRecords($this->_metadataPrefix, null, $from, $until, $set);
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
					printf("[#%u/%u in %s] ", $r++, $total, $set);
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