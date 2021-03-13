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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Doctrine\DBAL\DBALException;

use JambageCom\Import\Api\Api;


/**
 * Class for example slots to import files into TYPO3 tables
 */
class FunctionSlots implements \TYPO3\CMS\Core\SingletonInterface
{
    protected $tables = ['tt_board'];
    protected $mainTable = 'tt_board';

    /**
     * Constructor
     */
    public function __construct ()
    {
        $languageFile = 'EXT:' . IMPORT_TT_BOARD_COMMENTS_EXT . '/Resources/Private/Language/locallang.xlf';
        $this->getLanguageService()->includeLLFile($languageFile);
    }

    public function getTables ()
    {
        return $this->tables;
    }

    public function getMainTable ()
    {
        return $this->mainTable;
    }

    /**
     * Adds entries to the menu selector of the import extension
     *
     * @return mixed[] Array with entries for the import menu
     */
    public function getMenu (
        $pObj,
        array $menu
    )
    {
        $tables = $this->getTables();
        foreach ($tables as $table) {
            $menuItem = $this->getLanguageService()->getLL('menu.' . $table);
            $menu[$table] = $menuItem;
        }
        $result = [$pObj, $menu];
        return $result;
    }

    /**
     * imports into the tables tt_board and sys_category if tt_board is part of the given tables
     *
     * @return mixed[] Array with entries for the import menu
     */
    public function importTables (
        $pObj,
        $pid,
        array $paramTables
    )
    {
            // Rendering of the output via fluid
        $api = GeneralUtility::makeInstance(Api::class);
        $mainTable = $this->getMainTable();
        $tables = $this->getTables();
        foreach ($tables as $table) {
            if (in_array($table, $paramTables)) {
                switch ($table) {

                case $mainTable:
                        // import the addresses
                    $relationFile =
                        GeneralUtility::getFileAbsFileName(
                            'EXT:' . IMPORT_TT_BOARD_COMMENTS_EXT . '/Resources/Public/Relations/CommentsTtBoard.xml'
                        );

                    try {
                        GeneralUtility::makeInstance(ConnectionPool::class)
                            ->getConnectionForTable($mainTable)
                            ->exec('ALTER TABLE ' . $mainTable . ' DROP KEY parent;');
                        GeneralUtility::makeInstance(ConnectionPool::class)
                            ->getConnectionForTable($mainTable)
                            ->exec('ALTER TABLE ' . $mainTable . ' DROP KEY parent_select;');
                    } catch (DBALException $e) {
                        // ignore exceptions because of missing keys
                    }

                    $mode = 0;
                    $api->importTableFromTable($relationFile, '', $mode);

                    GeneralUtility::makeInstance(ConnectionPool::class)
                        ->getConnectionForTable($mainTable)
                        ->exec('ALTER TABLE ' . $mainTable . ' ADD KEY parent (pid);');
                    GeneralUtility::makeInstance(ConnectionPool::class)
                        ->getConnectionForTable($mainTable)
                        ->exec('ALTER TABLE ' . $mainTable . ' ADD KEY parent_select (pid,parent);');
                    break;
                }
            }
        }
    }

    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}

