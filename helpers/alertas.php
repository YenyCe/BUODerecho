<?php
function mostrarAlerta()
{
    if (!isset($_SESSION['alerta'])) {
        return '';
    }

    $tipo = $_SESSION['alerta']['tipo'];
    $mensaje = $_SESSION['alerta']['mensaje'];

    unset($_SESSION['alerta']);

    return "
        <div class='alerta {$tipo}'>
            {$mensaje}
        </div>
    ";
}
