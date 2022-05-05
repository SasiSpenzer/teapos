
<?php

if(isset($_POST['set_to_print'])){
    echo '<script type="text/javascript">',
        'printLabel();',
            '</script>'
    ;

 }
?>
<html>
<head>

    <!-- LabelWriter-API first -->
    <script   src="https://code.jquery.com/jquery-3.1.1.min.js"   integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="   crossorigin="anonymous"></script>
    <script src="http://labelwriter.com/software/dls/sdk/js/DYMO.Label.Framework.latest.js"></script>
    <!-- your script second -->
    <script>

        function printLabel() {
            alert('sas');
            $.get("./LabelCore.php", function (labelXml) {
                var label = dymo.label.framework.openLabelXml(labelXml);
                // open label
                // set label text


                // select printer to print on
                // for simplicity sake just use the first LabelWriter printer
                var printers = dymo.label.framework.getPrinters();

                if (printers.length == 0) throw "No DYMO printers are installed. Install DYMO printers.";
                var printerName = "";

                for (var i = 0; i < printers.length; ++i) {
                    var printer = printers[i];

                    if (printer.printerType == "TapePrinter") {

                        printerName = printer.name;
                        break;
                    }
                }


                if (printerName == "") throw "No LabelWriter printers found. Install LabelWriter printer";
                // finally print the label
                label.print(printerName);

            }, "text");
        }
    </script>

</head>




</body>
</html>

