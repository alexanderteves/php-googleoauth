<?php
    class SqliteStore implements DataStore {
        private $dbConn;
        private $date;
        private $expireSeconds;

        function __construct() {
            $this->dbConn = new PDO('sqlite:main.sqlite'); // TODO Hardcoded
            $this->dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->date = new DateTime();
            $this->expireSeconds = 3600;
        }

        function createEntry($identifier, $accessToken, $refreshToken, $expireTimestamp) {
            $statement = $this->dbConn->prepare('INSERT INTO token (identifier, accessToken, refreshToken, expireTimestamp) VALUES(?, ?, ?, ?)');
            $statement->bindParam(1, $identifier);
            $statement->bindParam(2, $accessToken);
            $statement->bindParam(3, $refreshToken);
            $statement->bindParam(4, $expireTimestamp);
            $result = $statement->execute();
            if(!($result)) {
                throw new Exception('Unable to create entry');
            } else {
                return TRUE;
            }
        }

        function readEntry($identifier) {
            $statement = $this->dbConn->prepare('SELECT * FROM token WHERE identifier = ?');
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
            $statement = $this->dbConn->prepare('UPDATE token SET accessToken = ?, expireTimestamp = ?  WHERE identifier = ?');
            $statement->bindParam(1, $accessToken);
            $statement->bindParam(2, $expireTimestamp);
            $statement->bindParam(3, $identifier);
            $result = $statement->execute();
            if(!($result)) {
                throw new Exception('Unable to update entry');
            } else {
                return TRUE;
            }
        }
    }
?>
