<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:Action>http://www.w3.org/2005/08/addressing/fault</wsa:Action>
        <wsa:MessageID>urn:uuid:5ceb8e59-42e2-07f4-559e-f3874f4b365b</wsa:MessageID>
        <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">0c20bd1d-3651-4fd4-b6c8-91bb64b205b3</wsa:RelatesTo>
        <wsa:FaultDetail>
            <wsa:ProblemHeaderQName>wsa:To</wsa:ProblemHeaderQName>
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