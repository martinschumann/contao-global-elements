<?php

declare(strict_types=1);

/*
 * This file is part of contao-garage/contao-global-elements.
 *
 * @author    Martin Schumann <martin.schumann@ontao-garage.de>
 * @license   MIT
 * @copyright Contao Garage 2026
 */

namespace ContaoGarage\GlobalElements\EventListener;

use Contao\CoreBundle\DataContainer\DataContainerOperation;
use Symfony\Component\HttpFoundation\RequestStack;

class ArticleListOperationListener
{
    private $request;

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    public function disableButton(DataContainerOperation $operation): void
    {
        if (
            null !== $this->request
            && 'global_elements' === $this->request->query->get('be_mod')
            && 'article' === $this->request->query->get('do')
            && $this->request->query->get('filter')
            && $this->request->query->get('popup')
            && $this->request->query->get('cid')
            && ! $this->request->query->get('id')
        ) {
            $operation->disable();
        }
    }
}
