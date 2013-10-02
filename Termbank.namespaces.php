<?php
/**
 * Translations of the namespaces introduced by MyExtension.
 *
 * @file
 */

$namespaceNames = array();

// For wikis where the MyExtension extension is not installed.
if( !defined( 'NS_MYEXTENSION' ) ) {
        define( 'NS_MYEXTENSION', 2510 );
}

if( !defined( 'NS_MYEXTENSION_TALK' ) ) {
        define( 'NS_MYEXTENSION_TALK', 2511 );
}

/** English */
$namespaceNames['en'] = array(
        NS_MYEXTENSION => 'MyNamespace',
        NS_MYEXTENSION_TALK => 'MyNamespace_talk',
);

/** Finnish (Suomi) */
$namespaceNames['fi'] = array(
        NS_MYEXTENSION => 'Nimiavaruuteni',
        NS_MYEXTENSION_TALK => 'Keskustelu_nimiavaruudestani',
);

?>
