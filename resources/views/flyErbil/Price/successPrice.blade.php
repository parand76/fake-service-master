<ota:OTA_AirPriceRS xmlns:ota="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" EchoToken="36732" RetransmissionIndicator="false" SequenceNmbr="284" Target="Production" TimeStamp="2021-04-12T12:19:14.329Z" Version="2.001" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 OTA_AirPriceRS.xsd">
    <ota:Success/>
    <ota:PricedItineraries>
        <ota:PricedItinerary SequenceNumber="1">
            <ota:AirItinerary>
                <ota:OriginDestinationOptions>
                    @if(!empty($Flightinfo['FlightNumberGO']))
                    <ota:OriginDestinationOption>
                        <ota:FlightSegment ResBookDesigCode="B" FlightNumber="{{$Flightinfo['FlightNumberGO']}}" DepartureDateTime="{{$Flightinfo['DepartureDateTimeGO']}}">
                            <ota:DepartureAirport LocationCode="{{$Flightinfo['OriginLocationGo']}}"/>
                            <ota:ArrivalAirport LocationCode="{{$Flightinfo['ArivalLocationGo']}}"/>
                            <ota:MarketingAirline Code="UBD"></ota:MarketingAirline>
                            <ota:TPA_Extensions>
                                <FareBasis xmlns="" Code="BOWCWEB"/>
                                <PriceGroup xmlns="" Name="Classic"/>
                                <FareRule xmlns="" Code="Classic">You may check-in 1 x 23 kg luggage and take 1 x 8 kg hand luggage with you. Infants are not entitled to carry checked baggage. The ticket is non-changeable/non-refundable. More information is available at flyskywork.com.</FareRule>
                            </ota:TPA_Extensions>
                        </ota:FlightSegment>
                    </ota:OriginDestinationOption>
                    <ota:OriginDestinationOption>
                        <ota:FlightSegment ResBookDesigCode="B" FlightNumber="{{$Flightinfo['FlightNumberBack']}}" DepartureDateTime="{{$Flightinfo['DepartureDateTimeBack']}}">
                            <ota:DepartureAirport LocationCode="{{$Flightinfo['OriginLocationBack']}}"/>
                            <ota:ArrivalAirport LocationCode="{{$Flightinfo['ArivalLocationBack']}}"/>
                            <ota:MarketingAirline Code="UBD"></ota:MarketingAirline>
                            <ota:TPA_Extensions>
                                <FareBasis xmlns="" Code="BOWCWEB"/>
                                <PriceGroup xmlns="" Name="Classic"/>
                                <FareRule xmlns="" Code="Classic">You may check-in 1 x 23 kg luggage and take 1 x 8 kg hand luggage with you. Infants are not entitled to carry checked baggage. The ticket is non-changeable/non-refundable. More information is available at flyskywork.com.</FareRule>
                            </ota:TPA_Extensions>
                        </ota:FlightSegment>
                    </ota:OriginDestinationOption>
                    @else
                    <ota:OriginDestinationOption>
                        <ota:FlightSegment ResBookDesigCode="B" FlightNumber="{{$Flightinfo['FlightNumber']}}" DepartureDateTime="{{$Flightinfo['DepartureDateTime']}}">
                            <ota:DepartureAirport LocationCode="{{$Flightinfo['OriginLocation']}}"/>
                            <ota:ArrivalAirport LocationCode="{{$Flightinfo['ArivalLocation']}}"/>
                            <ota:MarketingAirline Code="UBD"></ota:MarketingAirline>
                            <ota:TPA_Extensions>
                                <FareBasis xmlns="" Code="BOWCWEB"/>
                                <PriceGroup xmlns="" Name="Classic"/>
                                <FareRule xmlns="" Code="Classic">You may check-in 1 x 23 kg luggage and take 1 x 8 kg hand luggage with you. Infants are not entitled to carry checked baggage. The ticket is non-changeable/non-refundable. More information is available at flyskywork.com.</FareRule>
                            </ota:TPA_Extensions>
                        </ota:FlightSegment>
                    </ota:OriginDestinationOption>
                    @endif
                </ota:OriginDestinationOptions>
            </ota:AirItinerary>
            <ota:AirItineraryPricingInfo>
                <ota:ItinTotalFare>
                    <ota:BaseFare Amount="{{$pricing['totalbasefare']}}" CurrencyCode="EUR"/>
                    <ota:TotalFare Amount="{{$pricing['totalTotalefare']}}" CurrencyCode="EUR"/>
                </ota:ItinTotalFare>
                <ota:PTC_FareBreakdowns>
                    @foreach($passengers as $key=>$passenger)
                    <ota:PTC_FareBreakdown>
                        <ota:PassengerTypeQuantity Code="{{$passenger['PassengerTypeQuantity']['@attributes']['Code']}}" Quantity="{{$passenger['PassengerTypeQuantity']['@attributes']['Quantity']}}"/>
                        <ota:FareBasisCodes>
                            <ota:FareBasisCode>BOWCWEB</ota:FareBasisCode>
                            <ota:FareBasisCode>BOWCWEB</ota:FareBasisCode>
                        </ota:FareBasisCodes>
                        <ota:PassengerFare>
                            @if($passenger['PassengerTypeQuantity']['@attributes']['Code']=="ADT")
                            <ota:BaseFare Amount="{{$pricing['adultBaseFare']}}" CurrencyCode="EUR" DecimalPlaces="2"/>
                            <ota:TotalFare Amount="{{$pricing['adultTotaleFare']}}" CurrencyCode="EUR" DecimalPlaces="2"/>
                            @endif
                            @if($passenger['PassengerTypeQuantity']['@attributes']['Code']=="CHD")
                            <ota:BaseFare Amount="{{$pricing['childBaseFare']}}" CurrencyCode="EUR" DecimalPlaces="2"/>
                            <ota:TotalFare Amount="{{$pricing['childTotaleFare']}}" CurrencyCode="EUR" DecimalPlaces="2"/>
                            @endif
                            @if($passenger['PassengerTypeQuantity']['@attributes']['Code']=="INF")
                            <ota:BaseFare Amount="{{$pricing['infantBaseFare']}}" CurrencyCode="EUR" DecimalPlaces="2"/>
                            <ota:TotalFare Amount="{{$pricing['infantTotalFare']}}" CurrencyCode="EUR" DecimalPlaces="2"/>
                            @endif
                            <ota:UnstructuredFareCalc>CPH UBD EBL 150.00EUR END EBL UBD CPH 150.00EUR END</ota:UnstructuredFareCalc>
                        </ota:PassengerFare>
                        <ota:TravelerRefNumber RPH="{{$key+1}}"/>
                    </ota:PTC_FareBreakdown>
                    @endforeach
                </ota:PTC_FareBreakdowns>
            </ota:AirItineraryPricingInfo>
        </ota:PricedItinerary>
    </ota:PricedItineraries>
</ota:OTA_AirPriceRS>