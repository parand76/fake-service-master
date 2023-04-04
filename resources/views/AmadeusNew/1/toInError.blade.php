<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:Action>http://www.w3.org/2005/08/addressing/fault</wsa:Action>
        <wsa:MessageID>urn:uuid:3378542f-7c8a-5e94-a5e2-870211b77829</wsa:MessageID>
        <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">f4fbdabd-10f1-48a5-be35-898383c26b07</wsa:RelatesTo>
        <wsa:FaultDetail>
            <wsa:ProblemHeaderQName>wsa:To</wsa:ProblemHeaderQName>
        </wsa:FaultDetail>
    </soap:Header>
    <soap:Body>
        <soap:Fault>
            <faultcode>wsa:MissingAddressInEPR</faultcode>
            <faultstring>A header representing a Message Addressing Property is not valid and the message cannot be processed</faultstring>
            <faultactor>SI:muxDZ1</faultactor>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>