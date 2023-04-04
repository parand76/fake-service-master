<ota:OTA_AirBookRS xmlns:ota="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" EchoToken="456789" RetransmissionIndicator="false" SequenceNmbr="1" Target="Production" TimeStamp="2021-04-12T12:25:34.391Z" Version="2.001" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 OTA_AirBookRS.xsd">
    <ota:Success/>
    <ota:AirReservation CreateDateTime="2021-04-12T12:25:28.000Z">
        <ota:AirItinerary>
           
            <ota:OriginDestinationOptions>
            @if(!empty($Flightinfo['FlightNumberGO']))
                <ota:OriginDestinationOption>
                    <ota:FlightSegment ResBookDesigCode="B" Status="30" FlightNumber="{{$Flightinfo['FlightNumberGO']}}" ArrivalDateTime="{{$Flightinfo['ArrivalDateTimeGO']}}" DepartureDateTime="{{$Flightinfo['DepartureDateTimeGO']}}" RPH="1">
                        <ota:DepartureAirport LocationCode="{{$Flightinfo['OriginLocationGo']}}"/>
                        <ota:ArrivalAirport LocationCode="{{$Flightinfo['ArivalLocationGo']}}"/>
                        <ota:OperatingAirline FlightNumber="{{$Flightinfo['FlightNumberGO']}}" Code="UBD"/>
                        <ota:MarketingAirline Code="UBD"/>
                        <ota:TPA_Extensions>
                            <FareBasis xmlns="" Code="BOWCWEB"/>
                            <PriceGroup xmlns="" Name="Classic"/>
                            <FareRule xmlns="" Code="Classic">You may check-in 1 x 23 kg luggage and take 1 x 8 kg hand luggage with you. Infants are not entitled to carry checked baggage. The ticket is non-changeable/non-refundable. More information is available at flyskywork.com.</FareRule>
                            <Operations xmlns="">
                                <Operation ModificationType="10" Name="CANCEL"/>
                                <Operation ModificationType="30" Name="REBOOK"/>
                                <Operation ModificationType="3" Name="CHANGE_NAME"/>
                            </Operations>
                        </ota:TPA_Extensions>
                    </ota:FlightSegment>
                </ota:OriginDestinationOption>
                <ota:OriginDestinationOption>
                    <ota:FlightSegment ResBookDesigCode="B" Status="30" FlightNumber="{{$Flightinfo['FlightNumberBack']}}" ArrivalDateTime="{{$Flightinfo['ArrivalDateTimeBack']}}" DepartureDateTime="{{$Flightinfo['DepartureDateTimeBack']}}" RPH="2">
                        <ota:DepartureAirport LocationCode="{{$Flightinfo['OriginLocationBack']}}"/>
                        <ota:ArrivalAirport LocationCode="{{$Flightinfo['ArivalLocationBack']}}"/>
                        <ota:OperatingAirline FlightNumber="{{$Flightinfo['FlightNumberBack']}}" Code="UBD"/>
                        <ota:MarketingAirline Code="UBD"/>
                        <ota:TPA_Extensions>
                            <FareBasis xmlns="" Code="BOWCWEB"/>
                            <PriceGroup xmlns="" Name="Classic"/>
                            <FareRule xmlns="" Code="Classic">You may check-in 1 x 23 kg luggage and take 1 x 8 kg hand luggage with you. Infants are not entitled to carry checked baggage. The ticket is non-changeable/non-refundable. More information is available at flyskywork.com.</FareRule>
                            <Operations xmlns="">
                                <Operation ModificationType="10" Name="CANCEL"/>
                                <Operation ModificationType="30" Name="REBOOK"/>
                                <Operation ModificationType="3" Name="CHANGE_NAME"/>
                            </Operations>
                        </ota:TPA_Extensions>
                    </ota:FlightSegment>
                </ota:OriginDestinationOption>
                @else
                <ota:OriginDestinationOption>
                    <ota:FlightSegment ResBookDesigCode="B" Status="30" FlightNumber="{{$Flightinfo['FlightNumber']}}" ArrivalDateTime="{{$Flightinfo['ArrivalDateTime']}}" DepartureDateTime="{{$Flightinfo['DepartureDateTime']}}" RPH="2">
                        <ota:DepartureAirport LocationCode="{{$Flightinfo['OriginLocation']}}"/>
                        <ota:ArrivalAirport LocationCode="{{$Flightinfo['ArivalLocation']}}"/>
                        <ota:OperatingAirline FlightNumber="{{$Flightinfo['FlightNumber']}}" Code="UBD"/>
                        <ota:MarketingAirline Code="UBD"/>
                        <ota:TPA_Extensions>
                            <FareBasis xmlns="" Code="BOWCWEB"/>
                            <PriceGroup xmlns="" Name="Classic"/>
                            <FareRule xmlns="" Code="Classic">You may check-in 1 x 23 kg luggage and take 1 x 8 kg hand luggage with you. Infants are not entitled to carry checked baggage. The ticket is non-changeable/non-refundable. More information is available at flyskywork.com.</FareRule>
                            <Operations xmlns="">
                                <Operation ModificationType="10" Name="CANCEL"/>
                                <Operation ModificationType="30" Name="REBOOK"/>
                                <Operation ModificationType="3" Name="CHANGE_NAME"/>
                            </Operations>
                        </ota:TPA_Extensions>
                    </ota:FlightSegment>
                </ota:OriginDestinationOption>
                @endif
            </ota:OriginDestinationOptions>
        </ota:AirItinerary>
        <ota:PriceInfo>
            <ota:ItinTotalFare>
                <ota:BaseFare Amount="{{$pricing['totalbasefare']}}" CurrencyCode="USD"/>
                <ota:TotalFare Amount="{{$pricing['totalTotalefare']}}" CurrencyCode="USD"/>
            </ota:ItinTotalFare>
            <ota:PTC_FareBreakdowns>
            @foreach($passengers as $key=>$passenger)
                <ota:PTC_FareBreakdown FlightRefNumberRPHList="1">
                    <ota:PassengerTypeQuantity Code="{{ $passenger['@attributes']['PassengerTypeCode']}}" Quantity="1"/>
                    <ota:FareBasisCodes>
                        <ota:FareBasisCode>BOWCWEB</ota:FareBasisCode>
                    </ota:FareBasisCodes>
                    <ota:PassengerFare>
                        @if($passenger['@attributes']['PassengerTypeCode']=="ADT")
                        <ota:BaseFare Amount="{{$pricing['adultBaseFare']}}" CurrencyCode="USD" DecimalPlaces="2"/>
                        <ota:TotalFare Amount="{{$pricing['adultTotaleFare']}}" CurrencyCode="USD" DecimalPlaces="2"/>
                        @endif
                        @if($passenger['@attributes']['PassengerTypeCode']=="CHD")
                        <ota:BaseFare Amount="{{$pricing['childBaseFare']}}" CurrencyCode="USD" DecimalPlaces="2"/>
                        <ota:TotalFare Amount="{{$pricing['childTotaleFare']}}" CurrencyCode="USD" DecimalPlaces="2"/>
                        @endif
                        @if($passenger['@attributes']['PassengerTypeCode']=="INF")
                        <ota:BaseFare Amount="{{$pricing['infantBaseFare']}}" CurrencyCode="USD" DecimalPlaces="2"/>
                        <ota:TotalFare Amount="{{$pricing['infantTotalFare']}}" CurrencyCode="USD" DecimalPlaces="2"/>
                        @endif
                    </ota:PassengerFare>
                    <ota:TravelerRefNumber RPH="1"/>
                    <ota:TicketDesignators>
                        <ota:TicketDesignator FlightRefRPH="1"/>
                    </ota:TicketDesignators>
                </ota:PTC_FareBreakdown>
                @endforeach
            </ota:PTC_FareBreakdowns>
        </ota:PriceInfo>
        <ota:TravelerInfo>
            @foreach($passengers as $key=>$passenger)
            <ota:AirTraveler PassengerTypeCode="{{$passenger['@attributes']['PassengerTypeCode']}}" Gender="Unknown">
                <ota:PersonName>
                    <ota:NamePrefix>{{$passenger['PersonName']['NamePrefix']}}</ota:NamePrefix>
                    <ota:GivenName>{{$passenger['PersonName']['GivenName']}}</ota:GivenName>
                    <ota:Surname>{{$passenger['PersonName']['Surname']}}</ota:Surname>
                </ota:PersonName>
                @if(isset($passenger['Email']))
                <ota:Email>{{$passenger['Email']}}</ota:Email>
                @endif
                <ota:TravelerRefNumber RPH="{{$key+1}}"/>
                <ota:FlightSegmentRPHs>
                    <ota:FlightSegmentRPH>1</ota:FlightSegmentRPH>
                    <ota:FlightSegmentRPH>2</ota:FlightSegmentRPH>
                </ota:FlightSegmentRPHs>
            </ota:AirTraveler>
            @endforeach
            
        </ota:TravelerInfo>
        <ota:Ticketing TicketTimeLimit="{{$tickets['timelimitTicket']}}" TicketType="eTicket"/>
        <ota:BookingReferenceID Type="14" ID="{{$tickets['pnr']}}">
            <ota:CompanyName Code="6A"/>
        </ota:BookingReferenceID>
    </ota:AirReservation>
</ota:OTA_AirBookRS>
