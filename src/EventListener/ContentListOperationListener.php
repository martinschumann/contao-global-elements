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

use Contao\ContentModel;
use Contao\CoreBundle\DataContainer\DataContainerOperation;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContentListOperationListener
{
    public function __construct(
        private readonly ScopeMatcher $scopeMatcher,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly ContainerInterface $container,
    ) {
    }

    public function disableEditButton(DataContainerOperation $operation): void
    {
        if (
            'article' === $this->requestStack->getCurrentRequest()?->query->get('do')
            && $this->requestStack->getCurrentRequest()?->query->get('filter')
            && $this->requestStack->getCurrentRequest()?->query->get('popup')
            && $this->requestStack->getCurrentRequest()?->query->get('cid')
        ) {
            if ('alias' !== $operation->getRecord()['type']) {
                $operation->disable();
            }
        }
    }

    public function disableDeleteButton(DataContainerOperation $operation): void
    {
        // In popup introspection allow deleting only for aliases (elements inluding
        // elements by reference) and disable deleting for referenced elements
        if (
            'article' === $this->requestStack->getCurrentRequest()?->query->get('do')
            && $this->requestStack->getCurrentRequest()?->query->get('filter')
            && $this->requestStack->getCurrentRequest()?->query->get('popup')
            && $this->requestStack->getCurrentRequest()?->query->get('cid')
        ) {
            if ('alias' !== $operation->getRecord()['type']) {
                $operation->disable();
            }
        } else {
            $count = ContentModel::countByCteAlias($operation->getRecord()['id']);

            if ($count > 0) {
                $operation->disable();
            }
        }
    }

    public function adjustShowButton(DataContainerOperation $operation): void
    {
        // Alter show button for introspecting references of a content element
        if (
            'global_elements' === $this->requestStack->getCurrentRequest()?->query->get('do')
            && 'tl_content' === $this->requestStack->getCurrentRequest()?->query->get('table')
            && ('cg_global_elements_archive' === $operation->getRecord()['ptable'])
        ) {
            $count = ContentModel::countByCteAlias($operation->getRecord()['id']);

            if ($count < 1) {
                return;
            }

            $arguments = [
                'do' => 'article',
                'be_mod' => 'global_elements',
                'filter' => 1,
                'cid' => $operation->getRecord()['id'],
                'popup' => 1,
            ];

            $operation->setUrl('/contao?'.http_build_query($arguments));
            $operation['icon'] = '/bundles/globalelements/icons/check-references.svg';
            $operation['label'] = \sprintf($this->translator->trans('action.introspect.1', [], 'GlobalElementsBundle'), $operation->getRecord()['id']);
            $operation['title'] = \sprintf($this->translator->trans('action.introspect.1', [], 'GlobalElementsBundle'), $operation->getRecord()['id']);
        }

        // Disable any other actions in introspection popup
        if (
            'article' === $this->requestStack->getCurrentRequest()?->query->get('do')
            && $this->requestStack->getCurrentRequest()?->query->get('filter')
            && $this->requestStack->getCurrentRequest()?->query->get('popup')
            && $this->requestStack->getCurrentRequest()?->query->get('cid')
        ) {
            $operation->disable();
        }
    }
}
