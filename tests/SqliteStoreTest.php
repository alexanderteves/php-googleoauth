<?php
    require_once('PHPUnit/Framework/TestCase.php');
    require_once('src/DataStore.php');
    require_once('src/SqliteStore.php');

    class SqliteStoreTest extends PHPUnit_Framework_TestCase {
        public function setUp() {
            $this->config = parse_ini_file('config.ini');
            $this->sqlitestore = new SqliteStore($this->config);
            $this->date = new Datetime();
        }

        /**
        * @expectedException PDOException
        * @expectedExceptionMessage identifier is not unique
        */
        public function testCreateEntry() {
            $this->assertTrue($this->sqlitestore->createEntry('testentry', 'abc123', 'def456', $this->date->getTimestamp()));
            $this->sqlitestore->createEntry('testentry', 'abc123', 'def456', $this->date->getTimestamp());
        }
        
        /**
        * @expectedException Exception
        * @excpedtedExceptionMessage No data returned from database for identifier
        */
        public function testReadEntry() {
            $this->sqlitestore->readEntry('fakeentryidentifier');
        }

        public function testUpdateEntry() {
            $this->assertTrue($this->sqlitestore->updateEntry('testentry', 'ghi789', $this->date->getTimestamp()));
        }

        public function testDeleteEntry() {
            $this->tearDown();
        }

        public function tearDown() {
            $this->assertTrue($this->sqlitestore->deleteEntry('testentry'));
        }
    }
?>
