<?php

declare(strict_types=1);

/*
 * This file is part of contao-garage/contao-global-elements.
 *
 * @author    Martin Schumann <martin.schumann@ontao-garage.de>
 * @license   LGPL-3.0-or-later
 * @copyright Contao Garage 2026
 */

namespace ContaoGarage\GlobalElements\EventListener;

use Contao\CoreBundle\DataContainer\DataContainerOperation;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleListOperationListener
{
    public function __construct(
        private readonly ScopeMatcher $scopeMatcher,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly ContainerInterface $container,
    ) {
    }

    public function disableButton(DataContainerOperation $operation): void
    {
        if (
            'global_elements' === $this->requestStack->getCurrentRequest()?->query->get('be_mod')
            && 'article' === $this->requestStack->getCurrentRequest()?->query->get('do')
            && $this->requestStack->getCurrentRequest()?->query->get('filter')
            && $this->requestStack->getCurrentRequest()?->query->get('popup')
            && $this->requestStack->getCurrentRequest()?->query->get('cid')
            && !$this->requestStack->getCurrentRequest()?->query->get('id')
        ) {
            $operation->disable();
        }
    }
}
