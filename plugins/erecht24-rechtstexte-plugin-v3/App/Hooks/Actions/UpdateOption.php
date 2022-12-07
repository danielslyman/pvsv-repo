<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Hooks\Actions;

class UpdateOption
{

	/**
	 * Function adds time stamps to wp option
	 * @param $newValue
	 * @param $oldValue
	 * @return mixed
	 */
    public function execute(
	    $newValue,
		$oldValue
    ) {
    	if ( ($newValue['document_de_local'] ?? '') !== ($oldValue['document_de_local'] ?? '') )
    		$newValue['document_de_last_update_local'] = date("Y-m-d H:i:s", time());

    	if ( ($newValue['document_en_local'] ?? '') !== ($oldValue['document_en_local'] ?? '') )
    		$newValue['document_en_last_update_local'] = date("Y-m-d H:i:s", time());

    	return $newValue;
    }
}