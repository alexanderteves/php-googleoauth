<?php
    class JsonStore implements DataStore {
        private $jsonFilename;
        private $date;
        private $expireSeconds;

        function __construct() {
            $this->jsonFilename = '/tmp/datastore.json'; // TODO Hardcoded
            $this->date = new DateTime();
            $this->expireSeconds = 3600; // TODO Hardcoded
        }

        function createEntry($identifier, $accessToken, $refreshToken, $expireTimestamp) {
            $jsonString = file_get_contents($this->jsonFilename);
            $json = json_decode($jsonString);
            if(isset($json->$identifier)) {
                throw new Exception('Duplicate identifier');
            }
            $json->$identifier->accessToken = $accessToken;
            $json->$identifier->refreshToken = $refreshToken;
            $json->$identifier->expireTimestamp = $expireTimestamp;
            file_put_contents($this->jsonFilename, json_encode($json), LOCK_EX);
            return TRUE;
        }

        function readEntry($identifier) {
            $jsonString = file_get_contents($this->jsonFilename);
            $json = json_decode($jsonString);
            if(isset($json->$identifier)) {
                return array(
                    'identifier' => $json->$identifier,
                    'accessToken' => $json->$identifier->accessToken,
                    'refreshToken' => $json->$identifier->refreshToken,
                    'expireTimestamp' => $json->$identifier->expireTimestamp
                );
            } else {
                throw new Exception('Unknown identifier');
            }
        }

        function updateEntry($identifier, $accessToken, $expireTimestamp) {
            $jsonString = file_get_contents($this->jsonFilename);
            $json = json_decode($jsonString);
            if(isset($json->$identifier)) {
                $json->$identifier->accessToken = $accessToken;
                $json->$identifier->expireTimestamp = $expireTimestamp;
                file_put_contents($this->jsonFilename, json_encode($json), LOCK_EX);
                return TRUE;
            } else {
                throw new Exception('Unknown identifier');
            }
        }

        function deleteEntry($identifier) {
            throw new Exception('Not supported yet');
        }

        function listEntries() {
            throw new Exception('Not supported yet');
        }
    }
?>
