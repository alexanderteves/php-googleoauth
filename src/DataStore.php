<?php
    /**
    * Implement this interface for all storage mechanisms you want to use with GoogleOauth.php
    */
    interface DataStore {
        /**
        * Creates a new data element
        *
        * @param string $identifier Unique identifier for element
        * @param string $accessToken First access token obtained
        * @param string $refreshToken First refresh token obtained
        * @param int $expireTimestamp UNIX epoch timestamp of creation moment
        * @return bool Return TRUE on success, else throw exception 
        */
        function createEntry($identifier, $accessToken, $refreshToken, $expireTimestamp);
        
        /**
        * Read one data element
        *
        * @param string $identifier Unique identifier for element
        * @return array Array with keys 'identifier', 'accessToken', 'refreshToken', 'expireTimestamp' and their respective values on success, else throw exception
        */
        function readEntry($identifier);

        /**
        * Update specific element's access token
        *
        * @param string $identifier Unique identifier for element
        * @param string $accessToken New access token
        * @param $expireTimestamp New timestamp from which token will be expired
        * @return bool Return TRUE on success, else throw exception
        */
        function updateEntry($identifier, $accessToken, $expireTimeStamp);

        /**
        * Delete specific element
        *
        * @param string $identifier Unique identifier for element
        * @return bool Return TRUE on success, else throw exception
        */
        function deleteEntry($identifier);

        /**
        * List all elements
        *
        * @return array Array of all identifiers (might be empty), throw exception on error
        */
        function listEntries();
    }
?>
