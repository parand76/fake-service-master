<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
        <wsa:From>
            <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
        </wsa:From>
        <wsa:Action>http://webservices.amadeus.com/VLSSOQ_04_1_1A</wsa:Action>
        <wsa:MessageID>urn:uuid:6bda4d64-8472-4314-0132-0339ec2327fc</wsa:MessageID>
        <awsse:Session TransactionStatusCode="End"/>
    </soap:Header>
    <soap:Body>
        <soap:Fault>
            <faultcode>soap:{{ $message }}</faultcode>
            <faultstring> 12|validation|soap message header incorrect</faultstring>
            <faultactor>SI:muxDZ2</faultactor>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>