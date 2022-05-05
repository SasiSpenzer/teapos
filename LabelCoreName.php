<?php
session_start();

function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';
}


?>
<?xml version="1.0" encoding="utf-8"?>
<ContinuousLabel Version="8.0" Units="twips">
    <PaperOrientation>Landscape</PaperOrientation>
    <Id>Tape12mm</Id>
    <PaperName>12mm</PaperName>
    <LengthMode>Auto</LengthMode>
    <LabelLength>0</LabelLength>
    <RootCell>
        <Length>0</Length>
        <LengthMode>Auto</LengthMode>
        <BorderWidth>0</BorderWidth>
        <BorderStyle>Solid</BorderStyle>
        <BorderColor Alpha="255" Red="0" Green="0" Blue="0" />
        <SubcellsOrientation>Horizontal</SubcellsOrientation>
        <Subcells>
            <Cell>
                <TextObject>
                    <Name>TEXT</Name>
                    <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />
                    <BackColor Alpha="0" Red="255" Green="255" Blue="255" />
                    <LinkedObjectName></LinkedObjectName>
                    <Rotation>Rotation0</Rotation>
                    <IsMirrored>False</IsMirrored>
                    <IsVariable>True</IsVariable>
                    <HorizontalAlignment>Left</HorizontalAlignment>
                    <VerticalAlignment>Middle</VerticalAlignment>
                    <TextFitMode>ShrinkToFit</TextFitMode>
                    <UseFullFontHeight>True</UseFullFontHeight>
                    <Verticalized>False</Verticalized>
                    <StyledText />
                </TextObject>
                <ObjectMargin Left="0" Top="0" Right="0" Bottom="0" />
                <Length>0</Length>
                <LengthMode>Auto</LengthMode>
                <BorderWidth>0</BorderWidth>
                <BorderStyle>Solid</BorderStyle>
                <BorderColor Alpha="255" Red="0" Green="0" Blue="0" />
            </Cell>
            <Cell>
                <AddressObject>
                    <Name>ADDRESS</Name>
                    <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />
                    <BackColor Alpha="0" Red="255" Green="255" Blue="255" />
                    <LinkedObjectName></LinkedObjectName>
                    <Rotation>Rotation0</Rotation>
                    <IsMirrored>False</IsMirrored>
                    <IsVariable>True</IsVariable>
                    <HorizontalAlignment>Left</HorizontalAlignment>
                    <VerticalAlignment>Middle</VerticalAlignment>
                    <TextFitMode>ShrinkToFit</TextFitMode>
                    <UseFullFontHeight>True</UseFullFontHeight>
                    <Verticalized>False</Verticalized>
                    <StyledText>
                        <Element>
                            <String><?php echo $_SESSION['product_name_label'].PHP_EOL; ?>
Best Before :- <?php echo  $_SESSION['product_exdate_label'] ; ?></String>
                            <Attributes>
                                <Font Family="Arial" Size="10" Bold="False" Italic="False" Underline="False" Strikeout="False" />
                                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />
                            </Attributes>
                        </Element>
                    </StyledText>
                    <ShowBarcodeFor9DigitZipOnly>False</ShowBarcodeFor9DigitZipOnly>
                    <BarcodePosition>AboveAddress</BarcodePosition>
                </AddressObject>
                <ObjectMargin Left="150" Top="0" Right="150" Bottom="0" />
                <Length>2000</Length>
                <LengthMode>Fixed</LengthMode>
                <BorderWidth>0</BorderWidth>
                <BorderStyle>Solid</BorderStyle>
                <BorderColor Alpha="255" Red="0" Green="0" Blue="0" />
            </Cell>
        </Subcells>
    </RootCell>
</ContinuousLabel>