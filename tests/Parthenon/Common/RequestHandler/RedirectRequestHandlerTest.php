<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\RequestHandler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectRequestHandlerTest extends TestCase
{
    public function testSupportsNonuserRoute()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('form');
        $request->method('get')->willReturn('parthenon_user_signup');

        $requestHandler = new RedirectRequestHandler($urlGenerator, 'path', 'parthenon_user_signup');
        $this->assertTrue($requestHandler->supports($request));
    }

    public function testDoesNotSupportsNonuserRoute()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('form');
        $request->method('get')->willReturn('parthenon_user_view');

        $requestHandler = new RedirectRequestHandler($urlGenerator, 'path', 'parthenon_user_signup');
        $this->assertFalse($requestHandler->supports($request));
    }

    public function testCallsFormCreateViewDefault()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $form = $this->createMock(Form::class);
        $formView = $this->createMock(FormView::class);

        $form->method('createView')->willReturn($formView);

        $requestHandler = new RedirectRequestHandler($urlGenerator, 'path', 'parthenon_user_signup');
        $this->assertEquals(['form' => $formView], $requestHandler->generateDefaultOutput($form));
    }

    public function testCallsFormCreateViewError()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $form = $this->createMock(Form::class);
        $formView = $this->createMock(FormView::class);

        $form->method('createView')->willReturn($formView);

        $requestHandler = new RedirectRequestHandler($urlGenerator, 'path', 'parthenon_user_signup');
        $this->assertEquals(['form' => $formView], $requestHandler->generateErrorOutput($form));
    }

    public function testCallsFormCreateViewSucces()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate', 'path')->willReturn('route');

        $form = $this->createMock(Form::class);
        $formView = $this->createMock(FormView::class);

        $form->method('createView')->willReturn($formView);

        $requestHandler = new RedirectRequestHandler($urlGenerator, 'path', 'parthenon_user_signup');
        $this->assertInstanceOf(RedirectResponse::class, $requestHandler->generateSuccessOutput($form));
    }
}
