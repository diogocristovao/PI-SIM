<?php
/*Terminal Node 1*/
if
(
    (
        $ODOR == 1
    ) &&
    (
        $VISUAL == 1
    ) &&
    $TEMPERATURE <= 36.5
)
{
    $TERMINALNODE = -1;
    $CLASS = 3;
}

/*Terminal Node 2*/
if
(
    (
        $ODOR == 1
    ) &&
    (
        $VISUAL == 1
    ) &&
    $TEMPERATURE > 36.5 &&
    $TEMPERATURE <= 37.5
)
{
    $TERMINALNODE = -2;
    $CLASS = 2;
}

/*Terminal Node 3*/
if
(
    (
        $ODOR == 1
    ) &&
    (
        $VISUAL == 1
    ) &&
    $TEMPERATURE > 37.5
)
{
    $TERMINALNODE = -3;
    $CLASS = 3;
}

/*Terminal Node 4*/
if
(
    (
        $ODOR == 0
    ) &&
    (
        $VISUAL == 1
    ) &&
    $CONDUCTIVITY <= 62.5 &&
    $TEMPERATURE <= 34.5
)
{
    $TERMINALNODE = -4;
    $CLASS = 2;
}

/*Terminal Node 5*/
if
(
    (
        $ODOR == 0
    ) &&
    (
        $VISUAL == 1
    ) &&
    $CONDUCTIVITY <= 62.5 &&
    $TEMPERATURE > 34.5 &&
    $TEMPERATURE <= 37.5
)
{
    $TERMINALNODE = -5;
    $CLASS = 1;
}

/*Terminal Node 6*/
if
(
    (
        $ODOR == 0
    ) &&
    (
        $VISUAL == 1
    ) &&
    $CONDUCTIVITY <= 62.5 &&
    $TEMPERATURE > 37.5
)
{
    $TERMINALNODE = -6;
    $CLASS = 2;
}

/*Terminal Node 7*/
if
(
    (
        $ODOR == 0
    ) &&
    (
        $VISUAL == 1
    ) &&
    $CONDUCTIVITY > 62.5 &&
    $PH <= 4.5
)
{
    $TERMINALNODE = -7;
    $CLASS = 3;
}

/*Terminal Node 8*/
if
(
    (
        $ODOR == 0
    ) &&
    (
        $VISUAL == 1
    ) &&
    $CONDUCTIVITY > 62.5 &&
    $PH > 4.5 &&
    $PH <= 8.5 &&
    $TEMPERATURE <= 37.5
)
{
    $TERMINALNODE = -8;
    $CLASS = 1;
}

/*Terminal Node 9*/
if
(
    (
        $ODOR == 0
    ) &&
    (
        $VISUAL == 1
    ) &&
    $CONDUCTIVITY > 62.5 &&
    $PH > 4.5 &&
    $PH <= 8.5 &&
    $TEMPERATURE > 37.5
)
{
    $TERMINALNODE = -9;
    $CLASS = 2;
}

/*Terminal Node 10*/
if
(
    (
        $ODOR == 0
    ) &&
    (
        $VISUAL == 1
    ) &&
    $CONDUCTIVITY > 62.5 &&
    $PH > 8.5 &&
    $AGE <= 50.5
)
{
    $TERMINALNODE = -10;
    $CLASS = 3;
}

/*Terminal Node 11*/
if
(
    (
        $ODOR == 0
    ) &&
    (
        $VISUAL == 1
    ) &&
    $CONDUCTIVITY > 62.5 &&
    $PH > 8.5 &&
    $AGE > 50.5
)
{
    $TERMINALNODE = -11;
    $CLASS = 2;
}

/*Terminal Node 12*/
if
(
    (
        $ODOR == 1
    ) &&
    (
        $VISUAL == 0
    ) &&
    $PH <= 4.5
)
{
    $TERMINALNODE = -12;
    $CLASS = 2;
}

/*Terminal Node 13*/
if
(
    (
        $ODOR == 1
    ) &&
    (
        $VISUAL == 0
    ) &&
    $PH > 4.5 &&
    $PH <= 8.5 &&
    $TEMPERATURE <= 33.5
)
{
    $TERMINALNODE = -13;
    $CLASS = 2;
}

/*Terminal Node 14*/
if
(
    (
        $ODOR == 1
    ) &&
    (
        $VISUAL == 0
    ) &&
    $PH > 4.5 &&
    $PH <= 8.5 &&
    $TEMPERATURE > 33.5 &&
    $TEMPERATURE <= 41.5
)
{
    $TERMINALNODE = -14;
    $CLASS = 1;
}

/*Terminal Node 15*/
if
(
    (
        $ODOR == 1
    ) &&
    (
        $VISUAL == 0
    ) &&
    $PH > 4.5 &&
    $PH <= 8.5 &&
    $TEMPERATURE > 41.5
)
{
    $TERMINALNODE = -15;
    $CLASS = 2;
}

/*Terminal Node 16*/
if
(
    (
        $ODOR == 1
    ) &&
    (
        $VISUAL == 0
    ) &&
    $PH > 8.5 &&
    $TEMPERATURE <= 37.5
)
{
    $TERMINALNODE = -16;
    $CLASS = 2;
}

/*Terminal Node 17*/
if
(
    (
        $ODOR == 1
    ) &&
    (
        $VISUAL == 0
    ) &&
    $PH > 8.5 &&
    $TEMPERATURE > 37.5
)
{
    $TERMINALNODE = -17;
    $CLASS = 3;
}

/*Terminal Node 18*/
if
(
    (
        $ODOR == 0
    ) &&
    (
        $VISUAL == 0
    ) &&
    $PH <= 8.5
)
{
    $TERMINALNODE = -18;
    $CLASS = 1;
}

/*Terminal Node 19*/
if
(
    (
        $ODOR == 0
    ) &&
    (
        $VISUAL == 0
    ) &&
    $PH > 8.5 &&
    $TEMPERATURE <= 34.5
)
{
    $TERMINALNODE = -19;
    $CLASS = 2;
}

/*Terminal Node 20*/
if
(
    (
        $ODOR == 0
    ) &&
    (
        $VISUAL == 0
    ) &&
    $PH > 8.5 &&
    $TEMPERATURE > 34.5 &&
    $TEMPERATURE <= 37.5
)
{
    $TERMINALNODE = -20;
    $CLASS = 1;
}

/*Terminal Node 21*/
if
(
    (
        $ODOR == 0
    ) &&
    (
        $VISUAL == 0
    ) &&
    $PH > 8.5 &&
    $TEMPERATURE > 37.5
)
{
    $TERMINALNODE = -21;
    $CLASS = 2;
}

?>