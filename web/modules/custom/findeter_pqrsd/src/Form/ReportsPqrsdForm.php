<?php

namespace Drupal\findeter_pqrsd\Form;

use DateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;

/**
 * Provides a Findeter PQRSD Manager form.
 */
class ReportsPqrsdForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'findeter_pqrsd_filter_statistics';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#attached']['library'][] = 'findeter_pqrsd/reports_charts';

    $form['filter_group'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Filtrar por:'),
    ];

    $form['filter_group']['start_date'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Fecha de creacion:'),
      '#required' => TRUE,
      '#date_time_element' => 'none',
    ];

    $form['filter_group']['finished_date'] = [
      '#type' => 'datetime',
      '#title' => $this->t('al:'),
      '#required' => TRUE,
      '#date_time_element' => 'none',
    ];

    $form['filter_group']['actions'] = [
      '#type' => 'actions',
      '#weight' => 1,
    ];

    $form['filter_group']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filtrar'),
    ];

    $form['filter_group']['actions']['send_pdf'] = [
      '#type' => 'html_tag',
      '#tag' => 'a',
      '#href' => '#',
      '#value' => 'Exportar PDF',
      '#attributes' => [
        'href' => '#',
        'id' => 'send-pdf',
        'class' => 'export-link',
      ],
      '#suffix' => '<div class="ajax-progress ajax-progress-throbber"><div class="throbber">&nbsp;</div><div class="message">Espere, por favor...</div></div>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $button = $form_state->getTriggeringElement();

    // Validacion de fechas.
    $start_date = strtotime($form_state->getValue('start_date'));
    $finished_date = strtotime($form_state->getValue('finished_date'));

    if ($start_date > $finished_date) {
      $form_state->setErrorByName('start_date', $this->t('La fecha de inicio no debe ser mayor a la fecha final.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // $this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRebuild(TRUE);
    $form_state->setRedirect('findeter_pqrsd.reports_statistics');
  }

}
