<?php

namespace App\EventSubscriber;

use App\Repository\CategoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private Environment $twig;
    private CategoryRepository $categoryRepository;

    public function __construct(Environment $twig, CategoryRepository $categoryRepository)
    {
        $this->twig = $twig;
        $this->categoryRepository = $categoryRepository;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $this->twig->addGlobal('categories', $this->categoryRepository->findAll());
    }

    public static function getSubscribedEvents(): array
    {
        return ['kernel.controller' => 'onKernelController'];
    }
}
