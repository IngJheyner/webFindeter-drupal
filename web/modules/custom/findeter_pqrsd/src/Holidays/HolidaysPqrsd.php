<?php

namespace Drupal\findeter_pqrsd\Holidays;

use Drupal\Core\State\StateInterface;
use Drupal\Core\Http\ClientFactory;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Component\Serialization\Json;

/**
 * Dias festivos o feriados.
 *
 * @author 3ddesarrollo <email@email.com>
 */
class HolidaysPqrsd {

  /**
   * Servicio client Factory.
   *
   * @var Drupal\Core\Http\ClientFactory
   */
  protected $httpClientFactory;

  /**
   * State.
   *
   *
   * @var Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Servicio logger.
   *
   * @var Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * Undocumented function.
   *
   * @param Drupal\Core\Http\ClientFactory $http_client_factory
   *   HTTP.
   * @param Drupal\Core\State\StateInterface $state
   *   State.
   * @param Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   Logger.
   */
  public function __construct(ClientFactory $http_client_factory, StateInterface $state, LoggerChannelFactoryInterface $logger) {

    $this->httpClientFactory = $http_client_factory;
    $this->state = $state;
    $this->logger = $logger;
  }

  /**
   * Definimos la fecha festivo.
   */
  public function isHoliDays($year, $mounth, $day) {

    // Obtenemos el estado de la variable de festivos.
    $holidays = $this->state->get('findeter_pqrsd.holidays');

    // Validamos si existe tal variable y si anio es igual a anio actual.
    if ((empty($holidays) || is_null($holidays)) || (isset($holidays['year']) && $holidays['year'] != $year)) {

      // Obtenemos los valores.
      if ($httpHoliDays = $this->httpHoliDays($year)) {
        $holidays = $httpHoliDays;
      }
      else {
        // Si hay algun error en la peticion HTTP se retorna false.
        return FALSE;
      }
    }

    // Buscamos la fecha enviada a la fecha encontrada en la variable de estado.
    if (in_array("$year-$mounth-$day", $holidays['date'])) {
      return TRUE;
    }
    else {
      return FALSE;
    }

  }

  /**
   * HTTP request.
   *
   * @see https://calendarific.com/
   */
  public function httpHoliDays($year) {

    try {

      // Construimos el HTTP con el servicio factory.
      $client = $this->httpClientFactory->fromOptions(['base_uri' => "https://calendarific.com/api/v2/holidays"]);

      $response = $client->request('GET', "?api_key=8259291d8207df7c562f6fa9f3dd060ac11a7bbd&country=co&year=$year&type=national", [
        'headers' => [
          'Content-Type' => 'application/json',
        ],
      ]);

      if ($response->getStatusCode() == '200') {

        // Seteamos el json a un arreglo y se obtiene solo valores del indice holidays.
        $body = Json::decode($response->getBody());
        $holidays = $body['response']['holidays'];
        $iso = [];

        // Iteramos y obtenemos la fecha guardando en el arreglo iso.
        foreach ($holidays as $holiday) {
          $iso[] = $holiday['date']['iso'];
        }

        // Construimos el arreglo para ser guardado en la nueva variable de estado.
        $data = [
          "date" => $iso,
          "year" => $year,
        ];

        $this->state->set('findeter_pqrsd.holidays', $data);

        return $data;
      }
      else {

        // Registramos el error en dblog.
        $this->logger->get('API Calendarific')->info("Code: %code Mensaje: %message, Ha ocurrido algun error o alerta en la creacion de dias festivos.",
        [
          '%code' => $response->getStatusCode(),
          '%message' => $response->getReasonPhrase(),
        ]);

        return FALSE;

      }

    }
    catch (RequestException $e) {

      $this->logger->get('API Calendarific')->error("Código: %code Mensaje: %message",
      [
        '%code' => $e->getCode(),
        '%message' => $e->getResponse()->getBody()->getContents()
      ]);

      return FALSE;
    }

  }

}
