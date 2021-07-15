<?php
/**
 * Implements hook_views_data_alter().
 */
function findeter_pqrsd_views_data_alter(array &$data) {
    $data['node']['dias_transcurridos_respuesta'] = array(
      'title' => t('Dias transcurridos'),
      'field' => array(
        'title' => t('Dias transcurridos'),
        'help' => t('Dias transcurridos al momento de dar respuesta PQRSD'),
        'id' => 'dias_transcurridos_respuesta',
      ),
    );
  }