<?php
/**
 * Custom default model for Solr records -- used when a more specific model based on
 * the record_format field cannot be found.
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
 * @package  RecordDrivers
 * @author   Michael Birkner <birkner_michael@yahoo.de>
 * @license  https://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:record_drivers Wiki
 */
namespace Ipis\RecordDriver;

/**
 * Custom default model for Solr records -- used when a more specific model based on
 * the record_format field cannot be found.
 *
 * This should be used as the base class for all Solr-based record models.
 *
 * @category Ipis
 * @package  RecordDrivers
 * @author   Michael Birkner <birkner_michael@yahoo.de>
 * @license  https://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:record_drivers Wiki
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class SolrDefault extends \VuFind\RecordDriver\SolrDefault
{
    /**
     * Return an array of associative URL arrays with one or more of the following
     * keys:
     * 
     * Ipis: Checking if there is a description (url text) prepended to the URL
     * itself - separated by pipe character.
     *
     * <li>
     *   <ul>desc: URL description text to display (optional)</ul>
     *   <ul>url: fully-formed URL (required if 'route' is absent)</ul>
     *   <ul>route: VuFind route to build URL with (required if 'url' is absent)</ul>
     *   <ul>routeParams: Parameters for route (optional)</ul>
     *   <ul>queryString: Query params to append after building route (optional)</ul>
     * </li>
     *
     * @return array
     */
    public function getURLs()
    {
        $filter = function ($url) {
            if (strpos($url, '|') === false) {
                // Ipis: No pipe character = no description (url text)
                return ['url' => $url];
            } else {
                // Ipis: We have a pipe character = we have a description (url text)
                $splitted_url = explode('|', $url, 2);
                return ['url' => $splitted_url[1], 'desc' => $splitted_url[0]];
            }
            
        };
        return array_map($filter, (array)($this->fields['url'] ?? []));
    }
}
