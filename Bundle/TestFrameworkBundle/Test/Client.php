<?php

namespace Oro\Bundle\TestFrameworkBundle\Test;

use Symfony\Bundle\FrameworkBundle\Client as BaseClient;
use Symfony\Component\BrowserKit\Cookie;

class Client extends BaseClient
{
    const LOCAL_URL = 'http://localhost';

    /**
     * @var bool
     */
    protected $isHashNavigation = false;

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function request(
        string $method,
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        string $content = null,
        bool $changeHistory = true
    ) {
        if (strpos($uri, 'http://') === false && strpos($uri, 'https://') === false) {
            $uri = self::LOCAL_URL . $uri;
        }

        if ($this->getServerParameter('HTTP_X-WSSE', '') !== '' && !isset($server['HTTP_X-WSSE'])) {
            //generate new WSSE header
            parent::setServerParameters(WebTestCase::generateWsseAuthHeader());
        }

        // set the session cookie
        $sessionOptions = $this->kernel->getContainer()->getParameter('session.storage.options');
        $this->getCookieJar()->set(new Cookie($sessionOptions['name'], 'test'));

        parent::request($method, $uri, $parameters, $files, $server, $content, $changeHistory);

        return $this->crawler;
    }

    /**
     * Generates a URL or path for a specific route based on the given parameters.
     *
     * @param string $name
     * @param array $parameters
     * @param bool $absolute
     *
     * @return string
     */
    protected function getUrl($name, $parameters = [], $absolute = false)
    {
        return $this->getContainer()->get('router')->generate($name, $parameters, $absolute);
    }

    /**
     * @param array|null $content
     *
     * @return bool
     */
    protected function isRedirectResponse($content)
    {
        return $content && !empty($content['redirect']);
    }

    /**
     * @param array|null $content
     *
     * @return bool
     */
    protected function isContentResponse($content)
    {
        return $content && is_array($content) && array_key_exists('content', $content);
    }

    /**
     * @param array $server
     */
    public function mergeServerParameters(array $server)
    {
        $this->server = array_replace($this->server, $server);
    }
}
