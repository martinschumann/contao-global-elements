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

use Contao\DataContainer;
use Symfony\Component\HttpFoundation\RequestStack;

class ContentLoadConfigListener
{
    private $request;

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    public function onLoadConfig(DataContainer|null $dc = null): void
    {
        if (
            null !== $this->request
            && 'article' === $this->request->query->get('do')
            && $this->request->query->get('filter')
            && $this->request->query->get('popup')
            && $this->request->query->get('cid')
        ) {
            $GLOBALS['TL_DCA']['tl_content']['config']['closed'] = true;
            $GLOBALS['TL_DCA']['tl_content']['config']['notCopyable'] = true;
            $GLOBALS['TL_DCA']['tl_content']['config']['notSortable'] = true;
            unset(
                $GLOBALS['TL_DCA']['tl_content']['list']['global_operations']['all'],
                $GLOBALS['TL_DCA']['tl_content']['list']['operations']['copy'],
                $GLOBALS['TL_DCA']['tl_content']['list']['operations']['cut'],
                $GLOBALS['TL_DCA']['tl_content']['list']['operations']['toggle'],
                $GLOBALS['TL_DCA']['tl_content']['list']['operations']['show'],
            );
        }
    }
}
