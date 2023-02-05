<?php

declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
require_once './Page.php';

class News extends Page
{

    private string $hash = "";

    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData(): string
    {

        if (strlen($this->hash) <= 0) {
            throw new Exception("Bitte geben Sie in beiden Feldern etwas an!");
        } else {


            $sqlHash = $this->_database->real_escape_string($this->hash);

            $SQLabfrage = "SELECT * FROM hash2URL WHERE " . "hash = \"$sqlHash\"";

            $Recordset = $this->_database->query($SQLabfrage);
            $record = $Recordset->fetch_assoc();
            //call the url
            //print $record["url"];
            //$url = $Recordset["url"];
           return $record["url"];
        }
    }

    protected function generateView(): void
    {
        $url = $this->getViewData();
        header("Location: ". $url, true, 302);
        die();
    }


    protected function processReceivedData(): void
    {
        parent::processReceivedData();

        if (isset($_GET["Hash"])) {
            $this->hash = $_GET["Hash"];
        }
    }

    public static function main(): void
    {
        try {
            $page = new News();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

News::main();
