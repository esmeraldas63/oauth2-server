<?php
/**
 * OAuth 2.0 Abstract Server
 *
 * @package     league/oauth2-server
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace League\OAuth2\Server;

use League\OAuth2\Server\Exception;
use League\OAuth2\Server\TokenType\TokenTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use League\Event\Emitter;

/**
 * OAuth 2.0 Resource Server
 */

abstract class AbstractServer
{
    /**
     * The request object
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * Storage classes
     * @var array
     */
    protected $storages = [];

    /**
     * Token type
     * @var \League\OAuth2\Server\TokenType\TokenTypeInterface
     */
    protected $tokenType;

    /**
     * Event emitter
     * @var \League\Event\Emitter
     */
    protected $eventEmitter;

    /**
     * Abstract server constructor
     */
    public function __construct()
    {
        $this->setEventEmitter();
    }

    /**
     * Set an event emitter
     * @param object $emitter Event emitter object
     */
    public function setEventEmitter($emitter = null)
    {
        if ($emitter === null) {
            $this->eventEmitter = new Emitter;
        } else {
            $this->eventEmitter = $emitter;
        }
    }

    /**
     * Add an event listener to the event emitter
     * @param string   $eventName Event name
     * @param callable $listener  Callable function or method
     */
    public function addEventListener($eventName, callable $listener)
    {
        $this->eventEmitter->addListener($eventName, $listener);
    }

    /**
     * Returns the event emitter
     * @return \League\Event\Emitter
     */
    public function getEventEmitter()
    {
        return $this->eventEmitter;
    }

    /**
     * Sets the Request Object
     * @param \Symfony\Component\HttpFoundation\Request The Request Object
     * @return self
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Gets the Request object. It will create one from the globals if one is not set.
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        if ($this->request === null) {
            $this->request = Request::createFromGlobals();
        }

        return $this->request;
    }

    /**
     * Return a storage class
     * @param  string                                                                  $obj The class required
     * @return Storage\ClientInterface|Storage\ScopeInterface|Storage\SessionInterface|Storage\AccessTokenInterface|Storage\AuthCodeInterface|Storage\RefreshTokenInterface
     */
    public function getStorage($obj)
    {
        if (!isset($this->storages[$obj])) {
            throw new Exception\ServerErrorException(
                'The `'.$obj.'` storage interface has not been registered with the server'
            );
        }

        return $this->storages[$obj];
    }

    /**
     * Set the access token type
     * @param  TokenTypeInterface $tokenType The token type
     * @return void
     */
    public function setTokenType(TokenTypeInterface $tokenType)
    {
        $tokenType->setServer($this);
        $this->tokenType = $tokenType;
    }

    /**
     * Get the access token type
     * @return TokenTypeInterface
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }
}
