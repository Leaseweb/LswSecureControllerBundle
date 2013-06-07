<?php
namespace Lsw\SecureControllerBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Secure extends Annotation
{
    public $roles = "";
     
}