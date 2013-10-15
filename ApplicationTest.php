<?php
    require_once('PHPUnit/Framework/TestCase.php');
    require_once('src/UrlCreator.php');

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

        public function testUrlCreator() {
            $urlCreator = new UrlCreator($this->config);
            $this->assertStringStartsWith('https', $urlCreator->getUrl('adwords'));
            $this->assertTrue($urlCreator->getUrl('leberwurst') === '');
        }
    }
?>
