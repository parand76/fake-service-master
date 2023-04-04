<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
    <soap:Body>
        <PingResponse xmlns="http://epowerv5.amadeus.com.tr/WS">
            <OTA_PingRS Version="0">
                <Errors>
                    @foreach($errorlist as $key=>$error)
                    @foreach($error as $er)
                    {!!$er!!}
                    @endforeach
                    @endforeach
                </Errors>
            </OTA_PingRS>
        </PingResponse>
    </soap:Body>
</soap:Envelope>