<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
    <soap:Body>
    <CreateTicketResponse xmlns="http://epowerv5.amadeus.com.tr/WS">
            <OTA_AirBookRS Version="0">
            <AirReservation>
                    <AirItinerary>
                        <OriginDestinationOptions>
                            @foreach($response['information']['option'] as $key=>$option)
                            <OriginDestinationOption RefNumber="{{$option['RefNumber']}}" DirectionId="{{$option['DirectionId']}}" ElapsedTime="0155">
                                <FlightSegment DepartureDateTime="{{$option['DepartureDateTime']}}" ArrivalDateTime="{{$option['ArrivalDateTime']}}" FlightNumber="{{$option['RefNumber']}}" ResBookDesigCode="O" Status="HK">
                                    <FlightDuration>{{$option['DepartureDateTime']}}</FlightDuration>
                                    <DepartureAirport LocationCode="{{$option['DeparturLocationCode']}}" Terminal="5" />
                                    <ArrivalAirport LocationCode="{{$option['ArivelLocationCode']}}" Terminal="1" />
                                    <OperatingAirline Code="BA" />
                                    <Equipment AirEquipType="319" />
                                    <MarketingAirline Code="BA" />
                                    <BookingClassAvails>
                                        <BookingClassAvail ResBookDesigCode="O" ResBookDesigQuantity="9" RPH="ADT" AvailablePTC="ADT" ResBookDesigCabinCode="M" FareBasis="OZ0R" FareType="RP" />
                                        @if(!empty($response['countMembers']['child']))
                                        <BookingClassAvail ResBookDesigCode="O" ResBookDesigQuantity="9" RPH="CHD" AvailablePTC="CH" ResBookDesigCabinCode="M" FareBasis="OZ0R" FareType="RP" />
                                        @endif
                                        @if(!empty($response['countMembers']['infant']))
                                        <BookingClassAvail ResBookDesigCode="O" ResBookDesigQuantity="9" RPH="INF" AvailablePTC="IN" ResBookDesigCabinCode="M" FareBasis="OZ0R" FareType="RP" />
                                        @endif
                                    </BookingClassAvails>
                                </FlightSegment>
                            </OriginDestinationOption>
                            @endforeach
                        </OriginDestinationOptions>
                    </AirItinerary>
                    <PriceInfo>
                        <ItinTotalFare>
                            <BaseFare Currency="{{$response['information']['pricing']['curency']}}" Amount="{{$response['information']['pricing']['totalbasefare']}}" />
                            <MarkupFare Amount="0" />
                            <TotalFare Amount="{{$response['information']['pricing']['totalTotalefare']}}" Currency="{{$response['information']['pricing']['curency']}}" />
                            <TotalAmountInTicketingCurrency Amount="{{$response['information']['pricing']['totalTotalefare']}}" Currency="{{$response['information']['pricing']['curency']}}" />
                        </ItinTotalFare>
                        <PTC_FareBreakdowns>
                            @if(!empty($response['countMembers']['adult']))
                            <PTC_FareBreakdown>
                                <PassengerTypeQuantity Code="ADT" Quantity="{{$response['countMembers']['adult']}}" />
                                <PassengerFare>
                                    <BaseFare Amount="{{$response['information']['pricing']['adultBaseFare']}}" />
                                    <MarkupFare Amount="0" />
                                    <Taxes>
                                        <Tax Amount="{{$response['information']['pricing']['taxFare']}}" />
                                    </Taxes>
                                    <TotalFare Amount="{{$response['information']['pricing']['adultTotaleFare']}}" />
                                </PassengerFare>
                                <TicketDesignators>
                                    <TicketDesignator TicketDesignatorCode="70|PEN" TicketDesignatorExtension="TICKETS ARE NON-REFUNDABLE|" />
                                    <TicketDesignator TicketDesignatorCode="40|LTD" TicketDesignatorExtension="LAST TKT DTE|04OCT21| - SEE ADV PURCHASE|" />
                                    <TicketDesignator TicketDesignatorCode="79|SUR" TicketDesignatorExtension="FARE VALID FOR E TICKET ONLY|" />
                                </TicketDesignators>
                            </PTC_FareBreakdown>
                            @endif
                            @if(!empty($countMembers['child']))
                            <PTC_FareBreakdown>
                                <PassengerTypeQuantity Code="CHD" Quantity="{{$response['countMembers']['child']}}" />
                                <PassengerFare>
                                    <BaseFare Amount="{{$response['information']['pricing']['childBaseFare']}}" />
                                    <MarkupFare Amount="0" />
                                    <Taxes>
                                        <Tax Amount="{{$response['information']['pricing']['taxFare']}}" />
                                    </Taxes>
                                    <TotalFare Amount="{{$response['information']['pricing']['childTotaleFare']}}" />
                                </PassengerFare>
                                <TicketDesignators>
                                    <TicketDesignator TicketDesignatorCode="70|PEN" TicketDesignatorExtension="TICKETS ARE NON-REFUNDABLE|" />
                                    <TicketDesignator TicketDesignatorCode="40|LTD" TicketDesignatorExtension="LAST TKT DTE|04OCT21| - SEE ADV PURCHASE|" />
                                    <TicketDesignator TicketDesignatorCode="79|SUR" TicketDesignatorExtension="FARE VALID FOR E TICKET ONLY|" />
                                </TicketDesignators>
                            </PTC_FareBreakdown>
                            @endif

                            @if(!empty($response['countMembers']['infant']))
                            <PTC_FareBreakdown>
                                <PassengerTypeQuantity Code="INF" Quantity="$response['countMembers']['infant']" />
                                <PassengerFare>
                                    <BaseFare Amount="{{$response['information']['pricing']['infantBaseFare']}}" />
                                    <MarkupFare Amount="0" />
                                    <Taxes>
                                        <Tax Amount="{{$response['information']['pricing']['taxFare']}}" />
                                    </Taxes>
                                    <TotalFare Amount="{{$response['information']['pricing']['infantTotalFare']}}" />
                                </PassengerFare>
                                <TicketDesignators>
                                    <TicketDesignator TicketDesignatorCode="70|PEN" TicketDesignatorExtension="TICKETS ARE NON-REFUNDABLE|" />
                                    <TicketDesignator TicketDesignatorCode="40|LTD" TicketDesignatorExtension="LAST TKT DTE|04OCT21| - SEE ADV PURCHASE|" />
                                    <TicketDesignator TicketDesignatorCode="79|SUR" TicketDesignatorExtension="FARE VALID FOR E TICKET ONLY|" />
                                </TicketDesignators>
                            </PTC_FareBreakdown>
                            @endif

                        </PTC_FareBreakdowns>
                        <ServiceFees>
                            <ServiceFee Amount="0" MarkupFeeAmount="0">
                                <PassengerTypeQuantity Code="ADT" Quantity="{{$response['countMembers']['adult']}}" />
                            </ServiceFee>
                            @if(!empty($response['countMembers']['child']))
                            <ServiceFee Amount="0" MarkupFeeAmount="0">
                                <PassengerTypeQuantity Code="CHD" Quantity="{{$response['countMembers']['child']}}" />
                            </ServiceFee>
                            @endif
                            @if(!empty($response['countMembers']['infant']))
                            <ServiceFee Amount="0" MarkupFeeAmount="0">
                                <PassengerTypeQuantity Code="INF" Quantity="{{$response['countMembers']['infant']}}" />
                            </ServiceFee>
                            @endif
                        </ServiceFees>
                    </PriceInfo>
                    <TravelerInfo>

                        @if(isset($response['passInfo'][0]))
                        @foreach($response['passInfo'] as $key=>$info)
                        
                        <AirTraveler PassengerTypeCode="{{$info['type']}}" eTicketNumber="{{$info['ticketNumber']}}">
                            <NumberOfBaggages>0</NumberOfBaggages>
                            <NumberOfBaggages1>0</NumberOfBaggages1>
                            <HandLuggages>0</HandLuggages>
                            <HandLuggages1>0</HandLuggages1>
                            <PersonName>
                                <NamePrefix>{{$info['namePrefix']}}</NamePrefix>
                                <GivenName>{{$info['firstName']}}</GivenName>
                                <Surname>{{$info['surname']}}</Surname>
                            </PersonName>
                            <Email />
                            <Document DocIssueLocation="" DocType="DOCO" DocIssueCountry="" DocVisaExpirationDate="0001-01-01" />
                            <Document DocType="DOCS" DocIssueCountry="" DocVisaExpirationDate="0001-01-01" />
                            <Document DocType="DOCA" DocIssueCountry="" DocVisaExpirationDate="0001-01-01" />
                            <Document DocType="DOCA2" DocIssueCountry="" DocVisaExpirationDate="0001-01-01" />
                            <BirthDate>{{$info['birthday']}}</BirthDate>
                            <eTicketDocuments>
                                <ETicketInfo TicketNumber="{{$info['ticketNumber']}}">
                                    <AgencyAddress>
                                        <AddressLine>TAJ LOGAL</AddressLine>
                                        <CityName>EBL</CityName>
                                        <CountryName Code="IQ" />
                                    </AgencyAddress>
                                    <AgencyTelNo>964\750 59220 / 08</AgencyTelNo>
                                    <IATAID>00000000</IATAID>
                                    <TicketingDate>2020-01-04T13:15:41.4930787+00:00</TicketingDate>
                                    <PassengerName_Surname>Black/John MR</PassengerName_Surname>
                                    <IssuingAirline>EK</IssuingAirline>
                                    <BookingRef>1A/JO9SWL</BookingRef>
                                    <FareCalculation>IST EK DXB350.00NUC350.00END ROE1.000000</FareCalculation>
                                    <Endorsements>WP 202911 NON-END/FLEX</Endorsements>
                                    <ExchangeRate>5.9409</ExchangeRate>
                                    <PaymentType>C</PaymentType>
                                    <AirFareCurrency>USD</AirFareCurrency>
                                    <EquivalentFare>2080.00</EquivalentFare>
                                    <EquivalentFareCurrency>TRY</EquivalentFareCurrency>
                                    <TotalFare>2919.59</TotalFare>
                                    <TotalFareCurrency>TRY</TotalFareCurrency>
                                    <Taxes>
                                        <TaxInfo>
                                            <TaxType>X</TaxType>
                                            <CountryCode>YQ</CountryCode>
                                            <Amount>712.91</Amount>
                                            <Currency>TRY</Currency>
                                            <TaxNature>AC</TaxNature>
                                        </TaxInfo>
                                        <TaxInfo>
                                            <TaxType>X</TaxType>
                                            <CountryCode>M6</CountryCode>
                                            <Amount>19.77</Amount>
                                            <Currency>TRY</Currency>
                                            <TaxNature>SE</TaxNature>
                                        </TaxInfo>
                                        <TaxInfo>
                                            <TaxType>X</TaxType>
                                            <CountryCode>TR</CountryCode>
                                            <Amount>98.82</Amount>
                                            <Currency>TRY</Currency>
                                            <TaxNature>AE</TaxNature>
                                        </TaxInfo>
                                        <TaxInfo>
                                            <TaxType>X</TaxType>
                                            <CountryCode>ZR</CountryCode>
                                            <Amount>8.09</Amount>
                                            <Currency>TRY</Currency>
                                            <TaxNature>AP</TaxNature>
                                        </TaxInfo>
                                    </Taxes>
                                    <ControlNumbers>, EK/J72MXB</ControlNumbers>
                                    <Itineraries>
                                        <ETicketItineraryInfo>
                                            <From>SAW</From>
                                            <FromTerminal />
                                            <To>DXB</To>
                                            <ToTerminal>2</ToTerminal>
                                            <Carrier>EK</Carrier>
                                            <FlightNo>2227</FlightNo>
                                            <OperatingAirlineCode>FZ</OperatingAirlineCode>
                                            <MarketingAirlineCode>EK</MarketingAirlineCode>
                                            <Class>U</Class>
                                            @if(isset($response['information']['option'][0]))
                                            <DepartureDate>{{$response['information']['option'][0]['DepartureDateTime']}}</DepartureDate>
                                            <ArrivalDate>{{$response['information']['option'][0]['ArrivalDateTime']}}</ArrivalDate>
                                            @else
                                            <DepartureDate>{{$response['information']['option']['DepartureDateTime']}}</DepartureDate>
                                            <ArrivalDate>{{$response['information']['option']['ArrivalDateTime']}}</ArrivalDate>
                                            @endif
                                            <FareBasis>ULOWSTR1</FareBasis>
                                            <BaggageWeight>30</BaggageWeight>
                                            <BaggageWeightMeasureUnit>K</BaggageWeightMeasureUnit>
                                            <Status>OK</Status>
                                        </ETicketItineraryInfo>
                                    </Itineraries>
                                </ETicketInfo>
                            </eTicketDocuments>
                        </AirTraveler>
                        @endforeach


                        @else
                        <AirTraveler PassengerTypeCode="{{$response['passInfo']['type']}}" eTicketNumber="{{$response['passInfo']['ticketNumber']}}">
                            <NumberOfBaggages>0</NumberOfBaggages>
                            <NumberOfBaggages1>0</NumberOfBaggages1>
                            <HandLuggages>0</HandLuggages>
                            <HandLuggages1>0</HandLuggages1>
                            <PersonName>
                                <NamePrefix>{{$response['passInfo']['namePrefix']}}</NamePrefix>
                                <GivenName>{{$response['passInfo']['firstName']}}</GivenName>
                                <Surname>{{$response['passInfo']['surname']}}</Surname>
                            </PersonName>
                            <Email />
                            <Document DocIssueLocation="" DocType="DOCO" DocIssueCountry="" DocVisaExpirationDate="0001-01-01" />
                            <Document DocType="DOCS" DocIssueCountry="" DocVisaExpirationDate="0001-01-01" />
                            <Document DocType="DOCA" DocIssueCountry="" DocVisaExpirationDate="0001-01-01" />
                            <Document DocType="DOCA2" DocIssueCountry="" DocVisaExpirationDate="0001-01-01" />
                            <BirthDate>{{$response['passInfo']['birthday']}}</BirthDate>
                            <eTicketDocuments>
                                <ETicketInfo TicketNumber="{{$response['passInfo']['ticketNumber']}}">
                                    <AgencyAddress>
                                        <AddressLine>TAJ LOGAL</AddressLine>
                                        <CityName>EBL</CityName>
                                        <CountryName Code="IQ" />
                                    </AgencyAddress>
                                    <AgencyTelNo>964\750 59220 / 08</AgencyTelNo>
                                    <IATAID>00000000</IATAID>
                                    <TicketingDate>2020-01-04T13:15:41.4930787+00:00</TicketingDate>
                                    <PassengerName_Surname>Black/John MR</PassengerName_Surname>
                                    <IssuingAirline>EK</IssuingAirline>
                                    <BookingRef>1A/JO9SWL</BookingRef>
                                    <FareCalculation>IST EK DXB350.00NUC350.00END ROE1.000000</FareCalculation>
                                    <Endorsements>WP 202911 NON-END/FLEX</Endorsements>
                                    <ExchangeRate>5.9409</ExchangeRate>
                                    <PaymentType>C</PaymentType>
                                    <AirFareCurrency>USD</AirFareCurrency>
                                    <EquivalentFare>2080.00</EquivalentFare>
                                    <EquivalentFareCurrency>TRY</EquivalentFareCurrency>
                                    <TotalFare>2919.59</TotalFare>
                                    <TotalFareCurrency>TRY</TotalFareCurrency>
                                    <Taxes>
                                        <TaxInfo>
                                            <TaxType>X</TaxType>
                                            <CountryCode>YQ</CountryCode>
                                            <Amount>712.91</Amount>
                                            <Currency>TRY</Currency>
                                            <TaxNature>AC</TaxNature>
                                        </TaxInfo>
                                        <TaxInfo>
                                            <TaxType>X</TaxType>
                                            <CountryCode>M6</CountryCode>
                                            <Amount>19.77</Amount>
                                            <Currency>TRY</Currency>
                                            <TaxNature>SE</TaxNature>
                                        </TaxInfo>
                                        <TaxInfo>
                                            <TaxType>X</TaxType>
                                            <CountryCode>TR</CountryCode>
                                            <Amount>98.82</Amount>
                                            <Currency>TRY</Currency>
                                            <TaxNature>AE</TaxNature>
                                        </TaxInfo>
                                        <TaxInfo>
                                            <TaxType>X</TaxType>
                                            <CountryCode>ZR</CountryCode>
                                            <Amount>8.09</Amount>
                                            <Currency>TRY</Currency>
                                            <TaxNature>AP</TaxNature>
                                        </TaxInfo>
                                    </Taxes>
                                    <ControlNumbers>, EK/J72MXB</ControlNumbers>
                                    <Itineraries>
                                        <ETicketItineraryInfo>
                                            <From>SAW</From>
                                            <FromTerminal />
                                            <To>DXB</To>
                                            <ToTerminal>2</ToTerminal>
                                            <Carrier>EK</Carrier>
                                            <FlightNo>2227</FlightNo>
                                            <OperatingAirlineCode>FZ</OperatingAirlineCode>
                                            <MarketingAirlineCode>EK</MarketingAirlineCode>
                                            <Class>U</Class>
                                            @if(isset($response['information']['option'][0]))
                                            <DepartureDate>{{$response['information']['option'][0]['DepartureDateTime']}}</DepartureDate>
                                            <ArrivalDate>{{$response['information']['option'][0]['ArrivalDateTime']}}</ArrivalDate>
                                            @else
                                            <DepartureDate>{{$response['information']['option']['DepartureDateTime']}}</DepartureDate>
                                            <ArrivalDate>{{$response['information']['option']['ArrivalDateTime']}}</ArrivalDate>
                                            @endif
                                            <FareBasis>ULOWSTR1</FareBasis>
                                            <BaggageWeight>30</BaggageWeight>
                                            <BaggageWeightMeasureUnit>K</BaggageWeightMeasureUnit>
                                            <Status>OK</Status>
                                        </ETicketItineraryInfo>
                                    </Itineraries>
                                </ETicketInfo>
                            </eTicketDocuments>
                        </AirTraveler>
                        @endif

                    </TravelerInfo>
                    <Fulfillment>
                        <PaymentDetails>
                            <PaymentDetail PaymentType="None" PaymentFPType="FPCA" />
                        </PaymentDetails>
                        <DeliveryAddress>
                            <CountryName />
                        </DeliveryAddress>
                    </Fulfillment>
                    <Ticketing TicketTimeLimit="{{$response['information']['timeTiket']}}" TicketType="BookingOnly" />
                    <BookingReferenceID Type="F" ID_Context="{{$response['information']['contexId']}}" />
                    <FlightRulePenalties>
                        <FareRuleInfo>
                            <FareReference>OZ0R</FareReference>
                            <FilingAirline Code="BA" />
                            <MarketingAirline Code="BA" />
                            <DepartureAirport LocationCode="LON" />
                            <ArrivalAirport LocationCode="MUC" />
                            <FareRules>
                                <SubSection SubTitle="PENALTIES" SubCode="PE">
                                    <Paragraph>
                                        <Text>
                                            CANCELLATIONS

                                            ANY TIME
                                            TICKET IS NON-REFUNDABLE.
                                            NOTE -
                                            FARE COMPONENT IS NON-REFUNDABLE.
                                            ----------------------------------------------
                                            WAIVED FOR DEATH OF A PASSENGER AND PASSENGERS
                                            TRAVELLING COMPANIONS.
                                            ----------------------------------------------
                                            WHEN COMBINING NON-REFUNDABLE FARES WITH
                                            REFUNDABLE FARES
                                            1. THE AMOUNT PAID ON EACH REFUNDABLE FARE
                                            COMPONENT IS REFUNDED.
                                            2. THE AMOUNT PAID ON EACH NON-REFUNDABLE FARE
                                            COMPONENT WILL NOT BE REFUNDED.
                                            3. WHEN COMBINING FARES CHARGE THE SUM OF THE
                                            CANCELLATION FEES OF ALL CANCELLED FARE
                                            COMPONENTS.
                                            ----------------------------------------------
                                            REFUND OF UNUSED TAXES FEES AND CHARGES PAID TO
                                            THIRD PARTIES PERMITTED. ASSOCIATED CARRIER
                                            IMPOSED CHARGES WILL NOT BE REFUNDED.
                                            ----------------------------------------------
                                            REFUND PERMITTED WITHIN TICKET VALIDITY.
                                            ----------------------------------------------
                                            ANY NON-REFUNDABLE AMOUNT FROM A PREVIOUS TICKET
                                            REMAINS NON-REFUNDABLE FOLLOWING A CHANGE.
                                            ----------------------------------------------
                                            TICKET IS NOT TRANSFERABLE TO ANOTHER PERSON.
                                            ----------------------------------------------
                                            -------CANCELLATION REPRICING CONDITIONS------
                                            FLOWN COUPONS MUST BE REPRICED USING HISTORICAL
                                            FARES IN EFFECT ON THE PREVIOUS TICKETING DATE.
                                            THE FARE FOR THE JOURNEY TRAVELLED MUST BE CAPPED
                                            AT THE TOTAL FARE AMOUNT PLUS CARRIER IMPOSED
                                            CHARGE PAID ON THE TICKET BEING PRESENTED FOR
                                            REFUND.
                                            FULLY FLOWN FARE COMPONENTS MAY BE REPRICED USING
                                            ANY BOOKING CODE WITHIN THE SAME CABIN PROVIDED
                                            THE NEW FARE AMOUNT IS EQUAL OR HIGHER THAN
                                            ORIGINAL.
                                            PARTIALLY FLOWN FARE COMPONENTS MUST BE REPRICED
                                            USING THE SAME OR HIGHER BOOKING CODE.

                                            CHANGES

                                            ANY TIME
                                            CHARGE GBP 60.00 FOR REISSUE/REVALIDATION.
                                            NOTE -
                                            WITH THE EXCEPTION OF TICKETS CHANGED IN THE US
                                            ANY PENALTY FEE MUST BE COLLECTED VIA AN EMD.
                                            REFER TO SPEEDBIRDCLUB.COM OR BATRAVELTRADE.COM
                                            AND YOUR GDS FOR INSTRUCTIONS.
                                            ----------------------------------------------
                                            CHARGE APPLIES PER TRANSACTION - PER PERSON FOR
                                            ALL PASSENGER TYPES.
                                            INFANTS WITHOUT A SEAT - NO CHARGE.
                                            --------------------------------------------------
                                            A CHANGE IS A DATE/FLIGHT/ROUTING/BOOKING CODE
                                            CHANGE. NEW RESERVATION AND REISSUE/REVALIDATION
                                            MUST BE MADE ON THE SAME DAY.
                                            --------------------------------------------------
                                            REISSUE MUST BE MADE THE SAME DAY AS CHANGE OF
                                            RESERVATION BUT NO LATER THAN SCHEDULED DEPARTURE
                                            TIME OF FLIGHT BEING CHANGED.
                                            OTHERWISE THE TICKET WILL ONLY BE VALID FOR
                                            REFUND IF APPLICABLE.
                                            --------------------------------------------------
                                            WHEN MORE THAN ONE FARE COMPONENT IS CHANGED THE
                                            HIGHEST PENALTY OF ALL CHANGED FARE COMPONENTS
                                            WITHIN THE JOURNEY APPLIES.
                                            --------------------------------------------------
                                            --- REPRICING CONDITIONS ---
                                            A. BEFORE DEPARTURE OF JOURNEY WHEN THE FIRST
                                            FARE COMPONENT IS CHANGED THE ITINERARY MUST BE
                                            RE-PRICED USING CURRENT FARES IN EFFECT ON THE
                                            DATE THE TICKET IS REISSUED.
                                            B. BEFORE DEPARTURE OF JOURNEY WHEN CHANGES ARE
                                            TO BOOKING CODE ONLY IN THE FIRST FARE COMPONENT
                                            AND RESULT IN A HIGHER FARE THE ITINERARY MUST BE
                                            RE-PRICED USING HISTORICAL FARES IN EFFECT ON THE
                                            PREVIOUS TICKETING DATE OR USING CURRENT FARES IN
                                            EFFECT ON THE DATE THE TICKET IS REISSUED -
                                            WHICHEVER IS LOWER.
                                            C. BEFORE DEPARTURE OF JOURNEY WHEN THERE ARE NO
                                            CHANGES TO THE FIRST FARE COMPONENT BUT OTHER
                                            FARE COMPONENTS ARE CHANGED THE ITINERARY MUST BE
                                            RE-PRICED USING HISTORICAL FARES IN EFFECT ON THE
                                            PREVIOUS TICKETING DATE OR USING CURRENT FARES IN
                                            EFFECT ON THE DATE THE TICKET IS REISSUED-
                                            WHICHEVER IS LOWER.
                                            D. AFTER DEPARTURE OF JOURNEY THE ITINERARY MUST
                                            BE RE-PRICED USING HISTORICAL FARES IN EFFECT ON
                                            THE PREVIOUS TICKETING DATE.
                                            --------------------------------------------------
                                            1. IF SAME BOOKING CLASS IS USED NEW TICKET VALUE
                                            MUST BE LOWER - EQUAL OR HIGHER THAN PREVIOUS AND
                                            MUST COMPLY WITH ALL PROVISIONS OF THE NEW FARE
                                            BEING APPLIED.
                                            2. IF A DIFFERENT BOOKING CLASS IS USED NEW
                                            TICKET VALUE MUST BE EQUAL OR HIGHER THAN
                                            PREVIOUS AND MUST COMPLY WITH ALL PROVISIONS OF
                                            THE NEW FARE BEING APPLIED.
                                            --------------------------------------------------
                                            WHEN THE ITINERARY RESULTS IN A HIGHER FARE THE
                                            DIFFERENCE WILL BE COLLECTED. ANY APPLICABLE
                                            CHANGE FEE STILL APPLIES.
                                            --------------------------------------------------
                                            ANY NON-REFUNDABLE AMOUNT FROM A PREVIOUS TICKET
                                            REMAINS NON-REFUNDABLE FOLLOWING A CHANGE.
                                            --------------------------------------------------
                                            NO-SHOWS FOR A FLIGHT ARE CONSIDERED A
                                            CANCELLATION AFTER DEPARTURE AND CHANGES ARE NOT
                                            PERMITTED.
                                            --------------------------------------------------
                                            TICKET IS NOT TRANSFERABLE TO ANOTHER PERSON.
                                        </Text>
                                    </Paragraph>
                                </SubSection>
                            </FareRules>
                        </FareRuleInfo>
                        <FareRuleInfo>
                            <FareReference>OZ0R</FareReference>
                            <FilingAirline Code="BA" />
                            <MarketingAirline Code="BA" />
                            <DepartureAirport LocationCode="MUC" />
                            <ArrivalAirport LocationCode="LON" />
                            <FareRules>
                                <SubSection SubTitle="PENALTIES" SubCode="PE">
                                    <Paragraph>
                                        <Text>
                                            CANCELLATIONS

                                            ANY TIME
                                            TICKET IS NON-REFUNDABLE.
                                            NOTE -
                                            FARE COMPONENT IS NON-REFUNDABLE.
                                            ----------------------------------------------
                                            WAIVED FOR DEATH OF A PASSENGER AND PASSENGERS
                                            TRAVELLING COMPANIONS.
                                            ----------------------------------------------
                                            WHEN COMBINING NON-REFUNDABLE FARES WITH
                                            REFUNDABLE FARES
                                            1. THE AMOUNT PAID ON EACH REFUNDABLE FARE
                                            COMPONENT IS REFUNDED.
                                            2. THE AMOUNT PAID ON EACH NON-REFUNDABLE FARE
                                            COMPONENT WILL NOT BE REFUNDED.
                                            3. WHEN COMBINING FARES CHARGE THE SUM OF THE
                                            CANCELLATION FEES OF ALL CANCELLED FARE
                                            COMPONENTS.
                                            ----------------------------------------------
                                            REFUND OF UNUSED TAXES FEES AND CHARGES PAID TO
                                            THIRD PARTIES PERMITTED. ASSOCIATED CARRIER
                                            IMPOSED CHARGES WILL NOT BE REFUNDED.
                                            ----------------------------------------------
                                            REFUND PERMITTED WITHIN TICKET VALIDITY.
                                            ----------------------------------------------
                                            ANY NON-REFUNDABLE AMOUNT FROM A PREVIOUS TICKET
                                            REMAINS NON-REFUNDABLE FOLLOWING A CHANGE.
                                            ----------------------------------------------
                                            TICKET IS NOT TRANSFERABLE TO ANOTHER PERSON.
                                            ----------------------------------------------
                                            -------CANCELLATION REPRICING CONDITIONS------
                                            FLOWN COUPONS MUST BE REPRICED USING HISTORICAL
                                            FARES IN EFFECT ON THE PREVIOUS TICKETING DATE.
                                            THE FARE FOR THE JOURNEY TRAVELLED MUST BE CAPPED
                                            AT THE TOTAL FARE AMOUNT PLUS CARRIER IMPOSED
                                            CHARGE PAID ON THE TICKET BEING PRESENTED FOR
                                            REFUND.
                                            FULLY FLOWN FARE COMPONENTS MAY BE REPRICED USING
                                            ANY BOOKING CODE WITHIN THE SAME CABIN PROVIDED
                                            THE NEW FARE AMOUNT IS EQUAL OR HIGHER THAN
                                            ORIGINAL.
                                            PARTIALLY FLOWN FARE COMPONENTS MUST BE REPRICED
                                            USING THE SAME OR HIGHER BOOKING CODE.

                                            CHANGES

                                            ANY TIME
                                            CHARGE GBP 60.00 FOR REISSUE/REVALIDATION.
                                            NOTE -
                                            WITH THE EXCEPTION OF TICKETS CHANGED IN THE US
                                            ANY PENALTY FEE MUST BE COLLECTED VIA AN EMD.
                                            REFER TO SPEEDBIRDCLUB.COM OR BATRAVELTRADE.COM
                                            AND YOUR GDS FOR INSTRUCTIONS.
                                            ----------------------------------------------
                                            CHARGE APPLIES PER TRANSACTION - PER PERSON FOR
                                            ALL PASSENGER TYPES.
                                            INFANTS WITHOUT A SEAT - NO CHARGE.
                                            --------------------------------------------------
                                            A CHANGE IS A DATE/FLIGHT/ROUTING/BOOKING CODE
                                            CHANGE. NEW RESERVATION AND REISSUE/REVALIDATION
                                            MUST BE MADE ON THE SAME DAY.
                                            --------------------------------------------------
                                            REISSUE MUST BE MADE THE SAME DAY AS CHANGE OF
                                            RESERVATION BUT NO LATER THAN SCHEDULED DEPARTURE
                                            TIME OF FLIGHT BEING CHANGED.
                                            OTHERWISE THE TICKET WILL ONLY BE VALID FOR
                                            REFUND IF APPLICABLE.
                                            --------------------------------------------------
                                            WHEN MORE THAN ONE FARE COMPONENT IS CHANGED THE
                                            HIGHEST PENALTY OF ALL CHANGED FARE COMPONENTS
                                            WITHIN THE JOURNEY APPLIES.
                                            --------------------------------------------------
                                            --- REPRICING CONDITIONS ---
                                            A. BEFORE DEPARTURE OF JOURNEY WHEN THE FIRST
                                            FARE COMPONENT IS CHANGED THE ITINERARY MUST BE
                                            RE-PRICED USING CURRENT FARES IN EFFECT ON THE
                                            DATE THE TICKET IS REISSUED.
                                            B. BEFORE DEPARTURE OF JOURNEY WHEN CHANGES ARE
                                            TO BOOKING CODE ONLY IN THE FIRST FARE COMPONENT
                                            AND RESULT IN A HIGHER FARE THE ITINERARY MUST BE
                                            RE-PRICED USING HISTORICAL FARES IN EFFECT ON THE
                                            PREVIOUS TICKETING DATE OR USING CURRENT FARES IN
                                            EFFECT ON THE DATE THE TICKET IS REISSUED -
                                            WHICHEVER IS LOWER.
                                            C. BEFORE DEPARTURE OF JOURNEY WHEN THERE ARE NO
                                            CHANGES TO THE FIRST FARE COMPONENT BUT OTHER
                                            FARE COMPONENTS ARE CHANGED THE ITINERARY MUST BE
                                            RE-PRICED USING HISTORICAL FARES IN EFFECT ON THE
                                            PREVIOUS TICKETING DATE OR USING CURRENT FARES IN
                                            EFFECT ON THE DATE THE TICKET IS REISSUED-
                                            WHICHEVER IS LOWER.
                                            D. AFTER DEPARTURE OF JOURNEY THE ITINERARY MUST
                                            BE RE-PRICED USING HISTORICAL FARES IN EFFECT ON
                                            THE PREVIOUS TICKETING DATE.
                                            --------------------------------------------------
                                            1. IF SAME BOOKING CLASS IS USED NEW TICKET VALUE
                                            MUST BE LOWER - EQUAL OR HIGHER THAN PREVIOUS AND
                                            MUST COMPLY WITH ALL PROVISIONS OF THE NEW FARE
                                            BEING APPLIED.
                                            2. IF A DIFFERENT BOOKING CLASS IS USED NEW
                                            TICKET VALUE MUST BE EQUAL OR HIGHER THAN
                                            PREVIOUS AND MUST COMPLY WITH ALL PROVISIONS OF
                                            THE NEW FARE BEING APPLIED.
                                            --------------------------------------------------
                                            WHEN THE ITINERARY RESULTS IN A HIGHER FARE THE
                                            DIFFERENCE WILL BE COLLECTED. ANY APPLICABLE
                                            CHANGE FEE STILL APPLIES.
                                            --------------------------------------------------
                                            ANY NON-REFUNDABLE AMOUNT FROM A PREVIOUS TICKET
                                            REMAINS NON-REFUNDABLE FOLLOWING A CHANGE.
                                            --------------------------------------------------
                                            NO-SHOWS FOR A FLIGHT ARE CONSIDERED A
                                            CANCELLATION AFTER DEPARTURE AND CHANGES ARE NOT
                                            PERMITTED.
                                            --------------------------------------------------
                                            TICKET IS NOT TRANSFERABLE TO ANOTHER PERSON.
                                        </Text>
                                    </Paragraph>
                                </SubSection>
                            </FareRules>
                        </FareRuleInfo>
                    </FlightRulePenalties>
                    <FlightRulePenalties>
                        <FareRuleInfo>
                            <FareReference>OZ0R</FareReference>
                            <FilingAirline Code="BA" />
                            <MarketingAirline Code="BA" />
                            <DepartureAirport LocationCode="LON" />
                            <ArrivalAirport LocationCode="MUC" />
                            <FareRules>
                                <SubSection SubTitle="PENALTIES" SubCode="PE">
                                    <Paragraph>
                                        <Text>
                                            CANCELLATIONS

                                            ANY TIME
                                            TICKET IS NON-REFUNDABLE.
                                            NOTE -
                                            FARE COMPONENT IS NON-REFUNDABLE.
                                            ----------------------------------------------
                                            WAIVED FOR DEATH OF A PASSENGER AND PASSENGERS
                                            TRAVELLING COMPANIONS.
                                            ----------------------------------------------
                                            WHEN COMBINING NON-REFUNDABLE FARES WITH
                                            REFUNDABLE FARES
                                            1. THE AMOUNT PAID ON EACH REFUNDABLE FARE
                                            COMPONENT IS REFUNDED.
                                            2. THE AMOUNT PAID ON EACH NON-REFUNDABLE FARE
                                            COMPONENT WILL NOT BE REFUNDED.
                                            3. WHEN COMBINING FARES CHARGE THE SUM OF THE
                                            CANCELLATION FEES OF ALL CANCELLED FARE
                                            COMPONENTS.
                                            ----------------------------------------------
                                            REFUND OF UNUSED TAXES FEES AND CHARGES PAID TO
                                            THIRD PARTIES PERMITTED. ASSOCIATED CARRIER
                                            IMPOSED CHARGES WILL NOT BE REFUNDED.
                                            ----------------------------------------------
                                            REFUND PERMITTED WITHIN TICKET VALIDITY.
                                            ----------------------------------------------
                                            ANY NON-REFUNDABLE AMOUNT FROM A PREVIOUS TICKET
                                            REMAINS NON-REFUNDABLE FOLLOWING A CHANGE.
                                            ----------------------------------------------
                                            TICKET IS NOT TRANSFERABLE TO ANOTHER PERSON.
                                            ----------------------------------------------
                                            -------CANCELLATION REPRICING CONDITIONS------
                                            FLOWN COUPONS MUST BE REPRICED USING HISTORICAL
                                            FARES IN EFFECT ON THE PREVIOUS TICKETING DATE.
                                            THE FARE FOR THE JOURNEY TRAVELLED MUST BE CAPPED
                                            AT THE TOTAL FARE AMOUNT PLUS CARRIER IMPOSED
                                            CHARGE PAID ON THE TICKET BEING PRESENTED FOR
                                            REFUND.
                                            FULLY FLOWN FARE COMPONENTS MAY BE REPRICED USING
                                            ANY BOOKING CODE WITHIN THE SAME CABIN PROVIDED
                                            THE NEW FARE AMOUNT IS EQUAL OR HIGHER THAN
                                            ORIGINAL.
                                            PARTIALLY FLOWN FARE COMPONENTS MUST BE REPRICED
                                            USING THE SAME OR HIGHER BOOKING CODE.

                                            CHANGES

                                            ANY TIME
                                            CHARGE GBP 60.00 FOR REISSUE/REVALIDATION.
                                            NOTE -
                                            WITH THE EXCEPTION OF TICKETS CHANGED IN THE US
                                            ANY PENALTY FEE MUST BE COLLECTED VIA AN EMD.
                                            REFER TO SPEEDBIRDCLUB.COM OR BATRAVELTRADE.COM
                                            AND YOUR GDS FOR INSTRUCTIONS.
                                            ----------------------------------------------
                                            CHARGE APPLIES PER TRANSACTION - PER PERSON FOR
                                            ALL PASSENGER TYPES.
                                            INFANTS WITHOUT A SEAT - NO CHARGE.
                                            --------------------------------------------------
                                            A CHANGE IS A DATE/FLIGHT/ROUTING/BOOKING CODE
                                            CHANGE. NEW RESERVATION AND REISSUE/REVALIDATION
                                            MUST BE MADE ON THE SAME DAY.
                                            --------------------------------------------------
                                            REISSUE MUST BE MADE THE SAME DAY AS CHANGE OF
                                            RESERVATION BUT NO LATER THAN SCHEDULED DEPARTURE
                                            TIME OF FLIGHT BEING CHANGED.
                                            OTHERWISE THE TICKET WILL ONLY BE VALID FOR
                                            REFUND IF APPLICABLE.
                                            --------------------------------------------------
                                            WHEN MORE THAN ONE FARE COMPONENT IS CHANGED THE
                                            HIGHEST PENALTY OF ALL CHANGED FARE COMPONENTS
                                            WITHIN THE JOURNEY APPLIES.
                                            --------------------------------------------------
                                            --- REPRICING CONDITIONS ---
                                            A. BEFORE DEPARTURE OF JOURNEY WHEN THE FIRST
                                            FARE COMPONENT IS CHANGED THE ITINERARY MUST BE
                                            RE-PRICED USING CURRENT FARES IN EFFECT ON THE
                                            DATE THE TICKET IS REISSUED.
                                            B. BEFORE DEPARTURE OF JOURNEY WHEN CHANGES ARE
                                            TO BOOKING CODE ONLY IN THE FIRST FARE COMPONENT
                                            AND RESULT IN A HIGHER FARE THE ITINERARY MUST BE
                                            RE-PRICED USING HISTORICAL FARES IN EFFECT ON THE
                                            PREVIOUS TICKETING DATE OR USING CURRENT FARES IN
                                            EFFECT ON THE DATE THE TICKET IS REISSUED -
                                            WHICHEVER IS LOWER.
                                            C. BEFORE DEPARTURE OF JOURNEY WHEN THERE ARE NO
                                            CHANGES TO THE FIRST FARE COMPONENT BUT OTHER
                                            FARE COMPONENTS ARE CHANGED THE ITINERARY MUST BE
                                            RE-PRICED USING HISTORICAL FARES IN EFFECT ON THE
                                            PREVIOUS TICKETING DATE OR USING CURRENT FARES IN
                                            EFFECT ON THE DATE THE TICKET IS REISSUED-
                                            WHICHEVER IS LOWER.
                                            D. AFTER DEPARTURE OF JOURNEY THE ITINERARY MUST
                                            BE RE-PRICED USING HISTORICAL FARES IN EFFECT ON
                                            THE PREVIOUS TICKETING DATE.
                                            --------------------------------------------------
                                            1. IF SAME BOOKING CLASS IS USED NEW TICKET VALUE
                                            MUST BE LOWER - EQUAL OR HIGHER THAN PREVIOUS AND
                                            MUST COMPLY WITH ALL PROVISIONS OF THE NEW FARE
                                            BEING APPLIED.
                                            2. IF A DIFFERENT BOOKING CLASS IS USED NEW
                                            TICKET VALUE MUST BE EQUAL OR HIGHER THAN
                                            PREVIOUS AND MUST COMPLY WITH ALL PROVISIONS OF
                                            THE NEW FARE BEING APPLIED.
                                            --------------------------------------------------
                                            WHEN THE ITINERARY RESULTS IN A HIGHER FARE THE
                                            DIFFERENCE WILL BE COLLECTED. ANY APPLICABLE
                                            CHANGE FEE STILL APPLIES.
                                            --------------------------------------------------
                                            ANY NON-REFUNDABLE AMOUNT FROM A PREVIOUS TICKET
                                            REMAINS NON-REFUNDABLE FOLLOWING A CHANGE.
                                            --------------------------------------------------
                                            NO-SHOWS FOR A FLIGHT ARE CONSIDERED A
                                            CANCELLATION AFTER DEPARTURE AND CHANGES ARE NOT
                                            PERMITTED.
                                            --------------------------------------------------
                                            TICKET IS NOT TRANSFERABLE TO ANOTHER PERSON.
                                        </Text>
                                    </Paragraph>
                                </SubSection>
                            </FareRules>
                        </FareRuleInfo>
                        <FareRuleInfo>
                            <FareReference>OZ0R</FareReference>
                            <FilingAirline Code="BA" />
                            <MarketingAirline Code="BA" />
                            <DepartureAirport LocationCode="MUC" />
                            <ArrivalAirport LocationCode="LON" />
                            <FareRules>
                                <SubSection SubTitle="PENALTIES" SubCode="PE">
                                    <Paragraph>
                                        <Text>
                                            CANCELLATIONS

                                            ANY TIME
                                            TICKET IS NON-REFUNDABLE.
                                            NOTE -
                                            FARE COMPONENT IS NON-REFUNDABLE.
                                            ----------------------------------------------
                                            WAIVED FOR DEATH OF A PASSENGER AND PASSENGERS
                                            TRAVELLING COMPANIONS.
                                            ----------------------------------------------
                                            WHEN COMBINING NON-REFUNDABLE FARES WITH
                                            REFUNDABLE FARES
                                            1. THE AMOUNT PAID ON EACH REFUNDABLE FARE
                                            COMPONENT IS REFUNDED.
                                            2. THE AMOUNT PAID ON EACH NON-REFUNDABLE FARE
                                            COMPONENT WILL NOT BE REFUNDED.
                                            3. WHEN COMBINING FARES CHARGE THE SUM OF THE
                                            CANCELLATION FEES OF ALL CANCELLED FARE
                                            COMPONENTS.
                                            ----------------------------------------------
                                            REFUND OF UNUSED TAXES FEES AND CHARGES PAID TO
                                            THIRD PARTIES PERMITTED. ASSOCIATED CARRIER
                                            IMPOSED CHARGES WILL NOT BE REFUNDED.
                                            ----------------------------------------------
                                            REFUND PERMITTED WITHIN TICKET VALIDITY.
                                            ----------------------------------------------
                                            ANY NON-REFUNDABLE AMOUNT FROM A PREVIOUS TICKET
                                            REMAINS NON-REFUNDABLE FOLLOWING A CHANGE.
                                            ----------------------------------------------
                                            TICKET IS NOT TRANSFERABLE TO ANOTHER PERSON.
                                            ----------------------------------------------
                                            -------CANCELLATION REPRICING CONDITIONS------
                                            FLOWN COUPONS MUST BE REPRICED USING HISTORICAL
                                            FARES IN EFFECT ON THE PREVIOUS TICKETING DATE.
                                            THE FARE FOR THE JOURNEY TRAVELLED MUST BE CAPPED
                                            AT THE TOTAL FARE AMOUNT PLUS CARRIER IMPOSED
                                            CHARGE PAID ON THE TICKET BEING PRESENTED FOR
                                            REFUND.
                                            FULLY FLOWN FARE COMPONENTS MAY BE REPRICED USING
                                            ANY BOOKING CODE WITHIN THE SAME CABIN PROVIDED
                                            THE NEW FARE AMOUNT IS EQUAL OR HIGHER THAN
                                            ORIGINAL.
                                            PARTIALLY FLOWN FARE COMPONENTS MUST BE REPRICED
                                            USING THE SAME OR HIGHER BOOKING CODE.

                                            CHANGES

                                            ANY TIME
                                            CHARGE GBP 60.00 FOR REISSUE/REVALIDATION.
                                            NOTE -
                                            WITH THE EXCEPTION OF TICKETS CHANGED IN THE US
                                            ANY PENALTY FEE MUST BE COLLECTED VIA AN EMD.
                                            REFER TO SPEEDBIRDCLUB.COM OR BATRAVELTRADE.COM
                                            AND YOUR GDS FOR INSTRUCTIONS.
                                            ----------------------------------------------
                                            CHARGE APPLIES PER TRANSACTION - PER PERSON FOR
                                            ALL PASSENGER TYPES.
                                            INFANTS WITHOUT A SEAT - NO CHARGE.
                                            --------------------------------------------------
                                            A CHANGE IS A DATE/FLIGHT/ROUTING/BOOKING CODE
                                            CHANGE. NEW RESERVATION AND REISSUE/REVALIDATION
                                            MUST BE MADE ON THE SAME DAY.
                                            --------------------------------------------------
                                            REISSUE MUST BE MADE THE SAME DAY AS CHANGE OF
                                            RESERVATION BUT NO LATER THAN SCHEDULED DEPARTURE
                                            TIME OF FLIGHT BEING CHANGED.
                                            OTHERWISE THE TICKET WILL ONLY BE VALID FOR
                                            REFUND IF APPLICABLE.
                                            --------------------------------------------------
                                            WHEN MORE THAN ONE FARE COMPONENT IS CHANGED THE
                                            HIGHEST PENALTY OF ALL CHANGED FARE COMPONENTS
                                            WITHIN THE JOURNEY APPLIES.
                                            --------------------------------------------------
                                            --- REPRICING CONDITIONS ---
                                            A. BEFORE DEPARTURE OF JOURNEY WHEN THE FIRST
                                            FARE COMPONENT IS CHANGED THE ITINERARY MUST BE
                                            RE-PRICED USING CURRENT FARES IN EFFECT ON THE
                                            DATE THE TICKET IS REISSUED.
                                            B. BEFORE DEPARTURE OF JOURNEY WHEN CHANGES ARE
                                            TO BOOKING CODE ONLY IN THE FIRST FARE COMPONENT
                                            AND RESULT IN A HIGHER FARE THE ITINERARY MUST BE
                                            RE-PRICED USING HISTORICAL FARES IN EFFECT ON THE
                                            PREVIOUS TICKETING DATE OR USING CURRENT FARES IN
                                            EFFECT ON THE DATE THE TICKET IS REISSUED -
                                            WHICHEVER IS LOWER.
                                            C. BEFORE DEPARTURE OF JOURNEY WHEN THERE ARE NO
                                            CHANGES TO THE FIRST FARE COMPONENT BUT OTHER
                                            FARE COMPONENTS ARE CHANGED THE ITINERARY MUST BE
                                            RE-PRICED USING HISTORICAL FARES IN EFFECT ON THE
                                            PREVIOUS TICKETING DATE OR USING CURRENT FARES IN
                                            EFFECT ON THE DATE THE TICKET IS REISSUED-
                                            WHICHEVER IS LOWER.
                                            D. AFTER DEPARTURE OF JOURNEY THE ITINERARY MUST
                                            BE RE-PRICED USING HISTORICAL FARES IN EFFECT ON
                                            THE PREVIOUS TICKETING DATE.
                                            --------------------------------------------------
                                            1. IF SAME BOOKING CLASS IS USED NEW TICKET VALUE
                                            MUST BE LOWER - EQUAL OR HIGHER THAN PREVIOUS AND
                                            MUST COMPLY WITH ALL PROVISIONS OF THE NEW FARE
                                            BEING APPLIED.
                                            2. IF A DIFFERENT BOOKING CLASS IS USED NEW
                                            TICKET VALUE MUST BE EQUAL OR HIGHER THAN
                                            PREVIOUS AND MUST COMPLY WITH ALL PROVISIONS OF
                                            THE NEW FARE BEING APPLIED.
                                            --------------------------------------------------
                                            WHEN THE ITINERARY RESULTS IN A HIGHER FARE THE
                                            DIFFERENCE WILL BE COLLECTED. ANY APPLICABLE
                                            CHANGE FEE STILL APPLIES.
                                            --------------------------------------------------
                                            ANY NON-REFUNDABLE AMOUNT FROM A PREVIOUS TICKET
                                            REMAINS NON-REFUNDABLE FOLLOWING A CHANGE.
                                            --------------------------------------------------
                                            NO-SHOWS FOR A FLIGHT ARE CONSIDERED A
                                            CANCELLATION AFTER DEPARTURE AND CHANGES ARE NOT
                                            PERMITTED.
                                            --------------------------------------------------
                                            TICKET IS NOT TRANSFERABLE TO ANOTHER PERSON.
                                        </Text>
                                    </Paragraph>
                                </SubSection>
                            </FareRules>
                        </FareRuleInfo>
                    </FlightRulePenalties>
                    <FlightRulePenalties>
                        <FareRuleInfo>
                            <FareReference>OZ0R</FareReference>
                            <FilingAirline Code="BA" />
                            <MarketingAirline Code="BA" />
                            <DepartureAirport LocationCode="LON" />
                            <ArrivalAirport LocationCode="MUC" />
                            <FareRules>
                                <SubSection SubTitle="PENALTIES" SubCode="PE">
                                    <Paragraph>
                                        <Text>
                                            CANCELLATIONS

                                            ANY TIME
                                            TICKET IS NON-REFUNDABLE.
                                            NOTE -
                                            FARE COMPONENT IS NON-REFUNDABLE.
                                            ----------------------------------------------
                                            WAIVED FOR DEATH OF A PASSENGER AND PASSENGERS
                                            TRAVELLING COMPANIONS.
                                            ----------------------------------------------
                                            WHEN COMBINING NON-REFUNDABLE FARES WITH
                                            REFUNDABLE FARES
                                            1. THE AMOUNT PAID ON EACH REFUNDABLE FARE
                                            COMPONENT IS REFUNDED.
                                            2. THE AMOUNT PAID ON EACH NON-REFUNDABLE FARE
                                            COMPONENT WILL NOT BE REFUNDED.
                                            3. WHEN COMBINING FARES CHARGE THE SUM OF THE
                                            CANCELLATION FEES OF ALL CANCELLED FARE
                                            COMPONENTS.
                                            ----------------------------------------------
                                            REFUND OF UNUSED TAXES FEES AND CHARGES PAID TO
                                            THIRD PARTIES PERMITTED. ASSOCIATED CARRIER
                                            IMPOSED CHARGES WILL NOT BE REFUNDED.
                                            ----------------------------------------------
                                            REFUND PERMITTED WITHIN TICKET VALIDITY.
                                            ----------------------------------------------
                                            ANY NON-REFUNDABLE AMOUNT FROM A PREVIOUS TICKET
                                            REMAINS NON-REFUNDABLE FOLLOWING A CHANGE.
                                            ----------------------------------------------
                                            TICKET IS NOT TRANSFERABLE TO ANOTHER PERSON.
                                            ----------------------------------------------
                                            -------CANCELLATION REPRICING CONDITIONS------
                                            FLOWN COUPONS MUST BE REPRICED USING HISTORICAL
                                            FARES IN EFFECT ON THE PREVIOUS TICKETING DATE.
                                            THE FARE FOR THE JOURNEY TRAVELLED MUST BE CAPPED
                                            AT THE TOTAL FARE AMOUNT PLUS CARRIER IMPOSED
                                            CHARGE PAID ON THE TICKET BEING PRESENTED FOR
                                            REFUND.
                                            FULLY FLOWN FARE COMPONENTS MAY BE REPRICED USING
                                            ANY BOOKING CODE WITHIN THE SAME CABIN PROVIDED
                                            THE NEW FARE AMOUNT IS EQUAL OR HIGHER THAN
                                            ORIGINAL.
                                            PARTIALLY FLOWN FARE COMPONENTS MUST BE REPRICED
                                            USING THE SAME OR HIGHER BOOKING CODE.

                                            CHANGES

                                            ANY TIME
                                            CHARGE GBP 60.00 FOR REISSUE/REVALIDATION.
                                            NOTE -
                                            WITH THE EXCEPTION OF TICKETS CHANGED IN THE US
                                            ANY PENALTY FEE MUST BE COLLECTED VIA AN EMD.
                                            REFER TO SPEEDBIRDCLUB.COM OR BATRAVELTRADE.COM
                                            AND YOUR GDS FOR INSTRUCTIONS.
                                            ----------------------------------------------
                                            CHARGE APPLIES PER TRANSACTION - PER PERSON FOR
                                            ALL PASSENGER TYPES.
                                            INFANTS WITHOUT A SEAT - NO CHARGE.
                                            --------------------------------------------------
                                            A CHANGE IS A DATE/FLIGHT/ROUTING/BOOKING CODE
                                            CHANGE. NEW RESERVATION AND REISSUE/REVALIDATION
                                            MUST BE MADE ON THE SAME DAY.
                                            --------------------------------------------------
                                            REISSUE MUST BE MADE THE SAME DAY AS CHANGE OF
                                            RESERVATION BUT NO LATER THAN SCHEDULED DEPARTURE
                                            TIME OF FLIGHT BEING CHANGED.
                                            OTHERWISE THE TICKET WILL ONLY BE VALID FOR
                                            REFUND IF APPLICABLE.
                                            --------------------------------------------------
                                            WHEN MORE THAN ONE FARE COMPONENT IS CHANGED THE
                                            HIGHEST PENALTY OF ALL CHANGED FARE COMPONENTS
                                            WITHIN THE JOURNEY APPLIES.
                                            --------------------------------------------------
                                            --- REPRICING CONDITIONS ---
                                            A. BEFORE DEPARTURE OF JOURNEY WHEN THE FIRST
                                            FARE COMPONENT IS CHANGED THE ITINERARY MUST BE
                                            RE-PRICED USING CURRENT FARES IN EFFECT ON THE
                                            DATE THE TICKET IS REISSUED.
                                            B. BEFORE DEPARTURE OF JOURNEY WHEN CHANGES ARE
                                            TO BOOKING CODE ONLY IN THE FIRST FARE COMPONENT
                                            AND RESULT IN A HIGHER FARE THE ITINERARY MUST BE
                                            RE-PRICED USING HISTORICAL FARES IN EFFECT ON THE
                                            PREVIOUS TICKETING DATE OR USING CURRENT FARES IN
                                            EFFECT ON THE DATE THE TICKET IS REISSUED -
                                            WHICHEVER IS LOWER.
                                            C. BEFORE DEPARTURE OF JOURNEY WHEN THERE ARE NO
                                            CHANGES TO THE FIRST FARE COMPONENT BUT OTHER
                                            FARE COMPONENTS ARE CHANGED THE ITINERARY MUST BE
                                            RE-PRICED USING HISTORICAL FARES IN EFFECT ON THE
                                            PREVIOUS TICKETING DATE OR USING CURRENT FARES IN
                                            EFFECT ON THE DATE THE TICKET IS REISSUED-
                                            WHICHEVER IS LOWER.
                                            D. AFTER DEPARTURE OF JOURNEY THE ITINERARY MUST
                                            BE RE-PRICED USING HISTORICAL FARES IN EFFECT ON
                                            THE PREVIOUS TICKETING DATE.
                                            --------------------------------------------------
                                            1. IF SAME BOOKING CLASS IS USED NEW TICKET VALUE
                                            MUST BE LOWER - EQUAL OR HIGHER THAN PREVIOUS AND
                                            MUST COMPLY WITH ALL PROVISIONS OF THE NEW FARE
                                            BEING APPLIED.
                                            2. IF A DIFFERENT BOOKING CLASS IS USED NEW
                                            TICKET VALUE MUST BE EQUAL OR HIGHER THAN
                                            PREVIOUS AND MUST COMPLY WITH ALL PROVISIONS OF
                                            THE NEW FARE BEING APPLIED.
                                            --------------------------------------------------
                                            WHEN THE ITINERARY RESULTS IN A HIGHER FARE THE
                                            DIFFERENCE WILL BE COLLECTED. ANY APPLICABLE
                                            CHANGE FEE STILL APPLIES.
                                            --------------------------------------------------
                                            ANY NON-REFUNDABLE AMOUNT FROM A PREVIOUS TICKET
                                            REMAINS NON-REFUNDABLE FOLLOWING A CHANGE.
                                            --------------------------------------------------
                                            NO-SHOWS FOR A FLIGHT ARE CONSIDERED A
                                            CANCELLATION AFTER DEPARTURE AND CHANGES ARE NOT
                                            PERMITTED.
                                            --------------------------------------------------
                                            TICKET IS NOT TRANSFERABLE TO ANOTHER PERSON.
                                        </Text>
                                    </Paragraph>
                                </SubSection>
                            </FareRules>
                        </FareRuleInfo>
                        <FareRuleInfo>
                            <FareReference>OZ0R</FareReference>
                            <FilingAirline Code="BA" />
                            <MarketingAirline Code="BA" />
                            <DepartureAirport LocationCode="MUC" />
                            <ArrivalAirport LocationCode="LON" />
                            <FareRules>
                                <SubSection SubTitle="PENALTIES" SubCode="PE">
                                    <Paragraph>
                                        <Text>
                                            CANCELLATIONS

                                            ANY TIME
                                            TICKET IS NON-REFUNDABLE.
                                            NOTE -
                                            FARE COMPONENT IS NON-REFUNDABLE.
                                            ----------------------------------------------
                                            WAIVED FOR DEATH OF A PASSENGER AND PASSENGERS
                                            TRAVELLING COMPANIONS.
                                            ----------------------------------------------
                                            WHEN COMBINING NON-REFUNDABLE FARES WITH
                                            REFUNDABLE FARES
                                            1. THE AMOUNT PAID ON EACH REFUNDABLE FARE
                                            COMPONENT IS REFUNDED.
                                            2. THE AMOUNT PAID ON EACH NON-REFUNDABLE FARE
                                            COMPONENT WILL NOT BE REFUNDED.
                                            3. WHEN COMBINING FARES CHARGE THE SUM OF THE
                                            CANCELLATION FEES OF ALL CANCELLED FARE
                                            COMPONENTS.
                                            ----------------------------------------------
                                            REFUND OF UNUSED TAXES FEES AND CHARGES PAID TO
                                            THIRD PARTIES PERMITTED. ASSOCIATED CARRIER
                                            IMPOSED CHARGES WILL NOT BE REFUNDED.
                                            ----------------------------------------------
                                            REFUND PERMITTED WITHIN TICKET VALIDITY.
                                            ----------------------------------------------
                                            ANY NON-REFUNDABLE AMOUNT FROM A PREVIOUS TICKET
                                            REMAINS NON-REFUNDABLE FOLLOWING A CHANGE.
                                            ----------------------------------------------
                                            TICKET IS NOT TRANSFERABLE TO ANOTHER PERSON.
                                            ----------------------------------------------
                                            -------CANCELLATION REPRICING CONDITIONS------
                                            FLOWN COUPONS MUST BE REPRICED USING HISTORICAL
                                            FARES IN EFFECT ON THE PREVIOUS TICKETING DATE.
                                            THE FARE FOR THE JOURNEY TRAVELLED MUST BE CAPPED
                                            AT THE TOTAL FARE AMOUNT PLUS CARRIER IMPOSED
                                            CHARGE PAID ON THE TICKET BEING PRESENTED FOR
                                            REFUND.
                                            FULLY FLOWN FARE COMPONENTS MAY BE REPRICED USING
                                            ANY BOOKING CODE WITHIN THE SAME CABIN PROVIDED
                                            THE NEW FARE AMOUNT IS EQUAL OR HIGHER THAN
                                            ORIGINAL.
                                            PARTIALLY FLOWN FARE COMPONENTS MUST BE REPRICED
                                            USING THE SAME OR HIGHER BOOKING CODE.

                                            CHANGES

                                            ANY TIME
                                            CHARGE GBP 60.00 FOR REISSUE/REVALIDATION.
                                            NOTE -
                                            WITH THE EXCEPTION OF TICKETS CHANGED IN THE US
                                            ANY PENALTY FEE MUST BE COLLECTED VIA AN EMD.
                                            REFER TO SPEEDBIRDCLUB.COM OR BATRAVELTRADE.COM
                                            AND YOUR GDS FOR INSTRUCTIONS.
                                            ----------------------------------------------
                                            CHARGE APPLIES PER TRANSACTION - PER PERSON FOR
                                            ALL PASSENGER TYPES.
                                            INFANTS WITHOUT A SEAT - NO CHARGE.
                                            --------------------------------------------------
                                            A CHANGE IS A DATE/FLIGHT/ROUTING/BOOKING CODE
                                            CHANGE. NEW RESERVATION AND REISSUE/REVALIDATION
                                            MUST BE MADE ON THE SAME DAY.
                                            --------------------------------------------------
                                            REISSUE MUST BE MADE THE SAME DAY AS CHANGE OF
                                            RESERVATION BUT NO LATER THAN SCHEDULED DEPARTURE
                                            TIME OF FLIGHT BEING CHANGED.
                                            OTHERWISE THE TICKET WILL ONLY BE VALID FOR
                                            REFUND IF APPLICABLE.
                                            --------------------------------------------------
                                            WHEN MORE THAN ONE FARE COMPONENT IS CHANGED THE
                                            HIGHEST PENALTY OF ALL CHANGED FARE COMPONENTS
                                            WITHIN THE JOURNEY APPLIES.
                                            --------------------------------------------------
                                            --- REPRICING CONDITIONS ---
                                            A. BEFORE DEPARTURE OF JOURNEY WHEN THE FIRST
                                            FARE COMPONENT IS CHANGED THE ITINERARY MUST BE
                                            RE-PRICED USING CURRENT FARES IN EFFECT ON THE
                                            DATE THE TICKET IS REISSUED.
                                            B. BEFORE DEPARTURE OF JOURNEY WHEN CHANGES ARE
                                            TO BOOKING CODE ONLY IN THE FIRST FARE COMPONENT
                                            AND RESULT IN A HIGHER FARE THE ITINERARY MUST BE
                                            RE-PRICED USING HISTORICAL FARES IN EFFECT ON THE
                                            PREVIOUS TICKETING DATE OR USING CURRENT FARES IN
                                            EFFECT ON THE DATE THE TICKET IS REISSUED -
                                            WHICHEVER IS LOWER.
                                            C. BEFORE DEPARTURE OF JOURNEY WHEN THERE ARE NO
                                            CHANGES TO THE FIRST FARE COMPONENT BUT OTHER
                                            FARE COMPONENTS ARE CHANGED THE ITINERARY MUST BE
                                            RE-PRICED USING HISTORICAL FARES IN EFFECT ON THE
                                            PREVIOUS TICKETING DATE OR USING CURRENT FARES IN
                                            EFFECT ON THE DATE THE TICKET IS REISSUED-
                                            WHICHEVER IS LOWER.
                                            D. AFTER DEPARTURE OF JOURNEY THE ITINERARY MUST
                                            BE RE-PRICED USING HISTORICAL FARES IN EFFECT ON
                                            THE PREVIOUS TICKETING DATE.
                                            --------------------------------------------------
                                            1. IF SAME BOOKING CLASS IS USED NEW TICKET VALUE
                                            MUST BE LOWER - EQUAL OR HIGHER THAN PREVIOUS AND
                                            MUST COMPLY WITH ALL PROVISIONS OF THE NEW FARE
                                            BEING APPLIED.
                                            2. IF A DIFFERENT BOOKING CLASS IS USED NEW
                                            TICKET VALUE MUST BE EQUAL OR HIGHER THAN
                                            PREVIOUS AND MUST COMPLY WITH ALL PROVISIONS OF
                                            THE NEW FARE BEING APPLIED.
                                            --------------------------------------------------
                                            WHEN THE ITINERARY RESULTS IN A HIGHER FARE THE
                                            DIFFERENCE WILL BE COLLECTED. ANY APPLICABLE
                                            CHANGE FEE STILL APPLIES.
                                            --------------------------------------------------
                                            ANY NON-REFUNDABLE AMOUNT FROM A PREVIOUS TICKET
                                            REMAINS NON-REFUNDABLE FOLLOWING A CHANGE.
                                            --------------------------------------------------
                                            NO-SHOWS FOR A FLIGHT ARE CONSIDERED A
                                            CANCELLATION AFTER DEPARTURE AND CHANGES ARE NOT
                                            PERMITTED.
                                            --------------------------------------------------
                                            TICKET IS NOT TRANSFERABLE TO ANOTHER PERSON.
                                        </Text>
                                    </Paragraph>
                                </SubSection>
                            </FareRules>
                        </FareRuleInfo>
                    </FlightRulePenalties>
                    <FlightMiniRules />
                    <PriceMessageInfo>
                        <PriceMessageInfo>
                            <MiniRulesPriceMessages>
                                <Text />
                            </MiniRulesPriceMessages>
                        </PriceMessageInfo>
                    </PriceMessageInfo>
                    <PNRRemarks>
                        <PNRRemark RemarkType="RM" RemarkCategory="" Note="MEMBER_NAME:FirstName LastName" />
                        <PNRRemark RemarkType="RM" RemarkCategory="D" Note="DELIVERY TYPE: BookingOnly" />
                        <PNRRemark RemarkType="RM" RemarkCategory="A" Note="PORTAL: wssomariste" />
                        <PNRRemark RemarkType="RM" RemarkCategory="E" Note="FARE: 425.40 USD" />
                        <PNRRemark RemarkType="RM" RemarkCategory="G" Note="PAYMENT TYPE: DEFAULT" />
                        <PNRRemark RemarkType="RC" RemarkCategory="" Note="FLIGHT ADT TICKETFEE:USD 199.50" />
                        <PNRRemark RemarkType="RC" RemarkCategory="" Note="FLIGHT CHD TICKETFEE:USD 181.70" />
                        <PNRRemark RemarkType="RC" RemarkCategory="" Note="FLIGHT INF TICKETFEE:USD 44.20" />
                        <PNRRemark RemarkType="RM" RemarkCategory="" Note="ASF FLIGHT TOTAL:USD 425.40" />
                        <PNRRemark RemarkType="RM" RemarkCategory="" Note="ASF PNR TOTAL:USD 425.40" />
                        <PNRRemark RemarkType="RC" RemarkCategory="" Note="PRICING COMMANDFXP/R,UP,FC-USD,VC-BA,EBL.EBL/LI" />
                    </PNRRemarks>
                </AirReservation>
                <Success />
            </OTA_AirBookRS>
        </CreateTicketResponse>
    </soap:Body>
</soap:Envelope>