<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
        <wsa:From>
            <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
        </wsa:From>
        <wsa:Action>http://webservices.amadeus.com/FMPTBQ_21_4_1A</wsa:Action>
        <wsa:MessageID>urn:uuid:373aed90-d87e-00c4-f9bb-6f847a1e6b25</wsa:MessageID>
        <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">ee4f8b2a-c3f0-444a-a650-c02866b71a1f</wsa:RelatesTo>
        <awsse:Session TransactionStatusCode="End">
            <awsse:SessionId>01LD2WM85R</awsse:SessionId>
            <awsse:SequenceNumber>1</awsse:SequenceNumber>
            <awsse:SecurityToken>2SJC43H5938RL36VRQQNDS6KHI</awsse:SecurityToken>
        </awsse:Session>
    </soap:Header>
    <soap:Body>
        <soap:Fault xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <faultcode>soap:Client</faultcode>
            <faultstring>18|Presentation|Serializing/Deserializing error: [type = Composite] [name = unitNumberDetail] [Error = Unknown item found or found at the wrong position]</faultstring>
            <faultactor>SI:APA</faultactor>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>