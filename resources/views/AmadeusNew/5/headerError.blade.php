<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:Action>http://www.w3.org/2005/08/addressing/fault</wsa:Action>
        <wsa:MessageID>urn:uuid:8059a368-237d-a484-4199-fad8a9caaede</wsa:MessageID>
    </soap:Header>
    <soap:Body>
        <soap:Fault>
            <faultcode>wsa:{{ $message }}</faultcode>
            <faultstring>A required header representing a Message Addressing Property is not present</faultstring>
            <faultactor>SI:muxDZ1</faultactor>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>