<?php
/**
 * Media Defining Class
 *
 * This file defines an auto loader class that handles the loading of DForms 
 * classes in PHP scripts.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the Creative 
 * Commons Attribution-Share Alike United States license that is available 
 * through the world-wide-web at the following URI: 
 * http://creativecommons.org/licenses/by-nc-nd/3.0/us/. If you did 
 * not receive a copy of the license and are unable to obtain it through
 * the web, please send a note to the author and a copy will be provided
 * for you.
 *
 * @category   HTML
 * @package    DForms
 * @subpackage Media
 * @author     Greg Thornton <xdissent@gmail.com>
 * @copyright  2009 Greg Thornton
 * @license    http://creativecommons.org/licenses/by-sa/3.0/us/
 * @link       http://xdissent.github.com/dforms/
 */

/**
 * A base class that provides media statically, or optionally per instance.
 *
 * Subclasses define their media on a class basis by overriding the 
 * ``defineMedia`` static method. Instances may simply supply their own
 * ``media`` member variable, which will short circuit the dynamically
 * generated value (since ``__get()`` won't be called at all for ``media``).
 *
 * Media is inherited by subclasses even if they do not define their own media.
 *
 * @category   HTML
 * @package    DForms
 * @subpackage Media
 * @author     Greg Thornton <xdissent@gmail.com>
 * @copyright  2009 Greg Thornton
 * @license    http://creativecommons.org/licenses/by-sa/3.0/us/
 * @link       http://xdissent.github.com/dforms/
 */
abstract class DForms_Media_MediaDefiningClass
{
    private $_media;
    
    /**
     * Catches requests for ``media`` property and returns media for the class.
     *
     * @return array
     */
    public function __get($name)
    {
        /**
         * Intercept requests for ``media`` property.
         */
        if ($name == 'media') {
            /**
             * Return the form's media.
             */
            if (is_null($this->_media)) {
                $media = $this->getDefinedMedia();
                $this->_media = new DForms_Media_Media($media);
            }
            return $this->_media;
        }
    }

    /**
     * Returns the defined media for the class with inheritance.
     *
     * @return array
     */
    protected function getDefinedMedia($class=null)
    {
        /**
         * Determine the class name.
         */
        if (is_null($class)) {
            $class = get_class($this);
        }
        
        /**
         * Get the class's defined media.
         *
         * PHP5.3 equivalent::
         *
         *     $media = static::defineMedia();
         */
        $media = call_user_func(array($class, 'defineMedia'));
        
        /**
         * Determine the parent class of the form.
         */
        $parent = get_parent_class($class);
        
        /**
         * Bail early if we're dealing with a direct subclass of the base class.
         */
        if ($parent == __CLASS__) {
            return $media;
        }
        
        /**
         * Get parent class media media.
         */
        $parent_media = $this->getDefinedMedia($parent);
        
        /**
         * Merge parent media into this class's media.
         *
         */
        foreach ($parent_media as $key => $val) {
            /**
             * Merge or copy data to the media array.
             */
            if (array_key_exists($key, $media)) {
                $media[$key] = array_merge_recursive($parent_media[$key], $media[$key]);
            } else {
                $media[$key] = $parent_media[$key];
            }
        }
        
        /**
         * Return the media.
         */
        return $media;
    }
    
    /**
     * Defines the media for the class.
     *
     * Subclasses should override this method to return an associative array
     * of media paths. Keys should be the type of media (currently only ``js``
     * and ``css``) and values should be relative or absolute URLs. Media is
     * inherited by subclasses *automatically*, and subclasses shoud *not*
     * call the parent method.
     *
     * .. note:: This method must remain public until PHP5.3 since it must be
     *    accessed from ``call_user_func()``. It should eventually be protected.
     */
    public static function defineMedia()
    {
        /**
         * An empty array is a default.
         */
        return array();
    }
}