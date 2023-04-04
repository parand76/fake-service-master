<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
    <soap:Body>
        <SearchFlightResponse xmlns="http://epowerv5.amadeus.com.tr/WS">
            <OTA_AirLowFareSearchRS Version="0">
                <HasMoreResult>false</HasMoreResult>
                <Errors>
                    <Error Type="EpowerInternalError" ErrorCode="EPW.0000" ShortText="{{$error}}" Code="A001" NodeList="EPower" BreakFlow="false" />
                </Errors>
            </OTA_AirLowFareSearchRS>
        </SearchFlightResponse>
    </soap:Body>
</soap:Envelope>