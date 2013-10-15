<?php
    class UrlCreator {
        public function __construct($config) {
            $this->redirectUri = urlencode($config['redirectUri']);
            $this->clientId = urlencode($config['clientId']);
        }

        public function getUrl($scope) {
            $responseType = 'code';
            $accessType = 'offline';
            $approvalPrompt = 'force';
            try {
                $scopeUrl = $this->getScopeUrl($scope);
                $url = "https://accounts.google.com/o/oauth2/auth"
                    . "?redirect_uri=$this->redirectUri"
                    . "&response_type=$responseType"
                    . "&client_id=$this->clientId"
                    . "&access_type=$accessType"
                    . "&approval_prompt=$approvalPrompt"
                    . "&scope=$scopeUrl";
            } catch(Exception $e) {
                error_log($e->getMessage());
                $url = '';
            }
            return $url;
        }
        
        private function getScopeUrl($scope) {
            switch($scope) {
                case 'adwords':
                    return urlencode('https://adwords.google.com/api/adwords/');
                    break;
                case 'webmastertools':
                    return urlencode('https://www.google.com/webmasters/tools/feeds');
                    break;
                case 'content':
                    return urlencode('https://www.googleapis.com/auth/structuredcontent');
                    break;
                default:
                    throw new Exception("Unknown scope '$scope'");
            }
        }
    }
?>
