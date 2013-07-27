<?php
namespace Lsw\SecureControllerBundle\Tests\Security;

use PHPUnit_Framework_TestCase;
use Lsw\SecureControllerBundle\Security\ControllerListener;
use Lsw\SecureControllerBundle\Tests\Fixtures\Entity\ClassForClassAnnotation;
use Lsw\SecureControllerBundle\Tests\Fixtures\Entity\ClassForClassAnnotationsMultiLines;
use Lsw\SecureControllerBundle\Tests\Fixtures\Entity\ClassForClassAnnotationsComaSeparated;
use Lsw\SecureControllerBundle\Tests\Fixtures\Entity\ClassForMethodsAnnotations;

use Lsw\SecureControllerBundle\Annotation\Secure;


/**
 * Class ControllerListenerTest
 * @package Lsw\SecureControllerBundle\Tests\Security
 * @author Grégoire Hébert <gregoirehebert@reflexece.com>
 */
class ControllerListenerTest extends PHPUnit_Framework_TestCase
{
    private $classForMethodsAnnotations;
    private $classForClassAnnotation;
    private $classForClassAnnotationsMultiLines;
    private $classForClassAnnotationsComaSeparated;
    private $securityContext;

    /**
     * Tests when a class has a secure annotation
     */
    public function testGettingAnnotationFromClassAndRoleNotGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotation,'stubMethod')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_EDIT"))
                )));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->atLeastOnce())->method('isGranted')->with($this->equalTo('ROLE_USER_EDIT'))->will($this->returnValue(false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when a class has a secure annotation
     */
    public function testGettingAnnotationFromClassAndRoleGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotation,'stubMethod')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_EDIT"))
                )));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->atLeastOnce())->method('isGranted')->with($this->equalTo('ROLE_USER_EDIT'))->will($this->returnValue(true));

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $this->assertNull($controllerListener->onKernelController($event));
    }

    /**
     * Tests when a class has secure annotations written in multiple lines
     */
    public function testGettingMultiLinesAnnotationsFromClassAndLastRoleNotGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotationsMultiLines,'stubMethod')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_EDIT")),
                    new Secure(array('roles'=>"ROLE_USER_ADD"))
                )));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true,false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when a class has secure annotations written in multiple lines
     */
    public function testGettingMultiLinesAnnotationsFromClassAndFirstRoleNotGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotationsMultiLines,'stubMethod')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_EDIT")),
                    new Secure(array('roles'=>"ROLE_USER_ADD"))
                )));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(false,true));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when a class has secure annotations written in multiple lines
     */
    public function testGettingMultiLinesAnnotationsFromClassAndAllRolesGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotationsMultiLines,'stubMethod')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_EDIT")),
                    new Secure(array('roles'=>"ROLE_USER_ADD"))
                )));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true,true));

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $this->assertNull($controllerListener->onKernelController($event));
    }

    /**
     * Tests when a class has secure annotations written in a coma separated way
     */
    public function testGettingComaSeparatedAnnotationsFromClassAndLastRoleNotGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotationsComaSeparated,'stubMethod')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_EDIT,ROLE_USER_ADD"))
                )));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true,false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when a class has secure annotations written in a coma separated way
     */
    public function testGettingComaSeparatedAnnotationsFromClassAndFirstRoleNotGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotationsComaSeparated,'stubMethod')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_EDIT,ROLE_USER_ADD"))
                )));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(false,true));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when a class has secure annotations written in a coma separated way
     */
    public function testGettingComaSeparatedAnnotationsFromClassAndAllRolesGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotationsComaSeparated,'stubMethod')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_EDIT,ROLE_USER_ADD"))
                )));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true,true));

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $this->assertNull($controllerListener->onKernelController($event));
    }

    /**
     * Tests when at least one method has a secure annotation
     */
    public function testGettingAnnotationFromMethodsAndRoleNotGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations,'UniqueRole')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_EDIT"))
                )));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->atLeastOnce())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->atLeastOnce())->method('isGranted')->with($this->equalTo('ROLE_USER_EDIT'))->will($this->returnValue(false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when at least one method has a secure annotation
     */
    public function testGettingAnnotationFromMethodsAndRoleGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations,'UniqueRole')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_EDIT"))
                )));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->atLeastOnce())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->atLeastOnce())->method('isGranted')->with($this->equalTo('ROLE_USER_EDIT'))->will($this->returnValue(true));

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $this->assertNull($controllerListener->onKernelController($event));
    }

    /**
     * Tests when at least one method has secure annotations written in multiple lines
     */
    public function testGettingMultiLinesAnnotationsFromMethodsAndAllRolesGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations,'MultiRolesMultiLines')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_ADD")),
                    new Secure(array('roles'=>"ROLE_USER_REMOVE"))
                )));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true,true));

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $this->assertNull($controllerListener->onKernelController($event));
    }

    /**
     * Tests when at least one method has secure annotations written in multiple lines
     */
    public function testGettingMultiLinesAnnotationsFromMethodsAndLastRoleNotGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations,'MultiRolesMultiLines')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_ADD")),
                    new Secure(array('roles'=>"ROLE_USER_REMOVE"))
                )));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true,false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when at least one method has secure annotations written in multiple lines
     */
    public function testGettingMultiLinesAnnotationsFromMethodsAndFirstRoleNotGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations,'MultiRolesMultiLines')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_ADD")),
                    new Secure(array('roles'=>"ROLE_USER_REMOVE"))
                )));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(false,true));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when at least one method has secure annotations in a coma separated way
     */
    public function testGettingComaSeparatedAnnotationsFromMethodsAndAllRolesGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations,'MultiRolesComaSeparated')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_DUPLICATE,ROLE_USER_ACCESS"))
                )));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true,true));

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $this->assertNull($controllerListener->onKernelController($event));
    }

    /**
     * Tests when at least one method has secure annotations in a coma separated way
     */
    public function testGettingComaSeparatedAnnotationsFromMethodsAndLastRoleNotGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations,'MultiRolesComaSeparated')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_DUPLICATE,ROLE_USER_ACCESS"))
                )));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true,false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when at least one method has secure annotations in a coma separated way
     */
    public function testGettingComaSeparatedAnnotationsFromMethodsAndFirstRoleNotGranted()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations,'MultiRolesComaSeparated')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_DUPLICATE,ROLE_USER_ACCESS"))
                )));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->securityContext->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(false,true));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when at least one method or the class has secure annotations
     */
    public function testGettingAnnotationWithoutFirewall()
    {
        $eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $eventBuilder->disableOriginalConstructor();

        $event = $eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations,'RoleWithoutFireWall')));

        $reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
                    new Secure(array('roles'=>"ROLE_USER_LOGIN"))
                )));

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException');

        $controllerListener = new ControllerListener($reader,$this->securityContext);
        $controllerListener->onKernelController($event);
    }

    /**
     * Instantiate any needed classes.
     */
    public function setUp()
    {
        if (!($this->classForClassAnnotation instanceof ClassForClassAnnotation)) {
            $this->classForClassAnnotation = new ClassForClassAnnotation();
        }

        if (!($this->classForClassAnnotationsMultiLines instanceof ClassForClassAnnotationsMultiLines)) {
            $this->classForClassAnnotationsMultiLines = new ClassForClassAnnotationsMultiLines();
        }

        if (!($this->classForClassAnnotationsComaSeparated instanceof ClassForClassAnnotationsComaSeparated)) {
            $this->classForClassAnnotationsComaSeparated = new ClassForClassAnnotationsComaSeparated();
        }

        if (!($this->classForMethodsAnnotations instanceof ClassForMethodsAnnotations)) {
            $this->classForMethodsAnnotations = new ClassForMethodsAnnotations();
        }

    }

}