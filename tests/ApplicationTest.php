<?php
    require_once('PHPUnit/Framework/TestCase.php');

    class ApplicationTest extends PHPUnit_Framework_TestCase {
        public function setUp() {
            $this->config = parse_ini_file('config.ini');
        }

        public function testConfig() {
            # Section generic_api
            $this->assertTrue(array_key_exists('clientId', $this->config));
            $this->assertTrue(array_key_exists('clientSecret', $this->config));
            $this->assertTrue(array_key_exists('tokenUrl', $this->config));
            $this->assertTrue(array_key_exists('redirectUri', $this->config));

            # Section database
            $this->assertTrue(array_key_exists('dbFile', $this->config));
            $this->assertTrue(array_key_exists('tableName', $this->config));
        }
    }
?>
