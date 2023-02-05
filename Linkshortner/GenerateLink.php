<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
require_once './Page.php';

class GenerateLink extends Page
{
    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData():void
    {
    }

    protected function generateView():void
    {
        $this->generatePageHeader('H-DA Link Shortner!');

        echo<<<EOT
        <body>
            <header>
                <nav id="navigation" role="navigation">
                    <a href="GenerateLink.php">Home</a>
                    <a href="GenerateLink.php">Product</a>
                    <a href="GenerateLink.php">Company</a>
                    <a href="GenerateLink.php">Blog</a>
                </nav>
            </header>
            <main>
                <h1>Link Shortner!</h1>
                <form id="linkForm" method="post">
                    <input id="inputLink" name="inputLink" type="text" onkeyup="requestData()">
                    <button type="submit">Send!</button>
                </form>
                <p id = "linkHash" name="txtHash">Hash:</p>

                <p>Send a URL and we shorten for you!</p>
            </main>
        </body>
        <footer>H_DA EWA 2021</footer>
EOT;
        $this->generatePageFooter();
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();

        if(isset($_POST["inputLink"])){
            $url = $_POST["inputLink"];
            if (strlen($url) <= 0) {
                throw new Exception("Bitte geben Sie in beiden Feldern etwas an!");
            }else{
            
                $hash = hash('crc32', $url);
                $sqlUrl = $this->_database->real_escape_string($url);
                $sqlHash = $this->_database->real_escape_string($hash);

                $SQLabfrage = "SELECT * FROM hash2URL WHERE " .
                    "url = \"$sqlUrl\" AND hash = \"$sqlHash\"";
                
                $Recordset = $this->_database->query($SQLabfrage);

                if ($Recordset->num_rows > 0) {
                    $Recordset->free();
                    throw new Exception("Dieser Hash ist bereits eingetragen.");
                } else { // also neu eintragen!
                    $SQLabfrage = "INSERT INTO hash2URL SET " .
                        "url = \"$sqlUrl\", hash = \"$sqlHash\"";
                    $this->_database->query($SQLabfrage);
                }
            }
        }
    }

    public static function main():void
    {
        try {
            $page = new GenerateLink();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

GenerateLink::main();
