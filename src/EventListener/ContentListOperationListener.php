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

use Contao\ContentModel;
use Contao\CoreBundle\DataContainer\DataContainerOperation;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContentListOperationListener
{
    private $request;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
    ) {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    public function disableEditButton(DataContainerOperation $operation): void
    {
        if (
            null !== $this->request
            && 'article' === $this->request->query->get('do')
            && $this->request->query->get('filter')
            && $this->request->query->get('popup')
            && $this->request->query->get('cid')
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
            null !== $this->request
            && 'article' === $this->request->query->get('do')
            && $this->request->query->get('filter')
            && $this->request->query->get('popup')
            && $this->request->query->get('cid')
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
            null !== $this->request
            && 'global_elements' === $this->request->query->get('do')
            && 'tl_content' === $this->request->query->get('table')
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
            null !== $this->request
            && 'article' === $this->request->query->get('do')
            && $this->request->query->get('filter')
            && $this->request->query->get('popup')
            && $this->request->query->get('cid')
        ) {
            $operation->disable();
        }
    }
}
