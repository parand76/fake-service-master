<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
        <wsa:From>
            <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
        </wsa:From>
        <wsa:Action>http://webservices.amadeus.com/PNRADD_21_1_1A</wsa:Action>
        <wsa:MessageID>urn:uuid:27f06c56-6d21-3414-2d11-c1435f866e91</wsa:MessageID>
        <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">4adab4f2-d51b-4625-933c-3768a9021a9b</wsa:RelatesTo>
        <awsse:Session TransactionStatusCode="InSeries">
            <awsse:SessionId>{{ $SessionId }}</awsse:SessionId>
            <awsse:SequenceNumber>{{ $SessionSequenceNumber }}</awsse:SequenceNumber>
            <awsse:SecurityToken>{{ $SessionToken }}</awsse:SecurityToken>
        </awsse:Session>
    </soap:Header>
    <soap:Body>
        <PNR_Reply xmlns="http://xml.amadeus.com/PNRACC_21_1_1A">
            <pnrHeader>
                <reservationInfo>
                    <reservation>
                        <companyId>1A</companyId>
                        <controlNumber>{{ $ControlNumber }}</controlNumber>
                    </reservation>
                </reservationInfo>
            </pnrHeader>
            <securityInformation>
                <responsibilityInformation>
                    <typeOfPnrElement>RP</typeOfPnrElement>
                </responsibilityInformation>
            </securityInformation>
            <sbrPOSDetails>
                <sbrUserIdentificationOwn>
                    <originIdentification>
                        <inHouseIdentification1> </inHouseIdentification1>
                    </originIdentification>
                </sbrUserIdentificationOwn>
            </sbrPOSDetails>
            <sbrCreationPosDetails>
                <sbrUserIdentificationOwn>
                    <originIdentification>
                        <inHouseIdentification1> </inHouseIdentification1>
                    </originIdentification>
                </sbrUserIdentificationOwn>
            </sbrCreationPosDetails>
            <sbrUpdatorPosDetails>
                <sbrUserIdentificationOwn>
                    <originIdentification>
                        <inHouseIdentification1> </inHouseIdentification1>
                    </originIdentification>
                </sbrUserIdentificationOwn>
            </sbrUpdatorPosDetails>
            <originDestinationDetails>
                <originDestination></originDestination>
            </originDestinationDetails>
            <dataElementsMaster>
                <marker2></marker2>
            </dataElementsMaster>
        </PNR_Reply>
    </soap:Body>
</soap:Envelope>