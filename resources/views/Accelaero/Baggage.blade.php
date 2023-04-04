<soap:Envelope
    xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <soap:Body xmlns:ns1="http://www.opentravel.org/OTA/2003/05">
        <ns1:AA_OTA_AirBaggageDetailsRS EchoToken="11868765275150-
            1300257933" PrimaryLangID="en-us" SequenceNmbr="1"
            Version="2006.01">
            <ns1:Success/>
            <ns1:Warnings/>
            <ns1:OnDBaggagesEnabled>false</ns1:OnDBaggagesEnabled>
            <ns1:BaggageDetailsResponses>
                <ns1:BaggageDetailsResponse>
                    <ns1:FlightSegmentInfo ArrivalDateTime="2011-12-10T23:00:00"
                        DepartureDateTime="2011-12-10T19:00:00" FlightNumber="3O1234"
                        RPH="25728" SegmentCode="CMN/CDG">
                        <ns1:DepartureAirport LocationCode="CMN"/>
                        <ns1:ArrivalAirport LocationCode="CDG" Terminal="A"/>
                    </ns1:FlightSegmentInfo>
                    <ns1:Baggage>
                        <ns1:baggageCode>20 Kg</ns1:baggageCode>
                        <ns1:baggageDescription>20 Kg</ns1:baggageDescription>
                        <ns1:baggageCharge>110</ns1:baggageCharge>
                        <ns1:currencyCode>MAD</ns1:currencyCode>
                    </ns1:Baggage>
                    <ns1:Baggage>
                        <ns1:baggageCode>25 Kg</ns1:baggageCode>
                        <ns1:baggageDescription>25 Kg</ns1:baggageDescription>
                        <ns1:baggageCharge>440</ns1:baggageCharge>
                        <ns1:currencyCode>MAD</ns1:currencyCode>
                    </ns1:Baggage>
                    <ns1:Baggage>
                        <ns1:baggageCode>30 Kg</ns1:baggageCode>
                        <ns1:baggageDescription>30 Kg</ns1:baggageDescription>
                        <ns1:baggageCharge>770</ns1:baggageCharge>
                        <ns1:currencyCode>MAD</ns1:currencyCode>
                    </ns1:Baggage>
                    <ns1:Baggage>
                        <ns1:baggageCode>No BAG</ns1:baggageCode>
                        <ns1:baggageDescription>No Baggage</ns1:baggageDescription>
                        <ns1:baggageCharge>0</ns1:baggageCharge>
                        <ns1:currencyCode>MAD</ns1:currencyCode>
                    </ns1:Baggage>
                </ns1:BaggageDetailsResponse>
            </ns1:BaggageDetailsResponses>
            <ns1:Errors/>
        </ns1:AA_OTA_AirBaggageDetailsRS>
    </soap:Body>
</soap:Envelope>