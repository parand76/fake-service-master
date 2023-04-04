<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
        <wsa:From>
            <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
        </wsa:From>
        <wsa:Action>http://webservices.amadeus.com/VLSSOQ_04_1_1A</wsa:Action>
        <wsa:MessageID>urn:uuid:098e6ffa-899b-d5d4-8d60-4c3c5fcc8599</wsa:MessageID>
        <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">68518800-02e1-4722-b94d-0aec6899cb32</wsa:RelatesTo>
        <awsse:Session TransactionStatusCode="End">
            <awsse:SessionId>{{ $SessionId }}</awsse:SessionId>
            <awsse:SequenceNumber>3</awsse:SequenceNumber>
            <awsse:SecurityToken>{{ $SessionToken }}</awsse:SecurityToken>
        </awsse:Session>
    </soap:Header>
    <soap:Body>
        <Security_SignOutReply xmlns="http://xml.amadeus.com/VLSSOR_04_1_1A">
            <processStatus>
                <statusCode>P</statusCode>
            </processStatus>
        </Security_SignOutReply>
    </soap:Body>
</soap:Envelope>