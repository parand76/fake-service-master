<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
        <wsa:From>
            <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
        </wsa:From>
        <wsa:Action>http://webservices.amadeus.com/TTKTIQ_15_1_1A</wsa:Action>
        <wsa:MessageID>urn:uuid:43e6e15d-cb95-55b4-e50b-3cd2cb9e377f</wsa:MessageID>
        <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">e39f365b-1330-4218-b8c4-d0565aa32308</wsa:RelatesTo>
        <awsse:Session TransactionStatusCode="InSeries">
            <awsse:SessionId>{{ $SessionId }}</awsse:SessionId>
            <awsse:SequenceNumber>{{ $SessionSequenceNumber }}</awsse:SequenceNumber>
            <awsse:SecurityToken>{{ $SessionToken }}</awsse:SecurityToken>
        </awsse:Session>
    </soap:Header>
    <soap:Body>
        <DocIssuance_IssueTicketReply xmlns="http://xml.amadeus.com/TTKTIR_15_1_1A">
            <processingStatus>
                <statusCode>X</statusCode>
            </processingStatus>
            <errorGroup>
                <errorOrWarningCodeDetails>
                    <errorDetails>
                        <errorCode>3025</errorCode>
                    </errorDetails>
                </errorOrWarningCodeDetails>
                <errorWarningDescription>
                    <freeTextDetails>
                        <textSubjectQualifier>3</textSubjectQualifier>
                        <source>M</source>
                        <encoding>1</encoding>
                    </freeTextDetails>
                    <freeText>ALL PASSENGERS/SEGMENTS ALREADY TICKETED</freeText>
                </errorWarningDescription>
            </errorGroup>
        </DocIssuance_IssueTicketReply>
    </soap:Body>
</soap:Envelope>