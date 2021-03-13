<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'comments into tt_board',
	'description' => 'This extends the import extension for the conversion from comments to tt_board',
	'category' => 'backend',
	'author' => 'Franz Holzinger',
	'author_email' => 'franz@ttproducts.de',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'author_company' => '',
	'version' => '0.1.0',
	'constraints' => array(
		'depends' => array(
			'php' => '5.5.0-7.99.99',
			'typo3' => '8.7.0-9.99.99',
			'import' => '0.5.0-0.0.0',
			'func' => '9.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

