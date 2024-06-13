<?php
function sad($odor, $visual, $temperature, $conductivity, $ph, $age) {
    $TERMINALNODE = 0;
    $CLASS = 0;

    // Coloque aqui todas as regras de decisão de sad.php
    /*Terminal Node 1*/
    if ($odor == 1 && $visual == 1 && $temperature <= 36.5) {
        $TERMINALNODE = -1;
        $CLASS = 3;
    }

    /*Terminal Node 2*/
    if ($odor == 1 && $visual == 1 && $temperature > 36.5 && $temperature <= 37.5) {
        $TERMINALNODE = -2;
        $CLASS = 2;
    }

    /*Terminal Node 3*/
    if ($odor == 1 && $visual == 1 && $temperature > 37.5) {
        $TERMINALNODE = -3;
        $CLASS = 3;
    }

    /*Terminal Node 4*/
    if ($odor == 0 && $visual == 1 && $conductivity <= 62.5 && $temperature <= 34.5) {
        $TERMINALNODE = -4;
        $CLASS = 2;
    }

    /*Terminal Node 5*/
    if ($odor == 0 && $visual == 1 && $conductivity <= 62.5 && $temperature > 34.5 && $temperature <= 37.5) {
        $TERMINALNODE = -5;
        $CLASS = 1;
    }

    /*Terminal Node 6*/
    if ($odor == 0 && $visual == 1 && $conductivity <= 62.5 && $temperature > 37.5) {
        $TERMINALNODE = -6;
        $CLASS = 2;
    }

    /*Terminal Node 7*/
    if ($odor == 0 && $visual == 1 && $conductivity > 62.5 && $ph <= 4.5) {
        $TERMINALNODE = -7;
        $CLASS = 3;
    }

    /*Terminal Node 8*/
    if ($odor == 0 && $visual == 1 && $conductivity > 62.5 && $ph > 4.5 && $ph <= 8.5 && $temperature <= 37.5) {
        $TERMINALNODE = -8;
        $CLASS = 1;
    }

    /*Terminal Node 9*/
    if ($odor == 0 && $visual == 1 && $conductivity > 62.5 && $ph > 4.5 && $ph <= 8.5 && $temperature > 37.5) {
        $TERMINALNODE = -9;
        $CLASS = 2;
    }

    /*Terminal Node 10*/
    if ($odor == 0 && $visual == 1 && $conductivity > 62.5 && $ph > 8.5 && $age <= 50.5) {
        $TERMINALNODE = -10;
        $CLASS = 3;
    }

    /*Terminal Node 11*/
    if ($odor == 0 && $visual == 1 && $conductivity > 62.5 && $ph > 8.5 && $age > 50.5) {
        $TERMINALNODE = -11;
        $CLASS = 2;
    }

    /*Terminal Node 12*/
    if ($odor == 1 && $visual == 0 && $ph <= 4.5) {
        $TERMINALNODE = -12;
        $CLASS = 2;
    }

    /*Terminal Node 13*/
    if ($odor == 1 && $visual == 0 && $ph > 4.5 && $ph <= 8.5 && $temperature <= 33.5) {
        $TERMINALNODE = -13;
        $CLASS = 2;
    }

    /*Terminal Node 14*/
    if ($odor == 1 && $visual == 0 && $ph > 4.5 && $ph <= 8.5 && $temperature > 33.5 && $temperature <= 41.5) {
        $TERMINALNODE = -14;
        $CLASS = 1;
    }

    /*Terminal Node 15*/
    if ($odor == 1 && $visual == 0 && $ph > 4.5 && $ph <= 8.5 && $temperature > 41.5) {
        $TERMINALNODE = -15;
        $CLASS = 2;
    }

    /*Terminal Node 16*/
    if ($odor == 1 && $visual == 0 && $ph > 8.5 && $temperature <= 37.5) {
        $TERMINALNODE = -16;
        $CLASS = 2;
    }

    /*Terminal Node 17*/
    if ($odor == 1 && $visual == 0 && $ph > 8.5 && $temperature > 37.5) {
        $TERMINALNODE = -17;
        $CLASS = 3;
    }

    /*Terminal Node 18*/
    if ($odor == 0 && $visual == 0 && $ph <= 8.5) {
        $TERMINALNODE = -18;
        $CLASS = 1;
    }

    /*Terminal Node 19*/
    if ($odor == 0 && $visual == 0 && $ph > 8.5 && $temperature <= 34.5) {
        $TERMINALNODE = -19;
        $CLASS = 2;
    }

    /*Terminal Node 20*/
    if ($odor == 0 && $visual == 0 && $ph > 8.5 && $temperature > 34.5 && $temperature <= 37.5) {
        $TERMINALNODE = -20;
        $CLASS = 1;
    }

    /*Terminal Node 21*/
    if ($odor == 0 && $visual == 0 && $ph > 8.5 && $temperature > 37.5) {
        $TERMINALNODE = -21;
        $CLASS = 2;
    }

    switch ($CLASS) {
        case 1:
            return "NORMAL";
        case 2:
            return "ALERT";
        case 3:
            return "URGENT_ALERT";
        default:
            return "DESCONHECIDO";
    }
}
?>
