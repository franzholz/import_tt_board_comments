<?php
namespace JambageCom\ImportTtBoardComments\Slots;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

use JambageCom\TslibFetce\Utility\FormUtility;


/**
 * Class for example slots to check and modify records before they get stored
 */
class RecordSlots implements \TYPO3\CMS\Core\SingletonInterface
{
    protected $doublePostCheckKey = null;

    /**
     * Checks the given row of a table during the import if it shall be stored.
     *
     * @return mixed[] Array with the parameters of the function call
     */
    public function checkConvertedRecord (
        $tableName,
        $row,
        $check,
        $duplicateCheckNeeded
    )
    {
        if (
            $tableName != 'tt_board'
        ) {
            return;
        }

    // implement the check here
        $check = true;
        $duplicateCheckNeeded = false;

        if (
            isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][IMPORT_TT_BOARD_COMMENTS_EXT]['spam']['words']) || 
            version_compare(TYPO3_version, '9.5.0', '<') &&
            isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][IMPORT_TT_BOARD_COMMENTS_EXT]['spam.']['words'])
        ) {
            $spamWords = '';
            if (version_compare(TYPO3_version, '9.5.0', '<')) {
                $spamWords = explode(',', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][IMPORT_TT_BOARD_COMMENTS_EXT]['spam.']['words']);
            } else {
                $spamWords = explode(',', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][IMPORT_TT_BOARD_COMMENTS_EXT]['spam']['words']);
            }
            $spamSearch = '/(' . implode('|', $spamWords) . ')/i';            
            $fields = ['author', 'email', 'city', 'subject', 'message'];

            foreach ($fields as $field) {
                $found = preg_match($spamSearch, $row[$field]);
                if ($found)
                    break;
            }
            $check = !$found;
        }

        if (
            $check &&
            isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][IMPORT_TT_BOARD_COMMENTS_EXT]['doublePostCheck']['fields'])
        ) {
            $formUtility = GeneralUtility::makeInstance(FormUtility::class);
            $this->doublePostCheckKey =
                $formUtility->calcDoublePostKey(
                    $row,
                    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][IMPORT_TT_BOARD_COMMENTS_EXT]['doublePostCheck']['fields']
                );

            if (
                $formUtility->checkDoublePostExist(
                    $tableName,
                    'doublePostCheck',
                    $this->doublePostCheckKey
                )
            ) {
                $check = false;
            }
        }


        $result = [
            $tableName,
            $row,
            $check,
            $duplicateCheckNeeded
        ];
        return $result;
    }
    
    /**
     * modifies the given row of a table during the import
     *
     * @return mixed[] Array with the parameters of the function call
     */
    public function converteRecord (
        $tableName,
        $row,
        $convertedRow
    )
    {
        if (
            $tableName == 'tt_board'
        ) {
            $convertedRow = $row;
            $convertedRow['doublePostCheck'] = $this->doublePostCheckKey;
        }

        $result = [
            $tableName,
            $row,
            $convertedRow
        ];
        return $result;
    }
}

