<?php

/**
 * Fastrack Reader/Viewer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Fastrack Reader/Viewer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Fastrack.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		Fastrack
 * @author		Hill Range Services http://fastrack.hillrange.com.au
 * @copyright	Copyright (C) 2014  Hill Range Services  All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
/*
 * This file is part of Mustache.php. *
 * (c) 2010-2014 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mustache class autoloader.
 */
class Mustache_Autoloader
{
    private $baseDir;

    /**
     * Autoloader constructor.
     *
     * @param string $baseDir Mustache library base directory (default: dirname(__FILE__).'/..')
     */
    public function __construct($baseDir = null)
    {
        if ($baseDir === null) {
            $baseDir = dirname(__FILE__).'/..';
        }

        // realpath doesn't always work, for example, with stream URIs
        $realDir = realpath($baseDir);
        if (is_dir($realDir)) {
            $this->baseDir = $realDir;
        } else {
            $this->baseDir = $baseDir;
        }
    }

    /**
     * Register a new instance as an SPL autoloader.
     *
     * @param string $baseDir Mustache library base directory (default: dirname(__FILE__).'/..')
     *
     * @return Mustache_Autoloader Registered Autoloader instance
     */
    public static function register($baseDir = null)
    {
        $loader = new self($baseDir);
        spl_autoload_register(array($loader, 'autoload'));

        return $loader;
    }

    /**
     * Autoload Mustache classes.
     *
     * @param string $class
     */
    public function autoload($class)
    {
        if ($class[0] === '\\') {
            $class = substr($class, 1);
        }

        if (strpos($class, 'Mustache') !== 0) {
            return;
        }

        $file = sprintf('%s/%s.php', $this->baseDir, str_replace('_', '/', $class));
        if (is_file($file)) {
            require $file;
        }
    }
}
