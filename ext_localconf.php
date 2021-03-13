<?php
defined('TYPO3_MODE') || die('Access denied.');

define('IMPORT_TT_BOARD_COMMENTS_EXT', 'import_tt_board_comments');

if (TYPO3_MODE == 'BE') {
    call_user_func(function () {

        $extensionConfiguration = [];

        if (
            defined('TYPO3_version') &&
            version_compare(TYPO3_version, '9.0.0', '>=')
        ) {
            $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
            )->get(IMPORT_TT_BOARD_COMMENTS_EXT);
        } else {
            $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][IMPORT_TT_BOARD_COMMENTS_EXT]);
        }

        if (isset($extensionConfiguration) && is_array($extensionConfiguration)) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][IMPORT_TT_BOARD_COMMENTS_EXT] = $extensionConfiguration;
        } else {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][IMPORT_TT_BOARD_COMMENTS_EXT] = [];
        }

        /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
        $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
        $signalSlotDispatcher->connect(
            \JambageCom\Import\Controller\ImportTablesWizardModuleFunctionController::class,
                                                    // Signal class name
            'menu',                                 // Signal name
            \JambageCom\ImportTtBoardComments\Slots\FunctionSlots::class,   // Slot class name
            'getMenu'                               // Slot name
        );

        $signalSlotDispatcher->connect(
            \JambageCom\Import\Controller\ImportTablesWizardModuleFunctionController::class,
                                                            // Signal class name
            'import',                                       // Signal name
            \JambageCom\ImportTtBoardComments\Slots\FunctionSlots::class,   // Slot class name
            'importTables'                               // Slot name
        );

        $signalSlotDispatcher->connect(
            \JambageCom\Import\Api\Api::class,
                                                    // Signal class name
            'check',                                // Signal name
            \JambageCom\ImportTtBoardComments\Slots\RecordSlots::class,   // Slot class name
            'checkConvertedRecord'                    // Slot name
        );

        $signalSlotDispatcher->connect(
            \JambageCom\Import\Api\Api::class,
                                                    // Signal class name
            'convert',                              // Signal name
            \JambageCom\ImportTtBoardComments\Slots\RecordSlots::class,   // Slot class name
            'converteRecord'                        // Slot name
        );

    });
}

