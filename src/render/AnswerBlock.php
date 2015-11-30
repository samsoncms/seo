<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 24.09.2015
 * Time: 16:17
 */

namespace samsoncms\seo\render;

/**
 * Class AnswerBlock block which show response from server
 * @package samsoncms\seo\render
 */
class AnswerBlock {

    /** @var string View to item */
    public $viewItem = 'element/answerblock';

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
            ->output();

        return $html;
    }
}