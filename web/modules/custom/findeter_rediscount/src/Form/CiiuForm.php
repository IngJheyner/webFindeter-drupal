<?php

namespace Drupal\findeter_rediscount\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a findeter_rediscount form.
 */
class CiiuForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'findeter_rediscount_ciiu';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'd-flex justify-content-around align-items-center px-md-5',
      ]
    ];

    $form['container']['code'] = [
      '#type' => 'textfield',
      '#size' => 40,
      '#placeholder' => 'Digite el codigo CIIU de la empresa',
      '#required' => TRUE,
    ];

    $form['container']['actions'] = [
      '#type' => 'actions',
    ];
    $form['container']['actions']['send_code'] = [
      '#type' => 'button',
      //'#value' => $this->t('Buscar'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (mb_strlen($form_state->getValue('code')) < 3) {
      $form_state->setErrorByName('code', $this->t('Message should be at least 10 characters.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /*$this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRedirect('<front>');*/
  }

}
