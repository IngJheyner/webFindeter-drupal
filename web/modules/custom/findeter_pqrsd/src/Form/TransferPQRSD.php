<?php

namespace Drupal\findeter_pqrsd\Form;

use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\node\Entity\Node;

/**
 * Class TransferPQRSD.
 */
class TransferPQRSD extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'transfer_pqrsd';
  }

  /**
   * Building all elements in the form
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {

    $config = $this->config('findeter_pqrsd.admin');

    $form_state->setCached(FALSE);

    // Nid param to store in the new references
    $form['node_id'] = array(
      '#type' => 'hidden',
      '#value' => $nid,
    );

    // start col 2
    $form['email'] = [
      '#type'       => 'textfield',
      '#title'      => 'Ingrese el correo a donde transferir',
      '#attributes' => ['placeholder'=>'Diligencie el correo'],
      '#required'   => true
    ];

    // Submit with ajax event that after the operation, close the modal
    $form['submit'] = [
      '#type'  => 'submit',
      '#value' => 'Transferir',
      '#ajax'  => array(
        'callback' => '::ajaxCloseModal',
        'event'    => 'click',
      ),
    ];

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attached']['library'][] = 'findeter_pqrsd/global-scripts';

    $form_state->setRebuild();

    return $form;
  }

  /**
   * To validate values of the form
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);


    if(!filter_var($form_state->getValue('email'), FILTER_VALIDATE_EMAIL)){
      $form_state->setErrorByName('email', 'Debe ingresar una dirección de correo válido');
    }
  }


  /**
   * Submit funtion (also close the modal window)
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  public function ajaxCloseModal(array &$form, FormStateInterface $form_state) {

    $response = new AjaxResponse();

    $mailBody[] = 'Reciba un cordial saludo de parte de Findeter';
    $mailBody[] = 'Le hacemos entrega de la información del siguiente radicado:';

    $formValues = $form_state->getValues();

    $node = Node::load($formValues['node_id']);

    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'findeter_pqrsd';
    $key = 'transfer_pqrsd';
    $to = $formValues['email'];
    
    $nameRadicador[] = $node->get('field_pqrsd_primer_nombre')->getString();
    $nameRadicador[] = $node->get('field_pqrsd_segundo_nombre')->getString();
    $nameRadicador[] = $node->get('field_pqrsd_segundo_apellido')->getString();

    $mailBody[] = '<b>#Radicado: </b> '.$formValues['node_id'];
    $mailBody[] = '<b>Peticionario: </b>'.implode(' ',$nameRadicador);
    $mailBody[] = '<b>Email: </b>'.$node->get('field_pqrsd_email')->getString();
    $mailBody[] = '<b>Teléfono: </b>'.$node->get('field_pqrsd_telefono')->getString();
    $mailBody[] = '<b>Dirección: </b>'.$node->get('field_pqrsd_direccion')->getString();

    $tidDepartment = $node->get('field_pqrsd_departamento')->getString();
    $nameDepartment = \Drupal\taxonomy\Entity\Term::load($tidDepartment)->get('name')->value;

    $tidMunicipality = $node->get('field_pqrsd_municipio')->getString();
    $nameMunicipality = \Drupal\taxonomy\Entity\Term::load($tidMunicipality)->get('name')->value;

    $mailBody[] = '<b>Departamento/municipio: </b>'.$nameDepartment.' - '.$nameMunicipality;
    $mailBody[] = '<b>Rango de edad: </b>'.$node->get('field_pqrsd_rango_edad')->getString();
    $mailBody[] = '<b>Fecha de registro: </b>'.$node->getCreatedTime();
    $mailBody[] = '<b>Producto: </b>'.$node->get('field_pqrsd_nombre_producto')->getString();
    $mailBody[] = '<b>Canal de recepción: </b>'.$node->get('field_pqrsd_canal_recepcion')->getString();
    $mailBody[] = '<b>Forma de recepción: </b>'.$node->get('field_pqrsd_forma_recepcion')->getString();
    $mailBody[] = '<b>Medio de respuesta: </b>'.$node->get('field_pqrsd_medio_respuesta')->getString();

    $params['message'] = implode('<br>',$mailBody);

    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
   
    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

    if($result['result'] == true){
      $node = Node::load($formValues['node_id']);
      $node->field_pqrsd_respuesta[] = 'PQRSD Trasladada a: '.$formValues['email'];
      $node->uid = 0; 
      $node->save();
  
      $response->addCommand(new InvokeCommand(NULL, 'afterAsignCallback', ['Reloading page!']));
    }else{
      \Drupal::messenger()->addError('Ocurrió un problema al enviar el correo.');
      $response->addCommand(new CloseModalDialogCommand());
    }
    
    return $response;

  }

}
