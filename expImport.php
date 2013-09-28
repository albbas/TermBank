<?php

/*
 * This file imports a csv file given in the expressions command line
 * variable to termwiki
 *
 * The first field in the csv file is the expression
 * The second field in the csv file is the content of the expression page
 */
// Standard boilerplate to define $IP
if (getenv('MW_INSTALL_PATH') !== false) {
    $IP = getenv('MW_INSTALL_PATH');
} else {
    $dir = dirname(__FILE__); $IP = "$dir/../..";
}
require_once("$IP/maintenance/Maintenance.php");

const SEPARATOR = '%';

class TBImportExternalDatabase extends Maintenance {

    public function __construct() {
        parent::__construct();
        $this->mDescription = '...';
        $this->addOption('expressions', '.', true, true);
    }

    public function execute() {

        foreach($this->parseCSV($this->getOption('expressions')) as $expression => $content) {

            $title = Title::makeTitleSafe(NS_EXPRESSION, $expression);
            if (!$title) {
                echo "Invalid title for {$expression}\n";
                continue;
            }

            $this->insert($title, $content);
        }

    }

    protected function parseCSV($filename, $uniq = 0) {
        $data = file_get_contents($filename);
        $rows = str_getcsv($data, "\n");

        $output = array();
        foreach ($rows as $row) {
            $values = str_getcsv($row, "\t");
            $output[$values[0]] = $values[1];
        }
        return $output;
    }

    protected function insert(Title $title, $content) {
        $content = str_replace("|language", "\n|language", $content);
        $content = str_replace("|pos", "\n|pos", $content);
        $content = str_replace("}}", "\n}}\n", $content);

        $user = User::newFromName('SD-expression importer', false);
        $page = new WikiPage($title);
        $page->doEdit($content, 'SD-expression importer', 0, false, $user);
    }
}

$maintClass = 'TBImportExternalDatabase';
require_once(DO_MAINTENANCE);

