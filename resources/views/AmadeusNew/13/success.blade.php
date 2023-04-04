<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
        <wsa:From>
            <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
        </wsa:From>
        <wsa:Action>http://webservices.amadeus.com/TTKTIQ_15_1_1A</wsa:Action>
        <wsa:MessageID>urn:uuid:45358aa7-e0b1-2684-2d9f-769d45b966a2</wsa:MessageID>
        <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">7bb88834-6ffb-46e7-a50a-5ea4c7760e2f</wsa:RelatesTo>
        <awsse:Session TransactionStatusCode="InSeries">
            <awsse:SessionId>{{ $SessionId }}</awsse:SessionId>
            <awsse:SequenceNumber>{{ $SessionSequenceNumber }}</awsse:SequenceNumber>
            <awsse:SecurityToken>{{ $SessionToken }}</awsse:SecurityToken>
        </awsse:Session>
    </soap:Header>
    <soap:Body>
        <DocIssuance_IssueTicketReply xmlns="http://xml.amadeus.com/TTKTIR_15_1_1A">
            <processingStatus>
                <statusCode>O</statusCode>
            </processingStatus>
            <errorGroup>
                <errorOrWarningCodeDetails>
                    <errorDetails>
                        <errorCode>OK</errorCode>
                    </errorDetails>
                </errorOrWarningCodeDetails>
                <errorWarningDescription>
                    <freeTextDetails>
                        <textSubjectQualifier>3</textSubjectQualifier>
                        <source>M</source>
                        <encoding>1</encoding>
                    </freeTextDetails>
                    <freeText>OK ETICKET</freeText>
                </errorWarningDescription>
            </errorGroup>
        </DocIssuance_IssueTicketReply>
    </soap:Body>
</soap:Envelope>