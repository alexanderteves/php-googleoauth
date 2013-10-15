<?php
    require_once('PHPUnit/Framework/TestCase.php');
    require_once('src/DataStore.php');
    require_once('src/SqliteStore.php');

    class SqliteStoreTest extends PHPUnit_Framework_TestCase {
        public function setUp() {
            $this->config = parse_ini_file('config.ini');
            $this->sqlitestore = new SqliteStore($this->config);
        }
        
        /**
        * @expectedException Exception
        * @excpedtedExceptionMessage No data returned from database for identifier
        */
        public function testReadEntry() {
            $this->sqlitestore->readEntry('fakeentryidentifier');
        }
    }
?>
