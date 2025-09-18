<?php
/**
 * Configuración global para URLs y rutas
 * Este archivo debe ser incluido en todas las páginas que necesiten getBaseUrl()
 */

if (!function_exists('getBaseUrl')) {
    /**
     * Obtiene la URL base del proyecto de forma robusta
     * Funciona tanto en localhost como en Azure/producción con HTTPS
     */
    function getBaseUrl() {
        // Detectar protocolo de forma más robusta para Azure
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
                   || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                   || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
                   || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        
        $protocol = $isHttps ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        
        // Detectar si estamos en localhost o en Azure/producción
        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
            return $protocol . '://' . $host . '/PlanMaster';
        } else {
            return $protocol . '://' . $host;
        }
    }
}
?>