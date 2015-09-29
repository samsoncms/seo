<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 24.09.2015
 * Time: 16:21
 */

namespace samsoncms\seo\render;

use samsoncms\seo\render\Button;

/**
 * Class Element generate elements
 * @package samsoncms\seo\render
 */
class Element {

    /** @var string View of group */
    public $groupView = 'element/group';

    /** @var string Name of default group */
    public $defaultGroup = 'default';

    /** @var  \samson\core\ExternalModule */
    public $renderer;

    public function __construct($renderer = null)
    {
        // Save renderer
        $this->renderer = $renderer ? $renderer : m('seo');
    }

    /**
     * Create element
     * @param $element
     * @return mixed
     */
    public function createElement($element)
    {

        // Get class name of element by type of config
        $className = '\samsoncms\seo\render\\'.$element['Type'];

        // Get instance of element
        $instance = new $className($this->renderer, $element['Data']);

        return $instance;
    }

    /**
     * Render all passed elements by config
     * @param $elements
     * @param $nested
     * @return string
     */
    public function renderElements($elements, $nested = false)
    {

        $groups = array();

        // Iterate all elements and get view of them
        foreach ($elements as $element) {

            // Check if this element is nested
            $isNested = (isset($element['Nested'])&&$element['Nested'] == true);

            // If not right go further
            if ($isNested == $nested) {
                continue;
            }

            // Check if this element is need to be hide
            $isHide = (isset($element['Hide'])&&$element['Hide'] == true);

            // If not right go further
            if ($isHide) {
                continue;
            }

            // Find group id
            if (isset($element['group'])) {
                $group = $element['group'];
            } else {
                $group = $this->defaultGroup;
            }

            // Create single element by data
            $elementInstance = $this->createElement($element);

            $content = $elementInstance->render();

            // Add content to group
            if (isset($groups[$group])) {

                // Save group array
                $groups[$group] .= $content;
            } else {

                // Save group array
                $groups[$group] = $content;
            }

        }

        // Get views
        return $this->renderGroup($groups);
    }

    /**
     * Render only nested elements
     * @param $elements
     * @return String
     */
    public function renderNestedElements($elements)
    {
        return $this->renderElements($elements, false);
    }

    /**
     * Render not nested elements
     * @param $elements
     * @return String
     */
    public function renderNotNestedElements($elements)
    {
        return $this->renderElements($elements, true);
    }

    /**
     * Render group of elements
     * @param $groups
     * @return string
     */
    public function renderGroup($groups)
    {

        // Iterate groups
        $html = '';
        foreach ($groups as $groupName => $groupContent) {

            // Get view by group
            $html .= $this->renderer
                ->view($this->groupView)
                ->group($groupName)
                ->content($groupContent)
                ->output();
        }

        return $html;
    }

}