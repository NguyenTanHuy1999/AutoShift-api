<?php

namespace Gma\Acl\Traits;
/* Class TransformableTrait
 * @package Prettus\Repository\Traits
*/
trait GmaEntityTranslationTrait
{

    /**
     * Translate model
     * @param string $locale
     */
    public function translate($locale = '') {
        if ($locale === 'en') {
            if (!empty($this->en)) {
                foreach ($this->en as $field => $value) {
                    $this->$field = $value;
                }
            }
        }
    }
}