<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
require_once './Page.php';

class CalculateHash extends Page
{
    private string $inputUrl="";

    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData():array
    {
        // Aufruf prüfen:
        $linkHashPair = array();
        $data = array();

        //$linkHashPair["link"] = $this->link;

        $linkHashPair["hash"] = hash('crc32', $this->inputUrl);
        //$data[] = array("hash" => hash('crc32', $this->inputUrl));

        $data[] = $linkHashPair;
        //$linkHashPair->free();
        return $data;
    }

    protected function generateView():void
    {
        header("Content-Type: application/json; charset=UTF-8");      
        $JSON_DATA = json_encode($this->getViewData());
        if($this->inputUrl !== ""){
            echo $JSON_DATA;
        }
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();

        // Aufruf prüfen:
        if (isset($_GET["inputLink"])){
            $this->inputUrl = $_GET["inputLink"];
        }
    }

    public static function main():void
    {
        try {
            $page = new CalculateHash();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

CalculateHash::main();
