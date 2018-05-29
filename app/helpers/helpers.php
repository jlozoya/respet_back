<?php
if (!function_exists('config_path'))
{
    /**
     * Obtiene la ruta de configuración.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}