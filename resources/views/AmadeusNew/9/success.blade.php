<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
        <wsa:From>
            <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
        </wsa:From>
        <wsa:Action>http://webservices.amadeus.com/TAUTCQ_04_1_1A</wsa:Action>
        <wsa:MessageID>urn:uuid:280f3780-8817-cee4-3542-11a85a2eded5</wsa:MessageID>
        <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">920d0743-7ff1-49fc-a74e-569617283d2c</wsa:RelatesTo>
        <awsse:Session TransactionStatusCode="InSeries">
            <awsse:SessionId>{{ $SessionId }}</awsse:SessionId>
            <awsse:SequenceNumber>{{ $SessionSequenceNumber }}</awsse:SequenceNumber>
            <awsse:SecurityToken>{{ $SessionToken }}</awsse:SecurityToken>
        </awsse:Session>
    </soap:Header>
    <soap:Body>
        <Ticket_CreateTSTFromPricingReply xmlns="http://xml.amadeus.com/TAUTCR_04_1_1A">
            <tstList>
                <tstReference>
                    <referenceType>TST</referenceType>
                    <uniqueReference>1</uniqueReference>
                    <iDDescription>
                        <iDSequenceNumber>1</iDSequenceNumber>
                    </iDDescription>
                </tstReference>
                <paxInformation>
                    <refDetails>
                        <refQualifier>PA</refQualifier>
                        <refNumber>2</refNumber>
                    </refDetails>
                </paxInformation>
            </tstList>
        </Ticket_CreateTSTFromPricingReply>
    </soap:Body>
</soap:Envelope>