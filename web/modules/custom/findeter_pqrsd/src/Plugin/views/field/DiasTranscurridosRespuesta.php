<?php

namespace Drupal\findeter_pqrsd\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Cmixin\BusinessDay;
use Carbon\Carbon;

/**
 * Field handler to flag the node type.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("dias_transcurridos_respuesta")
 */
class DiasTranscurridosRespuesta extends FieldPluginBase {

  /**
   * Query Data.
   *
   *   Mixed.
   *
   * @return mixed
   *   Return services.
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * Query Data Options.
   *
   *   Mixed.
   *
   * @return mixed
   *   Return services.
   */
  // Protected function defineOptions() {.
  // $options = parent::defineOptions();
  // $options['node_type'] = array('default' => 'pqrsd');
  // return $options;
  // }.

  /**
   * Query Data.
   *
   *   Mixed.
   *
   * @return mixed
   *   Return services.
   */
  /*public function buildOptionsForm(&$form, FormStateInterface $form_state) {
  $types = NodeType::loadMultiple();
  $options = [];
  foreach ($types as $key => $type) {
  $options[$key] = $type->label();
  }
  $form['node_type'] = array(
  '#title' => $this->t('Which node type should be flagged?'),
  '#type' => 'select',
  '#default_value' => $this->options['node_type'],
  '#options' => $options,
  );
  parent::buildOptionsForm($form, $form_state);
  }*/

  /**
   * Render column Data.
   *
   *   Mixed.
   *
   * @return string
   *   Return services.
   */
  public function render(ResultRow $values) {

    // Zona horaria.
    BusinessDay::enable('Carbon\Carbon');
    Carbon::setHolidaysRegion('co');

    // Recogemos la consulta en una variable.
    $row = $values->_entity;

    // If ($node->bundle() == $this->options['node_type']) {.
    // return $this->t('Hey, I\'m of the type: @type', array('@type' => $this->options['node_type']));
    // }
    // else {
    // return $this->t('Hey, I\'m something else.');
    // }.

    // Contador de dias para cada fecha.
    $days = 0;

    if (isset($row->getFields()['field_pqrsd_fecha_respuesta'][0])) {

      $dateI = $row->get('created')->getValue()[0]['value'];
      $dateF = strtotime($row->get('field_pqrsd_fecha_respuesta')->getValue()[0]['value']);

      for ($timeStamp = $dateI; $timeStamp <= $dateF; $timeStamp += 86400) {

        // Declaramos variabes para la fecha fesitvos.
        $day = date('d', $timeStamp);
        $mounth = date('m', $timeStamp);
        $year = date('Y', $timeStamp);

        // Obtenemos si es un dia vacacional o festivo.
        $holiDays = Carbon::parse("$year-$mounth-$day")->isHoliday();

        // Ni sabados ni Domingos.
        if (!in_array(date('N', $timeStamp), [6, 7])) {
          // Ni dias feriados.
          if (!$holiDays) {
            $days++;
          }
        }

      }

      return $this->t('@days dias.', ['@days' => $days]);

    }

    return $days == 0 ? '' : '-';

  }

}
