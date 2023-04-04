<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
    <soap:Body>
        <GetFlightRulesResponse xmlns="http://epowerv5.amadeus.com.tr/WS">
            <OTA_AirRulesRS Version="0">
                <Errors>
                    <Error Type="ValidationError" ShortText="SearchedFlightRecommendations can not be null or empty" Code="A000" NodeList="Agency" BreakFlow="true" />
                </Errors>
            </OTA_AirRulesRS>
        </GetFlightRulesResponse>
    </soap:Body>
</soap:Envelope>