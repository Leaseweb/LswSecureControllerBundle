<?php
namespace Lsw\SecureControllerBundle\Tests\Fixtures\Entity;

use Lsw\SecureControllerBundle\Annotation\Secure;

/**
 * Class ClassForClassAnnotation
 * Note that any annotation needed for tests is coming from a mock. So these are only visualise what's going on behind.
 * @Secure(roles="ROLE_USER_EDIT")
 * @package Lsw\SecureControllerBundle\Tests\Fixtures\Entity
 * @author Grégoire Hébert <gregoirehebert@reflexece.com>
 */
class ClassForClassAnnotation
{
    public function stubMethod(){
    }
}