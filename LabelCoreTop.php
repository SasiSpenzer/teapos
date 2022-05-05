<?php
session_start();

function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';
}

?>

<?xml version="1.0" encoding="utf-8"?>
<DieCutLabel Version="8.0" Units="twips">
    <PaperOrientation>Landscape</PaperOrientation>
    <Id>Address</Id>
    <PaperName>30252 Address</PaperName>
    <DrawCommands>
        <RoundRectangle X="0" Y="0" Width="1581" Height="5040" Rx="270" Ry="270" />
    </DrawCommands>
    <ObjectInfo>
        <AddressObject>
            <Name>Address</Name>
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
                    <String>Jim Best
                        Good, Bedder, and Best
                        900 Park Ave
                        New York, NY 10021-0231</String>
                    <Attributes>
                        <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />
                        <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />
                    </Attributes>
                </Element>
            </StyledText>
            <ShowBarcodeFor9DigitZipOnly>False</ShowBarcodeFor9DigitZipOnly>
            <BarcodePosition>AboveAddress</BarcodePosition>
            <LineFonts>
                <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />
                <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />
                <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />
                <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />
            </LineFonts>
        </AddressObject>
        <Bounds X="386.339630126953" Y="313.018859863281" Width="3658.01879882813" Height="861.509460449219" />
    </ObjectInfo>
    <ObjectInfo>
        <BarcodeObject>
            <Name>BARCODE</Name>
            <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />
            <BackColor Alpha="0" Red="255" Green="255" Blue="255" />
            <LinkedObjectName></LinkedObjectName>
            <Rotation>Rotation0</Rotation>
            <IsMirrored>False</IsMirrored>
            <IsVariable>True</IsVariable>
            <Text>12345</Text>
            <Type>Code39</Type>
            <Size>Medium</Size>
            <TextPosition>Bottom</TextPosition>
            <TextFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />
            <CheckSumFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />
            <TextEmbedding>None</TextEmbedding>
            <ECLevel>0</ECLevel>
            <HorizontalAlignment>Center</HorizontalAlignment>
            <QuietZonesPadding Left="0" Top="0" Right="0" Bottom="0" />
        </BarcodeObject>
        <Bounds X="2073" Y="406.415094339623" Width="2880" Height="720" />
    </ObjectInfo>
</DieCutLabel>