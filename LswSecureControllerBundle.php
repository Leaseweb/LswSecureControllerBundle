<?php

namespace Lsw\SecureControllerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Lsw\SecureControllerBundle\DependencyInjection\LswSecureControllerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
* Bundle that provides '@Secure' annotation to secure actions in controllers by specifying required roles.
*
* @author Maurits van der Schee <m.vanderschee@leaseweb.com>
*/
class LswSecureControllerBundle extends Bundle
{
}
