<?php
namespace Drupal\findeter_pqrsd\Api;
/**
 * Interface para API Http cliente SMFC
 * @author 3ddesarrollo
 */

interface ApiSmfcInterface{

    /**
     * Retorna el numero de entidad para ser guardado junto con el numero de radicado para SMFC
     * @param $num_settled numero de radicado de la queja
     * @return $num_code_entity
     */
    public function getTipCodeEntity(string $num_settled): string;

    /**
     * Agregar extensiones de archivos que acepta la API SMFC
     * @return string $xtension
     */
    public function getExtFile(): string;
    /**
     * Login
     * Used to enter SMFC system and get access token
     * El sistema de login se ejecuta cada 12hrs mediante el cron
     */
    public function login(): bool;

    /**
     * RefreshToken
     * Actualizar la variable de estado token para una nueva peticion pasado los 30min.
     */
    //public function refreshToken(): void;

    /**
     * POST Quejas
     * Envio de quejas al sistema SMFC
     * @param $nid 
     *  - parametro $nid con argumento de identificacion del nodo.
     */
    public function postComplaints(int $nid): void;

}