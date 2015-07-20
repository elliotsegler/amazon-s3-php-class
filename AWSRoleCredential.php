<?php
  class AWSRoleCredential {
    private $code;
    private $lastUpdated;
    private $typeStr;
    public $accessKeyId;
    public $secretAccessKey;
    public $token;
    private $expiration;

    public function __construct($role) {
      $this->getRole($role);
    }

    public function __get($variable) {
      if(isset($this->data[$variable])) {
        if ($this->isExpired) {
          $this->getRole($role);
        }
        return $this->data[$variable];
      } else {
        throw new Exception('Unknown variable');
      }
    }

    private function isExpired() {
      if (isset($this->expiration)) {
        if (time() >= $this->expiration) {
          return true;
        } else {
          return false;
        }
      } else {
        throw new Exception("Expriration is not set");
      }
    }

    private function getRole($role) {
      //TODO: Sanitize this...
      $url = "http://169.254.169.254/latest/meta-data/iam/security-credentials/$role";
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_URL,$url);
      $result=curl_exec($curl);
      $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);

      if ($http_status_code == 200) {
        $resObj = json_decode($result, true);
        try {
          $this->code = $resObj["Code"];
          $this->lastUpdated = $resObj["LastUpdated"];
          $this->typeStr = $resObj["Type"];
          $this->accessKeyId = $resObj["AccessKeyId"];
          $this->secretAccessKey = $resObj["SecretAccessKey"];
          $this->token = $resObj["Token"];
          $this->expiration = strtotime($resObj["Expiration"]);
        }
        catch (Exception $e) {
          die("Bang!");
          throw new Exception("Error in response...");
        }
      } else {
        throw new Exception("Got error $http_status_code");
      }
    }
  }

  //$cred = new AWSRoleCredential('aws-elasticbeanstalk-ec2-role');
  //print "Access Key: ".$cred->accessKeyId."\n";
  //print "Secret Key: ".$cred->secretAccessKey."\n";
  //print "Session Token: ".$cred->token."\n";
  //var_dump($cred);
?>
