<?php
    class SqliteStore implements DataStore {
        private $dbFile;
        private $tableName;
        private $dbConn;
        private $date;
        private $expireSeconds;

        function __construct($config) {
            $this->dbFile = $config['dbFile'];
            $this->tableName = $config['tableName'];
            $this->dbConn = new PDO('sqlite:' . $this->dbFile);
            $this->dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->date = new DateTime();
            $this->expireSeconds = 3600;
        }

        function createEntry($identifier, $accessToken, $refreshToken, $expireTimestamp) {
            $statement = $this->dbConn->prepare("INSERT INTO $this->tableName (identifier, accessToken, refreshToken, expireTimestamp) VALUES(?, ?, ?, ?)");
            $statement->bindParam(1, $identifier);
            $statement->bindParam(2, $accessToken);
            $statement->bindParam(3, $refreshToken);
            $statement->bindParam(4, $expireTimestamp);
            $row = $statement->execute();
            if(! $row) {
                throw new Exception('Unable to create entry');
            } else {
                return TRUE;
            }
        }

        function readEntry($identifier) {
            $statement = $this->dbConn->prepare("SELECT * FROM $this->tableName WHERE identifier = ?");
            $statement->bindParam(1, $identifier);
            $statement->execute();
            $row = $statement->fetch();
            if(! $row) {
                throw new Exception('No data returned from database for identifier');
            }
            return array(
                'identifier' => $row['identifier'],
                'accessToken' => $row['accessToken'],
                'refreshToken' => $row['refreshToken'],
                'expireTimestamp' => $row['expireTimestamp']
            );
        }

        function updateEntry($identifier, $accessToken, $expireTimestamp) {
            $statement = $this->dbConn->prepare("UPDATE $this->tableName SET accessToken = ?, expireTimestamp = ?  WHERE identifier = ?");
            $statement->bindParam(1, $accessToken);
            $statement->bindParam(2, $expireTimestamp);
            $statement->bindParam(3, $identifier);
            $row = $statement->execute();
            if(! $row) {
                throw new Exception('Unable to update entry');
            } else {
                return TRUE;
            }
        }
        
        function deleteEntry($identifier) {
            $statement = $this->dbConn->prepare("DELETE FROM $this->tableName WHERE identifier = ?");
            $statement->bindParam(1, $identifier);
            $row = $statement->execute();
            if(! $row) {
                throw new Exception('Unable to delete entry');
            } else {
                return TRUE;
            }
        }

        function listEntries() {
            $entries = array();
            $statement = $this->dbConn->prepare("SELECT identifier FROM $this->tableName");
            $statement->execute();
            $rows = $statement->fetchAll();
            if($rows === FALSE) {
                throw new Exception('An error occured on the SELECT statement');
            }
            foreach($rows as $row) {
                array_push($entries, $row['identifier']);
            }
            return $entries;
        }
    }
?>
