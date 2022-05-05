<?php
/**
 * This is a demo script for the functions of the PHP ESC/POS print driver,
 * 2017-11-27-old-Escpos.php.
 *
 * Most printers implement only a subset of the functionality of the driver, so
 * will not render this output correctly in all cases.
 *
 * @author Michael Billington <michael.billington@gmail.com>
 */

require_once("Escpos.php");
$printer = new Escpos();
///* Initialize */
$printer -> initialize();
///* Text */
$printer -> text("Hello world\n");
$printer -> cut();
///* Line feeds */
$printer -> text("ABC");
$printer -> feed(7);
$printer -> text("DEF");
$printer -> feedReverse(3);
$printer -> text("GHI");
$printer -> feed();
$printer -> cut();
// set some variables
