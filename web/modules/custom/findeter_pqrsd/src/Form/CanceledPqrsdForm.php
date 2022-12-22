<?php

namespace Drupal\findeter_pqrsd\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

/**
 * Provides a confirmation form before clearing out the examples.
 */
class CanceledPqrsdForm extends ConfirmFormBase {
  /**
   * Render.
   *
   * @var object
   */
  protected $node;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'findeter_pqrsd_canceled';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {
    $this->node = $node;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('¿Desea anular el radicado No. %nid?', ['%nid' => $this->node->get('field_pqrsd_numero_radicado')->getValue()[0]['value']]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('view.pqrsd.page_1');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Despublicar el radicado.
    $this->node->setUnpublished();
    $this->node->save();

    $this->messenger()->addStatus($this->t('Se ha confirmado la anulación del radicado No. %nid, para mayor información contactarse con el administrador del sistema.', ['%nid' => $this->node->get('field_pqrsd_numero_radicado')->getValue()[0]['value']]));

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
