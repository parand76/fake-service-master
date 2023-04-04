<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:Action>http://www.w3.org/2005/08/addressing/fault</wsa:Action>
        <wsa:MessageID>urn:uuid:ee96cc12-fd07-56a4-7134-344bd1791ef0</wsa:MessageID>
        <wsa:FaultDetail>
            <wsa:ProblemHeaderQName>wsa:MessageID</wsa:ProblemHeaderQName>
        </wsa:FaultDetail>
    </soap:Header>
    <soap:Body>
        <soap:Fault>
            <faultcode>wsa:MessageAddressingHeaderRequired</faultcode>
            <faultstring>A required header representing a Message Addressing Property is not present</faultstring>
            <faultactor>SI:muxDZ2</faultactor>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>