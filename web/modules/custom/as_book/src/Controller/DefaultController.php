<?php

namespace Drupal\as_book\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Session\AccountProxy;

/**
 * Class DefaultController.
 *
 * @package Drupal\as_book\Controller
 */
class DefaultController extends ControllerBase {
    
    /**
     * Drupal\Core\Routing\CurrentRouteMatch definition.
     *
     * @var \Drupal\Core\Routing\CurrentRouteMatch
     */
    protected $currentRouteMatch;
    /**
     * Drupal\Core\Entity\Query\QueryFactory definition.
     *
     * @var \Drupal\Core\Entity\Query\QueryFactory
     */
    protected $entityQuery;
    /**
     * Drupal\Core\Session\AccountProxy definition.
     *
     * @var \Drupal\Core\Session\AccountProxy
     */
    protected $currentUser;
    
    /**
     * {@inheritdoc}
     */
    public function __construct(
        CurrentRouteMatch $current_route_match,
        QueryFactory $entity_query,
        AccountProxy $current_user
    ) {
        $this->currentRouteMatch = $current_route_match;
        $this->entityQuery = $entity_query;
        $this->currentUser = $current_user;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('current_route_match'),
            $container->get('entity.query'),
            $container->get('current_user')
        );
    }
    
    /**
     * Testdrupal.
     *
     * @return string
     *   Return Hello string.
     */
    public function testDrupal() {
        // \Drupal::entityQuery('node');
        $query = $this->entityQuery->get('node');
        $query->condition('type', 'book');
        $query->condition('status', 1);
        $query->condition('field_author', 2);
        $result = $query->execute();
        
        $nodes = \Drupal\node\Entity\Node::loadMultiple($result);
        $books = [];
        
        // Génération des Render Array des livres en mode d'affichage Teaser à
        // partir de l'objet d'entité des noeuds.
        foreach ($nodes as $node) {
            $book = node_view($node, 'teaser');
            $books[] = $book;
        }
        
        return [
            '#theme' => 'book_list',
            'books' => $books,
        ];
    }
    
}
