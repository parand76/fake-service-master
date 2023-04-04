<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <soap:Body xmlns:ns1="http://www.opentravel.org/OTA/2003/05">
        <ns1:OTA_AirPriceRS EchoToken="11868765275150-1300257933" PrimaryLangID="en-us" RetransmissionIndicator="false" SequenceNmbr="1" TransactionIdentifier="TID$165492398057175035-1.demo2144" Version="2006.01">
            <ns1:PricedItineraries>
                <ns1:PricedItinerary SequenceNumber="1">
                    <ns1:AirItinerary>
                        <ns1:OriginDestinationOptions>
                            {{-- loop --}}
                            @if (isset($option[0]))
                                @foreach ($option as $item)
                                
                                    <ns1:OriginDestinationOption>
                                        <ns1:FlightSegment ArrivalDateTime="{{ $item['ns1__FlightSegment']['@attributes']['ArrivalDateTime'] }}" DepartureDateTime="{{ $item['ns1__FlightSegment']['@attributes']['DepartureDateTime'] }}" FlightNumber="{{ $item['ns1__FlightSegment']['@attributes']['FlightNumber'] }}" RPH="{{ $item['ns1__FlightSegment']['@attributes']['RPH'] }}" returnFlag="false">
                                            <ns1:DepartureAirport LocationCode="{{ $item['ns1__FlightSegment']['ns1__DepartureAirport']['@attributes']['LocationCode'] }}" Terminal="TerminalX" />
                                            <ns1:ArrivalAirport LocationCode="{{ $item['ns1__FlightSegment']['ns1__ArrivalAirport']['@attributes']['LocationCode'] }}" Terminal="TerminalX" />
                                        </ns1:FlightSegment>
                                    </ns1:OriginDestinationOption>
                                
                                @endforeach
                            @endif

                            @if (isset($option['ns1__FlightSegment']))

                                <ns1:OriginDestinationOption>
                                    <ns1:FlightSegment ArrivalDateTime="{{ $option['ns1__FlightSegment']['@attributes']['ArrivalDateTime'] }}" DepartureDateTime="{{ $option['ns1__FlightSegment']['@attributes']['DepartureDateTime'] }}" FlightNumber="{{ $option['ns1__FlightSegment']['@attributes']['FlightNumber'] }}" RPH="{{ $option['ns1__FlightSegment']['@attributes']['RPH'] }}" returnFlag="false">
                                        <ns1:DepartureAirport LocationCode="{{ $option['ns1__FlightSegment']['ns1__DepartureAirport']['@attributes']['LocationCode'] }}" Terminal="TerminalX" />
                                        <ns1:ArrivalAirport LocationCode="{{ $option['ns1__FlightSegment']['ns1__ArrivalAirport']['@attributes']['LocationCode'] }}" Terminal="TerminalX" />
                                    </ns1:FlightSegment>
                                </ns1:OriginDestinationOption>
                                
                            @endif

                            @if (isset($Bundle[0]))
                                @foreach ($Bundle as $bun)

                                    <ns1:AABundledServiceExt applicableOnd="{{ $bun['@attributes']['applicableOnd'] }}" applicableOndSequence="0">
                                        @foreach ($bun['ns1__bundledService'] as $service)
                                        
                                            <ns1:bundledService>
                                                <ns1:bunldedServiceId>{{ $service['ns1__bunldedServiceId'] }}</ns1:bunldedServiceId>
                                                <ns1:bundledServiceName>{{ $service['ns1__bundledServiceName'] }}</ns1:bundledServiceName>
                                                <ns1:perPaxBundledFee>{{ $service['ns1__perPaxBundledFee'] }}</ns1:perPaxBundledFee>
                                                <ns1:bookingClasses>{{ $service['ns1__bookingClasses'] }}</ns1:bookingClasses>
                                                <ns1:description>{{ $service['ns1__description'] }}</ns1:description>
                                                    @foreach ($service['ns1__includedServies'] as $inc)
                                                        
                                                        <ns1:includedServies>{{ $inc }}</ns1:includedServies>

                                                    @endforeach 
                                            </ns1:bundledService>

                                        @endforeach
                                    </ns1:AABundledServiceExt>

                                @endforeach
                            @endif

                            @if (isset($Bundle['@attributes']))
                                <ns1:AABundledServiceExt applicableOnd="{{ $Bundle['@attributes']['applicableOnd'] }}" applicableOndSequence="0">

                                        @foreach ($bun['ns1__bundledService'] as $service)
                                        
                                            <ns1:bundledService>
                                                <ns1:bunldedServiceId>{{ $service['ns1__bunldedServiceId'] }}</ns1:bunldedServiceId>
                                                <ns1:bundledServiceName>{{ $service['ns1__bundledServiceName'] }}</ns1:bundledServiceName>
                                                <ns1:perPaxBundledFee>{{ $service['ns1__perPaxBundledFee'] }}</ns1:perPaxBundledFee>
                                                <ns1:bookingClasses>{{ $service['ns1__bookingClasses'] }}</ns1:bookingClasses>
                                                <ns1:description>{{ $service['ns1__description'] }}</ns1:description>
                                                    @foreach ($service['ns1__includedServies'] as $inc)
                                                        
                                                        <ns1:includedServies>{{ $inc }}</ns1:includedServies>

                                                    @endforeach 
                                            </ns1:bundledService>

                                        @endforeach
                                        
                                </ns1:AABundledServiceExt>
                            @endif
                        </ns1:OriginDestinationOptions>
                    </ns1:AirItinerary>
                    <ns1:AirItineraryPricingInfo PricingSource="Published">
                        <ns1:ItinTotalFare NegotiatedFare="false">
                            <ns1:BaseFare Amount="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__BaseFare']['@attributes']['Amount'] }}" CurrencyCode="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__BaseFare']['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__BaseFare']['@attributes']['DecimalPlaces'] }}" />
                            <ns1:TotalFare Amount="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__TotalFare']['@attributes']['Amount'] }}" CurrencyCode="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__TotalFare']['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__TotalFare']['@attributes']['DecimalPlaces'] }}" />
                            <ns1:TotalEquivFare Amount="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__TotalEquivFare']['@attributes']['Amount'] }}" CurrencyCode="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__TotalEquivFare']['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__TotalEquivFare']['@attributes']['DecimalPlaces'] }}" />
                            <ns1:TotalFareWithCCFee Amount="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__TotalFareWithCCFee']['@attributes']['Amount'] }}" CurrencyCode="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__TotalFareWithCCFee']['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__TotalFareWithCCFee']['@attributes']['DecimalPlaces'] }}" />
                            <ns1:TotalEquivFareWithCCFee Amount="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__TotalEquivFareWithCCFee']['@attributes']['Amount'] }}" CurrencyCode="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__TotalEquivFareWithCCFee']['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $PricingInfo['ns1__ItinTotalFare']['ns1__TotalEquivFareWithCCFee']['@attributes']['DecimalPlaces'] }}" />
                        </ns1:ItinTotalFare>
                        <ns1:PTC_FareBreakdowns>
                            @if (isset($PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['@attributes']))
                                
                                <ns1:PTC_FareBreakdown PricingSource="Published">
                                    <ns1:PassengerTypeQuantity Age="0" Code="{{ $PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerTypeQuantity']['@attributes']['Code'] }}" Quantity="{{ $PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerTypeQuantity']['@attributes']['Quantity'] }}" />
                                    <ns1:FareBasisCodes>
                                        <ns1:FareBasisCode>{{ $PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__FareBasisCodes']['ns1__FareBasisCode'] }}</ns1:FareBasisCode>
                                    </ns1:FareBasisCodes>
                                    <ns1:PassengerFare NetiatedFare="false">
                                        <ns1:BaseFare Amount="{{ $PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__BaseFare']['@attributes']['Amount'] }}" CurrencyCode="{{ $PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__BaseFare']['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__BaseFare']['@attributes']['DecimalPlaces'] }}" />
                                        <ns1:Taxes>
                                            @foreach ($PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__Taxes']['ns1__Tax'] as $tax)
                                                
                                                <ns1:Tax Amount="{{ $tax['@attributes']['Amount'] }}" CurrencyCode="{{ $tax['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $tax['@attributes']['DecimalPlaces'] }}" TaxCode="{{ $tax['@attributes']['TaxCode'] }}" TaxName="{{ $tax['@attributes']['TaxName'] }}" />

                                            @endforeach
                                        </ns1:Taxes>
                                        <ns1:Fees>
                                            @foreach ($PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__Fees']['ns1__Fee'] as $fee)
                                                
                                                <ns1:Fee Amount="{{ $fee['@attributes']['Amount'] }}" CurrencyCode="{{ $fee['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $fee['@attributes']['DecimalPlaces'] }}" FeeCode="{{ $fee['@attributes']['FeeCode'] }}" />
                                            
                                            @endforeach
                                        </ns1:Fees>
                                        <ns1:TotalFare Amount="{{ $PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__TotalFare']['@attributes']['Amount'] }}" CurrencyCode="{{ $PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__TotalFare']['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__TotalFare']['@attributes']['DecimalPlaces'] }}" />
                                    </ns1:PassengerFare>
                                    @if (isset($PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__TravelerRefNumber']['@attributes']))
                                    
                                        <ns1:TravelerRefNumber RPH="{{ $PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__TravelerRefNumber']['@attributes']['RPH'] }}" />
                                        
                                    @endif

                                    @if (isset($PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__TravelerRefNumber'][0]))
                                        @foreach ($PricingInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__TravelerRefNumber'] as $ref)
                                            
                                            <ns1:TravelerRefNumber RPH="{{ $ref['@attributes']['RPH'] }}" />

                                        @endforeach   
                                    @endif
                                </ns1:PTC_FareBreakdown>

                            @endif
                        </ns1:PTC_FareBreakdowns>
                    </ns1:AirItineraryPricingInfo>
                </ns1:PricedItinerary>
            </ns1:PricedItineraries>
            <ns1:Success />
            <ns1:Errors />
        </ns1:OTA_AirPriceRS>
    </soap:Body>
</soap:Envelope>