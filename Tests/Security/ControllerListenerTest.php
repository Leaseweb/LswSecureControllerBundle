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
 * Class ControllerListenerTest.
 *
 * @author Grégoire Hébert <gregoirehebert@reflexece.com>
 */
class ControllerListenerTest extends PHPUnit_Framework_TestCase
{
    private $classForMethodsAnnotations;
    private $classForClassAnnotation;
    private $classForClassAnnotationsMultiLines;
    private $classForClassAnnotationsComaSeparated;
    private $eventBuilder;
    private $reader;
    private $authorizationChecker;
    private $tokenStorage;

    /**
     * Tests when a class has a secure annotation.
     */
    public function testGettingAnnotationFromClassAndRoleNotGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotation, 'stubMethod')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_EDIT')),
        )));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->atLeastOnce())->method('isGranted')->with($this->equalTo('ROLE_USER_EDIT'))->will($this->returnValue(false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when a class has a secure annotation.
     */
    public function testGettingAnnotationFromClassAndRoleGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotation, 'stubMethod')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_EDIT')),
        )));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->atLeastOnce())->method('isGranted')->with($this->equalTo('ROLE_USER_EDIT'))->will($this->returnValue(true));

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $this->assertNull($controllerListener->onKernelController($event));
    }

    /**
     * Tests when a class has secure annotations written in multiple lines.
     */
    public function testGettingMultiLinesAnnotationsFromClassAndLastRoleNotGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotationsMultiLines, 'stubMethod')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_EDIT')),
            new Secure(array('roles' => 'ROLE_USER_ADD')),
        )));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true, false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when a class has secure annotations written in multiple lines.
     */
    public function testGettingMultiLinesAnnotationsFromClassAndFirstRoleNotGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotationsMultiLines, 'stubMethod')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_EDIT')),
            new Secure(array('roles' => 'ROLE_USER_ADD')),
        )));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(false, true));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when a class has secure annotations written in multiple lines.
     */
    public function testGettingMultiLinesAnnotationsFromClassAndAllRolesGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotationsMultiLines, 'stubMethod')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_EDIT')),
            new Secure(array('roles' => 'ROLE_USER_ADD')),
        )));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true, true));

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $this->assertNull($controllerListener->onKernelController($event));
    }

    /**
     * Tests when a class has secure annotations written in a coma separated way.
     */
    public function testGettingComaSeparatedAnnotationsFromClassAndLastRoleNotGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotationsComaSeparated, 'stubMethod')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_EDIT,ROLE_USER_ADD')),
        )));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true, false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when a class has secure annotations written in a coma separated way.
     */
    public function testGettingComaSeparatedAnnotationsFromClassAndFirstRoleNotGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotationsComaSeparated, 'stubMethod')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_EDIT,ROLE_USER_ADD')),
        )));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(false, true));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when a class has secure annotations written in a coma separated way.
     */
    public function testGettingComaSeparatedAnnotationsFromClassAndAllRolesGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForClassAnnotationsComaSeparated, 'stubMethod')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_EDIT,ROLE_USER_ADD')),
        )));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array()));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true, true));

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $this->assertNull($controllerListener->onKernelController($event));
    }

    /**
     * Tests when at least one method has a secure annotation.
     */
    public function testGettingAnnotationFromMethodsAndRoleNotGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations, 'UniqueRole')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_EDIT')),
        )));

        $this->tokenStorage->expects($this->atLeastOnce())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->atLeastOnce())->method('isGranted')->with($this->equalTo('ROLE_USER_EDIT'))->will($this->returnValue(false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when at least one method has a secure annotation.
     */
    public function testGettingAnnotationFromMethodsAndRoleGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations, 'UniqueRole')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_EDIT')),
        )));

        $this->tokenStorage->expects($this->atLeastOnce())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->atLeastOnce())->method('isGranted')->with($this->equalTo('ROLE_USER_EDIT'))->will($this->returnValue(true));

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $this->assertNull($controllerListener->onKernelController($event));
    }

    /**
     * Tests when at least one method has secure annotations written in multiple lines.
     */
    public function testGettingMultiLinesAnnotationsFromMethodsAndAllRolesGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations, 'MultiRolesMultiLines')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_ADD')),
            new Secure(array('roles' => 'ROLE_USER_REMOVE')),
        )));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true, true));

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $this->assertNull($controllerListener->onKernelController($event));
    }

    /**
     * Tests when at least one method has secure annotations written in multiple lines.
     */
    public function testGettingMultiLinesAnnotationsFromMethodsAndLastRoleNotGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations, 'MultiRolesMultiLines')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_ADD')),
            new Secure(array('roles' => 'ROLE_USER_REMOVE')),
        )));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true, false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when at least one method has secure annotations written in multiple lines.
     */
    public function testGettingMultiLinesAnnotationsFromMethodsAndFirstRoleNotGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations, 'MultiRolesMultiLines')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_ADD')),
            new Secure(array('roles' => 'ROLE_USER_REMOVE')),
        )));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(false, true));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when at least one method has secure annotations in a coma separated way.
     */
    public function testGettingComaSeparatedAnnotationsFromMethodsAndAllRolesGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations, 'MultiRolesComaSeparated')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_DUPLICATE,ROLE_USER_ACCESS')),
        )));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true, true));

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $this->assertNull($controllerListener->onKernelController($event));
    }

    /**
     * Tests when at least one method has secure annotations in a coma separated way.
     */
    public function testGettingComaSeparatedAnnotationsFromMethodsAndLastRoleNotGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations, 'MultiRolesComaSeparated')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_DUPLICATE,ROLE_USER_ACCESS')),
        )));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(true, false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when at least one method has secure annotations in a coma separated way.
     */
    public function testGettingComaSeparatedAnnotationsFromMethodsAndFirstRoleNotGranted()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations, 'MultiRolesComaSeparated')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_DUPLICATE,ROLE_USER_ACCESS')),
        )));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(true));
        $this->authorizationChecker->expects($this->any())->method('isGranted')->will($this->onConsecutiveCalls(false, true));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
        $controllerListener->onKernelController($event);
    }

    /**
     * Tests when at least one method or the class has secure annotations.
     */
    public function testGettingAnnotationWithoutFirewall()
    {
        $event = $this->eventBuilder->getMock();
        $event->expects($this->any())->method('getController')->will($this->returnValue(array($this->classForMethodsAnnotations, 'RoleWithoutFireWall')));

        $this->reader->expects($this->atLeastOnce())->method('getClassAnnotations')->will($this->returnValue(array()));
        $this->reader->expects($this->atLeastOnce())->method('getMethodAnnotations')->will($this->returnValue(array(
            new Secure(array('roles' => 'ROLE_USER_LOGIN')),
        )));

        $this->tokenStorage->expects($this->any())->method('getToken')->will($this->returnValue(false));

        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException');

        $controllerListener = new ControllerListener($this->reader, $this->authorizationChecker, $this->tokenStorage);
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

        $this->eventBuilder = $this->getmockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent', array('getController'));
        $this->eventBuilder->disableOriginalConstructor();
        $this->reader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
        $this->authorizationChecker = $this->getMock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
    }
}
