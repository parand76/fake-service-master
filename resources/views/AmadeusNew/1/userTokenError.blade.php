<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
        <wsa:From>
            <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
        </wsa:From>
        <wsa:Action>http://webservices.amadeus.com/FMPTBQ_21_4_1A</wsa:Action>
        <wsa:MessageID>urn:uuid:13e17c1c-e80b-4bb4-393e-0ac3ce6b25a0</wsa:MessageID>
        <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">56fd8c0f-9cfe-4bd4-b6fc-40ed517248cb</wsa:RelatesTo>
        <awsse:Session TransactionStatusCode="End">
            <awsse:SessionId>00R8G3B67Z</awsse:SessionId>
            <awsse:SequenceNumber>1</awsse:SequenceNumber>
            <awsse:SecurityToken>13VHLGXPJQVW1N6D7Y2TCKQ8T</awsse:SecurityToken>
        </awsse:Session>
    </soap:Header>
    <soap:Body>
        <soap:Fault>
            <faultcode>wsse:InvalidSecurityToken</faultcode>
            <faultstring>An invalid security token was provided</faultstring>
            <faultactor>SI:srvDZ1M</faultactor>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>
