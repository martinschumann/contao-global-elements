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

use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class KernelRequestListener
{
    public function __construct(
        private readonly ScopeMatcher $scopeMatcher,
        private readonly RequestStack $requestStack,
        private readonly ContainerInterface $container,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$this->scopeMatcher->isBackendRequest($this->requestStack->getCurrentRequest())) {
            return;
        }

        if (
            'global_elements' === $this->requestStack->getCurrentRequest()->query->get('be_mod')
            && $this->requestStack->getCurrentRequest()->query->get('filter')
            && $this->requestStack->getCurrentRequest()->query->get('cid')
        ) {
            $sessionBag = $this->requestStack->getSession()->getBag('contao_backend');
            $data = $sessionBag->all();

            if (isset($data['tl_page_node'])) {
                $sessionBag->set('discardedPageNode', $data['tl_page_node']);
                $sessionBag->set('tl_page_node', null);
            }
        }
    }
}
