<?php
/**
 * IPIS: LocalFile controller
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
 * @package  Controller
 * @author   Michael Birkner <birkner_michael@yahoo.de>
 * @license  https://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:controllers Wiki
 */
namespace Ipis\Controller;

/**
 * IPIS: LocalFile controller
 *
 * @category Ipis
 * @package  Controller
 * @author   Michael Birkner <birkner_michael@yahoo.de>
 * @license  https://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:controllers Wiki
 */
class LocalFileController extends \VuFind\Controller\AbstractBase
{

    /**
     * Open a file or show an error message
     *
     * @return void|\Laminas\View\Model\ViewModel
     */
    public function openAction()
    {
        $ipisConfig = $this->getConfig('ipis')->toArray();
        $webaccessPath = rtrim($ipisConfig['webaccess_path'], '/');

        $filename = $this->params()->fromQuery('filename');
        $fullFilePath = $webaccessPath.'/'.$filename;

        if (file_exists($fullFilePath)) {
            $ext = substr($fullFilePath, -3, 3);
            if ($ext == 'pdf') {
                header('Content-Type: application/pdf');
            }
            else {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($fullFilePath) . '"');
                header('Content-Length: ' . filesize($fullFilePath));
            }
            
            header('Content-Description: File Transfer');
            header('Pragma: public');
            
            readfile($fullFilePath);
        } else {
            return $this->createViewModel(['filename' => $filename]);
        }
    }
}
