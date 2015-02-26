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
 * along with GiCalReader.  If not, see <http://www.gnu.org/licenses/>.
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
 * Mustache Template array Loader implementation.
 *
 * An ArrayLoader instance loads Mustache Template source by name from an initial array:
 *
 *     $loader = new ArrayLoader(
 *         'foo' => '{{ bar }}',
 *         'baz' => 'Hey {{ qux }}!'
 *     );
 *
 *     $tpl = $loader->load('foo'); // '{{ bar }}'
 *
 * The ArrayLoader is used internally as a partials loader by Mustache_Engine instance when an array of partials
 * is set. It can also be used as a quick-and-dirty Template loader.
 */
class Mustache_Loader_ArrayLoader implements Mustache_Loader, Mustache_Loader_MutableLoader
{
    private $templates;

    /**
     * ArrayLoader constructor.
     *
     * @param array $templates Associative array of Template source (default: array())
     */
    public function __construct(array $templates = array())
    {
        $this->templates = $templates;
    }

    /**
     * Load a Template.
     *
     * @throws Mustache_Exception_UnknownTemplateException If a template file is not found.
     *
     * @param string $name
     *
     * @return string Mustache Template source
     */
    public function load($name)
    {
        if (!isset($this->templates[$name])) {
            throw new Mustache_Exception_UnknownTemplateException($name);
        }

        return $this->templates[$name];
    }

    /**
     * Set an associative array of Template sources for this loader.
     *
     * @param array $templates
     */
    public function setTemplates(array $templates)
    {
        $this->templates = $templates;
    }

    /**
     * Set a Template source by name.
     *
     * @param string $name
     * @param string $template Mustache Template source
     */
    public function setTemplate($name, $template)
    {
        $this->templates[$name] = $template;
    }
}
