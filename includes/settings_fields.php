<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

return array(
    'enabled'               => array(
        'title'   => __( 'Activar/Desactivar', 'mpandco' ),
        'type'    => 'checkbox',
        'label'   => __( 'Activar mPandco Payment', 'mpandco' ),
        'default' => 'no',
    ),
    'title'                 => array(
        'title'       => __( 'Titulo', 'mpandco' ),
        'type'        => 'text',
        'description' => __( 'Esto controla el título que el usuario ve durante el proceso de pago.', 'mpandco' ),
        'default'     => __( 'mPandco', 'mpandco' ),
        'desc_tip'    => true,
    ),
    'description'           => array(
        'title'       => __( 'Description', 'woocommerce' ),
        'type'        => 'text',
        'desc_tip'    => true,
        'description' => __( 'Esto controla la descripción que el usuario ve durante el proceso de pago.', 'mpandco' ),
        'default'     => __( "Pago via mPandco.",'mpandco'),
    ),
    'advanced'              => array(
        'title'       => __( 'Advanced options', 'woocommerce' ),
        'type'        => 'title',
    ),
    'debug'                 => array(
        'title'       => __( 'Debug log', 'mpandco' ),
        'type'        => 'checkbox',
        'label'       => __( 'Habilitar el registro', 'mpandco' ),
        'default'     => 'no',
        'description' => sprintf( __( 'Registrar eventos mPandco, dentro de %s Nota: esto puede registrar información personal. Recomendamos usar esto sólo con fines de depuración y borrar los registros cuando haya terminado.', 'mpandco' ), '<code>' . WC_Log_Handler_File::get_log_file_path( 'mpandco' ) . '</code>' ),
    ),
    'api_details'           => array(
        'title'       => __( 'Credenciales API', 'mpandco' ),
        'type'        => 'title',
        'description' => sprintf( __( 'Ingrese sus credenciales de API de mPandco para procesar pagos a través de mPandco. Recuerde crear una cuenta en <a href="%s">App mPandco</a> y registre su aplicación oAuth.', 'mpandco' ),'https://app.mpandco.com'),
    ),

    'api_id'                => array(
        'title'        => __( 'Client ID  de su mPandco', 'mpandco'),
        'type'         => 'password',
        'description'  => __('El identificador mPandco que recibió para su cuenta OAUTH electrónica mPandco', 'mpandco'),
        'default'      => '',
        'desc_tip'     => true,
        'placeholder'  => __('Client ID', 'mpandco')
    ),
    'api_secret'                => array(
        'title'        => __( 'Client Secret de su mPandco', 'mpandco'),
        'type'         => 'password',
        'description'  => __('La llave secreta mPandco que recibio para su cuenta OAUTH electronica mPandco', 'mpandco'),
        'default'      => '',
        'desc_tip'     => true,
        'placeholder'  => __('Client Secret', 'mpandco')
    ),
    'testmode'              => array(
        'title'       => __( 'mPandco sandbox', 'mpandco' ),
        'type'        => 'checkbox',
        'label'       => __( 'Activar modo de pruebas sandbox', 'mpandco' ),
        'default'     => 'no',
        'description' => sprintf( __( 'Para usar el modo de prueba sandbox de mPandco debe crear una cuenta en <a href="%s">Sandbox mPandco</a> y registre su aplicación oAuth para obtener su llaves de prueba.', 'mpandco' ), 'https://test.mpandco.com' ),
    ),
    'sandbox_api_id'                => array(
        'title'        => __( 'Client ID  de su mPandco sandbox', 'mpandco'),
        'type'         => 'password',
        'description'  => __('El identificador mPandco que recibió para su cuenta OAUTH electrónica mPandco sandbox', 'mpandco'),
        'default'      => '',
        'desc_tip'     => true,
        'placeholder'  => __('Client ID Sandbox', 'mpandco')
    ),
    'sandbox_api_secret'                => array(
        'title'        => __( 'Client Secret de su mPandco sandbox', 'mpandco'),
        'type'         => 'password',
        'description'  => __('La llave secreta mPandco que recibio para su cuenta OAUTH electronica mPandco sandbox', 'mpandco'),
        'default'      => '',
        'desc_tip'     => true,
        'placeholder'  => __('Client Secret Sandbox', 'mpandco')
    ),
);