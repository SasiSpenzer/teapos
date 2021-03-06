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
                    <HorizontalAlignment>Center</HorizontalAlignment>
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
                <BarcodeObject>
                    <Name>BARCODE</Name>
                    <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />
                    <BackColor Alpha="0" Red="255" Green="255" Blue="255" />
                    <LinkedObjectName></LinkedObjectName>
                    <Rotation>Rotation0</Rotation>
                    <IsMirrored>False</IsMirrored>
                    <IsVariable>True</IsVariable>
                    <Text><?php echo $_SESSION['product_barcode_label'];?></Text>
                    <Type>Code39</Type>
                    <Size>Small</Size>
                    <TextPosition>Bottom</TextPosition>
                    <TextFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />
                    <CheckSumFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />
                    <TextEmbedding>None</TextEmbedding>
                    <ECLevel>0</ECLevel>
                    <HorizontalAlignment>Center</HorizontalAlignment>
                    <QuietZonesPadding Left="0" Top="0" Right="0" Bottom="0" />
                </BarcodeObject>
                <ObjectMargin Left="150" Top="0" Right="150" Bottom="0" />
                <Length>2880</Length>
                <LengthMode>Auto</LengthMode>
                <BorderWidth>0</BorderWidth>
                <BorderStyle>Solid</BorderStyle>
                <BorderColor Alpha="255" Red="0" Green="0" Blue="0" />
            </Cell>
        </Subcells>
    </RootCell>
</ContinuousLabel>