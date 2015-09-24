<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 24.09.2015
 * Time: 16:17
 */

namespace samsoncms\seo\render;

/**
 * Class Button custom button in tab
 * @package samsoncms\seo\render
 */
class Button {

    /** @var string View to item */
    public $viewItem = 'element/button';

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
            ->view($this->viewItem)
            ->set('text', $this->data['Title'])
            ->set('Link', $this->data['Link'])
            ->output();
        return $html;
    }
}