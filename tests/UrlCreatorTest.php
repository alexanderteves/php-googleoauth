<?php
    require_once('PHPUnit/Framework/TestCase.php');
    require_once('src/UrlCreator.php');

    class UrlCreatorTest extends PHPUnit_Framework_TestCase {
        public function setUp() {
            $this->config = parse_ini_file('config.ini');
            $this->urlcreator = new UrlCreator($this->config);
        }
        
        public function testGetUrl() {
            $this->assertStringStartsWith('https', $this->urlcreator->getUrl('adwords'));
            $this->assertTrue($this->urlcreator->getUrl('leberwurst') === '');
        }
    }
?>
