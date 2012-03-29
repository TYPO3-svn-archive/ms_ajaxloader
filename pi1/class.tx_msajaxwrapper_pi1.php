<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Michael Stein <info@michaelstein-itb.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin 'Ajaxwrapper' for the 'ms_ajaxwrapper' extension.
 *
 * @author	Michael Stein <info@michaelstein-itb.de>
 * @package	TYPO3
 * @subpackage	tx_msajaxwrapper
 */
class tx_msajaxwrapper_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_msajaxwrapper_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_msajaxwrapper_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ms_ajaxwrapper';	// The extension key.
	var $pi_checkCHash = true;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
//		$this->conf = $conf;
//		t3lib_div::debug($this->conf, __LINE__);exit;
		if (!$this->cObj) {
			$this->cObj = t3lib_div::makeInstance('tslib_cObj');
		}

		$this->conf = $conf;
		$this->loadFlexform();


		if (T3JQUERY === true) {
			tx_t3jquery::addJqJS();
		} elseif($this->conf['includeJquery'] == 1) {
			$GLOBALS['TSFE']->additionalHeaderData[$ext_key] = '<script src="'.$this->getPath($this->conf['pathToJquery']).'" type="text/javascript"></script>';
		}

		$this->template = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), '###TEMPLATE###');
		$this->addJsFile($this->conf['ajaxLoader']);

		$marker['###MS_AJAX_LOADER###'] = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), '###TEMPLATE_LOADER###');
		$this->template = $this->cObj->substituteMarkerArray($this->template, $marker);

		$this->addFieldsToData();
		$this->fillMarkerArray();


		$this->markerArray['###CONTENT_UID###'] = $this->conf['content_uid'];

		$content = $this->cObj->substituteMarkerArray($this->template, $this->markerArray);

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * This function is copied from extension t3jquery
	 * Add JS-File to the HTML
	 *
	 * @param string $file
	 * @return void
	 */
	protected function addJsFile($file)
	{
		if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {
			$pagerender = $GLOBALS['TSFE']->getPageRenderer();
			if ($this->conf['tofooter'] == 'footer') {
				$pagerender->addJsFooterFile($file, $this->conf['type'], $this->conf['compress'], $this->conf['forceOnTop'], $this->conf['allWrap']);
			} else {
				$pagerender->addJsFile($file, $this->conf['type'], $this->conf['compress'], $this->conf['forceOnTop'], $this->conf['allWrap']);
			}
		} else {
			$temp_file = '<script type="text/javascript" src="' . $file . '"></script>';
			if ($this->conf['tofooter'] == 'footer') {
				$GLOBALS['TSFE']->additionalFooterData['t3jquery.jsfile.'.$file] = $temp_file;
			} else {
				$GLOBALS['TSFE']->additionalHeaderData['t3jquery.jsfile.'.$file] = $temp_file;
			}
		}
	}


	/**
	 * Add field for stdwrap to $this->cObj->data
	 *
	 * @return void
	 */
	protected function addFieldsToData () {
		$addFields = array_flip(t3lib_div::trimExplode(',', $this->conf['addFieldsToData']));
		$this->cObj->data = t3lib_div::array_merge_recursive_overrule(
			$this->cObj->data,
			$addFields
		);
	}

	/**
	 * fill Markerarray with all data from $this->cObj-data
	 *
	 * @return void
	 */
	protected function fillMarkerArray() {
		foreach ($this->cObj->data as $key => $data) {
			$this->markerArray['###' . strtoupper($key) . '###'] = $this->cObj->stdWrap($data, $this->conf['wrapData.'][$key . '.']);
		}
	}

	/**
	 * get pluginsettings from flesform
	 *
	 * @return void
	 */
	protected function loadFlexform () {
		$this->pi_initPIflexForm();
		if ($this->cObj->data['pi_flexform']) {
			foreach ($this->cObj->data['pi_flexform']['data'] as $sheet => $data ) {
				foreach ($data as $lang => $value) {
					foreach ($value as $key => $val) {
						$ffValue = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], $key, $sheet);
						if (strlen($ffValue) > 0) {
							$this->conf[$key] = $ffValue;
						}
					}
				}
			}
		}
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ms_ajaxwrapper/pi1/class.tx_msajaxwrapper_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ms_ajaxwrapper/pi1/class.tx_msajaxwrapper_pi1.php']);
}

?>