<?php

declare(strict_types=1);

namespace Handlers\User;

use Domain\User\UserInterface;
use Domain\User\UserRepository;
use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class TemplateChangeHandler extends AbstractHandler
{
    private array $templates = ['default', 'light'];

    /**
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        if (!$this->container->exist('user')) {
            return $this->json(['success' => false, 'error' => 'You are not authorized']);
        }

        $template = $request->template;
        /** @var UserInterface $user */
        $user = $this->container->getUser();

        if ($template === $user->getTemplate()) {
            return $this->json(['success' => true]);
        }

        if (!in_array($template, $this->templates, true)) {
            return $this->json(['success' => false, 'error' => "Unknown template: $template"]);
        }

        $user->setTemplate($template);

        $repository = new UserRepository($this->container);
        $repository->saveTemplate($user);

        return $this->json(['success' => true]);
    }
}
