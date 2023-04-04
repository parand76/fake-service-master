<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:Action>http://www.w3.org/2005/08/addressing/fault</wsa:Action>
        <wsa:MessageID>urn:uuid:82d7dc29-ceb1-7ec4-351c-98d4e99ae81b</wsa:MessageID>
        <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">690dc22d-cd26-4025-857c-54221f363040</wsa:RelatesTo>
        <wsa:FaultDetail>
            <wsa:ProblemHeaderQName>wsa:Action</wsa:ProblemHeaderQName>
        </wsa:FaultDetail>
    </soap:Header>
    <soap:Body>
        <soap:Fault>
            <faultcode>wsa:MessageAddressingHeaderRequired</faultcode>
            <faultstring>A required header representing a Message Addressing Property is not present</faultstring>
            <faultactor>SI:muxDZ1</faultactor>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>