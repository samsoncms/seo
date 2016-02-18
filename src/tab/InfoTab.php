<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 29.09.2015
 * Time: 13:21
 */

namespace samsoncms\seo\tab;

/**
 * Class InfoTab
 * For render some info about module
 * @package samsoncms\seo\tab
 */
class InfoTab {

    /** @var string Path to main view */
    public $indexView = 'info/index';

    public function __construct($renderer, $data = null)
    {
        // Save renderer
        $this->renderer = $renderer;

        // Save passed data
        $this->data = $data;
    }

    // Render view
    public function render()
    {
        return $this->renderer
            ->view($this->indexView)
            ->set($this->data, 'data')
            ->output();
    }

}