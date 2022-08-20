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
                return ['url' => $splitted_url[1], 'desc' => $this->translate($splitted_url[0])];
            }
        };
        return array_map($filter, (array)($this->fields['url'] ?? []));
    }

    /**
     * Returns one of three things: a full URL to a thumbnail preview of the record
     * if an image is available in an external system; an array of parameters to
     * send to VuFind's internal cover generator if no fixed URL exists; or false
     * if no thumbnail can be generated.
     * 
     * IPIS: Get icon based on record provenance
     *
     * @param string $size Size of thumbnail (small, medium or large -- small is
     * default).
     *
     * @return string|array|bool
     */
    public function getThumbnail($size = 'small')
    {
        // IPIS: Don't show thumbnail from URL in Solr index field "thumbnail"
        /*
        if (!empty($this->fields['thumbnail'])) {
            return $this->fields['thumbnail'];
        }
        */

        // IPIS: Get record format (= ihs, wifo, ...) for getting the appropriate
        // icon for the record. See information on "getThumbnail" on wiki page
        // https://vufind.org/wiki/development:architecture:record_driver_method_master_list
        $arr = [
            //'author'     => mb_substr($this->getPrimaryAuthor(), 0, 300, 'utf-8'),
            //'callnumber' => $this->getCallNumber(),
            'size'       => $size,
            //'title'      => mb_substr($this->getTitle(), 0, 300, 'utf-8'),
            //'recordid'   => $this->getUniqueID(),
            //'source'   => $this->getSourceIdentifier(),
            'contenttype' => $this->getRecordFormat(),
        ];
        
        // IPIS: We don't need these IDs because we only show icons
        /*
        if ($isbn = $this->getCleanISBN()) {
            $arr['isbn'] = $isbn;
        }
        if ($issn = $this->getCleanISSN()) {
            $arr['issn'] = $issn;
        }
        if ($oclc = $this->getCleanOCLCNum()) {
            $arr['oclc'] = $oclc;
        }
        if ($upc = $this->getCleanUPC()) {
            $arr['upc'] = $upc;
        }
        if ($nbn = $this->getCleanNBN()) {
            $arr['nbn'] = $nbn['nbn'];
        }
        if ($ismn = $this->getCleanISMN()) {
            $arr['ismn'] = $ismn;
        }
        if ($uuid = $this->getCleanUuid()) {
            $arr['uuid'] = $uuid;
        }
        */
        
        // IPIS: We don't need the extra details from an ILS driver because we don't
        // have an ILS.
        /*
        // If an ILS driver has injected extra details, check for IDs in there
        // to fill gaps:
        if ($ilsDetails = $this->getExtraDetail('ils_details')) {
            $idTypes = ['isbn', 'issn', 'oclc', 'upc', 'nbn', 'ismn', 'uuid'];
            foreach ($idTypes as $key) {
                if (!isset($arr[$key]) && isset($ilsDetails[$key])) {
                    $arr[$key] = $ilsDetails[$key];
                }
            }
        }
        */
        
        return $arr;
    }

    /**
     * IPIS: Get the value from the "record_format" field (ihs, wifo, ...)
     *
     * @return String
     */
    public function getRecordFormat()
    {
        return $this->fields['record_format'] ?? 'unknown';
    }
}
