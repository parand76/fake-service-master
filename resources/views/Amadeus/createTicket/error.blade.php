<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
    <soap:Body>
        <CreateTicketResponse xmlns="http://epowerv5.amadeus.com.tr/WS">
            <OTA_AirBookRS Version="0">
                <Errors>
                    @foreach($errorlist as $error)
                    {!! $error !!}
                    @endforeach
                </Errors>
            </OTA_AirBookRS>
        </CreateTicketResponse>
    </soap:Body>
</soap:Envelope>