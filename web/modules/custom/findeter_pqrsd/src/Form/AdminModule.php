<?php

namespace Drupal\findeter_pqrsd\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\Entity\Role;

/**
 * Class AdminModule.
 */
class AdminModule extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'findeter_pqrsd.admin',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pqrsd_admin_module';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the value stored in the config.
    $config = $this->config('findeter_pqrsd.admin');

    $form['intro']['#markup'] = '<p>Este formulario permite administrar las parámetros esenciales para el funcionamiento de los formularios de Findeter (PQRSD).</p>';

    // Asign user.
    $form['user-fieldset'] = [
      '#type'        => 'fieldset',
      '#title'       => 'Asignación de formularios',
      '#collapsible' => TRUE,
      '#collapsed'   => FALSE,
    ];

    $userStorage = \Drupal::entityTypeManager()->getStorage('user');
    $query = $userStorage->getQuery();
    $uids = $query
      ->condition('status', '1')
      ->execute();

    $users = $userStorage->loadMultiple($uids);
    $usersAsOptions = [];
    foreach ($users as $uid => $usr) {
      $usersAsOptions[$uid] = $usr->getAccountName();
    }

    $form['user-fieldset']['asign_user'] = [
      '#type'          => 'select',
      '#title'         => 'Usuario por defecto',
      '#options'       => $usersAsOptions,
      '#default_value' => $config->get('asign_user'),
      '#description'   => 'Seleccione de la anterior lista, el usuario al que se asignaran automáticamente todos los formularios enviados. <b>Solo se muestran usuarios habilitados.</b>'
    ];

    $roles = Role::loadMultiple();
    $rolesAsOptions = [];
    foreach ($roles as $idRol => $rol) {
      $rolesAsOptions[$idRol] = $rol->label();
    }
    unset($rolesAsOptions['anonymous']);

    $rolesSelected = explode(',', $config->get('roles'));

    $form['user-fieldset']['roles'] = [
      '#type'          => 'checkboxes',
      '#options'       => $rolesAsOptions,
      '#required'      => TRUE,
      '#default_value' => $rolesSelected,
      '#title'         => 'Roles de usuario permitidos',
      '#description'   => 'Seleccione los roles de usuario que estarán permitidos para reasignar los formularios (Solo los usuarios que tengan alguno de estos roles seleccionados, estarán habilitados para reasignar los formularios).<br>Si ninguno está seleccionado, todos los roles se tomarán en cuenta para la asignación.'
    ];

    // Allowed user roles.
    $form['node-display'] = [
      '#type'        => 'fieldset',
      '#title'       => 'Exportación a PDF',
      '#collapsible' => TRUE,
      '#collapsed'   => FALSE,
    ];

    // $viewModes = \Drupal::entityManager()->getViewModes('node');
    $viewModes = \Drupal::service('entity_display.repository')->getViewModes('node');
    $viewModesAsOption = [];
    foreach ($viewModes as $idMode => $viewMode) {
      $viewModesAsOption[$idMode] = $viewMode['label'];
    }

    $form['node-display']['view_mode'] = [
      '#type'          => 'select',
      '#options'       => $viewModesAsOption,
      '#required'      => TRUE,
      '#default_value' => $config->get('view_mode'),
      '#title'         => '"Display mode" para exportación',
      '#description'   => 'De la lista anterior, seleccione el display mode del tipo de contenido <i>"Solicitud usuario"</i> que se usará para exportar a PDF. <br> Puede consultar las opciones de cada display mode haciendo <a href="/admin/structure/types/manage/user_request/display">click aquí</a>'
    ];

    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('findeter_pqrsd')->getPath();
    $module_path .= '/css/pdf.css';

    if ($config->get('css_path') == '') {
      $defaultCss = $module_path;
    }
    else {
      $defaultCss = $config->get('css_path');
    }
    $form['node-display']['css_path'] = [
      '#type'          => 'textfield',
      '#title'         => 'Hoja de estilo exportación a PDF',
      '#default_value' => $defaultCss,
      '#required'      => TRUE,
      '#description'   => "Defina la ruta absoluta a la hoja de estilo que se usará para la exportación de los formularios. La ruta de la hora de estilos por defecto es: <i>$module_path</i>"
    ];

    // Allowed user roles.
    $form['semaphore'] = [
      '#type'        => 'fieldset',
      '#title'       => 'Valores de semáforo',
      '#collapsible' => TRUE,
      '#collapsed'   => FALSE,
    ];

    $form['semaphore']['tabla'] = [
      '#type' => 'table',
      '#header' => [
        'Tipo',
        'Tiempo alerta (naranja)',
        'Tiempo caducidad (rojo)',
        'Condición',
        'Tipos de dias'
      ],

    ];

    foreach ($config->get('semaphore') as $id => $semaphoreItem) {

      $form['semaphore']['tabla'][$id]['type'] = [
        '#markup' => $semaphoreItem['title'],
      ];
      $form['semaphore']['tabla'][$id]['orange'] = [
        '#type'          => 'textfield',
        '#title'         => 'Naranja',
        '#size'          => 5,
        '#maxlength'     => 5,
        '#required'      => TRUE,
        '#default_value' => $semaphoreItem['orange']
      ];
      $form['semaphore']['tabla'][$id]['red'] = [
        '#type'          => 'textfield',
        '#title'         => 'Rojo',
        '#size'          => 5,
        '#maxlength'     => 5,
        '#required'      => TRUE,
        '#default_value' => $semaphoreItem['red']
      ];
      $form['semaphore']['tabla'][$id]['contition'] = [
        '#markup' => implode('<br>', $semaphoreItem['condition']),
      ];

      $typeDays = 'Días hábiles';
      if ($semaphoreItem['logic'] == 'calendar days') {
        $typeDays = 'Días calendario';
      }
      $form['semaphore']['tabla'][$id]['type-days'] = [
        '#markup' => $typeDays,
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    parent::submitForm($form, $form_state);

    // Get the value stored in the config.
    $config = $this->config('findeter_pqrsd.admin');

    // Collect selected roles.
    $roles = [];
    foreach ($form_state->getValue('roles') as $rolAllowed) {
      if ($rolAllowed !== 0) {
        $roles[] = $rolAllowed;
      }
    }

    // Collect new values semaphore matrix.
    $matrixSemaphore = $config->get('semaphore');
    foreach ($form_state->getValue('tabla') as $id => $item) {
      $matrixSemaphore[$id]['orange'] = $item['orange'];
      $matrixSemaphore[$id]['red'] = $item['red'];
    }

    $this->config('findeter_pqrsd.admin')
      ->set('asign_user', trim($form_state->getValue('asign_user')))
      ->set('view_mode', trim($form_state->getValue('view_mode')))
      ->set('css_path', trim($form_state->getValue('css_path')))
      ->set('semaphore', $matrixSemaphore)
      ->set('roles', implode(',', $roles))
      ->save();

  }

}
