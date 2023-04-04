<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
    <soap:Body>
        <SearchFlightResponse xmlns="http://epowerv5.amadeus.com.tr/WS">
            <OTA_AirLowFareSearchRS Version="0">
                <HasMoreResult>false</HasMoreResult>
                <Success />
                <PricedItineraries>
                    @foreach($list as $resultsKey=>$results)
                    <PricedItinerary Currency="{{$curency}}" ProviderType="AmadeusProvider" SequenceNumber="{{$resultsKey}}">
                        <AirItinerary>
                            <OriginDestinationOptions>
                                @foreach($results['options'] as $optionsKey=>$item)
                                <OriginDestinationOption RefNumber="{{$item['RefNumber']}}" DirectionId="{{$item['DirectionId']}}" ElapsedTime="0155">
                                    <FlightSegment DepartureDateTime="{{$item['DepartureDateTime']}}" ArrivalDateTime="{{$item['ArrivalDateTime']}}" FlightNumber="{{$item['FlightNumber']}}" ResBookDesigCode="O">
                                        <FlightDuration>{{$item['FlightDuration']}}</FlightDuration>
                                        <DepartureAirport LocationCode="{{$item['DeparturLocationCode']}}" Terminal="5" />
                                        <ArrivalAirport LocationCode="{{$item['ArivelLocationCode']}}" Terminal="1" />
                                        <OperatingAirline Code="BA" />
                                        <Equipment AirEquipType="320" />
                                        <MarketingAirline Code="BA" />
                                        <Baggages>
                                            <Baggage Index="1" Type="ADT" />
                                        </Baggages>
                                        <BookingClassAvails>
                                            <BookingClassAvail ResBookDesigCode="O" ResBookDesigQuantity="9" RPH="ADT" AvailablePTC="ADT" ResBookDesigCabinCode="M" FareBasis="OZ0RO" FareType="RP" />
                                        </BookingClassAvails>
                                    </FlightSegment>
                                </OriginDestinationOption>
                                @endforeach
                            </OriginDestinationOptions>
                            <OriginDestinationCombinations>
                                @foreach($results['combinations'] as $combinationKey=>$combination)
                                <OriginDestinationCombination ValidatingAirlineCode="BA" ForceETicket="false" IndexList="{{$combination}}" CombinationID="{{$combinationKey}}" E_TicketEligibility="Eligible" ServiceFeeAmount="0" />
                                @endforeach
                            </OriginDestinationCombinations>
                        </AirItinerary>
                        <AirItineraryPricingInfo>
                            <ItinTotalFare>
                                <BaseFare Amount="{{$results['pricing']['totalbasefare']}}" />
                                <MarkupFare Amount="0" />
                                <TotalFare Amount="{{$results['pricing']['totalTotalefare']}}" />
                            </ItinTotalFare>
                            <PTC_FareBreakdowns>
                                @if(isset($passengers['adults']))
                                <PTC_FareBreakdown>
                                    <PassengerTypeQuantity Code="ADT" Quantity="{{$passengers['adults']}}" />
                                    <PassengerFare>
                                        <BaseFare Amount="{{$results['pricing']['adultBaseFare']}}" />
                                        <MarkupFare Amount="0" />
                                        <Taxes>
                                            <Tax Amount="57.14" />
                                        </Taxes>
                                        <TotalFare Amount="{{$results['pricing']['adultTotaleFare']}}" />
                                    </PassengerFare>
                                    <TicketDesignators>
                                        <TicketDesignator TicketDesignatorCode="70|PEN" TicketDesignatorExtension="TICKETS ARE NON-REFUNDABLE|" />
                                        <TicketDesignator TicketDesignatorCode="40|LTD" TicketDesignatorExtension="LAST TKT DTE|06SEP21| - SEE ADV PURCHASE|" />
                                        <TicketDesignator TicketDesignatorCode="79|SUR" TicketDesignatorExtension="FARE VALID FOR E TICKET ONLY|" />
                                    </TicketDesignators>
                                </PTC_FareBreakdown>
                                @endif
                                @if(!empty($passengers['childs']))
                                <PTC_FareBreakdown>
                                    <PassengerTypeQuantity Code="CHD" Quantity="{{$passengers['childs']}}" />
                                    <PassengerFare>
                                        <BaseFare Amount="{{$results['pricing']['childBaseFare']}}" />
                                        <MarkupFare Amount="0" />
                                        <Taxes>
                                            <Tax Amount="57.14" />
                                        </Taxes>
                                        <TotalFare Amount="{{$results['pricing']['childTotaleFare']}}" />
                                    </PassengerFare>
                                    <TicketDesignators>
                                        <TicketDesignator TicketDesignatorCode="70|PEN" TicketDesignatorExtension="TICKETS ARE NON-REFUNDABLE|" />
                                        <TicketDesignator TicketDesignatorCode="40|LTD" TicketDesignatorExtension="LAST TKT DTE|06SEP21| - SEE ADV PURCHASE|" />
                                        <TicketDesignator TicketDesignatorCode="79|SUR" TicketDesignatorExtension="FARE VALID FOR E TICKET ONLY|" />
                                    </TicketDesignators>
                                </PTC_FareBreakdown>
                                @endif
                                @if(!empty($passengers['infants']))
                                <PTC_FareBreakdown>
                                    <PassengerTypeQuantity Code="INF" Quantity="{{$passengers['infants']}}" />
                                    <PassengerFare>
                                        <BaseFare Amount="{{$results['pricing']['infantBaseFare']}}" />
                                        <MarkupFare Amount="0" />
                                        <Taxes>
                                            <Tax Amount="57.14" />
                                        </Taxes>
                                        <TotalFare Amount="{{$results['pricing']['infantTotalFare']}}" />
                                    </PassengerFare>
                                    <TicketDesignators>
                                        <TicketDesignator TicketDesignatorCode="70|PEN" TicketDesignatorExtension="TICKETS ARE NON-REFUNDABLE|" />
                                        <TicketDesignator TicketDesignatorCode="40|LTD" TicketDesignatorExtension="LAST TKT DTE|06SEP21| - SEE ADV PURCHASE|" />
                                        <TicketDesignator TicketDesignatorCode="79|SUR" TicketDesignatorExtension="FARE VALID FOR E TICKET ONLY|" />
                                    </TicketDesignators>
                                </PTC_FareBreakdown>
                                @endif
                            </PTC_FareBreakdowns>
                        </AirItineraryPricingInfo>
                    </PricedItinerary>
                    @endforeach
                    @if(!empty($onewayCombinable))

                    <PricedItineraryForOWC IsOneWayCombinable="true" Currency="{{$curency}}" SequenceNumber="{{count($list)+1}}">
                        <AirItinerary>
                            <OriginDestinationOptions>

                                @foreach($onewayCombinable['options'] as $oneCambinableKey=>$oneway)
                                <OriginDestinationOption RefNumber="{{$oneway['RefNumber']}}" DirectionId="{{$oneway['DirectionId']}}" ElapsedTime="0155" ProviderType="AmadeusProvider">
                                    <FlightSegment DepartureDateTime="{{$oneway['DepartureDateTime']}}" ArrivalDateTime="{{$oneway['ArrivalDateTime']}}" FlightNumber="{{$oneway['FlightNumber']}}" ResBookDesigCode="O">
                                        <FlightDuration>{{$oneway['FlightDuration']}}</FlightDuration>
                                        <DepartureAirport LocationCode="{{$oneway['DeparturLocationCode']}}" Terminal="5" />
                                        <ArrivalAirport LocationCode="{{$oneway['ArivelLocationCode']}}" Terminal="1" />
                                        <OperatingAirline Code="BA" />
                                        <Equipment AirEquipType="319" />
                                        <MarketingAirline Code="BA" />
                                        <Baggages>
                                            <Baggage Index="1" Type="ADT" />
                                        </Baggages>
                                        <BookingClassAvails>
                                            <BookingClassAvail ResBookDesigCode="O" ResBookDesigQuantity="5" RPH="ADT" AvailablePTC="ADT" ResBookDesigCabinCode="M" FareBasis="OZ0RO" FareType="RP" />
                                            <BookingClassAvail ResBookDesigCode="O" ResBookDesigQuantity="5" RPH="CHD" AvailablePTC="CH" ResBookDesigCabinCode="M" FareBasis="OZ0RO" FareType="RP" />
                                            <BookingClassAvail ResBookDesigCode="O" ResBookDesigQuantity="5" RPH="INF" AvailablePTC="IN" ResBookDesigCabinCode="M" FareBasis="OZ0RO" FareType="RP" />
                                        </BookingClassAvails>
                                    </FlightSegment>
                                    <OptionPricingInfo>
                                        <ItinTotalFare>
                                            <BaseFare Amount="{{$oneway['price']['totalbasefare']}}" />
                                            <MarkupFare Amount="0" />
                                            <TotalFare Amount="{{$oneway['price']['totalTotalefare']}}" />
                                        </ItinTotalFare>
                                        <PTC_FareBreakdowns>
                                            <PTC_FareBreakdown>
                                                <PassengerTypeQuantity Code="ADT" Quantity="{{$passengers['adults']}}" />
                                                <PassengerFare>
                                                    <BaseFare Amount="{{$oneway['price']['adultBaseFare']}}" />
                                                    <MarkupFare Amount="0" />
                                                    <Taxes>
                                                        <Tax Amount="57.14" />
                                                    </Taxes>
                                                    <TotalFare Amount="{{$oneway['price']['adultTotaleFare']}}" />
                                                </PassengerFare>
                                                <TicketDesignators>
                                                    <TicketDesignator TicketDesignatorCode="70|PEN" TicketDesignatorExtension="TICKETS ARE NON-REFUNDABLE|" />
                                                    <TicketDesignator TicketDesignatorCode="40|LTD" TicketDesignatorExtension="LAST TKT DTE|18OCT21| - SEE ADV PURCHASE|" />
                                                    <TicketDesignator TicketDesignatorCode="79|SUR" TicketDesignatorExtension="FARE VALID FOR E TICKET ONLY|" />
                                                </TicketDesignators>
                                            </PTC_FareBreakdown>
                                            @if(!empty($passengers['childs']))
                                            <PTC_FareBreakdown>
                                                <PassengerTypeQuantity Code="CHD" Quantity="{{$passengers['childs']}}" />
                                                <PassengerFare>
                                                    <BaseFare Amount="{{$oneway['price']['childBaseFare']}}" />
                                                    <MarkupFare Amount="0" />
                                                    <Taxes>
                                                        <Tax Amount="57.14" />
                                                    </Taxes>
                                                    <TotalFare Amount="{{$oneway['price']['childTotaleFare']}}" />
                                                </PassengerFare>
                                                <TicketDesignators>
                                                    <TicketDesignator TicketDesignatorCode="70|PEN" TicketDesignatorExtension="TICKETS ARE NON-REFUNDABLE|" />
                                                    <TicketDesignator TicketDesignatorCode="40|LTD" TicketDesignatorExtension="LAST TKT DTE|18OCT21| - SEE ADV PURCHASE|" />
                                                    <TicketDesignator TicketDesignatorCode="79|SUR" TicketDesignatorExtension="FARE VALID FOR E TICKET ONLY|" />
                                                </TicketDesignators>
                                            </PTC_FareBreakdown>
                                            @endif
                                            @if(!empty($passengers['infants']))
                                            <PTC_FareBreakdown>
                                                <PassengerTypeQuantity Code="INF" Quantity="{{$passengers['infants']}}" />
                                                <PassengerFare>
                                                    <BaseFare Amount="{{$oneway['price']['infantBaseFare']}}" />
                                                    <MarkupFare Amount="0" />
                                                    <Taxes>
                                                        <Tax Amount="57.14" />
                                                    </Taxes>
                                                    <TotalFare Amount="{{$oneway['price']['infantTotalFare']}}" />
                                                </PassengerFare>
                                                <TicketDesignators>
                                                    <TicketDesignator TicketDesignatorCode="70|PEN" TicketDesignatorExtension="TICKETS ARE NON-REFUNDABLE|" />
                                                    <TicketDesignator TicketDesignatorCode="40|LTD" TicketDesignatorExtension="LAST TKT DTE|18OCT21| - SEE ADV PURCHASE|" />
                                                    <TicketDesignator TicketDesignatorCode="79|SUR" TicketDesignatorExtension="FARE VALID FOR E TICKET ONLY|" />
                                                </TicketDesignators>
                                            </PTC_FareBreakdown>
                                            @endif
                                        </PTC_FareBreakdowns>
                                    </OptionPricingInfo>
                                </OriginDestinationOption>
                                @endforeach
                            </OriginDestinationOptions>
                            <OriginDestinationCombinations>

                                @foreach($onewayCombinable['combinations'] as $onewayCombinationCombinationKey=>$oneWayCombinableCombination)

                                <OriginDestinationCombination ValidatingAirlineCode="BA" ForceETicket="false" IndexList="{{$oneWayCombinableCombination}}" CombinationID="{{$onewayCombinationCombinationKey}}" E_TicketEligibility="Eligible" ServiceFeeAmount="0" />
                                @endforeach
                            </OriginDestinationCombinations>
                        </AirItinerary>
                    </PricedItineraryForOWC>
                    @endif
                    <FreeBaggages>
                        <Baggage Index="1" Quantity="0" Unit="PC" />
                        <Baggage Index="2" Quantity="1" Unit="PC" />
                        <Baggage Index="3" Quantity="20" Unit="KG" />
                    </FreeBaggages>
                </PricedItineraries>
            </OTA_AirLowFareSearchRS>
        </SearchFlightResponse>
    </soap:Body>
</soap:Envelope>