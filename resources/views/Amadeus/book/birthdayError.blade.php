<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
    <soap:Body>
        <soap:Fault>
            <faultcode>soap:Client</faultcode>
            <faultstring>Server was unable to read request.</faultstring>
            <detail>
                <OTA_ErrorRS ErrorCode="UNHANDLED_EXCEPTION" ErrorMessage="String was not recognized as a valid DateTime."/>
            </detail>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>