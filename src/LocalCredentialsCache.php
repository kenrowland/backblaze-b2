<?php


namespace BackblazeB2;


use Carbon\Carbon;

class LocalCredentialsCache implements CredentialsCacheInterface
{
    protected $authTimeSeconds;

    /** @var Carbon $reAuthTime */
    private $reAuthTime;
    private $authToken = null;
    private $apiUrl = null;
    private $downloadUrl = null;
    private $authTimeoutSeconds = 0;

    public function __construct($options)
    {
        $this->authTimeoutSeconds = $options['auth_timeout_seconds'] ?? 12 * 60 * 60; // 12 hour default

        // set reauthorize time to force an authentication to take place
        $this->reAuthTime = Carbon::now('UTC')->subSeconds($this->authTimeoutSeconds * 2);
    }


    /**
     * @inheritDoc
     */
    public function put($values, $ttl = null)
    {
        $this->authToken = $values['authorizationToken'];
        $this->apiUrl = $values['apiUrl'];
        $this->downloadUrl = $values['downloadUrl'];
        $this->reAuthTime = Carbon::now('UTC')->addSeconds($ttl ?? $this->authTimeoutSeconds);
    }

    /**
     * @inheritDoc
     */
    public function get()
    {
        if (Carbon::now('UTC')->timestamp > $this->reAuthTime->timestamp) {
            $this->authToken = $this->apiUrl = $this->downloadUrl = null;
            return false;
        }

        return [
            'authorizationToken' => $this->authToken,
            'apiUrl' => $this->apiUrl,
            'downloadUrl' => $this->downloadUrl
        ];
    }
}