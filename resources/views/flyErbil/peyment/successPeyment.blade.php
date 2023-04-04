<ota:OTA_AirDemandTicketRS xmlns:ota="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" EchoToken="456789" RetransmissionIndicator="false" SequenceNmbr="1" Target="Production" TimeStamp="2021-04-20T04:26:50.428Z" Version="2.001" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 OTA_AirDemandTicketRS.xsd">
    <ota:Success/>
    <ota:BookingReferenceID Type="14" ID="{{$pnr}}">
        <ota:CompanyName Code="6A"/>
    </ota:BookingReferenceID>
    
    @foreach($passengers as $key=>$passenger)
    <ota:TicketItemInfo ItemNumber="{{$key+1}}" NetAmount="{{$pricing['totalTotalefare']}}" PaymentType="1" TicketNumber="{{$tickets[$key]}}" TotalAmount="{{$pricing['totalTotalefare']}}" Type="eTicket">
        <ota:PassengerName PassengerTypeCode="{{$passenger['@attributes']['PassengerTypeCode']}}">
            <ota:NamePrefix>{{$passenger['PersonName']['NamePrefix']}}</ota:NamePrefix>
            <ota:GivenName>{{$passenger['PersonName']['GivenName']}}</ota:GivenName>
            <ota:Surname>{{$passenger['PersonName']['Surname']}}</ota:Surname>
        </ota:PassengerName>
    </ota:TicketItemInfo>
    @endforeach
    
</ota:OTA_AirDemandTicketRS>