<?php
/**
 * OTS feed content block.
 *
 * PHP version 7
 *
 * Copyright (C) Michael Birkner 2022.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Ipis
 * @package  ContentBlock
 * @author   Michael Birkner <birkner_michael@yahoo.de>
 * @license  https://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:contentblocks Wiki
 */
namespace Ipis\ContentBlock;

use \VuFind\Config\PluginManager as ConfigManager;
use \VuFind\Cache\Manager as CacheManager;

use Laminas\Http\Client;
use Laminas\Http\Request;

/**
 * OTS feed content block.
 *
 * @category Ipis
 * @package  ContentBlock
 * @author   Michael Birkner <birkner_michael@yahoo.de>
 * @license  https://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:contentblocks Wiki
 */
class OtsFeed implements \VuFind\ContentBlock\ContentBlockInterface
{
    /**
     * OTS feed config from otsfeed.ini
     *
     * @var array
     */
    protected $otsFeedConfig;

    /**
     * Facet cache plugin manager
     *
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * Lifetime of cache (in seconds).
     *
     * @var int
     */
    protected $cacheLifetime = 60;

    /**
     * OTS base URL
     * 
     * @var string
     */
    protected $otsBaseUrl = 'https://www.ots.at';


    /**
     * Constructor
     *
     * @param ConfigManager $configManager Configuration manager
     * @param CacheManager  $cacheManager  Cache manager
     */
    public function __construct(ConfigManager $configManager,
        CacheManager $cacheManager)
    {
        $this->otsFeedConfig = [];
        if ($configManager) {
            $this->otsFeedConfig = $configManager->get('otsfeed')->toArray();

            if (isset($this->otsFeedConfig['url'])) {
                $this->otsBaseUrl = rtrim($this->otsFeedConfig['url'], '/');
            }
        }
        
        $this->cacheManager = $cacheManager;
    }

    /**
     * Store the configuration of the content block.
     *
     * @param string $settings Settings from otsfeed.ini.
     *
     * @return void
     */
    public function setConfig($settings){}

    /**
     * Return context variables used for rendering the block's template.
     *
     * @return array
     */
    public function getContext()
    {       
        $otsFeedJson = $this->getOtsFeed();

        // Use in OtsFeed.phtml
        return [
            'feed' => $otsFeedJson,
            'link' => $this->otsBaseUrl.'/suche?query='
                .urlencode($this->otsFeedConfig['query'])
        ];
    }

    /**
     * Get data from OTS API or - if applicable - from cache.
     *
     * @return object The OTS json as object
     */
    protected function getOtsFeed() {
        // Get cache lifetime from config
        $this->cacheLifetime = $this->otsFeedConfig['lifetime'];

        // Get object cache.
        $cache = $this->cacheManager->getCache('object');

        // Get OTS feed from cache
        $otsFeed = null;
        if (isset($cache)) {
            $otsFeed = $cache->getItem('OTS_Feed');
        }

        // If OTS feed is in cache, check if it is still valid
        if ($otsFeed) {
            // Return OTS feed if is is still valid
            $lifetime = $otsFeed['lifetime'] ?? $this->cacheLifetime;
            if ((time() - $otsFeed['time']) <= $lifetime) {
                return $otsFeed['entry'];
            }
            // If not valid, clear expired OTS feed from cache and set variable to
            // null for further processing
            $cache->removeItem('OTS_Feed');
            $otsFeed = null;
        }

        // If the OTS feed was not cached or if it was invalidated by the cache
        // lifetime, get it from the API
        if (!$otsFeed) {
            // Request OTS feed from API
            $request = new Request();
            $request->setMethod(Request::METHOD_GET);
            $request->setUri($this->otsBaseUrl.'/api/liste');
            $request->getQuery()->app = $this->otsFeedConfig['apiKey'] ?? '';
            $request->getQuery()->query = $this->otsFeedConfig['query'] ?? '';
            $request->getQuery()->format = $this->otsFeedConfig['format'] ?? 'json';
            $request->getQuery()->anz = $this->otsFeedConfig['numberOfResults']
                ?? 10;
            $request->getQuery()->sourcetype = $this->otsFeedConfig['sourcetype']
                ?? 'ALL';
            $client = new Client();
            $response = $client->send($request);
            $otsFeed = \Laminas\Json\Json::decode($response->getBody());

            // Set feed to cache
            $otsFeedCacheItem = [
                'time' => time(),
                'lifetime' => $this->cacheLifetime,
                'entry' => $otsFeed,
            ];
            $cache->setItem('OTS_Feed', $otsFeedCacheItem);
        }

        return $otsFeed;
    }
}
?>