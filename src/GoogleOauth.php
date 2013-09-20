<?php
    require_once('HTTP/Request2.php');

    /**
    * Tool for creating, obtaining and managing Google OAuth2
    * access tokens
    *
    * @author Alexander Teves <alexander.teves@gmail.com>
    * @version 0.1
    */
    class GoogleOauth {
        private $expireSeconds;
        private $date;
        private $datastore;
        private $config;

        /** Used for indicating a successful operation */
        const SUCCESS = 0;
        /** Used for indicating a failed operation */
        const ERROR = 1;
        /** Used for indicating a failed datastore operation */
        const DATAERROR = 2;

        /**
        * Sets private members, datastore assignment follow the Dependency Injection
        * pattern
        *
        * @param DataStore $datastore Instance of class implementing the DataStore interface
        * @param array $config Needs keys (with according values) named 'clientId', 'clientSecret', 'tokenUrl', 'redirectUri'
        */
        function __construct($datastore, $config) {
            $this->expireSeconds = 3600; // TODO Hardcoded
            $this->date = new DateTime();
            $this->datastore = $datastore;
            $this->config = $config;
        }

        /**
        * Get token for specific entry from datastore. Triggers refresh / store on expired tokens.
        *
        * @param string $identifier The identifier the entry is saved as
        * @return string The token on success, an empty string on failure
        */
        function getToken($identifier) {
            try {
                $row = $this->datastore->readEntry($identifier);
                if(($this->date->getTimestamp() - $row['expireTimestamp']) >= $this->expireSeconds) {
                    $accessToken = $this->refreshToken($identifier, $row['refreshToken']);
                    $expireTimestamp = $this->date->getTimestamp() + $this->expireSeconds;
                    $this->datastore->updateEntry($identifier, $accessToken, $expireTimestamp);
                } else {
                    $accessToken = $row['accessToken'];
                }
            } catch(Exception $e) {
                error_log($e->getMessage());
                $accessToken = '';
            }
            return $accessToken;
        }

        /**
        * Creates a new access token with Google's api and saves it
        *
        * @param str $identifier The identifier the entry will be saved as
        * @param str $grantToken The grant token received after authentication
        * @return int Returns SUCCESS or ERROR const
        */
        function createToken($identifier, $grantToken) {
            $request = new HTTP_Request2($this->config['tokenUrl'], HTTP_Request2::METHOD_POST);
            $request->addPostParameter(array(
                'code' => $grantToken,
                'client_id' => $this->config['clientId'],
                'client_secret' => $this->config['clientSecret'],
                'redirect_uri' => $this->config['redirectUri'],
                'grant_type' => 'authorization_code'
            ));
            $request->setconfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE)); // TODO Handle certificates right
            try {
                $response = $request->send()->getBody();
                $json = json_decode($response);
                if(isset($json->access_token) && isset($json->refresh_token)) {
                    $expireTimestamp = $this->date->getTimestamp() + $this->expireSeconds;
                    $this->datastore->createEntry($identifier, $json->access_token, $json->refresh_token, $expireTimestamp);
                    return self::SUCCESS;
                } else {
                    throw new Exception("Response does not contain valid tokens ($response)");
                }
            } catch(Exception $e) {
                error_log($e->getMessage());
                return self::ERROR;
            }
        }

        /**
        * If a token is expired (Current and saved timestamp difference > 3600)
        * a refreshed one will be created
        *
        * @param str $identifier The identifier the entry is saved as
        * @param str $refreshToken The element's refresh token
        * @return str Refreshed access token
        */
        private function refreshToken($identifier, $refreshToken) {
            $request = new HTTP_Request2($this->config['tokenUrl'], HTTP_Request2::METHOD_POST);
            $request->addPostParameter(array(
                'client_id' => $this->config['clientId'],
                'client_secret' => $this->config['clientSecret'],
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token'
            ));
            $request->setconfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE)); // TODO Handle certificates right
            $response = $request->send()->getBody();
            $json = json_decode($response);
            if(isset($json->access_token)) {
                return $json->access_token;
            } else {
                throw new Exception("Response does not contain any tokens ($response)");
            }
        }
    }
?>
