<?php

namespace AppBundle;

class SakaiWS
{

    private $host;
    private $user;
    private $pass;
    private $token;
    private $portaltoken;
    private $soapUrl;
    private $uctSoapUrl;
    private $cookie;

    public function __construct($host = 'https://devslscle001.uct.ac.za', $user, $pass) {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->soapUrl = "https://devslscle001.uct.ac.za/sakai-ws/soap/sakai?wsdl";
        $this->uctSoapUrl = "https://devslscle001.uct.ac.za/sakai-ws/soap/uct?wsdl";
        $this->loginToServer();
    }

    private function loginToServer() {
        $login_wsdl = "https://devslscle001.uct.ac.za/sakai-ws/soap/login?wsdl";
        $login = new \SoapClient($login_wsdl, array('exceptions' => 0, 'trace' => 1));
        $this->token = $login->login($this->user, $this->pass);
        if (is_soap_fault($this->token)) {
            echo "SOAP Failt: (faultcode: {$this->token->faultcode}, faultstring: {$this->token->faultstring})";
        }
        $headers = $login->__getLastResponseHeaders();
        $this->cookie = trim(explode(";", explode(":", array_values(array_filter(explode("\n", $headers), function($k) {
                                return strpos($k, "Set-Cookie") > -1;
                              })
                            )[0]
                          )[1]
                        )[0]);
    }

    private function logout($isPortalSession = false) {
        $login_wsdl = "https://devslscle001.uct.ac.za/sakai-ws/soap/login?wsdl";
        $logout = new \SoapClient($login_wsdl);
        if ($isPortalSession) {
            $logout->logout($this->portaltoken);
        }
        else {
            $logout->logout($this->token);
        }
    }

    public function checkUserByEid($eid) {
        $checkRequest = new \SoapClient($this->soapUrl, array('exceptions' => 0));
        $details = $checkRequest->checkForUser($this->token, '01457245');
        if (is_soap_fault($details)) {
          echo "SOAP Failt: (faultcode: {$details->faultcode}, faultstring: {$details->faultstring})";
        }
        var_dump($details);
    }

    public function getOBStool($eid, $homeSite = null) {
        $checkTools = new \SoapClient($this->soapUrl, array('exceptions' => 0));
        if (!$homeSite) {
            $homeSite = $this->getUserHome($eid, $checkTools);
        }

        $siteToolsResponse = $checkTools->getPagesAndToolsForSite($this->token, $eid, $homeSite);
        $siteTools = xmlToArray($siteToolsResponse);
        $obsTool = array_values(
                       array_filter(
                           array_filter($siteTools['pages']['page'], function($page) {
                               return isset($page['tools']['tool']) && !is_sequential($page['tools']['tool']);
                           }),
                           function($page) {
                               if (!isset($page['tools']['tool'])) return false;

                               return $page['tools']['tool']['tool-title'] === 'One Button Studio'; //People may change the title. check against tool-id rather?
                           }
                       )
                   );

        if (!sizeof($obsTool)) {
            return false;
        }

        return $obsTool[0];
    }

    public function addOBStoolToSite($eid, $seriesId, $siteId = null) {
        if (!$siteId) {
            $siteId = $this->getUserHome($eid);
        }

        $createTool = new \SoapClient($this->uctSoapUrl, array('exceptions' => 0));
        $creation = $createTool->addExternalToolToSite(
                        //token
                        $this->token,
                        //site id
                        $siteId,
                        //Title of tool
                       'One Button Studio',
                        //lti launch url
                       'https://mediadev.uct.ac.za/lti',
                       //parameters
                       "sid=$seriesId;tool=/ltitools/courses"
        );
        if (is_soap_fault($creation)) {
            echo "SOAP Failt: (faultcode: {$creation->faultcode}, faultstring: {$creation->faultstring})";
        }

        return $creation;
    }

    public function getUserHome($eid, $checkTools = null) {
        if (!$checkTools) {
            $checkTools = new \SoapClient($this->soapUrl, array('exceptions' => 0));
        }
        $siteDetails = $checkTools->getAllSitesForUser($this->token, $eid);
        if (is_soap_fault($siteDetails)) {
            echo "SOAP Fault: (faultcode: {$siteDetails->faultcode}, faultstring: {$siteDetails->faultstring})";
        }

        $sites = xmlToArray($siteDetails)['item'];
        $homeSite = array_values(array_filter($sites, function($site) {
                        return $site['siteTitle'] === 'Home' && strpos($site['siteId'], '~') > -1;
                    }))[0];

        return $homeSite['siteId'];
    }

    public function __destruct() {
        $this->logout();
    }
}

function is_sequential($arr) {
    if (!is_array($arr)) return false;

    $keys = array_keys($arr);

    return array_keys($keys) === $keys;
}

function xmlToArray($xml) {
    return json_decode(json_encode(simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA)), true);
}
