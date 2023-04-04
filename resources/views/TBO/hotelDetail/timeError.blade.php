<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing">
    <s:Header>
        <a:Action s:mustUnderstand="1">http://TekTravel/HotelBookingApi/IHotelService/HotelDetailsResponse</a:Action>
    </s:Header>
    <s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
        <HotelDetailsResponse xmlns="http://TekTravel/HotelBookingApi">
            <Status>
                <StatusCode>{{$code}}</StatusCode>
                <Description>{{$error}}</Description>
            </Status>
        </HotelDetailsResponse>
    </s:Body>
</s:Envelope>