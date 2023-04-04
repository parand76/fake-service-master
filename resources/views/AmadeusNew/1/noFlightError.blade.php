<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
        <wsa:From>
            <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
        </wsa:From>
        <wsa:Action>http://webservices.amadeus.com/FMPTBQ_21_4_1A</wsa:Action>
        <wsa:MessageID>urn:uuid:c202884b-c0f0-4794-8935-61c0e2a428df</wsa:MessageID>
        <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">1597dfe6-c095-4ded-9867-7b2b54578a84</wsa:RelatesTo>
        <awsse:Session TransactionStatusCode="End">
            <awsse:SessionId>00R7GKR3NE</awsse:SessionId>
            <awsse:SequenceNumber>1</awsse:SequenceNumber>
            <awsse:SecurityToken>2CR4ITDVG6NKOKPT5SMUMBS0Y</awsse:SecurityToken>
        </awsse:Session>
    </soap:Header>
    <soap:Body>
        <Fare_MasterPricerTravelBoardSearchReply xmlns="http://xml.amadeus.com/FMPTBR_21_4_1A">
            <errorMessage>
                <applicationError>
                    <applicationErrorDetail>
                        <error>866</error>
                    </applicationErrorDetail>
                </applicationError>
                <errorMessageText>
                    <freeTextQualification>
                        <textSubjectQualifier>1</textSubjectQualifier>
                    </freeTextQualification>
                    <description>NO FARE FOUND FOR REQUESTED ITINERARY</description>
                </errorMessageText>
            </errorMessage>
        </Fare_MasterPricerTravelBoardSearchReply>
    </soap:Body>
</soap:Envelope>