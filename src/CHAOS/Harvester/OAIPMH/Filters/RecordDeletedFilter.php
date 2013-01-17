<?php
namespace CHAOS\Harvester\OAIPMH\Filters;
class RecordDeletedFilter extends \CHAOS\Harvester\Filters\Filter {
	
	
	public function passes($externalObject) {
		/* @var $externalObject \SimpleXMLElement */
		
		if(strval($externalObject->header->attributes()->status) === "deleted") {
			return "The record was deleted.";
		} else {
			return true;
		}
	}
}