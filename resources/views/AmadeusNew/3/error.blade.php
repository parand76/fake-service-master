<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <soap:Fault>
            <faultcode>{{ $message }}</faultcode>
            <faultstring> 12|Presentation|soap message header incorrect</faultstring>
            <faultactor>SI:muxDZ1</faultactor>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>