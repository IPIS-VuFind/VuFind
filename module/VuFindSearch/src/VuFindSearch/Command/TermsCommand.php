<?php

/**
 * Fetch terms from the backend (currently only supported by Solr)
 *
 * PHP version 7
 *
 * Copyright (C) Villanova University 2021.
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
 * @category VuFind
 * @package  Search
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org
 */
namespace VuFindSearch\Command;

use VuFindSearch\Backend\Solr\Backend;
use VuFindSearch\ParamBag;

/**
 * Fetch terms from the backend (currently only supported by Solr)
 *
 * @category VuFind
 * @package  Search
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org
 */
class TermsCommand extends CallMethodCommand
{
    /**
     * Constructor.
     *
     * @param string    $backendId Search backend identifier
     * @param string    $field     Index field
     * @param string    $start     Starting term (blank for beginning of list)
     * @param int       $limit     Maximum number of terms
     * @param ?ParamBag $params    Search backend parameters
     */
    public function __construct(
        string $backendId,
        string $field,
        string $start,
        int $limit,
        ParamBag $params = null
    ) {
        parent::__construct(
            $backendId,
            Backend::class, // we should define interface, if needed in more places
            'terms',
            [$field, $start, $limit],
            $params
        );
    }
}
