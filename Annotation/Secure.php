<?php
namespace Lsw\SecureControllerBundle\Annotation;

/**
 * @Annotation
 */
class Secure
{
    public $roles = "";

    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $values['roles'] = $values['value'];
        }

        $this->roles = $values['roles'];
    }
}