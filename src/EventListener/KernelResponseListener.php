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

use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class KernelResponseListener
{
    private $request;

    public function __construct(
        private readonly ScopeMatcher $scopeMatcher,
        private readonly RequestStack $requestStack,
    ) {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (! $this->scopeMatcher->isBackendRequest($this->request)) {
            return;
        }

        $sessionBag = $this->request->getSession()->getBag('contao_backend');
        $data = $sessionBag->all();

        // Restore discarded filter and page node
        if (isset($data['discardedFilter']['tl_article']) && \is_array($data['discardedFilter']['tl_article'])) {
            $sessionBag->set('filter', $data['discardedFilter']);
            $sessionBag->set('discardedFilter', []);
        }

        if (isset($data['discardedPageNode']) && ((int) $data['discardedPageNode'] > 0)) {
            $sessionBag->set('tl_page_node', $data['discardedPageNode']);
            $sessionBag->set('discardedPageNode', null);
        }
    }
}
