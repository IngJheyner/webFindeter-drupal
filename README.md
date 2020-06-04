# Algunas configuraciones WebFinde
 - Drupal `8.8.6`
     - Se estara evaluando si actualizar a la ultima version de drupal `8.9`
 - Instalar un entorno de desarrollo local con [LARAGON](https://laragon.org/).    
 - Instalar [DRUSH](https://www.drupal.org/node/594744) globalmente dentro de su entorno.
 - Seguir instrucciones para importar y exportar las configuraciones de nuestro ambiente local en drupal [ENLACE](https://sdos.es/blog/exportar-contenidos-con-drupal)
     - En el archivo settings.php de nuestor sitio configurar la linea 
      `$settings['config_sync_directory'] = 'git/estructura';` (***git*** es una carpeta raiz de nuestro directorio donde se van a guardar la importacion de los archivos .ylm) despues con el comando `drush.cim` importamos los archivos necesarios que se encuentra en la carpeta estructura. Recordar copiar esos archivos en el directorio que acabo de configurar.
    - En esta configuracion solo ira instalado el modulo ***adminToolbar***, si la configuracion sale bien ya lo demas modulos lo iremos implementando.
    - Lenguaje en español instalado, si desea agrega la configuracion de host que recomienda drupal al instalar, edite la siguiente linea en su archivo settings.php
    `$settings['trusted_host_patterns'] = [
    '^webfinde\.test$',
  ];`

  # Tema
  - Una buena opcion es utilizar la herramienta del framework `Bootstrap`, para eso drupal nos da la opcion de instalar el tema ***Bootstrap Barrio*** para la cual creamos un subtema llamado ***webFine-Custom*** donde iran las configuraciones y demas archivos que queramos modificar.
      - Para utilizarlo, NO nos vamos a basar en los archivos que trae por configuracion como los css y js que viene por CDN, para eso, copie la carpeta ***libraries/*** en su carpeta raiz. 
      - Instale [Bootstrap Barrio](https://www.drupal.org/project/bootstrap_barrio) en su CMS.
      - Copie la carpeta `WebFinde-custom` en la ruta `themes/bootstrap_barrio/` y remplacelo por la carpeta `subtheme` que viene por defecto.
      - Instalar y dejarlo por predeterminado en su CMS, probar.
 
