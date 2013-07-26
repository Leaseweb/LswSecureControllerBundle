<?php
namespace Lsw\SecureControllerBundle\Tests\Fixtures\Entity;

use Lsw\SecureControllerBundle\Annotation\Secure;

/**
 * Class ClassForMethodsAnnotations
 * Note that any annotation needed for tests is coming from a mock. So these are only visualise what's going on behind.
 * @package Lsw\SecureControllerBundle\Tests\Fixtures\Entity
 * @author Grégoire Hébert <gregoirehebert@reflexece.com>
 */
class ClassForMethodsAnnotations
{
    /**
     * @Secure(roles="ROLE_USER_EDIT")
     * @return bool
     */
    public function UniqueRole()
    {
        return true;
    }

    /**
     * @Secure(roles="ROLE_USER_ADD")
     * @Secure(roles="ROLE_USER_REMOVE")
     * @return bool
     */
    public function MultiRolesMultiLines()
    {
        return true;
    }

    /**
     * @Secure(roles="ROLE_USER_DUPLICATE,ROLE_USER_ACCESS")
     * @return bool
     */
    public function MultiRolesComaSeparated()
    {
        return true;
    }

    /**
     * @Secure(roles="ROLE_USER_LOGIN")
     * @return bool
     */
    public function RoleWithoutFireWall()
    {
        return true;
    }
}