<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing"><s:Header><a:Action s:mustUnderstand="1">http://TekTravel/HotelBookingApi/IHotelService/HotelBookResponse</a:Action></s:Header><s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><HotelBookResponse xmlns="http://TekTravel/HotelBookingApi"><Status><StatusCode>{{$code}}</StatusCode><Description>{{$message}}</Description></Status><BookingStatus>Vouchered</BookingStatus><BookingId>0</BookingId><ConfirmationNo>{{$confirmationNumber}}</ConfirmationNo><TripId>173307</TripId><SupplierReferenceNo/><PriceChange Status="false" AvailableOnNewPrice="false"/></HotelBookResponse>
    </s:Body>
</s:Envelope>