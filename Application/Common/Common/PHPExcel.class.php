<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    1.8.0, 2014-03-02
 */


/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}


/**
 * PHPExcel
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel
{
    /**
     * Unique ID
     *
     * @var string
     */
    private $_uniqueID;

    /**
     * Document properties
     *
     * @var PHPExcel_DocumentProperties
     */
    private $_properties;

    /**
     * Document security
     *
     * @var PHPExcel_DocumentSecurity
     */
    private $_security;

    /**
     * Collection of Worksheet objects
     *
     * @var PHPExcel_Worksheet[]
     */
    private $_workSheetCollection = array();

    /**
	 * Calculation Engine
	 *
	 * @var PHPExcel_Calculation
	 */
	private $_calculationEngine = NULL;

    /**
     * Active sheet index
     *
     * @var int
     */
    private $_activeSheetIndex = 0;

    /**
     * Named ranges
     *
     * @var PHPExcel_NamedRange[]
     */
    private $_namedRanges = array();

    /**
     * CellXf supervisor
     *
     * @var PHPExcel_Style
     */
    private $_cellXfSupervisor;

    /**
     * CellXf collection
     *
     * @var PHPExcel_Style[]
     */
    private $_cellXfCollection = array();

    /**
     * CellStyleXf collection
     *
     * @var PHPExcel_Style[]
     */
    private $_cellStyleXfCollection = array();

	/**
	* _hasMacros : this workbook have macros ?
	*
	* @var bool
	*/
	private $_hasMacros = FALSE;

	/**
	* _macrosCode : all macros code (the vbaProject.bin file, this include form, code,  etc.), NULL if no macro
	*
	* @var binary
	*/
	private $_macrosCode=NULL;
	/**
	* _macrosCertificate : if macros are signed, contains vbaProjectSignature.bin file, NULL if not signed
	*
	* @var binary
	*/
	private $_macrosCertificate=NULL;

	/**
	* _ribbonXMLData : NULL if workbook is'nt Excel 2007 or not contain a customized UI
	*
	* @var NULL|string
	*/
	private $_ribbonXMLData=NULL;

	/**
	* _ribbonBinObjects : NULL if workbook is'nt Excel 2007 or not contain embedded objects (picture(s)) for Ribbon Elements
	* ignored if $_ribbonXMLData is null
	*
	* @var NULL|array
	*/
	private $_ribbonBinObjects=NULL;

	/**
	* The workbook has macros ?
	*
	* @return true if workbook has macros, false if not
	*/
	public function hasMacros(){
		return $this->_hasMacros;
	}

	/**
	* Define if a workbook has macros
	*
	* @param true|false
	*/
	public function setHasMacros($hasMacros=false){
		$this->_hasMacros=(bool)$hasMacros;
	}

	/**
	* Set the macros code
	*
	* @param binary string|null
	*/
	public function setMacrosCode($MacrosCode){
		$this->_macrosCode=$MacrosCode;
		$this->setHasMacros(!is_null($MacrosCode));
	}

	/**
	* Return the macros code
	*
	* @return binary|null
	*/
	public function getMacrosCode(){
		return $this->_macrosCode;
	}

	/**
	* Set the macros certificate
	*
	* @param binary|null
	*/
	public function setMacrosCertificate($Certificate=NULL){
		$this->_macrosCertificate=$Certificate;
	}

	/**
	* Is the project signed ?
	*
	* @return true|false
	*/
	public function hasMacrosCertificate(){
		return !is_null($this->_macrosCertificate);
	}

	/**
	* Return the macros certificate
	*
	* @return binary|null
	*/
	public function getMacrosCertificate(){
		return $this->_macrosCertificate;
	}

	/**
	* Remove all macros, certificate from spreadsheet
	*
	* @param none
	* @return void
	*/
	public function discardMacros(){
		$this->_hasMacros=false;
		$this->_macrosCode=NULL;
		$this->_macrosCertificate=NULL;
	}

	/**
	* set ribbon XML data
	*
	*/
	public function setRibbonXMLData($Target=NULL, $XMLData=NULL){
		if(!is_null($Target) && !is_null($XMLData)){
			$this->_ribbonXMLData=array('target'=>$Target, 'data'=>$XMLData);
		}else{
			$this->_ribbonXMLData=NULL;
		}
	}

	/**
	* retrieve ribbon XML Data
	*
	* return string|null|array
	*/
	public function getRibbonXMLData($What='all'){//we need some constants here...
		$ReturnData=NULL;
		$What=strtolower($What);
		switch($What){
		case 'all':
			$ReturnData=$this->_ribbonXMLData;
			break;
		case 'target':
		case 'data':
			if(is_array($this->_ribbonXMLData) && array_key_exists($What,$this->_ribbonXMLData)){
				$ReturnData=$this->_ribbonXMLData[$What];
			}//else $ReturnData stay at null
			break;
		}//default: $ReturnData at null
		return $ReturnData;
	}

	/**
	* store binaries ribbon objects (pictures)
	*
	*/
	public function setRibbonBinObjects($BinObjectsNames=NULL, $BinObjectsData=NULL){
		if(!is_null($BinObjectsNames) && !is_null($BinObjectsData)){
			$this->_ribbonBinObjects=array('names'=>$BinObjectsNames, 'data'=>$BinObjectsData);
		}else{
			$this->_ribbonBinObjects=NULL;
		}
	}
	/**
	* return the extension of a filename. Internal use for a array_map callback (php<5.3 don't like lambda function)
	*
	*/
	private function _getExtensionOnly($ThePath){
		return pathinfo($ThePath, PATHINFO_EXTENSION);
	}

	/**
	* retrieve Binaries Ribbon Objects
	*
	*/
	public function getRibbonBinObjects($What='all'){
		$ReturnData=NULL;
		$What=strtolower($What);
		switch($What){
		case 'all':
			return $this->_ribbonBinObjects;
			break;
		case 'names':
		case 'data':
			if(is_array($this->_ribbonBinObjects) && array_key_exists($What, $this->_ribbonBinObjects)){
				$ReturnData=$this->_ribbonBinObjects[$What];
			}
			break;
		case 'types':
			if(is_array($this->_ribbonBinObjects) && array_key_exists('data', $this->_ribbonBinObjects) && is_array($this->_ribbonBinObjects['data'])){
				$tmpTypes=array_keys($this->_ribbonBinObjects['data']);
				$ReturnData=array_unique(array_map(array($this,'_getExtensionOnly'), $tmpTypes));
			}else
				$ReturnData=array();//the caller want an array... not null if empty
			break;
		}
		return $ReturnData;
	}

	/**
	* This workbook have a custom UI ?
	*
	* @return true|false
	*/
	public function hasRibbon(){
		return !is_null($this->_ribbonXMLData);
	}

	/**
	* This workbook have additionnal object for the ribbon ?
	*
	* @return true|false
	*/
	public function hasRibbonBinObjects(){
		return !is_null($this->_ribbonBinObjects);
	}

	/**
     * Check if a sheet with a specified code name already exists
     *
     * @param string $pSheetCodeName  Name of the worksheet to check
     * @return boolean
     */
    public function sheetCodeNameExists($pSheetCodeName)
    {
		return ($this->getSheetByCodeName($pSheetCodeName) !== NULL);
    }

	/**
	 * Get sheet by code name. Warning : sheet don't have always a code name !
	 *
	 * @param string $pName Sheet name
	 * @return PHPExcel_Worksheet
	 */
	public function getSheetByCodeName($pName = '')
	{
		$worksheetCount = count($this->_workSheetCollection);
		for ($i = 0; $i < $worksheetCount; ++$i) {
			if ($this->_workSheetCollection[$i]->getCodeName() == $pName) {
				return $this->_workSheetCollection[$i];
			}
		}

		return null;
	}

	 /**
	 * Create a new PHPExcel with one Worksheet
	 */
	public function __construct()
	{
		$this->_uniqueID = uniqid();
		$this->_calculationEngine	= PHPExcel_Calculation::getInstance($this);

		// Initialise worksheet collection and add one worksheet
		$this->_workSheetCollection = array();
		$this->_workSheetCollection[] = new PHPExcel_Worksheet($this);
		$this->_activeSheetIndex = 0;

        // Create document properties
        $this->_properties = new PHPExcel_DocumentProperties();

        // Create document security
        $this->_security = new PHPExcel_DocumentSecurity();

        // Set named ranges
        $this->_namedRanges = array();

        // Create the cellXf supervisor
        $this->_cellXfSupervisor = new PHPExcel_Style(true);
        $this->_cellXfSupervisor->bindParent($this);

        // Create the default style
        $this->addCellXf(new PHPExcel_Style);
        $this->addCellStyleXf(new PHPExcel_Style);
    }

    /**
     * Code to execute when this worksheet is unset()
     *
     */
    public function __destruct() {
        PHPExcel_Calculation::unsetInstance($this);
        $this->disconnectWorksheets();
    }    //    function __destruct()

    /**
     * Disconnect all worksheets from this PHPExcel workbook object,
     *    typically so that the PHPExcel object can be unset
     *
     */
    public function disconnectWorksheets()
    {
    	$worksheet = NULL;
        foreach($this->_workSheetCollection as $k => &$worksheet) {
            $worksheet->disconnectCells();
            $this->_workSheetCollection[$k] = null;
        }
        unset($worksheet);
        $this->_workSheetCollection = array();
    }

	/**
	 * Return the calculation engine for this worksheet
	 *
	 * @return PHPExcel_Calculation
	 */
	public function getCalculationEngine()
	{
		return $this->_calculationEngine;
	}	//	function getCellCacheController()

    /**
     * Get properties
     *
     * @return PHPExcel_DocumentProperties
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Set properties
     *
     * @param PHPExcel_DocumentProperties    $pValue
     */
    public function setProperties(PHPExcel_DocumentProperties $pValue)
    {
        $this->_properties = $pValue;
    }

    /**
     * Get security
     *
     * @return PHPExcel_DocumentSecurity
     */
    public function getSecurity()
    {
        return $this->_security;
    }

    /**
     * Set security
     *
     * @param PHPExcel_DocumentSecurity    $pValue
     */
    public function setSecurity(PHPExcel_DocumentSecurity $pValue)
    {
        $this->_security = $pValue;
    }

    /**
     * Get active sheet
     *
     * @return PHPExcel_Worksheet
     */
    public function getActiveSheet()
    {
        return $this->_workSheetCollection[$this->_activeSheetIndex];
    }

    /**
     * Create sheet and add it to this workbook
     *
     * @param  int|null $iSheetIndex Index where sheet should go (0,1,..., or null for last)
     * @return PHPExcel_Worksheet
     * @throws PHPExcel_Exception
     */
    public function createSheet($iSheetIndex = NULL)
    {
        $newSheet = new PHPExcel_Worksheet($this);
        $this->addSheet($newSheet, $iSheetIndex);
        return $newSheet;
    }

    /**
     * Check if a sheet with a specified name already exists
     *
     * @param  string $pSheetName  Name of the worksheet to check
     * @return boolean
     */
    public function sheetNameExists($pSheetName)
    {
        return ($this->getSheetByName($pSheetName) !== NULL);
    }

    /**
     * Add sheet
     *
     * @param  PHPExcel_Worksheet $pSheet
     * @param  int|null $iSheetIndex Index where sheet should go (0,1,..., or null for last)
     * @return PHPExcel_Worksheet
     * @throws PHPExcel_Exception
     */
    public function addSheet(PHPExcel_Worksheet $pSheet, $iSheetIndex = NULL)
    {
        if ($this->sheetNameExists($pSheet->getTitle())) {
            throw new PHPExcel_Exception(
            	"Workbook already contains a worksheet named '{$pSheet->getTitle()}'. Rename this worksheet first."
            );
        }

        if($iSheetIndex === NULL) {
            if ($this->_activeSheetIndex < 0) {
                $this->_activeSheetIndex = 0;
            }
            $this->_workSheetCollection[] = $pSheet;
        } else {
            // Insert the sheet at the requested index
            array_splice(
                $this->_workSheetCollection,
                $iSheetIndex,
                0,
                array($pSheet)
                );

            // Adjust active sheet index if necessary
            if ($this->_activeSheetIndex >= $iSheetIndex) {
                ++$this->_activeSheetIndex;
            }
        }

        if ($pSheet->getParent() === null) {
            $pSheet->rebindParent($this);
        }

        return $pSheet;
    }

    /**
     * Remove sheet by index
     *
     * @param  int $pIndex Active sheet index
     * @throws PHPExcel_Exception
     */
    public function removeSheetByIndex($pIndex = 0)
    {

        $numSheets = count($this->_workSheetCollection);

        if ($pIndex > $numSheets - 1) {
            throw new PHPExcel_Exception(
            	"You tried to remove a sheet by the out of bounds index: {$pIndex}. The actual number of sheets is {$numSheets}."
            );
        } else {
            array_splice($this->_workSheetCollection, $pIndex, 1);
        }
        // Adjust active sheet index if necessary
        if (($this->_activeSheetIndex >= $pIndex) &&
            ($pIndex > count($this->_workSheetCollection) - 1)) {
            --$this->_activeSheetIndex;
        }

    }

    /**
     * Get sheet by index
     *
     * @param  int $pIndex Sheet index
     * @return PHPExcel_Worksheet
     * @throws PHPExcel_Exception
     */
    public function getSheet($pIndex = 0)
    {

        $numSheets = count($this->_workSheetCollection);

        if ($pIndex > $numSheets - 1) {
            throw new PHPExcel_Exception(
            	"Your requested sheet index: {$pIndex} is out of bounds. The actual number of sheets is {$numSheets}."
           	);
        } else {
            return $this->_workSheetCollection[$pIndex];
        }
    }

    /**
     * Get all sheets
     *
     * @return PHPExcel_Worksheet[]
     */
    public function getAllSheets()
    {
        return $this->_workSheetCollection;
    }

    /**
     * Get sheet by name
     *
     * @param  string $pName Sheet name
     * @return PHPExcel_Worksheet
     */
    public function getSheetByName($pName = '')
    {
        $worksheetCount = count($this->_workSheetCollection);
        for ($i = 0; $i < $worksheetCount; ++$i) {
            if ($this->_workSheetCollection[$i]->getTitle() === $pName) {
                return $this->_workSheetCollection[$i];
            }
        }

        return NULL;
    }

    /**
     * Get index for sheet
     *
     * @param  PHPExcel_Worksheet $pSheet
     * @return Sheet index
     * @throws PHPExcel_Exception
     */
    public function getIndex(PHPExcel_Worksheet $pSheet)
    {
        foreach ($this->_workSheetCollection as $key => $value) {
            if ($value->getHashCode() == $pSheet->getHashCode()) {
                return $key;
            }
        }

        throw new PHPExcel_Exception("Sheet does not exist.");
    }

    /**
     * Set index for sheet by sheet name.
     *
     * @param  string $sheetName Sheet name to modify index for
     * @param  int $newIndex New index for the sheet
     * @return New sheet index
     * @throws PHPExcel_Exception
     */
    public function setIndexByName($sheetName, $newIndex)
    {
        $oldIndex = $this->getIndex($this->getSheetByName($sheetName));
        $pSheet = array_splice(
            $this->_workSheetCollection,
            $oldIndex,
            1
        );
        array_splice(
            $this->_workSheetCollection,
            $newIndex,
            0,
            $pSheet
        );
        return $newIndex;
    }

    /**
     * Get sheet count
     *
     * @return int
     */
    public function getSheetCount()
    {
        return count($this->_workSheetCollection);
    }

    /**
     * Get active sheet index
     *
     * @return int Active sheet index
     */
    public function getActiveSheetIndex()
    {
        return $this->_activeSheetIndex;
    }

    /**
     * Set active sheet index
     *
     * @param  int $pIndex Active sheet index
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function setActiveSheetIndex($pIndex = 0)
    {
    		$numSheets = count($this->_workSheetCollection);

        if ($pIndex > $numSheets - 1) {
            throw new PHPExcel_Exception(
            	"You tried to set a sheet active by the out of bounds index: {$pIndex}. The actual number of sheets is {$numSheets}."
            );
        } else {
            $this->_activeSheetIndex = $pIndex;
        }
        return $this->getActiveSheet();
    }

    /**
     * Set active sheet index by name
     *
     * @param  string $pValue Sheet title
     * @return PHPExcel_Worksheet
     * @throws PHPExcel_Exception
     */
    public function setActiveSheetIndexByName($pValue = '')
    {
        if (($worksheet = $this->getSheetByName($pValue)) instanceof PHPExcel_Worksheet) {
            $this->setActiveSheetIndex($this->getIndex($worksheet));
            return $worksheet;
        }

        throw new PHPExcel_Exception('Workbook does not contain sheet:' . $pValue);
    }

    /**
     * Get sheet names
     *
     * @return string[]
     */
    public function getSheetNames()
    {
        $returnValue = array();
        $worksheetCount = $this->getSheetCount();
        for ($i = 0; $i < $worksheetCount; ++$i) {
            $returnValue[] = $this->getSheet($i)->getTitle();
        }

        return $returnValue;
    }

    /**
     * Add external sheet
     *
     * @param  PHPExcel_Worksheet $pSheet External sheet to add
     * @param  int|null $iSheetIndex Index where sheet should go (0,1,..., or null for last)
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function addExternalSheet(PHPExcel_Worksheet $pSheet, $iSheetIndex = null) {
        if ($this->sheetNameExists($pSheet->getTitle())) {
            throw new PHPExcel_Exception("Workbook already contains a worksheet named '{$pSheet->getTitle()}'. Rename the external sheet first.");
        }

        // count how many cellXfs there are in this workbook currently, we will need this below
        $countCellXfs = count($this->_cellXfCollection);

        // copy all the shared cellXfs from the external workbook and append them to the current
        foreach ($pSheet->getParent()->getCellXfCollection() as $cellXf) {
            $this->addCellXf(clone $cellXf);
        }

        // move sheet to this workbook
        $pSheet->rebindParent($this);

        // update the cellXfs
        foreach ($pSheet->getCellCollection(false) as $cellID) {
            $cell = $pSheet->getCell($cellID);
            $cell->setXfIndex( $cell->getXfIndex() + $countCellXfs );
        }

        return $this->addSheet($pSheet, $iSheetIndex);
    }

    /**
     * Get named ranges
     *
     * @return PHPExcel_NamedRange[]
     */
    public function getNamedRanges() {
        return $this->_namedRanges;
    }

    /**
     * Add named range
     *
     * @param  PHPExcel_NamedRange $namedRange
     * @return PHPExcel
     */
    public function addNamedRange(PHPExcel_NamedRange $namedRange) {
        if ($namedRange->getScope() == null) {
            // global scope
            $this->_namedRanges[$namedRange->getName()] = $namedRange;
        } else {
            // local scope
            $this->_namedRanges[$namedRange->getScope()->getTitle().'!'.$namedRange->getName()] = $namedRange;
        }
        return true;
    }

    /**
     * Get named range
     *
     * @param  string $namedRange
     * @param  PHPExcel_Worksheet|null $pSheet Scope. Use null for global scope
     * @return PHPExcel_NamedRange|null
     */
    public function getNamedRange($namedRange, PHPExcel_Worksheet $pSheet = null) {
        $returnValue = null;

        if ($namedRange != '' && ($namedRange !== NULL)) {
            // first look for global defined name
            if (isset($this->_namedRanges[$namedRange])) {
                $returnValue = $this->_namedRanges[$namedRange];
            }

            // then look for local defined name (has priority over global defined name if both names exist)
            if (($pSheet !== NULL) && isset($this->_namedRanges[$pSheet->getTitle() . '!' . $namedRange])) {
                $returnValue = $this->_namedRanges[$pSheet->getTitle() . '!' . $namedRange];
            }
        }

        return $returnValue;
    }

    /**
     * Remove named range
     *
     * @param  string  $namedRange
     * @param  PHPExcel_Worksheet|null  $pSheet  Scope: use null for global scope.
     * @return PHPExcel
     */
    public function removeNamedRange($namedRange, PHPExcel_Worksheet $pSheet = null) {
        if ($pSheet === NULL) {
            if (isset($this->_namedRanges[$namedRange])) {
                unset($this->_namedRanges[$namedRange]);
            }
        } else {
            if (isset($this->_namedRanges[$pSheet->getTitle() . '!' . $namedRange])) {
                unset($this->_namedRanges[$pSheet->getTitle() . '!' . $namedRange]);
            }
        }
        return $this;
    }

    /**
     * Get worksheet iterator
     *
     * @return PHPExcel_WorksheetIterator
     */
    public function getWorksheetIterator() {
        return new PHPExcel_WorksheetIterator($this);
    }

    /**
     * Copy workbook (!= clone!)
     *
     * @return PHPExcel
     */
    public function copy() {
        $copied = clone $this;

        $worksheetCount = count($this->_workSheetCollection);
        for ($i = 0; $i < $worksheetCount; ++$i) {
            $this->_workSheetCollection[$i] = $this->_workSheetCollection[$i]->copy();
            $this->_workSheetCollection[$i]->rebindParent($this);
        }

        return $copied;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone() {
        foreach($this as $key => $val) {
            if (is_object($val) || (is_array($val))) {
                $this->{$key} = unserialize(serialize($val));
            }
        }
    }

    /**
     * Get the workbook collection of cellXfs
     *
     * @return PHPExcel_Style[]
     */
    public function getCellXfCollection()
    {
        return $this->_cellXfCollection;
    }

    /**
     * Get cellXf by index
     *
     * @param  int $pIndex
     * @return PHPExcel_Style
     */
    public function getCellXfByIndex($pIndex = 0)
    {
        return $this->_cellXfCollection[$pIndex];
    }

    /**
     * Get cellXf by hash code
     *
     * @param  string $pValue
     * @return PHPExcel_Style|false
     */
    public function getCellXfByHashCode($pValue = '')
    {
        foreach ($this->_cellXfCollection as $cellXf) {
            if ($cellXf->getHashCode() == $pValue) {
                return $cellXf;
            }
        }
        return false;
    }

    /**
     * Check if style exists in style collection
     *
     * @param  PHPExcel_Style $pCellStyle
     * @return boolean
     */
    public function cellXfExists($pCellStyle = null)
    {
        return in_array($pCellStyle, $this->_cellXfCollection, true);
    }

    /**
     * Get default style
     *
     * @return PHPExcel_Style
     * @throws PHPExcel_Exception
     */
    public function getDefaultStyle()
    {
        if (isset($this->_cellXfCollection[0])) {
            return $this->_cellXfCollection[0];
        }
        throw new PHPExcel_Exception('No default style found for this workbook');
    }

    /**
     * Add a cellXf to the workbook
     *
     * @param PHPExcel_Style $style
     */
    public function addCellXf(PHPExcel_Style $style)
    {
        $this->_cellXfCollection[] = $style;
        $style->setIndex(count($this->_cellXfCollection) - 1);
    }

    /**
     * Remove cellXf by index. It is ensured that all cells get their xf index updated.
     *
     * @param  int $pIndex Index to cellXf
     * @throws PHPExcel_Exception
     */
    public function removeCellXfByIndex($pIndex = 0)
    {
        if ($pIndex > count($this->_cellXfCollection) - 1) {
            throw new PHPExcel_Exception("CellXf index is out of bounds.");
        } else {
            // first remove the cellXf
            array_splice($this->_cellXfCollection, $pIndex, 1);

            // then update cellXf indexes for cells
            foreach ($this->_workSheetCollection as $worksheet) {
                foreach ($worksheet->getCellCollection(false) as $cellID) {
                    $cell = $worksheet->getCell($cellID);
                    $xfIndex = $cell->getXfIndex();
                    if ($xfIndex > $pIndex ) {
                        // decrease xf index by 1
                        $cell->setXfIndex($xfIndex - 1);
                    } else if ($xfIndex == $pIndex) {
                        // set to default xf index 0
                        $cell->setXfIndex(0);
                    }
                }
            }
        }
    }

    /**
     * Get the cellXf supervisor
     *
     * @return PHPExcel_Style
     */
    public function getCellXfSupervisor()
    {
        return $this->_cellXfSupervisor;
    }

    /**
     * Get the workbook collection of cellStyleXfs
     *
     * @return PHPExcel_Style[]
     */
    public function getCellStyleXfCollection()
    {
        return $this->_cellStyleXfCollection;
    }

    /**
     * Get cellStyleXf by index
     *
     * @param  int $pIndex
     * @return PHPExcel_Style
     */
    public function getCellStyleXfByIndex($pIndex = 0)
    {
        return $this->_cellStyleXfCollection[$pIndex];
    }

    /**
     * Get cellStyleXf by hash code
     *
     * @param  string $pValue
     * @return PHPExcel_Style|false
     */
    public function getCellStyleXfByHashCode($pValue = '')
    {
        foreach ($this->_cellXfStyleCollection as $cellStyleXf) {
            if ($cellStyleXf->getHashCode() == $pValue) {
                return $cellStyleXf;
            }
        }
        return false;
    }

    /**
     * Add a cellStyleXf to the workbook
     *
     * @param PHPExcel_Style $pStyle
     */
    public function addCellStyleXf(PHPExcel_Style $pStyle)
    {
        $this->_cellStyleXfCollection[] = $pStyle;
        $pStyle->setIndex(count($this->_cellStyleXfCollection) - 1);
    }

    /**
     * Remove cellStyleXf by index
     *
     * @param int $pIndex
     * @throws PHPExcel_Exception
     */
    public function removeCellStyleXfByIndex($pIndex = 0)
    {
        if ($pIndex > count($this->_cellStyleXfCollection) - 1) {
            throw new PHPExcel_Exception("CellStyleXf index is out of bounds.");
        } else {
            array_splice($this->_cellStyleXfCollection, $pIndex, 1);
        }
    }

    /**
     * Eliminate all unneeded cellXf and afterwards update the xfIndex for all cells
     * and columns in the workbook
     */
    public function garbageCollect()
    {
        // how many references are there to each cellXf ?
        $countReferencesCellXf = array();
        foreach ($this->_cellXfCollection as $index => $cellXf) {
            $countReferencesCellXf[$index] = 0;
        }

        foreach ($this->getWorksheetIterator() as $sheet) {

            // from cells
            foreach ($sheet->getCellCollection(false) as $cellID) {
                $cell = $sheet->getCell($cellID);
                ++$countReferencesCellXf[$cell->getXfIndex()];
            }

            // from row dimensions
            foreach ($sheet->getRowDimensions() as $rowDimension) {
                if ($rowDimension->getXfIndex() !== null) {
                    ++$countReferencesCellXf[$rowDimension->getXfIndex()];
                }
            }

            // from column dimensions
            foreach ($sheet->getColumnDimensions() as $columnDimension) {
                ++$countReferencesCellXf[$columnDimension->getXfIndex()];
            }
        }

        // remove cellXfs without references and create mapping so we can update xfIndex
        // for all cells and columns
        $countNeededCellXfs = 0;
        foreach ($this->_cellXfCollection as $index => $cellXf) {
            if ($countReferencesCellXf[$index] > 0 || $index == 0) { // we must never remove the first cellXf
                ++$countNeededCellXfs;
            } else {
                unset($this->_cellXfCollection[$index]);
            }
            $map[$index] = $countNeededCellXfs - 1;
        }
        $this->_cellXfCollection = array_values($this->_cellXfCollection);

        // update the index for all cellXfs
        foreach ($this->_cellXfCollection as $i => $cellXf) {
            $cellXf->setIndex($i);
        }

        // make sure there is always at least one cellXf (there should be)
        if (empty($this->_cellXfCollection)) {
            $this->_cellXfCollection[] = new PHPExcel_Style();
        }

        // update the xfIndex for all cells, row dimensions, column dimensions
        foreach ($this->getWorksheetIterator() as $sheet) {

            // for all cells
            foreach ($sheet->getCellCollection(false) as $cellID) {
                $cell = $sheet->getCell($cellID);
                $cell->setXfIndex( $map[$cell->getXfIndex()] );
            }

            // for all row dimensions
            foreach ($sheet->getRowDimensions() as $rowDimension) {
                if ($rowDimension->getXfIndex() !== null) {
                    $rowDimension->setXfIndex( $map[$rowDimension->getXfIndex()] );
                }
            }

            // for all column dimensions
            foreach ($sheet->getColumnDimensions() as $columnDimension) {
                $columnDimension->setXfIndex( $map[$columnDimension->getXfIndex()] );
            }

			// also do garbage collection for all the sheets
            $sheet->garbageCollect();
        }
    }

    /**
     * Return the unique ID value assigned to this spreadsheet workbook
     *
     * @return string
     */
    public function getID() {
        return $this->_uniqueID;
    }
    
    public function output( $data, $mete, $attr, $is_save = '' ){
    	//当前页面字符编码
    	ob_end_clean();
    	header("Content-Type:text/html;charset=utf-8");
    	$this->data = $data;
    	$this->mete = $mete;
    	$this->attr = $attr;
    
    	//设置文档属性
    	if( is_array($this->attr) && !empty($this->attr)){
    		if(!empty($this->attr['title'])) $this->getProperties()->setTitle(iconv('utf-8','gb2312',$this->attr['title']));
    		if(!empty($this->attr['author'])) $this->getProperties()->setCreator(iconv('utf-8','gb2312',$this->attr['author']));
    		if(!empty($this->attr['subject'])) $this->getProperties()->setSubject(iconv('utf-8','gb2312',$this->attr['subject']));
    		if(!empty($this->attr['keyword'])) $this->getProperties()->setKeywords(iconv('utf-8','gb2312',$this->attr['keyword']));
    		if(!empty($this->attr['description'])) $this->getProperties()->setDescription(iconv('utf-8','gb2312',$this->attr['description']));
    		$this->attr['filename'] = !empty($this->attr['filename'])?iconv('utf-8','gb2312',$this->attr['filename']):'data';
    	}
    	//设置excel当前sheet
    
    	$this->setActiveSheetIndex(0);
    	$Sheet = $this->getActiveSheet();
    	$Sheet->setTitle($this->attr['title']);
    	$width = !empty($this->attr['width'])?$this->attr['width']:0;	//默认为自动宽度
    	//设置表头
    	if( is_array($this->mete) && !empty($this->mete) ){
    		foreach($this->mete as $key => $val){
    			$key++;
    			$index = $this->n2l($key);
    			//定义列宽
    			if($width==0){
    				$Sheet->getColumnDimension($index)->setAutoSize(true);
    			}else{
    				$Sheet->getColumnDimension($index)->setWidth($width);
    			}
    			$this->setActiveSheetIndex(0)->setCellValue($index.'1', $this->str2utf8($val['header']));
    		}
    	}
    		
    	//表格写入数据
    	if( is_array($this->data) && !empty($this->data) ){
    		$i = 1;
    		foreach($this->data as $k => $v)
    		{
    			$i++;
    			foreach($this->mete as $kk => $vv)
    			{
    				$kk++;
    				$field_index = $this->n2l($kk).$i;
    				$field_value = $this->str2utf8($v[$vv['field']]);
    				if($vv['type']=='image'&&$field_value){
    					$draw = new PHPExcel_Worksheet_Drawing();
    					$draw->setPath($field_value);
    					$draw->setCoordinates($field_index);
    					$draw->setWorksheet($Sheet);
    				}else{
    					if(is_numeric($field_value)&&$field_value<1000000000){
    						$Sheet->setCellValueExplicit($field_index, $field_value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
    					}else{
    						$Sheet->setCellValueExplicit($field_index, $field_value, PHPExcel_Cell_DataType::TYPE_STRING);
    					}
    				}
    					
    					
    			}
    		}
    	}
    
    	$objWriter = PHPExcel_IOFactory::createWriter($this, 'Excel5');
    	if($is_save){
    		$filename = dirname(__FILE__).DS.'data.xls';
    		$objWriter->save($filename);
    		$file_size = filesize($filename);
    		header('Content-Type: application/vnd.ms-excel');
    		header('Content-Disposition: attachment;filename="'.$this->attr['filename'].'.xls"');
    		header('Cache-Control: max-age=0');
    		ob_clean();
    		flush();
    		header("Accept-Ranges: bytes");
    		header("Accept-Length: $file_size");
    		readfile("$filename");
    	}else{
    		header("Content-Type:application/force-download");
    		header("Content-Type:application/vnd.ms-execl");
    		header("Content-Type:application/octet-stream");
    		header("Content-Type:application/download");
    		header('Content-Type:application/vnd.ms-excel');
    		header('Content-Disposition: attachment;filename="'.$this->attr['filename'].'.xls"');
    		header('Cache-Control: max-age=0');
    		ob_clean();
    		flush();
    		header("Content-Transfer-Encoding:binary");
    		$objWriter->save("php://output");
    	}
    	exit;
    
    }
    
    /**
     *	读取excel内容
     *	@param	$filepath		String		excel文件路径
     *	@param	$start_line		Int			读取数据的起始行数默认为第二行开始
     *	return	array()	 返回读取到的数组
     */
    
    public function read($filepath, $start_line = 2){
    	if(empty($filepath))	return false;
    	if(file_exists($filepath)){
    		$objReader = PHPExcel_IOFactory::createReader('Excel5');
    		$objPHPExcel = $objReader->load($filepath);
    		$sheet = $objPHPExcel->getSheet(0);
    		$highestRow = $sheet->getHighestRow();        //取得总行数
    		$highestColumn = $sheet->getHighestColumn(); //取得总列数
    
    		$objWorksheet = $objPHPExcel->getActiveSheet();
    		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
    		$list = $data =array();
    			
    		for ($row = $start_line;$row <= $highestRow;$row++)
    		{
    		for ($col = 0;$col < $highestColumnIndex;$col++)
    		{
    		$data[$row][$col] =$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
    		}
    		}
    		return $data;
    		}
    		}
    		/**
    	 * 读取多个多个sheet excel
    	 * */
    		public function sheets_read($filepath, $start_line = 2){
    		if(empty($filepath))	return false;
    		if(file_exists($filepath)){
    		$objReader = PHPExcel_IOFactory::createReader('Excel5');
    		$objPHPExcel = $objReader->load($filepath);
    
    		$sheets = $objPHPExcel->getAllSheets();
    		$data =array();
    		foreach ($sheets as $k=>$sheet){
    		$highestRow = $sheet->getHighestRow();        //取得总行数
    		$highestColumn = $sheet->getHighestColumn(); //取得总列数
    			
    		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
    		for ($row = $start_line;$row <= $highestRow;$row++)
    		{
    		for ($col = 0;$col < $highestColumnIndex;$col++)
    		{
    		$data[$k][$row][$col] =$sheet->getCellByColumnAndRow($col, $row)->getValue();
    		}
    		}
    		}
    		return $data;
    		}
    		}
    			
    
    		/**
    	 * 字母转换为数字
    	 * @param	$char	String	需转换为数字的单个字母
    	 * return int	返回数字
    	 */
    	 private function l2n($char){
    	 if(strlen($char)==2){
    	 $s0=substr($char,0,1);
    	 $n=(ord($s0)-ord('A')+1)*26 +ord($s1)-ord('A')+1;
    }else{
    $n=ord($char)-ord('A')+1;
    }
    return $n;
    }
    
    	/**
    	 * 数字转换为字母 A-Z AA-AZ ...
    	 * @param	$int	int		需转换为字母的数字
    	 * return	String	返回字母 例如A 或者 AA
    	 */
    	 	private function n2l($int){
    	 	if($int > 26){
    	 	$s=$int%26;//余数
    	 	$n=floor($int/26);//整数
    	 	if($s !==0){
    	 	$c=chr(ord('A')+$n-1).chr(ord('A')+$s-1);
    }else{
    $c=chr(ord('A')+($n-1)-1).'Z';
    			}
    	 	}else{
    	 	$c=chr(ord('A')+$int-1);
    	 	}
    	 	return $c;
    	 	}
    
    	 	/**
    	 	* 字符串编码转换为utf-8
    	 	* @param  $str	 String	 字符串
    	 	* return 字符串
    	 	*/
    	 	private function str2utf8($str)
    	 		{
    	 		$out = iconv('UTF-8','UTF-8',$str);
    	 		if($out==$str)
    	 			{
    	 			return $out;
    	 	}
    	 	else
    	 	{
    			$out = iconv('GB2312','UTF-8',$str);
    			 }
    			 return $out;
    			 }
    			 /**
    	 	* 多个sheet导出
    	 	* */
    
    	 	public function sheetOutput( $data, $attr, $is_save = '' ){
    	 	//当前页面字符编码
    	 		ob_end_clean();
    	 		header("Content-Type:text/html;charset=utf-8");
		$this->data = $data;
    	 		$this->attr = $attr;
    	 		//设置文档属性
    	 		if( is_array($this->attr) && !empty($this->attr)){
    	 		if(!empty($this->attr['title'])) $this->getProperties()->setTitle(iconv('utf-8','gb2312',$this->attr['title']));
    	 		if(!empty($this->attr['author'])) $this->getProperties()->setCreator(iconv('utf-8','gb2312',$this->attr['author']));
    	 		if(!empty($this->attr['subject'])) $this->getProperties()->setSubject(iconv('utf-8','gb2312',$this->attr['subject']));
    	 		if(!empty($this->attr['keyword'])) $this->getProperties()->setKeywords(iconv('utf-8','gb2312',$this->attr['keyword']));
    	 		if(!empty($this->attr['description'])) $this->getProperties()->setDescription(iconv('utf-8','gb2312',$this->attr['description']));
    	 		$this->attr['filename'] = !empty($this->attr['filename'])?iconv('utf-8','gb2312',$this->attr['filename']):'data';
    	 		}
    	 		//设置excel当前sheet
    	 		foreach ($data as $k=>$v){
					if($k==0){
    	 		$this->setActiveSheetIndex(0);
    	 		$Sheet = $this->getActiveSheet();
					}else{
    					$this->createSheet();
    					$Sheet = $this->getActiveSheet($k);
    	 }
    	 $title=$v['title'];
    	 $this->mete=$v['mete'];
    	 $this->setActiveSheetIndex($k)->setTitle($title);
    	 $width = !empty($this->attr['width'])?$this->attr['width']:0;	//默认为自动宽度
    	 //设置表头
    	 if( is_array($this->mete) && !empty($this->mete) ){
    	 		foreach($this->mete as $key => $val){
    	 		$key++;
    	 		$index = $this->n2l($key);
    	 		//定义列宽
    	 				if($width==0){
    								$Sheet->getColumnDimension($index)->setAutoSize(true);
    	 }else{
    	 $Sheet->getColumnDimension($index)->setWidth($width);
    	 }
    	 			$this->setActiveSheetIndex($k)->setCellValue($index.'1', $this->str2utf8($val['header']));
    	 			}
    					}
    					$exelData=$v['data'];
    					//表格写入数据
    					if( is_array($exelData) && !empty($exelData) ){
    					$i = 1;
    					foreach($exelData as  $d)
    					{
    					$i++;
    					foreach($this->mete as $kk => $vv)
    					{
    						$kk++;
    					$field_index = $this->n2l($kk).$i;
    					$field_value = $this->str2utf8($d[$vv['field']]);
    					if(!empty($field_index))
    					{
    					if($vv['type']=='image'&&$field_value){
    					$draw = new PHPExcel_Worksheet_Drawing();
    					$draw->setPath($field_value);
    					$draw->setCoordinates($field_index);
    					$draw->setWorksheet($this->setActiveSheetIndex($k));
    					}else{
    					if(is_numeric($field_value)&&$field_value<1000000000){
    					$this->setActiveSheetIndex($k)->setCellValueExplicit($field_index, $field_value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
    					}else{
    					$this->setActiveSheetIndex($k)->setCellValueExplicit($field_index, $field_value, PHPExcel_Cell_DataType::TYPE_STRING);
    					}
    					}
    					}
    					}
    					}
    					}
    					unset($Sheet);
    					}
    					$objWriter = PHPExcel_IOFactory::createWriter($this, 'Excel5');
    					if($is_save){
    					$filename = dirname(__FILE__).DS.'data.xls';
    					$objWriter->save($filename);
    					$file_size = filesize($filename);
    					header('Content-Type: application/vnd.ms-excel');
    					header('Content-Disposition: attachment;filename="'.$this->attr['filename'].'.xls"');
    					header('Cache-Control: max-age=0');
    					header("Accept-Ranges: bytes");
			header("Accept-Length: $file_size");
    			readfile("$filename");
    					}else{
    					header("Content-Type:application/force-download");
    					header("Content-Type:application/vnd.ms-execl");
    					header("Content-Type:application/octet-stream");
			header("Content-Type:application/download");
    					header('Content-Type:application/vnd.ms-excel');
    					header('Content-Disposition: attachment;filename="'.$this->attr['filename'].'.xls"');
    					header('Cache-Control: max-age=0');
    					header("Content-Transfer-Encoding:binary");
    							$objWriter->save("php://output");
		}
		exit;
    
    					}

}
