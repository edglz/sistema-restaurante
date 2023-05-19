<?php

class Apisunat {

    public function crear_xml_factura($cabecera, $detalle, $ruta) {
        $validacion = new validaciondedatos();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        //$doc->encoding = 'ISO-8859-1';
        $doc->encoding = 'utf-8';

        $xmlCPE = '<?xml version="1.0" encoding="utf-8"?>
<Invoice xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2">
	<ext:UBLExtensions>
		<ext:UBLExtension>
			<ext:ExtensionContent>
			</ext:ExtensionContent>
		</ext:UBLExtension>
	</ext:UBLExtensions>
	<cbc:UBLVersionID>2.1</cbc:UBLVersionID>
	<cbc:CustomizationID schemeAgencyName="PE:SUNAT">2.0</cbc:CustomizationID>
	<cbc:ProfileID schemeName="Tipo de Operacion" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo51">' . $cabecera["TIPO_OPERACION"] . '</cbc:ProfileID>
	<cbc:ID>' . $cabecera["NRO_COMPROBANTE"] . '</cbc:ID>
	<cbc:IssueDate>' . $cabecera["FECHA_DOCUMENTO"] . '</cbc:IssueDate>
	<cbc:IssueTime>00:00:00</cbc:IssueTime>
	<cbc:DueDate>' . $cabecera["FECHA_VTO"] . '</cbc:DueDate>
	<cbc:InvoiceTypeCode listAgencyName="PE:SUNAT" listName="Tipo de Documento" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01" listID="' . $cabecera["TIPO_OPERACION"] . '" name="Tipo de Operacion" listSchemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo51">' . $cabecera["COD_TIPO_DOCUMENTO"] . '</cbc:InvoiceTypeCode>';
        if ($cabecera["TOTAL_LETRAS"] <> "") {
            $xmlCPE = $xmlCPE .
                    '<cbc:Note languageLocaleID="1000">' . $cabecera["TOTAL_LETRAS"] . '</cbc:Note>';
        }
        $xmlCPE = $xmlCPE .
                '<cbc:DocumentCurrencyCode listID="ISO 4217 Alpha" listName="Currency" listAgencyName="United Nations Economic Commission for Europe">' . $cabecera["COD_MONEDA"] . '</cbc:DocumentCurrencyCode>
            <cbc:LineCountNumeric>' . count($detalle) . '</cbc:LineCountNumeric>';
        if ($cabecera["NRO_OTR_COMPROBANTE"] <> "") {
            $xmlCPE = $xmlCPE .
                    '<cac:OrderReference>
                    <cbc:ID>' . $cabecera["NRO_OTR_COMPROBANTE"] . '</cbc:ID>
            </cac:OrderReference>';
        }
        if ($cabecera["NRO_GUIA_REMISION"] <> "") {
            $xmlCPE = $xmlCPE .
                    '<cac:DespatchDocumentReference>
		<cbc:ID>' . $cabecera["NRO_GUIA_REMISION"] . '</cbc:ID>
		<cbc:IssueDate>' . $cabecera["FECHA_GUIA_REMISION"] . '</cbc:IssueDate>
		<cbc:DocumentTypeCode listAgencyName="PE:SUNAT" listName="Tipo de Documento" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">' . $cabecera["COD_GUIA_REMISION"] . '</cbc:DocumentTypeCode>
            </cac:DespatchDocumentReference>';
        }
        $xmlCPE = $xmlCPE .
                '<cac:Signature>
		<cbc:ID>' . $cabecera["NRO_COMPROBANTE"] . '</cbc:ID>
		<cac:SignatoryParty>
			<cac:PartyIdentification>
				<cbc:ID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
			</cac:PartyIdentification>
			<cac:PartyName>
				<cbc:Name>' . $cabecera["RAZON_SOCIAL_EMPRESA"] . '</cbc:Name>
			</cac:PartyName>
		</cac:SignatoryParty>
		<cac:DigitalSignatureAttachment>
			<cac:ExternalReference>
				<cbc:URI>#' . $cabecera["NRO_COMPROBANTE"] . '</cbc:URI>
			</cac:ExternalReference>
		</cac:DigitalSignatureAttachment>
	</cac:Signature>
	<cac:AccountingSupplierParty>
		<cac:Party>
			<cac:PartyIdentification>
				<cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
			</cac:PartyIdentification>
			<cac:PartyName>
				<cbc:Name><![CDATA[' . $cabecera["NOMBRE_COMERCIAL_EMPRESA"] . ']]></cbc:Name>
			</cac:PartyName>
			<cac:PartyTaxScheme>
				<cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:RegistrationName>
				<cbc:CompanyID schemeID="' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:CompanyID>
				<cac:TaxScheme>
					<cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
				</cac:TaxScheme>
			</cac:PartyTaxScheme>
			<cac:PartyLegalEntity>
				<cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:RegistrationName>
				<cac:RegistrationAddress>
                                        <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI"><![CDATA[' . $cabecera["CODIGO_UBIGEO_EMPRESA"] . ']]></cbc:ID>
					<cbc:AddressTypeCode listAgencyName="PE:SUNAT" listName="Establecimientos anexos">0000</cbc:AddressTypeCode>
					<cbc:CityName><![CDATA[' . $cabecera["DEPARTAMENTO_EMPRESA"] . ']]></cbc:CityName>
					<cbc:CountrySubentity><![CDATA[' . $cabecera["PROVINCIA_EMPRESA"] . ']]></cbc:CountrySubentity>
					<cbc:District><![CDATA[' . $cabecera["DISTRITO_EMPRESA"] . ']]></cbc:District>
					<cac:AddressLine>
						<cbc:Line><![CDATA[' . $cabecera["DIRECCION_EMPRESA"] . ']]></cbc:Line>
					</cac:AddressLine>
					<cac:Country>
						<cbc:IdentificationCode listID="ISO 3166-1" listAgencyName="United Nations Economic Commission for Europe" listName="Country">' . $cabecera["CODIGO_PAIS_EMPRESA"] . '</cbc:IdentificationCode>
					</cac:Country>
				</cac:RegistrationAddress>
			</cac:PartyLegalEntity>
			<cac:Contact>
				<cbc:Name><![CDATA[' . $cabecera["CONTACTO_EMPRESA"] . ']]></cbc:Name>
			</cac:Contact>
		</cac:Party>
	</cac:AccountingSupplierParty>
	<cac:AccountingCustomerParty>
		<cac:Party>
			<cac:PartyIdentification>
				<cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_CLIENTE"] . '" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_CLIENTE"] . '</cbc:ID>
			</cac:PartyIdentification>
			<cac:PartyName>
				<cbc:Name><![CDATA[' . $cabecera["RAZON_SOCIAL_CLIENTE"] . ']]></cbc:Name>
			</cac:PartyName>
			<cac:PartyTaxScheme>
				<cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_CLIENTE"] . ']]></cbc:RegistrationName>
				<cbc:CompanyID schemeID="' . $cabecera["TIPO_DOCUMENTO_CLIENTE"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_CLIENTE"] . '</cbc:CompanyID>
				<cac:TaxScheme>
					<cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_CLIENTE"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_CLIENTE"] . '</cbc:ID>
				</cac:TaxScheme>
			</cac:PartyTaxScheme>
			<cac:PartyLegalEntity>
				<cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_CLIENTE"] . ']]></cbc:RegistrationName>
				<cac:RegistrationAddress>
					<cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">' . $cabecera["COD_UBIGEO_CLIENTE"] . '</cbc:ID>
					<cbc:CityName><![CDATA[' . $cabecera["DEPARTAMENTO_CLIENTE"] . ']]></cbc:CityName>
					<cbc:CountrySubentity><![CDATA[' . $cabecera["PROVINCIA_CLIENTE"] . ']]></cbc:CountrySubentity>
					<cbc:District><![CDATA[' . $cabecera["DISTRITO_CLIENTE"] . ']]></cbc:District>
					<cac:AddressLine>
						<cbc:Line><![CDATA[' . $cabecera["DIRECCION_CLIENTE"] . ']]></cbc:Line>
					</cac:AddressLine>                                        
					<cac:Country>
						<cbc:IdentificationCode listID="ISO 3166-1" listAgencyName="United Nations Economic Commission for Europe" listName="Country">' . $cabecera["COD_PAIS_CLIENTE"] . '</cbc:IdentificationCode>
					</cac:Country>
				</cac:RegistrationAddress>
			</cac:PartyLegalEntity>
		</cac:Party>
	</cac:AccountingCustomerParty>
	<cac:PaymentTerms>
      	<cbc:ID>FormaPago</cbc:ID>
      	<cbc:PaymentMeansID>Contado</cbc:PaymentMeansID>
   	</cac:PaymentTerms>';
        
        if ($cabecera["DSCTO_GLOBAL"] > 0) {
           $xmlCPE .= '<cac:AllowanceCharge>
                            <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
                            <cbc:AllowanceChargeReasonCode listName="Cargo/descuento" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo53">02</cbc:AllowanceChargeReasonCode>
                            <cbc:MultiplierFactorNumeric>'.$cabecera["DSCTO_PORCENTAJE"].'</cbc:MultiplierFactorNumeric>
                            <cbc:Amount currencyID="' . $cabecera["COD_MONEDA"] . '">'.$cabecera["DSCTO_GLOBAL"].'</cbc:Amount>
                            <cbc:BaseAmount currencyID="' . $cabecera["COD_MONEDA"] . '">'.$cabecera["DSCTO_SUBTOTAL"].'</cbc:BaseAmount>
                    </cac:AllowanceCharge>';
        }
        #RECARGO AL CONSUMO
        /*$xmlCPE .='<cac:AllowanceCharge>
		<cbc:ChargeIndicator>true</cbc:ChargeIndicator>
		<cbc:AllowanceChargeReasonCode listName="Cargo/descuento" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo53">46</cbc:AllowanceChargeReasonCode>
		<cbc:MultiplierFactorNumeric>0.00</cbc:MultiplierFactorNumeric>
		<cbc:Amount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:Amount>
		<cbc:BaseAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:BaseAmount>
	</cac:AllowanceCharge>';*/
        
        
	$xmlCPE .='<cac:TaxTotal>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . ($cabecera["TOTAL_IGV"] + $cabecera["TOTAL_BOLSA"]) . '</cbc:TaxAmount>';
        if ($cabecera["TOTAL_IGV"] > 0) {
            $xmlCPE = $xmlCPE . '
		<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_GRAVADAS"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_IGV"] . '</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">1000</cbc:ID>
					<cbc:Name>IGV</cbc:Name>
					<cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
        }
        if ($cabecera["TOTAL_BOLSA"] > 0) {
            $xmlCPE = $xmlCPE . '
                <cac:TaxSubtotal>
                    <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_BOLSA"] . '</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cac:TaxScheme>
                            <cbc:ID>7152</cbc:ID>
                            <cbc:Name>ICBPER</cbc:Name>
                            <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>';
        }
        if ($cabecera["TOTAL_ISC"] > 0) {
            $xmlCPE = $xmlCPE .
                    '<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_ISC"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_ISC"] . '</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">2000</cbc:ID>
					<cbc:Name>ISC</cbc:Name>
					<cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
        }
        //CAMPO NUEVO
        if ($cabecera["TOTAL_EXPORTACION"] > 0) {
            $xmlCPE = $xmlCPE .
                    '<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_EXPORTACION"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">G</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9995</cbc:ID>
					<cbc:Name>EXP</cbc:Name>
					<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
        }
        if ($cabecera["TOTAL_GRATUITAS"] > 0) {
            $xmlCPE = $xmlCPE .
                    '<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_GRATUITAS"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">Z</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
					<cbc:Name>GRA</cbc:Name>
					<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
        }
        if ($cabecera["TOTAL_EXONERADAS"] > 0) {
            $xmlCPE = $xmlCPE .
                    '<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_EXONERADAS"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
					<cbc:Name>EXO</cbc:Name>
					<cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
        }
        if ($cabecera["TOTAL_INAFECTA"] > 0) {
            $xmlCPE = $xmlCPE .
                    '<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_INAFECTA"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
					<cbc:Name>INA</cbc:Name>
					<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
        }
        if ($cabecera["TOTAL_OTR_IMP"] > 0) {
            $xmlCPE = $xmlCPE .
                    '<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_OTR_IMP"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_OTR_IMP"] . '</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9999</cbc:ID>
					<cbc:Name>OTR</cbc:Name>
					<cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
        }
        //TOTAL=GRAVADA+IGV+EXONERADA
        //NO ENTRA GRATUITA(INAFECTA) NI DESCUENTO
        //SUB_TOTAL=PRECIO(SIN IGV) * CANTIDAD
        $xmlCPE = $xmlCPE .
                '</cac:TaxTotal>
	<cac:LegalMonetaryTotal>
		<cbc:LineExtensionAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["SUB_TOTAL"] . '</cbc:LineExtensionAmount>
		<cbc:TaxInclusiveAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL"] . '</cbc:TaxInclusiveAmount>
		<cbc:AllowanceTotalAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_DESCUENTO"] . '</cbc:AllowanceTotalAmount>
		<cbc:ChargeTotalAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:ChargeTotalAmount>
		<cbc:PayableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL"] . '</cbc:PayableAmount>
	</cac:LegalMonetaryTotal>';
        for ($i = 0; $i < count($detalle); $i++) {
            $xmlCPE = $xmlCPE . '<cac:InvoiceLine>
		<cbc:ID>' . $detalle[$i]["txtITEM"] . '</cbc:ID>
		<cbc:InvoicedQuantity unitCode="' . $detalle[$i]["txtUNIDAD_MEDIDA_DET"] . '" unitCodeListID="UN/ECE rec 20" unitCodeListAgencyName="United Nations Economic Commission for Europe">' . $detalle[$i]["txtCANTIDAD_DET"] . '</cbc:InvoicedQuantity>
		<cbc:LineExtensionAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIMPORTE_DET"] . '</cbc:LineExtensionAmount>
		<cac:PricingReference>
			<cac:AlternativeConditionPrice>
				<cbc:PriceAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtPRECIO_DET"] . '</cbc:PriceAmount>
				<cbc:PriceTypeCode listName="Tipo de Precio" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16">' . $detalle[$i]["txtPRECIO_TIPO_CODIGO"] . '</cbc:PriceTypeCode>
			</cac:AlternativeConditionPrice>
		</cac:PricingReference>
		<cac:TaxTotal>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . ($detalle[$i]["txtIGV"] + $detalle[$i]["txtICBPER_DET"]) . '</cbc:TaxAmount>
			<cac:TaxSubtotal>
				<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIMPORTE_DET"] . '</cbc:TaxableAmount>
				<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIGV"] . '</cbc:TaxAmount>';
            //exonerada
            if ($detalle[$i]["txtCOD_TIPO_OPERACION"] == 20) {
                $xmlCPE .= '  <cac:TaxCategory>
                                        <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                                        <cbc:Percent>' . $cabecera["POR_IGV"] . '</cbc:Percent>
					<cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="Afectacion del IGV" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">' . $detalle[$i]["txtCOD_TIPO_OPERACION"] . '</cbc:TaxExemptionReasonCode>
                                        <cac:TaxScheme>
                                                <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                                                <cbc:Name>EXO</cbc:Name>
                                                <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                                        </cac:TaxScheme>
                                </cac:TaxCategory>';
            } else if ($cabecera["TOTAL_GRATUITAS"] > 0 || $detalle[$i]["txtCOD_TIPO_OPERACION"] == 11) {
                $xmlCPE .= '  <cac:TaxCategory>
                                        <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
                                        <cbc:Percent>' . $cabecera["POR_IGV"] . '</cbc:Percent>
					<cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="Afectacion del IGV" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">' . $detalle[$i]["txtCOD_TIPO_OPERACION"] . '</cbc:TaxExemptionReasonCode>
                                        <cac:TaxScheme>
                                                <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
                                                <cbc:Name>GRA</cbc:Name>
                                                <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                                        </cac:TaxScheme>
                                </cac:TaxCategory>';
            } else if ($cabecera["TOTAL_INAFECTA"] > 0) {
                $xmlCPE .= '  <cac:TaxCategory>
                                        <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
                                        <cbc:Percent>' . $cabecera["POR_IGV"] . '</cbc:Percent>
					<cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="SUNAT:Codigo de Tipo de Afectación del IGV" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">' . $detalle[$i]["txtCOD_TIPO_OPERACION"] . '</cbc:TaxExemptionReasonCode>
                                        <cac:TaxScheme>
                                                <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                                                <cbc:Name>INA</cbc:Name>
                                                <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                                        </cac:TaxScheme>
                                </cac:TaxCategory>';
            } else if ($detalle[$i]["txtCOD_TIPO_OPERACION"] == 40) {//exportacion
                $xmlCPE .= '  <cac:TaxCategory>
                                        <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">G</cbc:ID>
                                        <cbc:Percent>' . $cabecera["POR_IGV"] . '</cbc:Percent>
					<cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="SUNAT:Codigo de Tipo de Afectación del IGV" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">' . $detalle[$i]["txtCOD_TIPO_OPERACION"] . '</cbc:TaxExemptionReasonCode>
                                        <cac:TaxScheme>
                                                <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9995</cbc:ID>
                                                <cbc:Name>EXP</cbc:Name>
                                                <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                                        </cac:TaxScheme>
                                </cac:TaxCategory>';
            } else {
                $xmlCPE .= '<cac:TaxCategory>
                                    <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
                                    <cbc:Percent>' . $cabecera["POR_IGV"] . '</cbc:Percent>
                                    <cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="Afectacion del IGV" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">' . $detalle[$i]["txtCOD_TIPO_OPERACION"] . '</cbc:TaxExemptionReasonCode>
                                    <cac:TaxScheme>
                                            <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">1000</cbc:ID>
                                            <cbc:Name>IGV</cbc:Name>
                                            <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                                    </cac:TaxScheme>
                            </cac:TaxCategory>';
            }

            $xmlCPE .= '</cac:TaxSubtotal>';

            if ($detalle[$i]["txtICBPER_DET"] > 0) {//impuesto bolsa
                $xmlCPE .= '  
                    <cac:TaxSubtotal>
                        <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtICBPER_DET"] . '</cbc:TaxAmount>
                        <cbc:BaseUnitMeasure unitCode="' . $detalle[$i]["txtUNIDAD_MEDIDA_DET"] . '">' . number_format($detalle[$i]["txtCANTIDAD_DET"], 0) . '</cbc:BaseUnitMeasure>
                        <cbc:PerUnitAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["POR_ICBPER"] . '</cbc:PerUnitAmount>
                        <cac:TaxCategory>                            
                            <cac:TaxScheme>
                                <cbc:ID>7152</cbc:ID>
                                <cbc:Name>ICBPER</cbc:Name>
                                <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                            </cac:TaxScheme>
                        </cac:TaxCategory>
                    </cac:TaxSubtotal>    ';
            }

            $xmlCPE .= '</cac:TaxTotal>
		<cac:Item>
			<cbc:Description><![CDATA[' . $validacion->replace_invalid_caracters((isset($detalle[$i]["txtDESCRIPCION_DET"])) ? $detalle[$i]["txtDESCRIPCION_DET"] : "") . ']]></cbc:Description>
			<cac:SellersItemIdentification>
				<cbc:ID><![CDATA[' . $validacion->replace_invalid_caracters((isset($detalle[$i]["txtCODIGO_DET"])) ? $detalle[$i]["txtCODIGO_DET"] : "") . ']]></cbc:ID>
			</cac:SellersItemIdentification>';
            if (isset($detalle[$i]["CODIGO_PRODUCTO_SUNAT"]) && !empty($detalle[$i]["CODIGO_PRODUCTO_SUNAT"])) {
                $xmlCPE .= '<cac:CommodityClassification>
                                <cbc:ItemClassificationCode listID="UNSPSC" listAgencyName="GS1 US" listName="Item Classification">' . $detalle[$i]["CODIGO_PRODUCTO_SUNAT"] . '</cbc:ItemClassificationCode>
                            </cac:CommodityClassification>';
            }
            $xmlCPE .= '
		</cac:Item>
		<cac:Price>
			<cbc:PriceAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtPRECIO_SIN_IGV_DET"] . '</cbc:PriceAmount>
		</cac:Price>
	</cac:InvoiceLine>';
        }

        $xmlCPE = $xmlCPE . '</Invoice>';
        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.XML');
        $resp['respuesta'] = 'OK';
        $resp['url_xml'] = $ruta . '.XML';
        return $resp;
    }

    public function crear_xml_nota_credito($cabecera, $detalle, $ruta) {
        $validacion = new validaciondedatos();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        //$doc->encoding = 'ISO-8859-1';
        $doc->encoding = 'utf-8';

        $xmlCPE = '<?xml version="1.0" encoding="UTF-8"?>
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent>
            </ext:ExtensionContent>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>2.0</cbc:CustomizationID>
    <cbc:ID>' . $cabecera["NRO_COMPROBANTE"] . '</cbc:ID>
    <cbc:IssueDate>' . $cabecera["FECHA_DOCUMENTO"] . '</cbc:IssueDate>
    <cbc:IssueTime>00:00:00</cbc:IssueTime>
    <cbc:DocumentCurrencyCode>' . $cabecera["COD_MONEDA"] . '</cbc:DocumentCurrencyCode>
    <cac:DiscrepancyResponse>
        <cbc:ReferenceID>' . $cabecera["NRO_DOCUMENTO_MODIFICA"] . '</cbc:ReferenceID>
        <cbc:ResponseCode>' . $cabecera["COD_TIPO_MOTIVO"] . '</cbc:ResponseCode>
        <cbc:Description><![CDATA[' . $cabecera["DESCRIPCION_MOTIVO"] . ']]></cbc:Description>
    </cac:DiscrepancyResponse>
    <cac:BillingReference>
        <cac:InvoiceDocumentReference>
            <cbc:ID>' . $cabecera["NRO_DOCUMENTO_MODIFICA"] . '</cbc:ID>
            <cbc:DocumentTypeCode>' . $cabecera["TIPO_COMPROBANTE_MODIFICA"] . '</cbc:DocumentTypeCode>
        </cac:InvoiceDocumentReference>
    </cac:BillingReference>
    <cac:Signature>
        <cbc:ID>IDSignST</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[' . $cabecera["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#SignatureSP</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[' . $cabecera["NOMBRE_COMERCIAL_EMPRESA"] . ']]></cbc:Name>
            </cac:PartyName>
            <cac:PartyLegalEntity>
<cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:RegistrationName>
                <cac:RegistrationAddress>
                    <cbc:AddressTypeCode>0001</cbc:AddressTypeCode>
                </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_CLIENTE"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_CLIENTE"] . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyLegalEntity>
<cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_CLIENTE"] . ']]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>
    
    <cac:TaxTotal>
            <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . ($cabecera["TOTAL_IGV"] + $cabecera["TOTAL_BOLSA"]) . '</cbc:TaxAmount>';
    if ($cabecera["TOTAL_IGV"] > 0) {
        $xmlCPE .= '
            <cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_GRAVADAS"] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_IGV"] . '</cbc:TaxAmount>
                    <cac:TaxCategory>
                            <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
                            <cac:TaxScheme>
                                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">1000</cbc:ID>
                                    <cbc:Name>IGV</cbc:Name>
                                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                            </cac:TaxScheme>
                    </cac:TaxCategory>
            </cac:TaxSubtotal>';
    }
    
    if ($cabecera["TOTAL_BOLSA"] > 0) {
        $xmlCPE .= '
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_BOLSA"] . '</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>7152</cbc:ID>
                        <cbc:Name>ICBPER</cbc:Name>
                        <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>';
    }
    
    if ($cabecera["TOTAL_ISC"] > 0) {
        $xmlCPE .=
                '<cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_ISC"] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_ISC"] . '</cbc:TaxAmount>
                    <cac:TaxCategory>
                            <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
                            <cac:TaxScheme>
                                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">2000</cbc:ID>
                                    <cbc:Name>ISC</cbc:Name>
                                    <cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
                            </cac:TaxScheme>
                    </cac:TaxCategory>
            </cac:TaxSubtotal>';
    }
    //CAMPO NUEVO
    if ($cabecera["TOTAL_EXPORTACION"] > 0) {
        $xmlCPE .=
                '<cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_EXPORTACION"] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
                    <cac:TaxCategory>
                            <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">G</cbc:ID>
                            <cac:TaxScheme>
                                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9995</cbc:ID>
                                    <cbc:Name>EXP</cbc:Name>
                                    <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                            </cac:TaxScheme>
                    </cac:TaxCategory>
            </cac:TaxSubtotal>';
    }
    
    if ($cabecera["TOTAL_GRATUITAS"] > 0) {
        $xmlCPE .=
                '<cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_GRATUITAS"] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
                    <cac:TaxCategory>
                            <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">Z</cbc:ID>
                            <cac:TaxScheme>
                                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
                                    <cbc:Name>GRA</cbc:Name>
                                    <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                            </cac:TaxScheme>
                    </cac:TaxCategory>
            </cac:TaxSubtotal>';
    }
    
    if ($cabecera["TOTAL_EXONERADAS"] > 0) {
        $xmlCPE .=
                '<cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_EXONERADAS"] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
                    <cac:TaxCategory>
                            <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                            <cac:TaxScheme>
                                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                                    <cbc:Name>EXO</cbc:Name>
                                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                            </cac:TaxScheme>
                    </cac:TaxCategory>
            </cac:TaxSubtotal>';
    }
    
    if ($cabecera["TOTAL_INAFECTA"] > 0) {
        $xmlCPE .=
                '<cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_INAFECTA"] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
                    <cac:TaxCategory>
                            <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
                            <cac:TaxScheme>
                                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                                    <cbc:Name>INA</cbc:Name>
                                    <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                            </cac:TaxScheme>
                    </cac:TaxCategory>
            </cac:TaxSubtotal>';
    }
    
    if ($cabecera["TOTAL_OTR_IMP"] > 0) {
        $xmlCPE .=
                '<cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_OTR_IMP"] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_OTR_IMP"] . '</cbc:TaxAmount>
                    <cac:TaxCategory>
                            <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
                            <cac:TaxScheme>
                                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9999</cbc:ID>
                                    <cbc:Name>OTR</cbc:Name>
                                    <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                            </cac:TaxScheme>
                    </cac:TaxCategory>
            </cac:TaxSubtotal>';
    }
        
    $xmlCPE .= '</cac:TaxTotal>';
    
    $xmlCPE .= '<cac:LegalMonetaryTotal>
        <cbc:PayableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL"] . '</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>';


        for ($i = 0; $i < count($detalle); $i++) {

            $xmlCPE .= '
            <cac:CreditNoteLine>
                <cbc:ID>' . $detalle[$i]["txtITEM"] . '</cbc:ID>
                <cbc:CreditedQuantity unitCode="' . $detalle[$i]["txtUNIDAD_MEDIDA_DET"] . '">' . $detalle[$i]["txtCANTIDAD_DET"] . '</cbc:CreditedQuantity>
                <cbc:LineExtensionAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIMPORTE_DET"] . '</cbc:LineExtensionAmount>
                <cac:PricingReference>
                    <cac:AlternativeConditionPrice>
                        <cbc:PriceAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtPRECIO_DET"] . '</cbc:PriceAmount>
                        <cbc:PriceTypeCode>' . $detalle[$i]["txtPRECIO_TIPO_CODIGO"] . '</cbc:PriceTypeCode>
                    </cac:AlternativeConditionPrice>
                </cac:PricingReference>
                <cac:TaxTotal>
                    <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIGV"] . '</cbc:TaxAmount>
                        <cac:TaxSubtotal>
                            <cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIMPORTE_DET"] . '</cbc:TaxableAmount>
                            <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIGV"] . '</cbc:TaxAmount>';
            //exonerada
            if ($detalle[$i]["txtCOD_TIPO_OPERACION"] == 20) {
                $xmlCPE .= '
                            <cac:TaxCategory>
                                <cbc:Percent>' . $cabecera["POR_IGV"] . '</cbc:Percent>
                                <cbc:TaxExemptionReasonCode>' . $detalle[$i]["txtCOD_TIPO_OPERACION"] . '</cbc:TaxExemptionReasonCode>
                                <cac:TaxScheme>
                                    <cbc:ID>9997</cbc:ID>
                                    <cbc:Name>EXO</cbc:Name>
                                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                                </cac:TaxScheme>
                            </cac:TaxCategory>';
            } else if ($detalle[$i]["txtCOD_TIPO_OPERACION"] == 30) {
                $xmlCPE .= '
                            <cac:TaxCategory>
                                <cbc:Percent>' . $cabecera["POR_IGV"] . '</cbc:Percent>
                                <cbc:TaxExemptionReasonCode>' . $detalle[$i]["txtCOD_TIPO_OPERACION"] . '</cbc:TaxExemptionReasonCode>
                                <cac:TaxScheme>
                                    <cbc:ID>9998</cbc:ID>
                                    <cbc:Name>INA</cbc:Name>
                                    <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                                </cac:TaxScheme>
                            </cac:TaxCategory>';
            } else if ($detalle[$i]["txtCOD_TIPO_OPERACION"] == 10) {
                $xmlCPE .= '
                            <cac:TaxCategory>
                                <cbc:Percent>' . $cabecera["POR_IGV"] . '</cbc:Percent>
                                <cbc:TaxExemptionReasonCode>' . $detalle[$i]["txtCOD_TIPO_OPERACION"] . '</cbc:TaxExemptionReasonCode>
                                <cac:TaxScheme>
                                    <cbc:ID>1000</cbc:ID>
                                    <cbc:Name>IGV</cbc:Name>
                                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                                </cac:TaxScheme>
                            </cac:TaxCategory>';
            }

            $xmlCPE .= '
                        </cac:TaxSubtotal>
               ';
            
            if($detalle[$i]["txtICBPER_DET"] > 0):
                $xmlCPE .= '<cac:TaxSubtotal>
                    <cbc:TaxAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtICBPER_DET"].'</cbc:TaxAmount>
                    <cbc:BaseUnitMeasure unitCode="' . $detalle[$i]["txtUNIDAD_MEDIDA_DET"] . '">' . number_format($detalle[$i]["txtCANTIDAD_DET"], 0) . '</cbc:BaseUnitMeasure>
                    <cbc:PerUnitAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["POR_ICBPER"] . '</cbc:PerUnitAmount>                                           
                    <cac:TaxCategory>
                        <cac:TaxScheme>
                            <cbc:ID>7152</cbc:ID>
                            <cbc:Name>ICBPER</cbc:Name>
                            <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>';
            endif;
            
            
            $xmlCPE .= ' </cac:TaxTotal>
                <cac:Item>
                    <cbc:Description><![CDATA[' . $validacion->replace_invalid_caracters((isset($detalle[$i]["txtDESCRIPCION_DET"])) ? $detalle[$i]["txtDESCRIPCION_DET"] : "") . ']]></cbc:Description>
                    <cac:SellersItemIdentification>
                        <cbc:ID><![CDATA[' . $validacion->replace_invalid_caracters((isset($detalle[$i]["txtCODIGO_DET"])) ? $detalle[$i]["txtCODIGO_DET"] : "") . ']]></cbc:ID>
                    </cac:SellersItemIdentification>
                </cac:Item>
                <cac:Price>
                    <cbc:PriceAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtPRECIO_DET"] . '</cbc:PriceAmount>
                </cac:Price>
            </cac:CreditNoteLine>';
        }

        $xmlCPE = $xmlCPE . '</CreditNote>';
        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.XML');

        $resp['respuesta'] = 'OK';
        $resp['url_xml'] = $ruta . '.XML';
        return $resp;
    }

    public function crear_xml_nota_debito($cabecera, $detalle, $ruta) {
        $validacion = new validaciondedatos();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        //$doc->encoding = 'ISO-8859-1';
        $doc->encoding = 'utf-8';

        $xmlCPE = '<?xml version="1.0" encoding="UTF-8"?>
<DebitNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent>
            </ext:ExtensionContent>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>2.0</cbc:CustomizationID>
    <cbc:ID>' . $cabecera["NRO_COMPROBANTE"] . '</cbc:ID>
    <cbc:IssueDate>' . $cabecera["FECHA_DOCUMENTO"] . '</cbc:IssueDate>
    <cbc:IssueTime>00:00:00</cbc:IssueTime>
    <cbc:DocumentCurrencyCode>' . $cabecera["COD_MONEDA"] . '</cbc:DocumentCurrencyCode>
    <cac:DiscrepancyResponse>
        <cbc:ReferenceID>' . $cabecera["NRO_DOCUMENTO_MODIFICA"] . '</cbc:ReferenceID>
        <cbc:ResponseCode>' . $cabecera["COD_TIPO_MOTIVO"] . '</cbc:ResponseCode>
        <cbc:Description><![CDATA[' . $cabecera["DESCRIPCION_MOTIVO"] . ']]></cbc:Description>
    </cac:DiscrepancyResponse>
    <cac:BillingReference>
        <cac:InvoiceDocumentReference>
            <cbc:ID>' . $cabecera["NRO_DOCUMENTO_MODIFICA"] . '</cbc:ID>
            <cbc:DocumentTypeCode>' . $cabecera["TIPO_COMPROBANTE_MODIFICA"] . '</cbc:DocumentTypeCode>
        </cac:InvoiceDocumentReference>
    </cac:BillingReference>
    <cac:Signature>
        <cbc:ID>IDSignST</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[' . $cabecera["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#SignatureSP</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[' . $cabecera["NOMBRE_COMERCIAL_EMPRESA"] . ']]></cbc:Name>
            </cac:PartyName>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:RegistrationName>
                <cac:RegistrationAddress>
                    <cbc:AddressTypeCode>0001</cbc:AddressTypeCode>
                </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_CLIENTE"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_CLIENTE"] . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyLegalEntity>
<cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_CLIENTE"] . ']]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_IGV"] . '</cbc:TaxAmount>
        <cac:TaxSubtotal>
<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_GRAVADAS"] . '</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_IGV"] . '</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">1000</cbc:ID>
                    <cbc:Name>IGV</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    <cac:RequestedMonetaryTotal>
<cbc:PayableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL"] . '</cbc:PayableAmount>
    </cac:RequestedMonetaryTotal>';

        for ($i = 0; $i < count($detalle); $i++) {
            $xmlCPE = $xmlCPE . '
    <cac:DebitNoteLine>
        <cbc:ID>' . $detalle[$i]["txtITEM"] . '</cbc:ID>
<cbc:DebitedQuantity unitCode="' . $detalle[$i]["txtUNIDAD_MEDIDA_DET"] . '">' . $detalle[$i]["txtCANTIDAD_DET"] . '</cbc:DebitedQuantity>
<cbc:LineExtensionAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIMPORTE_DET"] . '</cbc:LineExtensionAmount>
        <cac:PricingReference>
            <cac:AlternativeConditionPrice>
<cbc:PriceAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtPRECIO_DET"] . '</cbc:PriceAmount>
<cbc:PriceTypeCode>' . $detalle[$i]["txtPRECIO_TIPO_CODIGO"] . '</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
        </cac:PricingReference>
        <cac:TaxTotal>		
<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIGV"] . '</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIMPORTE_DET"] . '</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIGV"] . '</cbc:TaxAmount>';

            if ($detalle[$i]["txtCOD_TIPO_OPERACION"] == 30) {
                $xmlCPE .= '
                            <cac:TaxCategory>
                                <cbc:Percent>' . $cabecera["POR_IGV"] . '</cbc:Percent>
                                <cbc:TaxExemptionReasonCode>' . $detalle[$i]["txtCOD_TIPO_OPERACION"] . '</cbc:TaxExemptionReasonCode>
                                <cac:TaxScheme>
                                    <cbc:ID>9998</cbc:ID>
                                    <cbc:Name>INA</cbc:Name>
                                    <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                                </cac:TaxScheme>
                            </cac:TaxCategory>';
            } else if ($detalle[$i]["txtCOD_TIPO_OPERACION"] == 10) {
                $xmlCPE .= '
                <cac:TaxCategory>
                    <cbc:Percent>' . $cabecera["POR_IGV"] . '</cbc:Percent>
                    <cbc:TaxExemptionReasonCode>' . $detalle[$i]["txtCOD_TIPO_OPERACION"] . '</cbc:TaxExemptionReasonCode>
                    <cac:TaxScheme>
                        <cbc:ID>1000</cbc:ID>
                        <cbc:Name>IGV</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>';
            }

            $xmlCPE .= '
            </cac:TaxSubtotal>
        </cac:TaxTotal>
		
<cac:Item>
<cbc:Description><![CDATA[' . $validacion->replace_invalid_caracters((isset($detalle[$i]["txtDESCRIPCION_DET"])) ? $detalle[$i]["txtDESCRIPCION_DET"] : "") . ']]></cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID><![CDATA[' . $validacion->replace_invalid_caracters((isset($detalle[$i]["txtCODIGO_DET"])) ? $detalle[$i]["txtCODIGO_DET"] : "") . ']]></cbc:ID>
            </cac:SellersItemIdentification>
        </cac:Item>
<cac:Price>
<cbc:PriceAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtPRECIO_DET"] . '</cbc:PriceAmount>
</cac:Price>
    </cac:DebitNoteLine>';
        }

        $xmlCPE = $xmlCPE . '</DebitNote>';

        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.XML');

        $resp['respuesta'] = 'OK';
        $resp['url_xml'] = $ruta . '.XML';
        return $resp;
    }

    public function crear_xml_resumen_documentos($cabecera, $detalle, $ruta) {
        $validacion = new validaciondedatos();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'ISO-8859-1';
        $xmlCPE = '<?xml version="1.0" encoding="iso-8859-1" standalone="no"?>
        <SummaryDocuments 
        xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:SummaryDocuments-1" 
        xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
        xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
        xmlns:ds="http://www.w3.org/2000/09/xmldsig#" 
        xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" 
        xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1"
        xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" 
        xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2">
        <ext:UBLExtensions>
            <ext:UBLExtension>
                            <ext:ExtensionContent>
                </ext:ExtensionContent>
            </ext:UBLExtension>
        </ext:UBLExtensions>
        <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
        <cbc:CustomizationID>1.1</cbc:CustomizationID>
        <cbc:ID>' . $cabecera["CODIGO"] . '-' . $cabecera["SERIE"] . '-' . $cabecera["SECUENCIA"] . '</cbc:ID>
        <cbc:ReferenceDate>' . $cabecera["FECHA_REFERENCIA"] . '</cbc:ReferenceDate>
        <cbc:IssueDate>' . $cabecera["FECHA_DOCUMENTO"] . '</cbc:IssueDate>
        <cac:Signature>
            <cbc:ID>' . $cabecera["CODIGO"] . '-' . $cabecera["SERIE"] . '-' . $cabecera["SECUENCIA"] . '</cbc:ID>
            <cac:SignatoryParty>
                <cac:PartyIdentification>
                    <cbc:ID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
                </cac:PartyIdentification>
                <cac:PartyName>
                    <cbc:Name>' . $cabecera["RAZON_SOCIAL_EMPRESA"] . '</cbc:Name>
                </cac:PartyName>
            </cac:SignatoryParty>
            <cac:DigitalSignatureAttachment>
                <cac:ExternalReference>
                    <cbc:URI>' . $cabecera["CODIGO"] . '-' . $cabecera["SERIE"] . '-' . $cabecera["SECUENCIA"] . '</cbc:URI>
                </cac:ExternalReference>
            </cac:DigitalSignatureAttachment>
        </cac:Signature>
        <cac:AccountingSupplierParty>
            <cbc:CustomerAssignedAccountID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:CustomerAssignedAccountID>
            <cbc:AdditionalAccountID>' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '</cbc:AdditionalAccountID>
            <cac:Party>
                <cac:PartyLegalEntity>
                    <cbc:RegistrationName>' . $cabecera["RAZON_SOCIAL_EMPRESA"] . '</cbc:RegistrationName>
                </cac:PartyLegalEntity>
            </cac:Party>
        </cac:AccountingSupplierParty>';
        for ($i = 0; $i < count($detalle); $i++) {
            $xmlCPE = $xmlCPE . '<sac:SummaryDocumentsLine>
            <cbc:LineID>' . $detalle[$i]["ITEM"] . '</cbc:LineID>
            <cbc:DocumentTypeCode>' . $detalle[$i]["TIPO_COMPROBANTE"] . '</cbc:DocumentTypeCode>
            <cbc:ID>' . $detalle[$i]["NRO_COMPROBANTE"] . '</cbc:ID>
            <cac:AccountingCustomerParty>
                <cbc:CustomerAssignedAccountID>' . $detalle[$i]["NRO_DOCUMENTO"] . '</cbc:CustomerAssignedAccountID>
                <cbc:AdditionalAccountID>' . $detalle[$i]["TIPO_DOCUMENTO"] . '</cbc:AdditionalAccountID>
            </cac:AccountingCustomerParty>';
            if ($detalle[$i]["TIPO_COMPROBANTE"] == "07" || $detalle[$i]["TIPO_COMPROBANTE"] == "08") {
                $xmlCPE = $xmlCPE . '<cac:BillingReference>
                <cac:InvoiceDocumentReference>
                    <cbc:ID>' . $detalle[$i]["NRO_COMPROBANTE_REF"] . '</cbc:ID>
                    <cbc:DocumentTypeCode>' . $detalle[$i]["TIPO_COMPROBANTE_REF"] . '</cbc:DocumentTypeCode>
                </cac:InvoiceDocumentReference>
            </cac:BillingReference>';
            }
            $xmlCPE = $xmlCPE . '<cac:Status>
                <cbc:ConditionCode>' . $detalle[$i]["STATUS"] . '</cbc:ConditionCode>
            </cac:Status>                
            <sac:TotalAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["TOTAL"] . '</sac:TotalAmount>';                       
        
            if (($detalle[$i]["GRAVADA"]) > 0) {
                $xmlCPE = $xmlCPE . '<sac:BillingPayment>
                <cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["GRAVADA"] . '</cbc:PaidAmount>
                <cbc:InstructionID>01</cbc:InstructionID>
            </sac:BillingPayment>';
            }

            if (($detalle[$i]["EXONERADO"]) > 0) {
                $xmlCPE = $xmlCPE . '<sac:BillingPayment>
                <cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["EXONERADO"] . '</cbc:PaidAmount>
                <cbc:InstructionID>02</cbc:InstructionID>
            </sac:BillingPayment>';
            }

            if (($detalle[$i]["INAFECTO"]) > 0) {
                $xmlCPE = $xmlCPE . '<sac:BillingPayment>
                <cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["INAFECTO"] . '</cbc:PaidAmount>
                <cbc:InstructionID>03</cbc:InstructionID>
            </sac:BillingPayment>';
            }

            if (($detalle[$i]["EXPORTACION"]) > 0) {
                $xmlCPE = $xmlCPE . '<sac:BillingPayment>
                <cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["EXPORTACION"] . '</cbc:PaidAmount>
                <cbc:InstructionID>04</cbc:InstructionID>
            </sac:BillingPayment>';
            }

            if (($detalle[$i]["GRATUITAS"]) > 0) {
                $xmlCPE = $xmlCPE . '<sac:BillingPayment>
                <cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["GRATUITAS"] . '</cbc:PaidAmount>
                <cbc:InstructionID>05</cbc:InstructionID>
            </sac:BillingPayment>';
            }

            if (($detalle[$i]["MONTO_CARGO_X_ASIG"]) > 0) {
                $xmlCPE = $xmlCPE . '<cac:AllowanceCharge>';
                if ($detalle[$i]["CARGO_X_ASIGNACION"] == 1) {
                    $xmlCPE = $xmlCPE . '<cbc:ChargeIndicator>true</cbc:ChargeIndicator>';
                } else {
                    $xmlCPE = $xmlCPE . '<cbc:ChargeIndicator>false</cbc:ChargeIndicator>';
                }
                $xmlCPE = $xmlCPE . '<cbc:Amount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["MONTO_CARGO_X_ASIG"] . '</cbc:Amount>
                        </cac:AllowanceCharge>';
            }
            if (($detalle[$i]["ISC"]) > 0) {
                $xmlCPE = $xmlCPE . '<cac:TaxTotal>
                <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["ISC"] . '</cbc:TaxAmount>
                <cac:TaxSubtotal>
                    <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["ISC"] . '</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cac:TaxScheme>
                            <cbc:ID>2000</cbc:ID>
                            <cbc:Name>ISC</cbc:Name>
                            <cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>
            </cac:TaxTotal>';
            }
            
            #DESCUENTO GLOBAL EN RESUMEN
            /*if (isset($detalle[$i]["DSCTO_GLOBAL"])) {
                if($detalle[$i]["DSCTO_GLOBAL"] > 0){
                    $xmlCPE .= '<cac:AllowanceCharge>
                                     <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
                                     <cbc:AllowanceChargeReasonCode listName="Cargo/descuento" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo53">02</cbc:AllowanceChargeReasonCode>
                                     <cbc:MultiplierFactorNumeric>'.$detalle[$i]["DSCTO_PORCENTAJE"].'</cbc:MultiplierFactorNumeric>
                                     <cbc:Amount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">'.$detalle[$i]["DSCTO_GLOBAL"].'</cbc:Amount>
                                     <cbc:BaseAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">'.$detalle[$i]["DSCTO_SUBTOTAL"].'</cbc:BaseAmount>
                             </cac:AllowanceCharge>';
                }
            }*/

            $xmlCPE = $xmlCPE . '<cac:TaxTotal>
                <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . ($detalle[$i]["IGV"] ) . '</cbc:TaxAmount>
                <cac:TaxSubtotal>
                    <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["IGV"] . '</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cac:TaxScheme>
                            <cbc:ID>1000</cbc:ID>
                            <cbc:Name>IGV</cbc:Name>
                            <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>';

            $xmlCPE = $xmlCPE . '</cac:TaxTotal>';

            if (($detalle[$i]["OTROS"]) > 0) {
                $xmlCPE = $xmlCPE . '<cac:TaxTotal>
                <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["OTROS"] . '</cbc:TaxAmount>
                <cac:TaxSubtotal>
                    <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["OTROS"] . '</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cac:TaxScheme>
                            <cbc:ID>9999</cbc:ID>
                            <cbc:Name>OTROS</cbc:Name>
                            <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>
            </cac:TaxTotal>';
            }

            if ($detalle[$i]["TOTAL_BOLSA"] > 0) {
                $xmlCPE = $xmlCPE . '
                    <cac:TaxTotal>
                        <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["TOTAL_BOLSA"] . '</cbc:TaxAmount>
                        <cac:TaxSubtotal>
                            <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["TOTAL_BOLSA"] . '</cbc:TaxAmount>
                            <cac:TaxCategory>
                                <cac:TaxScheme>
                                    <cbc:ID>7152</cbc:ID>
                                    <cbc:Name>ICBPER</cbc:Name>
                                    <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                                </cac:TaxScheme>
                            </cac:TaxCategory>
                        </cac:TaxSubtotal>
                    </cac:TaxTotal>';
            }

            $xmlCPE = $xmlCPE . '</sac:SummaryDocumentsLine>';
        }
        $xmlCPE = $xmlCPE . '</SummaryDocuments>';                

        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.XML');
        $resp['respuesta'] = 'OK';
        $resp['url_xml'] = $ruta . '.XML';
        return $resp;
    }

    public function enviar_documento($ruc, $usuario_sol, $pass_sol, $ruta_archivo, $ruta_archivo_cdr, $archivo, $ruta_ws) {
        //=================ZIPEAR ================
        $zip = new ZipArchive();
        $filenameXMLCPE = $ruta_archivo . '.ZIP';

        if ($zip->open($filenameXMLCPE, ZIPARCHIVE::CREATE) === true) {
            $zip->addFile($ruta_archivo . '.XML', $archivo . '.XML'); //ORIGEN, DESTINO
            $zip->close();
        }

        //===================ENVIO FACTURACION=====================
        $soapUrl = $ruta_ws;
        // xml post structure
        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
        xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
        xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken>
                    <wsse:Username>' . $ruc . $usuario_sol . '</wsse:Username>
                    <wsse:Password>' . $pass_sol . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
            <ser:sendBill>
                <fileName>' . $archivo . '.ZIP</fileName>
                <contentFile>' . base64_encode(file_get_contents($ruta_archivo . '.ZIP')) . '</contentFile>
            </ser:sendBill>
        </soapenv:Body>
        </soapenv:Envelope>';

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-length: " . strlen($xml_post_string),
        );

        $url = $soapUrl;

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode == 200) {
            $doc = new DOMDocument();
            $doc->loadXML($response);

            //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
            if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {
                $xmlCDR = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
                file_put_contents($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP', base64_decode($xmlCDR));

                //extraemos archivo zip a xml
                $zip = new ZipArchive;
                if ($zip->open($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP') === TRUE) {
                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $archivo . '.XML');
                    $zip->close();
                }

                //eliminamos los archivos Zipeados
                unlink($ruta_archivo . '.ZIP');
                unlink($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP');

                //=============hash CDR=================
                $doc_cdr = new DOMDocument();
                $doc_cdr->load($ruta_archivo_cdr . 'R-' . $archivo . '.XML');
                $resp['respuesta'] = 'OK';
                $resp['cod_sunat'] = $doc_cdr->getElementsByTagName('ResponseCode')->item(0)->nodeValue;
                $resp['msj_sunat'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                $resp['hash_cdr'] = $doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue;
            } else {
                $resp['respuesta'] = 'error';
                $resp['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $resp['msj_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $resp['hash_cdr'] = "";
            }
        } else {
            //echo "no responde web";
            $resp['respuesta'] = 'error';
            $resp['cod_sunat'] = "0000";
            $resp['msj_sunat'] = "Código de Error: 0000 <br /> Web Service de Prueba SUNAT - Fuera de Servicio: <a href='https://e-beta.sunat.gob.pe:443/ol-ti-itcpfegem-beta/billService' target='_blank'>https://e-beta.sunat.gob.pe:443/ol-ti-itcpfegem-beta/billService</a>, Para validar la información llamar al: *4000 (Desde Claro, Entel y Movistar) - SUNAT";
            $resp['hash_cdr'] = "";
        }
        return $resp;
    }

    //require_once('decode_64.php');
    public function enviar_documento_prueba($ruc, $usuario_sol, $pass_sol, $ruta_archivo, $ruta_archivo_cdr, $archivo, $ruta_ws) {
        try {
            //=================ZIPEAR ================
            $zip = new ZipArchive();
            $filenameXMLCPE = $ruta_archivo . '.ZIP';

            if ($zip->open($filenameXMLCPE, ZIPARCHIVE::CREATE) === true) {
                $zip->addFile($ruta_archivo . '.XML', $archivo . '.XML'); //ORIGEN, DESTINO
                $zip->close();
            }

            //===================ENVIO FACTURACION=====================
            $soapUrl = $ruta_ws; //"https://e-beta.sunat.gob.pe:443/ol-ti-itcpfegem-beta/billService"; // asmx URL of WSDL
            $soapUser = "";  //  username
            $soapPassword = ""; // password
            // xml post structure
            $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
    xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
    <soapenv:Header>
        <wsse:Security>
            <wsse:UsernameToken>
                <wsse:Username>' . $ruc . $usuario_sol . '</wsse:Username>
                <wsse:Password>' . $pass_sol . '</wsse:Password>
            </wsse:UsernameToken>
        </wsse:Security>
    </soapenv:Header>
    <soapenv:Body>
        <ser:sendBill>
            <fileName>' . $archivo . '.ZIP</fileName>
            <contentFile>' . base64_encode(file_get_contents($ruta_archivo . '.ZIP')) . '</contentFile>
        </ser:sendBill>
    </soapenv:Body>
    </soapenv:Envelope>';

            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: ",
                "Content-length: " . strlen($xml_post_string),
            ); //SOAPAction: your op URL

            $url = $soapUrl;

            //echo $xml_post_string;
            // PHP cURL  for https connection with auth
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // converting
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            //echo $httpcode;
            //echo $response;
            //if ($httpcode == 200) {//======LA PAGINA SI RESPONDE
            //echo $httpcode.'----'.$response;
            //convertimos de base 64 a archivo fisico
            $doc = new DOMDocument();
            $doc->loadXML($response);



            //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
            if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {
                $xmlCDR = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
                file_put_contents($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP', base64_decode($xmlCDR));

                //extraemos archivo zip a xml
                $zip = new ZipArchive;
                if ($zip->open($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP') === TRUE) {
                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $archivo . '.XML');
                    $zip->close();
                }

                //eliminamos los archivos Zipeados
                unlink($ruta_archivo . '.ZIP');
                unlink($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP');

                //=============hash CDR=================
                $doc_cdr = new DOMDocument();
                $doc_cdr->load(dirname(__FILE__) . '/' . $ruta_archivo_cdr . 'R-' . $archivo . '.XML');

                $mensaje['cod_sunat'] = $doc_cdr->getElementsByTagName('ResponseCode')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = $doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue;
            } else {
                $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc->getElementsByTagName('message')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = "";
            }
        } catch (Exception $e) {
            $mensaje['cod_sunat'] = "0000";
            $mensaje['msj_sunat'] = "SUNAT ESTA FUERA SERVICIO: " . $e->getMessage();
            $mensaje['hash_cdr'] = "";
        }
        //print_r($mensaje); 
        return $mensaje;
        //$xmlCDR = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
    }

    public function crear_xml_guia_remision($cabecera, $detalle, $ruta) {
        $validacion = new validaciondedatos();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'ISO-8859-1';
        $xmlCPE = '<?xml version="1.0" encoding="iso-8859-1"?>
    <DespatchAdvice xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2">
        <ext:UBLExtensions>
            <ext:UBLExtension>
                <ext:ExtensionContent>
                </ext:ExtensionContent>
            </ext:UBLExtension>
        </ext:UBLExtensions>
        <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
        <cbc:CustomizationID>1.0</cbc:CustomizationID>
    <cbc:ID>' . $cabecera["SERIE"] . '-' . $cabecera["SECUENCIA"] . '</cbc:ID>
    <cbc:IssueDate>' . $cabecera["FECHA_DOCUMENTO"] . '</cbc:IssueDate>
    <cbc:DespatchAdviceTypeCode>' . $cabecera["CODIGO"] . '</cbc:DespatchAdviceTypeCode>
    <cbc:Note>' . $cabecera["NOTA"] . '</cbc:Note>
    
    <cac:DespatchSupplierParty>
            <cbc:CustomerAssignedAccountID schemeID="' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '">' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:CustomerAssignedAccountID>
            <cac:Party>
                <cac:PartyLegalEntity>
    <cbc:RegistrationName><![CDATA[' . $validacion->replace_invalid_caracters($cabecera["RAZON_SOCIAL_EMPRESA"]) . ']]></cbc:RegistrationName>
                </cac:PartyLegalEntity>
            </cac:Party>
        </cac:DespatchSupplierParty>
    
    <cac:DeliveryCustomerParty>
    <cbc:CustomerAssignedAccountID schemeID="' . $cabecera["TIPO_DOCUMENTO_CLIENTE"] . '">' . $cabecera["NRO_DOCUMENTO_CLIENTE"] . '</cbc:CustomerAssignedAccountID>
            <cac:Party>
                <cac:PartyLegalEntity>
    <cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_CLIENTE"] . ']]></cbc:RegistrationName>
                </cac:PartyLegalEntity>
            </cac:Party>
        </cac:DeliveryCustomerParty>
    
    <cac:Shipment>
            <cbc:ID>1</cbc:ID>
            <cbc:HandlingCode>' . $cabecera["CODMOTIVO_TRASLADO"] . '</cbc:HandlingCode>
            <cbc:Information>' . $cabecera["MOTIVO_TRASLADO"] . '</cbc:Information>
            <cbc:GrossWeightMeasure unitCode="KGM">' . $cabecera["PESO"] . '</cbc:GrossWeightMeasure>
    <cbc:TotalTransportHandlingUnitQuantity>' . $cabecera["NUMERO_PAQUETES"] . '</cbc:TotalTransportHandlingUnitQuantity>
    
            <cac:ShipmentStage>
                <cbc:TransportModeCode>' . $cabecera["CODTIPO_TRANSPORTISTA"] . '</cbc:TransportModeCode>
                <cac:TransitPeriod>
                    <cbc:StartDate>' . $cabecera["FECHA_DOCUMENTO"] . '</cbc:StartDate>
                </cac:TransitPeriod>
                <cac:CarrierParty>
                    <cac:PartyIdentification>
    <cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_TRANSPORTE"] . '">' . $cabecera["NRO_DOCUMENTO_TRANSPORTE"] . '</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyName>
                        <cbc:Name><![CDATA[' . $cabecera["RAZON_SOCIAL_TRANSPORTE"] . ']]></cbc:Name>
                    </cac:PartyName>
                </cac:CarrierParty>
            </cac:ShipmentStage>
            
    <cac:Delivery>
                <cac:DeliveryAddress>
                    <cbc:ID>' . $cabecera["UBIGEO_DESTINO"] . '</cbc:ID>
                    <cbc:StreetName>' . $cabecera["DIR_DESTINO"] . '</cbc:StreetName>
                </cac:DeliveryAddress>
            </cac:Delivery>
            
    <cac:OriginAddress>
                <cbc:ID>' . $cabecera["UBIGEO_PARTIDA"] . '</cbc:ID>
                <cbc:StreetName>' . $cabecera["DIR_PARTIDA"] . '</cbc:StreetName>
    </cac:OriginAddress>
        </cac:Shipment>
        
        ';

        for ($i = 0; $i < count($detalle); $i++) {
            $xmlCPE .= '
                <cac:DespatchLine>
                    <cbc:ID>' . $detalle[$i]["ITEM"] . '</cbc:ID>
                    <cbc:DeliveredQuantity unitCode="' . $detalle[$i]["U_MEDIDA"] . '">' . $detalle[$i]["CANTIDAD"] . '</cbc:DeliveredQuantity>
                    <cac:OrderLineReference>
                            <cbc:LineID>' . $detalle[$i]["ITEM"] . '</cbc:LineID>
                    </cac:OrderLineReference>
                    <cac:Item>
                            <cbc:Name><![CDATA[' . $validacion->replace_invalid_caracters($detalle[$i]["DESCRIPCION"]) . ']]></cbc:Name>
                            <cac:SellersItemIdentification>
                                    <cbc:ID>' . $detalle[$i]["CODIGO_PRODUCTO"] . '</cbc:ID>
                            </cac:SellersItemIdentification>';
            if (isset($detalle[$i]["CODIGO_PRODUCTO_SUNAT"])) {
                $xmlCPE .= '<cac:CommodityClassification>
                                <cbc:ItemClassificationCode listID="UNSPSC" listAgencyName="GS1 US" listName="Item Classification">' . $detalle[$i]["CODIGO_PRODUCTO_SUNAT"] . '</cbc:ItemClassificationCode>
                            </cac:CommodityClassification>';
            }
            $xmlCPE .= '
                    </cac:Item>
                </cac:DespatchLine>';
        }
        $xmlCPE .= '</DespatchAdvice>';

        /*
          echo $xmlCPE;
          exit();
         */

        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.XML');
        $resp['respuesta'] = 'OK';
        $resp['url_xml'] = $ruta . '.XML';
        return $resp;
    }

    public function crear_xml_baja_sunat($cabecera, $detalle, $ruta) {
        $validacion = new validaciondedatos();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'ISO-8859-1';
        $xmlCPE = '<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?><VoidedDocuments xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:VoidedDocuments-1" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <ext:UBLExtensions>
    <ext:UBLExtension>
    <ext:ExtensionContent>
    </ext:ExtensionContent>
    </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
    <cbc:CustomizationID>1.0</cbc:CustomizationID>
    <cbc:ID>' . $cabecera["CODIGO"] . '-' . $cabecera["SERIE"] . '-' . $cabecera["SECUENCIA"] . '</cbc:ID>
    <cbc:ReferenceDate>' . $cabecera["FECHA_REFERENCIA"] . '</cbc:ReferenceDate>
    <cbc:IssueDate>' . $cabecera["FECHA_BAJA"] . '</cbc:IssueDate>
    <cac:Signature>
    <cbc:ID>IDSignKG</cbc:ID>
    <cac:SignatoryParty>
    <cac:PartyIdentification>
    <cbc:ID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
    </cac:PartyIdentification>
    <cac:PartyName>
    <cbc:Name>' . $validacion->replace_invalid_caracters($cabecera["RAZON_SOCIAL_EMPRESA"]) . '</cbc:Name>
    </cac:PartyName>
    </cac:SignatoryParty>
    <cac:DigitalSignatureAttachment>
    <cac:ExternalReference>
    <cbc:URI>#' . $cabecera["SERIE"] . '-' . $cabecera["SECUENCIA"] . '</cbc:URI>
    </cac:ExternalReference>
    </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
    <cbc:CustomerAssignedAccountID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:CustomerAssignedAccountID>
    <cbc:AdditionalAccountID>' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '</cbc:AdditionalAccountID>
    <cac:Party>
    <cac:PartyLegalEntity>
    <cbc:RegistrationName><![CDATA[' . $validacion->replace_invalid_caracters($cabecera["RAZON_SOCIAL_EMPRESA"]) . ']]></cbc:RegistrationName>
    </cac:PartyLegalEntity>
    </cac:Party>
    </cac:AccountingSupplierParty>';

        for ($i = 0; $i < count($detalle); $i++) {
            $xmlCPE = $xmlCPE . '<sac:VoidedDocumentsLine>
    <cbc:LineID>' . $detalle[$i]["ITEM"] . '</cbc:LineID>
    <cbc:DocumentTypeCode>' . $detalle[$i]["TIPO_COMPROBANTE"] . '</cbc:DocumentTypeCode>
    <sac:DocumentSerialID>' . $detalle[$i]["SERIE"] . '</sac:DocumentSerialID>
    <sac:DocumentNumberID>' . $detalle[$i]["NUMERO"] . '</sac:DocumentNumberID>
    <sac:VoidReasonDescription><![CDATA[' . $validacion->replace_invalid_caracters($detalle[$i]["MOTIVO"]) . ']]></sac:VoidReasonDescription>
    </sac:VoidedDocumentsLine>';
        }
        $xmlCPE = $xmlCPE . '</VoidedDocuments>';

        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.XML');
        $resp['respuesta'] = 'OK';
        $resp['url_xml'] = $ruta . '.XML';
        return $resp;
    }

    public function enviar_documento_para_baja($ruc, $usuario_sol, $pass_sol, $ruta_archivo, $ruta_archivo_cdr, $archivo, $ruta_ws) {
        try {
            //=================ZIPEAR ================
            $zip = new ZipArchive();
            $filenameXMLCPE = $ruta_archivo . '.ZIP';

            if ($zip->open($filenameXMLCPE, ZIPARCHIVE::CREATE) === true) {
                $zip->addFile($ruta_archivo . '.XML', $archivo . '.XML'); //ORIGEN, DESTINO
                $zip->close();
            }

            //===================ENVIO FACTURACION=====================
            $soapUrl = $ruta_ws;
            $soapUser = "";
            $soapPassword = "";
            // xml post structure
            $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
            xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
            xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
                <wsse:Security>
                    <wsse:UsernameToken>
                        <wsse:Username>' . $ruc . $usuario_sol . '</wsse:Username>
                        <wsse:Password>' . $pass_sol . '</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soapenv:Header>
            <soapenv:Body>
                <ser:sendSummary>
                    <fileName>' . $archivo . '.ZIP</fileName>
                    <contentFile>' . base64_encode(file_get_contents($ruta_archivo . '.ZIP')) . '</contentFile>
                </ser:sendSummary>
            </soapenv:Body>
            </soapenv:Envelope>';

            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: ",
                "Content-length: " . strlen($xml_post_string),
            ); //SOAPAction: your op URL

            $url = $soapUrl;

            // PHP cURL  for https connection with auth
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // converting
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            //convertimos de base 64 a archivo fisico
            $doc = new DOMDocument();
            $doc->loadXML($response);

            //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
            if (isset($doc->getElementsByTagName('ticket')->item(0)->nodeValue)) {
                $ticket = $doc->getElementsByTagName('ticket')->item(0)->nodeValue;

                unlink($ruta_archivo . '.ZIP');
                $mensaje['respuesta'] = 'OK';
                $mensaje['cod_ticket'] = $ticket;
                $mensaje['extra'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue . ' - ' . $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
            } else {
                $mensaje['respuesta'] = 'error';
                $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = "";
            }
        } catch (Exception $e) {
            $mensaje['respuesta'] = 'error';
            $mensaje['cod_sunat'] = "0000";
            $mensaje['msj_sunat'] = "SUNAT ESTA FUERA SERVICIO: " . $e->getMessage();
            $mensaje['hash_cdr'] = "";
        }
        return $mensaje;
    }

    public function enviar_resumen_boletas($ruc, $usuario_sol, $pass_sol, $ruta_archivo, $ruta_archivo_cdr, $archivo, $ruta_ws) {
        //=================ZIPEAR ================
        $zip = new ZipArchive();
        $filenameXMLCPE = $ruta_archivo . '.ZIP';

        if ($zip->open($filenameXMLCPE, ZIPARCHIVE::CREATE) === true) {
            $zip->addFile($ruta_archivo . '.XML', $archivo . '.XML'); //ORIGEN, DESTINO
            $zip->close();
        }

        //===================ENVIO FACTURACION=====================
        $soapUrl = $ruta_ws;
        $soapUser = "";
        $soapPassword = "";
        // xml post structure
        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
        xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
        xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken>
                    <wsse:Username>' . $ruc . $usuario_sol . '</wsse:Username>
                    <wsse:Password>' . $pass_sol . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
            <ser:sendSummary>
                <fileName>' . $archivo . '.ZIP</fileName>
                <contentFile>' . base64_encode(file_get_contents($ruta_archivo . '.ZIP')) . '</contentFile>
            </ser:sendSummary>
        </soapenv:Body>
        </soapenv:Envelope>';

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-length: " . strlen($xml_post_string),
        ); //SOAPAction: your op URL

        $url = $soapUrl;

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode == 200) {//======LA PAGINA SI RESPONDE
            //convertimos de base 64 a archivo fisico
            $doc = new DOMDocument();
            $doc->loadXML($response);

            //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
            if (isset($doc->getElementsByTagName('ticket')->item(0)->nodeValue)) {
                $ticket = $doc->getElementsByTagName('ticket')->item(0)->nodeValue;

                unlink($ruta_archivo . '.ZIP');
                $mensaje['respuesta'] = 'OK';
                $mensaje['cod_ticket'] = $ticket;
                $mensaje['extra'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue . ' - ' . $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
            } else {

                $mensaje['respuesta'] = 'error';
                $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = "";
            }
        } else {
            //echo "no responde web";
            $mensaje['respuesta'] = 'error';
            $mensaje['cod_sunat'] = "0000";
            $mensaje['msj_sunat'] = "SUNAT ESTA FUERA SERVICIO: " . $e->getMessage();
            $mensaje['hash_cdr'] = "";
        }
        return $mensaje;
    }

    function consultar_envio_ticket($ruc, $usuario_sol, $pass_sol, $ticket, $archivo, $ruta_archivo_cdr, $ruta_ws) {
        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <soapenv:Header>
        <wsse:Security>
        <wsse:UsernameToken>
        <wsse:Username>' . $ruc . $usuario_sol . '</wsse:Username>
        <wsse:Password>' . $pass_sol . '</wsse:Password>
        </wsse:UsernameToken>
        </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
        <ser:getStatus>
        <ticket>' . $ticket . '</ticket>
        </ser:getStatus>
        </soapenv:Body>
        </soapenv:Envelope>';

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-length: " . strlen($xml_post_string),
        ); //SOAPAction: your op URL
        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $ruta_ws);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode == 200) {//======LA PAGINA SI RESPONDE
            //echo $httpcode.'----'.$response;
            //convertimos de base 64 a archivo fisico
            $doc = new DOMDocument();
            $doc->loadXML($response);



            //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
            if (isset($doc->getElementsByTagName('content')->item(0)->nodeValue)) {
                $xmlCDR = $doc->getElementsByTagName('content')->item(0)->nodeValue;
                file_put_contents($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP', base64_decode($xmlCDR));

                //extraemos archivo zip a xml
                $zip = new ZipArchive;
                if ($zip->open($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP') === TRUE) {
                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $archivo . '.XML');
                    $zip->close();
                }

                //eliminamos los archivos Zipeados
                //unlink($ruta_archivo . '.ZIP');
                unlink($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP');

                //=============hash CDR=================
                $doc_cdr = new DOMDocument();
                $doc_cdr->load(dirname(__FILE__) . '/' . $ruta_archivo_cdr . 'R-' . $archivo . '.XML');

                $mensaje['respuesta'] = 'OK';
                $mensaje['cod_sunat'] = $doc_cdr->getElementsByTagName('ResponseCode')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                $mensaje['mensaje'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = $doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue;
            } else {
                $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = "";

                $mensaje['respuesta'] = 'error';
                $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje['mensaje'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = "";
            }
        } else {
            //echo "no responde web";
            $mensaje['respuesta'] = 'error';
            $mensaje['cod_sunat'] = "0000";
            $mensaje['mensaje'] = "SUNAT ESTA FUERA SERVICIO: ";
            $mensaje['hash_cdr'] = "";
        }
        return $mensaje;
    }

    /*
     *  0001	El comprobante existe y está aceptado.

      0002	El comprobante existe  pero está rechazado.

      0003	El comprobante existe pero está de baja.

      0004	Formato de RUC no es válido (debe de contener 11 caracteres numéricos).
      0005	Formato del tipo de comprobante no es válido (debe de contener 2 caracteres).
      0006	Formato de serie inválido (debe de contener 4 caracteres).
      0007	El numero de comprobante debe de ser mayor que cero.
      0008	El número de RUC no está inscrito en los registros de la SUNAT.
      0009	EL tipo de comprobante debe de ser (01, 07 o 08).

      0010	Sólo se puede consultar facturas, notas de crédito y debito electrónicas, cuya serie empieza con "F"
      0011	El comprobante de pago electrónico no existe.
      0012	El comprobante de pago electrónico no le pertenece.
     */

    public function consulta_validez($ruc, $usuario_sol, $pass_sol, $tipoC, $serieC, $numeroC) {
        //$soapUrl = 'https://e-factura.sunat.gob.pe/ol-it-wsconsvalidcpe/billValidService';
        $soapUrl = 'https://e-factura.sunat.gob.pe/ol-it-wsconscpegem/billConsultService';

        // xml post structure
        $xml_post_string = '<soapenv:Envelope xmlns:ser="http://service.sunat.gob.pe" 
        xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
              <wsse:Security>
                 <wsse:UsernameToken>
                  <wsse:Username>' . $ruc . $usuario_sol . '</wsse:Username>
                    <wsse:Password>' . $pass_sol . '</wsse:Password>
                 </wsse:UsernameToken>
              </wsse:Security>
           </soapenv:Header>
           <soapenv:Body>
              <ser:getStatus>
                 <rucComprobante>' . $ruc . '</rucComprobante>
                 <tipoComprobante>' . $tipoC . '</tipoComprobante>
                 <serieComprobante>' . $serieC . '</serieComprobante>
                 <numeroComprobante>' . $numeroC . '</numeroComprobante>
              </ser:getStatus>
           </soapenv:Body>
        </soapenv:Envelope>';

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-length: " . strlen($xml_post_string),
        );

        $url = $soapUrl;

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $doc = new DOMDocument();
        $doc->loadXML($response);

        $resp['cod_sunat'] = $doc->getElementsByTagName('statusCode')->item(0)->nodeValue;
        $resp['msj_sunat'] = $doc->getElementsByTagName('statusMessage')->item(0)->nodeValue;
        return $resp;
    }

    public function consulta_cdr($ruc, $usuario_sol, $pass_sol, $tipoC, $serieC, $numeroC) {
        //$soapUrl = 'https://e-factura.sunat.gob.pe/ol-it-wsconsvalidcpe/billValidService';
        $soapUrl = 'https://e-factura.sunat.gob.pe/ol-it-wsconscpegem/billConsultService';

        // xml post structure
        $xml_post_string = '<soapenv:Envelope xmlns:ser="http://service.sunat.gob.pe" 
        xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
              <wsse:Security>
                 <wsse:UsernameToken>
                  <wsse:Username>' . $ruc . $usuario_sol . '</wsse:Username>
                    <wsse:Password>' . $pass_sol . '</wsse:Password>
                 </wsse:UsernameToken>
              </wsse:Security>
           </soapenv:Header>
           <soapenv:Body>
              <ser:getStatus>
                 <rucComprobante>' . $ruc . '</rucComprobante>
                 <tipoComprobante>' . $tipoC . '</tipoComprobante>
                 <serieComprobante>' . $serieC . '</serieComprobante>
                 <numeroComprobante>' . $numeroC . '</numeroComprobante>
              </ser:getStatus>
           </soapenv:Body>
        </soapenv:Envelope>';

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-length: " . strlen($xml_post_string),
        );

        $url = $soapUrl;

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $doc = new DOMDocument();
        $doc->loadXML($response);

        $resp['cod_sunat'] = $doc->getElementsByTagName('statusCode')->item(0)->nodeValue;
        $resp['msj_sunat'] = $doc->getElementsByTagName('statusMessage')->item(0)->nodeValue;
        return $resp;
    }

}
