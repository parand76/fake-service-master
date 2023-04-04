<OTA_AirLowFareSearchRS xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="50987" TimeStamp="2021-11-28T08:09:01.096Z" Target="Test" Version="2.001" SequenceNmbr="1" PrimaryLangID="En-us">
    <Success />
    <PricedItineraries>
        <PricedItinerary SequenceNumber="1">
            <AirItinerary>
                <OriginDestinationOptions>
                    <OriginDestinationOption>
                        <FlightSegment FlightNumber="{{$result['options']['FlightNumber']}}" ResBookDesigCode="{{$result['options']['ResBookDesigCode']}}" DepartureDateTime="{{$result['options']['DepartureDateTime']}}" ArrivalDateTime="{{$result['options']['ArrivalDateTime']}}" Duration="{{$result['options']['FlightDuration']}}" StopQuantity="0" RPH="{{$result['options']['RPH']}}">
                            <DepartureAirport LocationCode="{{$result['options']['DeparturLocationCode']}}" />
                            <ArrivalAirport LocationCode="{{$result['options']['ArivelLocationCode']}}" />
                            <OperatingAirline Code="{{$result['options']['OperatingAirline']}}" />
                            <Equipment AirEquipType="{{$result['options']['AirEquipType']}}" />
                            <BookingClassAvails>
                                <BookingClassAvail ResBookDesigCode="{{$result['options']['ResBookDesigCode']}}" ResBookDesigQuantity="{{$result['options']['ResBookDesigQuantity']}}" />
                            </BookingClassAvails>
                        </FlightSegment>
                    </OriginDestinationOption>
                    @if($result['options']['tripType']=="twoWay")
                    <OriginDestinationOption>
                        <FlightSegment FlightNumber="{{$result['options']['FlightNumberBack']}}" ResBookDesigCode="{{$result['options']['ResBookDesigCodeBack']}}" DepartureDateTime="{{$result['options']['DepartureDateTimeBack']}}" ArrivalDateTime="{{$result['options']['ArrivalDateTimeBack']}}" Duration="{{$result['options']['FlightDurationBack']}}" StopQuantity="0" RPH="{{$result['options']['RPHBack']}}">
                            <DepartureAirport LocationCode="{{$result['options']['DeparturLocationCodeBack']}}" />
                            <ArrivalAirport LocationCode="{{$result['options']['ArivelLocationCodeBack']}}" />
                            <OperatingAirline Code="{{$result['options']['OperatingAirline']}}" />
                            <Equipment AirEquipType="{{$result['options']['AirEquipType']}}" />
                            <BookingClassAvails>
                                <BookingClassAvail ResBookDesigCode="{{$result['options']['ResBookDesigCodeBack']}}" ResBookDesigQuantity="{{$result['options']['ResBookDesigQuantityBack']}}" />
                            </BookingClassAvails>
                        </FlightSegment>
                    </OriginDestinationOption>
                    @endif
                </OriginDestinationOptions>
            </AirItinerary>
            <AirItineraryPricingInfo>
                <ItinTotalFare>

                    <BaseFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['totalbasefare']}}"/>
                    <TotalFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['totalTotalefare']}}"/>
                </ItinTotalFare>
                <PTC_FareBreakdowns>
                    <PTC_FareBreakdown>
                        <PassengerTypeQuantity Code="ADT" Quantity="{{$result['passengers']['adult']}}"/>
                        <FareBasisCodes>
                            <FareBasisCode FlightSegmentRPH="30566" fareRPH="22233">A10OW</FareBasisCode>
                            <FareBasisCode FlightSegmentRPH="31423" fareRPH="22250">A10OW</FareBasisCode>
                        </FareBasisCodes>
                        <PassengerFare>
                            <BaseFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['adultBaseFare']}}"/>
                            <Taxes>
                                <Tax TaxCode="B_S_D" TaxName="BGW Dom Surcharge" CurrencyCode="USD" DecimalPlaces="2">{{$result['pricing']['taxFare1']}}</Tax>
                                <Tax TaxCode="BGWD1" TaxName="BGW Dom Departure Tax" CurrencyCode="USD" DecimalPlaces="2">{{$result['pricing']['taxFare2']}}</Tax>
                                <Tax TaxCode="E_S_D" TaxName="EBL Dom Surcharge" CurrencyCode="USD" DecimalPlaces="2">{{$result['pricing']['taxFare3']}}</Tax>
                                <Tax TaxCode="EBLD1" TaxName="EBL_DOMESTIC" CurrencyCode="USD" DecimalPlaces="2">{{$result['pricing']['taxFare4']}}</Tax>
                            </Taxes>
                            <TotalFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['adultTotaleFare']}}"/>
                            <FareBaggageAllowance FlightSegmentRPH="30566" UnitOfMeasureQuantity="25" UnitOfMeasure="KG" UnitOfMeasureCode="16"/>
                            <FareBaggageAllowance FlightSegmentRPH="31423" UnitOfMeasureQuantity="25" UnitOfMeasure="KG" UnitOfMeasureCode="16"/>
                        </PassengerFare>
                    </PTC_FareBreakdown>
                    @if($result['passengers']['child']!=0)
                    <PTC_FareBreakdown>
                        <PassengerTypeQuantity Code="CHD" Quantity="{{$result['passengers']['child']}}"/>
                        <FareBasisCodes>
                            <FareBasisCode FlightSegmentRPH="30566" fareRPH="22233">A10OW</FareBasisCode>
                            <FareBasisCode FlightSegmentRPH="31423" fareRPH="22250">A10OW</FareBasisCode>
                        </FareBasisCodes>
                        <PassengerFare>
                            <BaseFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['childBaseFare']}}"/>
                            <Taxes>
                                <Tax TaxCode="B_S_D" TaxName="BGW Dom Surcharge" CurrencyCode="USD" DecimalPlaces="2">{{$result['pricing']['taxFare1']}}</Tax>
                                <Tax TaxCode="BGWD1" TaxName="BGW Dom Departure Tax" CurrencyCode="USD" DecimalPlaces="2">{{$result['pricing']['taxFare2']}}</Tax>
                                <Tax TaxCode="E_S_D" TaxName="EBL Dom Surcharge" CurrencyCode="USD" DecimalPlaces="2">{{$result['pricing']['taxFare3']}}</Tax>
                                <Tax TaxCode="EBLD1" TaxName="EBL_DOMESTIC" CurrencyCode="USD" DecimalPlaces="2">{{$result['pricing']['taxFare4']}}</Tax>
                            </Taxes>
                            <TotalFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['childTotaleFare']}}"/>
                            <FareBaggageAllowance FlightSegmentRPH="30566" UnitOfMeasureQuantity="25" UnitOfMeasure="KG" UnitOfMeasureCode="16"/>
                            <FareBaggageAllowance FlightSegmentRPH="31423" UnitOfMeasureQuantity="25" UnitOfMeasure="KG" UnitOfMeasureCode="16"/>
                        </PassengerFare>
                    </PTC_FareBreakdown>
                    @endif
                    @if($result['passengers']['infant']!=0)
                    <PTC_FareBreakdown>
                        <PassengerTypeQuantity Code="INF" Quantity="{{$result['passengers']['infant']}}"/>
                        <FareBasisCodes>
                            <FareBasisCode FlightSegmentRPH="30566" fareRPH="22233">A10OW</FareBasisCode>
                            <FareBasisCode FlightSegmentRPH="31423" fareRPH="22250">A10OW</FareBasisCode>
                        </FareBasisCodes>
                        <PassengerFare>
                            <BaseFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['infantBaseFare']}}"/>
                            <TotalFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['infantTotalFare']}}"/>
                            <FareBaggageAllowance FlightSegmentRPH="30566" UnitOfMeasureQuantity="0" UnitOfMeasure="KG" UnitOfMeasureCode="16"/>
                            <FareBaggageAllowance FlightSegmentRPH="31423" UnitOfMeasureQuantity="0" UnitOfMeasure="KG" UnitOfMeasureCode="16"/>
                        </PassengerFare>
                    </PTC_FareBreakdown>
                    @endif
                </PTC_FareBreakdowns>
                <FareInfos>
                    <FareInfo>
                        <RuleInfo>
                            <ChargesRules>
                                <VoluntaryChanges VolChangeInd="true">
                                    <Penalty PenaltyType="REVAL_REISSUE" DepartureStatus="BEFORE" CurrencyCode="USD" Amount="20"/>
                                </VoluntaryChanges>
                                <VoluntaryRefunds VolChangeInd="true">
                                    <Penalty PenaltyType="CANCEL_REFUND" DepartureStatus="BEFORE" CurrencyCode="USD" Amount="25"/>
                                </VoluntaryRefunds>
                            </ChargesRules>
                        </RuleInfo>
                        <DepartureAirport LocationCode="BGW"/>
                        <ArrivalAirport LocationCode="EBL"/>
                    </FareInfo>
                    <FareInfo>
                        <RuleInfo>
                            <ChargesRules>
                                <VoluntaryChanges VolChangeInd="true">
                                    <Penalty PenaltyType="REVAL_REISSUE" DepartureStatus="BEFORE" CurrencyCode="USD" Amount="20"/>
                                </VoluntaryChanges>
                                <VoluntaryRefunds VolChangeInd="true">
                                    <Penalty PenaltyType="CANCEL_REFUND" DepartureStatus="BEFORE" CurrencyCode="USD" Amount="25"/>
                                </VoluntaryRefunds>
                            </ChargesRules>
                        </RuleInfo>
                        <DepartureAirport LocationCode="EBL"/>
                        <ArrivalAirport LocationCode="BGW"/>
                    </FareInfo>
                </FareInfos>
            </AirItineraryPricingInfo>
        </PricedItinerary>
    </PricedItineraries>
</OTA_AirLowFareSearchRS>