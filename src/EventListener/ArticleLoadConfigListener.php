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
use Contao\Message;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DoctrineException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleLoadConfigListener
{
    public function __construct(
        private readonly ScopeMatcher $scopeMatcher,
        private readonly RequestStack $requestStack,
        private readonly Connection $connection,
        private readonly ContainerInterface $container,
        private readonly ContaoFramework $framework,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function onLoadConfig(DataContainer|null $dc = null): void
    {
        if (
            'global_elements' === $this->requestStack->getCurrentRequest()->query->get('be_mod')
            && $this->requestStack->getCurrentRequest()->query->get('filter')
            && $this->requestStack->getCurrentRequest()->query->get('cid')
        ) {
            // Do not show any operation buttons
            $GLOBALS['TL_DCA']['tl_article']['list']['sorting']['panelLayout'] = '';
            unset($GLOBALS['TL_DCA']['tl_article']['list']['global_operations']);
            $GLOBALS['TL_DCA']['tl_article']['config']['closed'] = true;

            try {
                $queryBuilder = $this->connection->createQueryBuilder();
                $queryBuilder
                    ->select('*')
                    ->from('tl_content')
                    ->andWhere('cteAlias = '.$this->requestStack->getCurrentRequest()->query->get('cid'))
                ;

                $query = 'CREATE TEMPORARY TABLE tl_content '.$queryBuilder->getSQL();
                $statement = $this->connection->prepare($query);
                $statement->execute();
            } catch (DoctrineException $exception) {
                Message::addError(\sprintf($this->translator->trans('error.introspect.sqlerror', [], 'GlobalElementsBundle'), $exception->getMessage()));

                return;
            }

            try {
                $queryBuilder = $this->connection->createQueryBuilder();
                $queryBuilder
                    ->select('article.*')
                    ->from('tl_article', 'article')
                    ->innerJoin('article', 'tl_content', 'content', 'article.id = content.pid')
                    ->andWhere("content.ptable = 'tl_article'")
                ;

                $query = 'CREATE TEMPORARY TABLE tl_article '.$queryBuilder->getSQL();
                $statement = $this->connection->prepare($query);
                $statement->execute();
            } catch (DoctrineException $exception) {
                Message::addError(\sprintf($this->translator->trans('error.introspect.sqlerror', [], 'GlobalElementsBundle'), $exception->getMessage()));

                return;
            }

            // Safeguard for records lacking parental record in 'tl_article'
            $GLOBALS['TL_DCA']['tl_content']['config']['doNotDeleteRecords'] = true;

            // Enable filtering by "tl_article.id"
            $GLOBALS['TL_DCA']['tl_article']['fields']['id']['filter'] = true;

            $sessionBag = $this->requestStack->getSession()->getBag('contao_backend');
            $data = $sessionBag->all();

            // Discard filter for table 'tl_article'
            if (isset($data['filter']['tl_article']) && \is_array($data['filter']['tl_article'])) {
                $sessionBag->set('discardedFilter', $data['filter']);
                $sessionBag->set('filter', ['tl_article' => []]);
            }
        }
    }
}
