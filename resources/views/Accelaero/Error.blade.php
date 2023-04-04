<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<soap:Body xmlns:ns1="http://www.opentravel.org/OTA/2003/05">
		<ns1:OTA_AirAvailRS EchoToken="11868765275150-1300257933" PrimaryLangID="en-us" RetransmissionIndicator="false"
			SequenceNmbr="1" TransactionIdentifier="TID$1654411245378205231.demo2117" Version="2006.01">
			<ns1:Warnings />
			<ns1:Errors>
				<ns1:Error Code="{{ $code }}" ShortText="{{ $error }}" Type="{{ $type }}" />
			</ns1:Errors>
		</ns1:OTA_AirAvailRS>
	</soap:Body>
</soap:Envelope>