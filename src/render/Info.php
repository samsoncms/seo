<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 24.09.2015
 * Time: 16:17
 */

namespace samsoncms\seo\render;

/**
 * Class Info block which show info about something
 * @package samsoncms\seo\render
 */
class Info{

    public function __construct($renderer, $data)
    {
        $this->renderer = $renderer;
        $this->data = $data;
    }

    /**
     * Render element
     * @return mixed
     */
    public function render()
    {
        $html = $this->renderer
            ->view($this->data['View'])
            ->output();

        return $html;
    }
}