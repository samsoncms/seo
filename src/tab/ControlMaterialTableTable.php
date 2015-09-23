<?php
namespace samsoncms\seo\tab;

use samson\activerecord\dbQuery;
use samson\pager\Pager;

/**
 * on 02.12.2014 at 12:41
 */
if (class_exists('\samson\cms\web\materialtable\MaterialTableTable')) {

    class ControlMaterialTableTable //extends \samson\cms\web\materialtable\MaterialTableTable
    {

        /**
         * Constructor
         * @param \samson\cms\CMSMaterial $material Current material object
         * @param Pager $pager Pager object
         * @param \samson\cms\Navigation Current table structure object
         * @param string $locale Locale string
         */
        public function __construct(\samson\cms\CMSMaterial & $material, Pager $pager = null, $structure, $locale = 'ru')
        {
            $this->dbQuery = new dbQuery();
            // Retrieve pointer to current module for rendering
            $this->renderModule = &s()->module($this->renderModule);
        }
    }
}
