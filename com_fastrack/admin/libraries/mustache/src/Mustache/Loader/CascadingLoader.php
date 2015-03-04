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
 * A Mustache Template cascading loader implementation, which delegates to other
 * Loader instances.
 */
class Mustache_Loader_CascadingLoader implements Mustache_Loader
{
    private $loaders;

    /**
     * Construct a CascadingLoader with an array of loaders:
     *
     *     $loader = new Mustache_Loader_CascadingLoader(array(
     *         new Mustache_Loader_InlineLoader(__FILE__, __COMPILER_HALT_OFFSET__),
     *         new Mustache_Loader_FilesystemLoader(__DIR__.'/templates')
     *     ));
     *
     * @param Mustache_Loader[] $loaders
     */
    public function __construct(array $loaders = array())
    {
        $this->loaders = array();
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    /**
     * Add a Loader instance.
     *
     * @param Mustache_Loader $loader
     */
    public function addLoader(Mustache_Loader $loader)
    {
        $this->loaders[] = $loader;
    }

    /**
     * Load a Template by name.
     *
     * @throws Mustache_Exception_UnknownTemplateException If a template file is not found.
     *
     * @param string $name
     *
     * @return string Mustache Template source
     */
    public function load($name)
    {
        foreach ($this->loaders as $loader) {
            try {
                return $loader->load($name);
            } catch (Mustache_Exception_UnknownTemplateException $e) {
                // do nothing, check the next loader.
            }
        }

        throw new Mustache_Exception_UnknownTemplateException($name);
    }
}
