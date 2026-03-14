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
use Symfony\Component\HttpKernel\Event\RequestEvent;

class KernelRequestListener
{
    private $request;

    public function __construct(
        private readonly ScopeMatcher $scopeMatcher,
        private readonly RequestStack $requestStack,
    ) {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (! $this->scopeMatcher->isBackendRequest($this->request)) {
            return;
        }

        if (
            null !== $this->request
            && 'global_elements' === $this->request->query->get('be_mod')
            && $this->request->query->get('filter')
            && $this->request->query->get('cid')
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
