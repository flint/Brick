<?php

namespace spec\Brick\Routing;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

require __DIR__ . '/../../../tests/Fixtures/AnnotatedController.php';

class AnnotationClassLoaderSpec extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Annotations\AnnotationReader $reader
     */
    function let($reader)
    {

        $this->beConstructedWith($reader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Brick\Routing\AnnotationClassLoader');
    }

    /**
     * @param Symfony\Component\Routing\Annotation\Route $annotation
     */
    function it_sets_the_constroller_based_on_reflection($annotation, $reader)
    {
        $reader->getMethodAnnotations(Argument::type('ReflectionMethod'))
            ->willReturn([$annotation]);

        $reader->getClassAnnotation(Argument::type('ReflectionClass'), 'Symfony\Component\Routing\Annotation\Route')
            ->willReturn([]);

        $annotation->getDefaults()->willReturn([]);
        $annotation->getRequirements()->willReturn([]);
        $annotation->getOptions()->willReturn([]);
        $annotation->getSchemes()->willReturn([]);
        $annotation->getMethods()->willReturn([]);
        $annotation->getHost()->willReturn();
        $annotation->getCondition()->willReturn();
        $annotation->getPath()->willReturn();
        $annotation->getName()->willReturn();

        $route = $this->load('Brick\Tests\Fixtures\AnnotatedController')
            ->get('brick_tests_fixtures_annotatedcontroller_helloaction');

        $route->getDefault('_controller')
            ->shouldReturn('Brick\Tests\Fixtures\AnnotatedController::helloAction');
    }

    /**
     * @param Brick\Annotation\Route $annotation
     */
    function it_sets_the_constroller_based_on_annotation_service($annotation, $reader)
    {
        $reader->getMethodAnnotations(Argument::type('ReflectionMethod'))
            ->willReturn([$annotation]);

        $reader->getClassAnnotation(Argument::type('ReflectionClass'), 'Symfony\Component\Routing\Annotation\Route')
            ->willReturn([]);

        $annotation->getDefaults()->willReturn([]);
        $annotation->getRequirements()->willReturn([]);
        $annotation->getOptions()->willReturn([]);
        $annotation->getSchemes()->willReturn([]);
        $annotation->getMethods()->willReturn([]);
        $annotation->getHost()->willReturn();
        $annotation->getCondition()->willReturn();
        $annotation->getPath()->willReturn();
        $annotation->getName()->willReturn();
        $annotation->getService()->willReturn('my_service_id');

        $route = $this->load('Brick\Tests\Fixtures\AnnotatedController')
            ->get('brick_tests_fixtures_annotatedcontroller_helloaction');

        $route->getDefault('_controller')
            ->shouldReturn('my_service_id:helloAction');
    }
}
