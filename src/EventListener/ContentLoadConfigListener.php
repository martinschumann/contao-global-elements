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

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ContentLoadConfigListener
{
    public function __construct(
        private readonly ScopeMatcher $scopeMatcher,
        private readonly RequestStack $requestStack,
        private readonly Connection $connection,
        private readonly ContainerInterface $container,
        private readonly ContaoFramework $framework,
    ) {
    }

    public function onLoadConfig(DataContainer|null $dc = null): void
    {
        if (
            'article' === $this->requestStack->getCurrentRequest()?->query->get('do')
            && $this->requestStack->getCurrentRequest()?->query->get('filter')
            && $this->requestStack->getCurrentRequest()?->query->get('popup')
            && $this->requestStack->getCurrentRequest()?->query->get('cid')
        ) {
            $GLOBALS['TL_DCA']['tl_content']['config']['closed'] = true;
            $GLOBALS['TL_DCA']['tl_content']['config']['notCopyable'] = true;
            $GLOBALS['TL_DCA']['tl_content']['config']['notSortable'] = true;
            unset(
                $GLOBALS['TL_DCA']['tl_content']['list']['global_operations']['all'],
                $GLOBALS['TL_DCA']['tl_content']['list']['operations']['copy'],
                $GLOBALS['TL_DCA']['tl_content']['list']['operations']['cut'],
                $GLOBALS['TL_DCA']['tl_content']['list']['operations']['toggle'],
                $GLOBALS['TL_DCA']['tl_content']['list']['operations']['show']
            );
        }
    }
}
