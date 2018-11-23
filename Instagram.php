<?php
/**
 * Instagram.php
 *
 * @author      Quang Ngoc Phu
 * @package     Instagram
 *
 */

class Instagram
{
    public $api_recent_uri    = "https://api.instagram.com/v1/users/self/media/recent/?";
    public $api_authorize_uri = "https://www.instagram.com/oauth/authorize/?";
    public $api_token_uri     = "https://api.instagram.com/oauth/access_token/";
    public $client_id         = "";
    public $client_secret     = "";
    public $redirect_uri      = "";
    public $code              = "";
    public $access_token      = "";
    public $response_type     = "code";
    public $grant_type        = "authorization_code";

    /**
     * setClientId
     * @param string $id
     */
    public function setClientId($id)
    {
        $this->client_id = $id;
    }

    /**
     * setClientSecret
     * @param string $secret
     */
    public function setClientSecret($secret)
    {
        $this->client_secret = $secret;
    }

    /**
     * setRedirectUri
     * @param string $uri
     */
    public function setRedirectUri($uri)
    {
        $this->redirect_uri = $uri;
    }

    /**
     * setCode
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * setResponseType
     * @param string $type
     */
    public function setResponseType($type = 'code')
    {
        $this->response_type = $type;
    }

    /**
     * setAccessToken
     * @param string $token
     */
    public function setAccessToken($token)
    {
        $this->access_token = $token;
    }

    /**
     * authorize
     */
    public function authorize()
    {
        $parameter = http_build_query(
            array(
                "client_id"     => $this->client_id,
                "redirect_uri"  => $this->redirect_uri,
                "response_type" => $this->response_type, // "code" or "token"
            )
        );
        header("location: " . $this->api_authorize_uri . $parameter );
    }

    /**
     * getAccessToken
     * @return json
     */
    public function getAccessToken()
    {
        $parameter = http_build_query(
            array(
                "client_id"     => $this->client_id,
                "client_secret" => $this->client_secret,
                "grant_type"    => $this->grant_type,
                "redirect_uri"  => $this->redirect_uri,
                "code"          => $this->code,
            )
        );

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $this->api_token_uri);
        curl_setopt($ch,CURLOPT_POST, 5);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $parameter);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    /**
     * getMediaRecent
     * @param  integer $photo_number
     * @return array
     */
    public function getMediaRecent($photo_number = 10)
    {
        $parameter = http_build_query(
            array(
                "access_token" => $this->access_token,
                "count"        => $photo_number,
            )
        );

        $json      = file_get_contents($this->api_recent_uri . $parameter);
        $instagram = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
        $result = array();
        foreach ($instagram['data'] as $val) {
            $created_time = date("F j, Y", $val['created_time']);
            $created_time = date("F j, Y", strtotime($created_time . " +1 days"));
            $result[] = array(
                'thumbnail'     => $val['images']['thumbnail'],
                'low'           => $val['images']['low_resolution'],
                'standard'      => $val['images']['standard_resolution'],
                'alt'           => $val['caption']['text'],
                'link'          => $val['link'],
                'created'       => $created_time,
                'like_count'    => $val['likes']['count'],
                'comment_count' => $val['comments']['count'],
                'tags'          => $val['tags'],
            );
        }
        return $result;
    }
}
