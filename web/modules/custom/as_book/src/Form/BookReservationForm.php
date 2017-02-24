<?php

namespace Drupal\as_book\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Session\AccountProxy;
use Drupal\node\Entity\Node;

/**
 * Class BookReservationForm.
 *
 * @package Drupal\as_book\Form
 */
class BookReservationForm extends FormBase {
    
    /**
     * Symfony\Component\HttpFoundation\RequestStack definition.
     *
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;
    /**
     * Drupal\Core\Session\AccountProxy definition.
     *
     * @var \Drupal\Core\Session\AccountProxy
     */
    protected $currentUser;
    
    public function __construct(
        RequestStack $request_stack,
        AccountProxy $current_user
    ) {
        $this->requestStack = $request_stack;
        $this->currentUser = $current_user;
    }
    
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('request_stack'),
            $container->get('current_user')
        );
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'book_reservation_form';
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        
        // $node = \Drupal::routeMatch()->getParameters('node');
        $node = $this->requestStack->getCurrentRequest()->get('node');
        
        $form['member'] = [
            '#type' => 'hidden',
            '#value' => $this->currentUser->id(),
        ];
        
        $form['book'] = [
            '#type' => 'hidden',
            '#value' => $node->id(),
        ];
        
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Réserver'),
        ];
        
        return $form;
    }
    
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }
    
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $book = $this->requestStack->getCurrentRequest()->get('node');
        $book_title = $book->getTitle();
        $user = $this->currentUser;
    
        $values = [
            'type' => 'reservation',
            'title' => 'Réservation de "' . $book_title . '" par "' . $user->getEmail() . '"',
            'field_member' => $form_state->getValue('member'),
        ];
        $node = Node::create($values);
        $node->set('field_book', $form_state->getValue('book'));
        $node->save();
        
        drupal_set_message('Votre réservation a bien été prise en charge.');
    }
}
