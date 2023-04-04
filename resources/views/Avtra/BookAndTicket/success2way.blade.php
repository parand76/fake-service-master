<OTA_AirBookRS xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="50987" TimeStamp="2021-11-30T05:38:36.107Z" Target="Test" Version="2.001" SequenceNmbr="1" PrimaryLangID="En-us"><Success />
 <AirReservation CreatedDateTme="{{$result['options']['CreatedDateTme']}}">
        <AirItinerary DirectionInd="{{$result['options']['DirectionInd']}}">
            <OriginDestinationOptions>
                <OriginDestinationOption>
                    <FlightSegment Status="{{$result['options']['status']}}" FlightNumber="{{$result['options']['FlightNumber']}}" FareBasisCode="{{$result['options']['FareBasisCode']}}" ResBookDesigCode="{{$result['options']['ResBookDesigCode']}}" DepartureDateTime="{{$result['options']['DepartureDateTime']}}" ArrivalDateTime="{{$result['options']['ArrivalDateTime']}}" StopQuantity="0" RPH="{{$result['options']['RPH']}}">
                        <DepartureAirport LocationCode="{{$result['options']['OriginLocationCode']}}" LocationName="{{$result['options']['DepartureAirport']}}" />
                        <ArrivalAirport LocationCode="{{$result['options']['ArivalLocationCode']}}" LocationName="{{$result['options']['ArrivalAirport']}}" />
                        <OperatingAirline Code="{{$result['options']['OperatingAirlineCode']}}" />
                        <Equipment AirEquipType="{{$result['options']['AirEquipType']}}" />
                    </FlightSegment>
                    @if(isset($result['options']['statusBack']))
                    <FlightSegment Status="{{$result['options']['statusBack']}}" FlightNumber="{{$result['options']['FlightNumberBack']}}" FareBasisCode="{{$result['options']['FareBasisCodeBack']}}" ResBookDesigCode="{{$result['options']['ResBookDesigCode']}}" DepartureDateTime="{{$result['options']['DepartureDateTimeBack']}}" ArrivalDateTime="{{$result['options']['ArrivalDateTimeBack']}}" StopQuantity="0" RPH="{{$result['options']['RPHBack']}}">
                        <DepartureAirport LocationCode="{{$result['options']['OriginLocationCodeBack']}}" LocationName="{{$result['options']['DepartureAirportBack']}}" />
                        <ArrivalAirport LocationCode="{{$result['options']['ArivalLocationCodeBack']}}" LocationName="{{$result['options']['ArrivalAirportBack']}}" />
                        <OperatingAirline Code="{{$result['options']['OperatingAirlineCodeBack']}}" />
                        <Equipment AirEquipType="{{$result['options']['AirEquipTypeBack']}}" />
                    </FlightSegment>
                    @endif
                </OriginDestinationOption>
            </OriginDestinationOptions>
        </AirItinerary>

        <ArrangerInfo>
            <CompanyInfo CompanyShortName="ALRAWDATAIN OTA" Code="ALRAWDATAINOTA" />
        </ArrangerInfo>
        <PriceInfo>
            <ItinTotalFare>
                <TotalFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['totalTotalefare']}}" />
            </ItinTotalFare>
            <PTC_FareBreakdowns>
                @if(!empty($result['passengers']['infant']))
                <PTC_FareBreakdown>
                    <PassengerTypeQuantity Code="INF" Quantity="$result['passengers']['count']['infant']" />
                    <FareBasisCodes>
                        <FareBasisCode>A8OW</FareBasisCode>
                        <FareBasisCode>A10OW</FareBasisCode>
                    </FareBasisCodes>
                    <PassengerFare>
                        <BaseFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['infantBaseFare']}}" />
                        <Taxes />
                        <TotalFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['infantTotalFare']}}" />
                    </PassengerFare>
                    <FareInfo>
                        <FareInfo FareBasisCode="A8OW">
                            <Fare BaseAmount="{{$result['pricing']['infantTotalFare']}}" />
                        </FareInfo>
                    </FareInfo>
                    <FareInfo>
                        <FareInfo FareBasisCode="A10OW">
                            <Fare BaseAmount="{{$result['pricing']['infantTotalFare']}}" />
                        </FareInfo>
                    </FareInfo>
                </PTC_FareBreakdown>
                @endif
                @if(!empty($result['passengers']['child']))
                <PTC_FareBreakdown>
                    <PassengerTypeQuantity Code="CHD" Quantity="{{$result['passengers']['count']['child']}}" />
                    <FareBasisCodes>
                        <FareBasisCode>A8OW</FareBasisCode>
                        <FareBasisCode>A10OW</FareBasisCode>
                    </FareBasisCodes>
                    <PassengerFare>
                        <BaseFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['childBaseFare']}}" />
                        <Taxes>
                            <Tax TaxCode="B_S_D" TaxName="BGW Dom Surcharge" CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['taxFare1']}}" />
                            <Tax TaxCode="E_S_D" TaxName="EBL Dom Surcharge" CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['taxFare2']}}" />
                            <Tax TaxCode="EBLD1" TaxName="EBL_DOMESTIC" CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['taxFare3']}}" />
                            <Tax TaxCode="BGWD1" TaxName="BGW Dom Departure Tax" CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['taxFare4']}}" />
                        </Taxes>
                        <TotalFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['childTotaleFare']}}" />
                    </PassengerFare>
                    <FareInfo>
                        <FareInfo FareBasisCode="A8OW">
                            <Fare BaseAmount="{{$result['pricing']['childBaseFare']}}" />
                        </FareInfo>
                    </FareInfo>
                    <FareInfo>
                        <FareInfo FareBasisCode="A10OW">
                            <Fare BaseAmount="26.90" />
                        </FareInfo>
                    </FareInfo>
                </PTC_FareBreakdown>
                @endif
                <PTC_FareBreakdown>
                    <PassengerTypeQuantity Code="ADT" Quantity="{{$result['passengers']['count']['adult']}}" />
                    <FareBasisCodes>
                        <FareBasisCode>A8OW</FareBasisCode>
                        <FareBasisCode>A10OW</FareBasisCode>
                    </FareBasisCodes>
                    <PassengerFare>
                        <BaseFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['adultBaseFare']}}" />
                        <Taxes>
                            <Tax TaxCode="B_S_D" TaxName="BGW Dom Surcharge" CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['taxFare1']}}" />
                            <Tax TaxCode="E_S_D" TaxName="EBL Dom Surcharge" CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['taxFare2']}}" />
                            <Tax TaxCode="EBLD1" TaxName="EBL_DOMESTIC" CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['taxFare3']}}" />
                            <Tax TaxCode="BGWD1" TaxName="BGW Dom Departure Tax" CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['taxFare4']}}" />
                        </Taxes>
                        <TotalFare CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['adultTotaleFare']}}" />
                    </PassengerFare>
                    <FareInfo>
                        <FareInfo FareBasisCode="A8OW">
                            <Fare BaseAmount="68.90" />
                        </FareInfo>
                    </FareInfo>
                    <FareInfo>
                        <FareInfo FareBasisCode="A10OW">
                            <Fare BaseAmount="35.90" />
                        </FareInfo>
                    </FareInfo>
                </PTC_FareBreakdown>
            </PTC_FareBreakdowns>
        </PriceInfo>
        <TravelerInfo>

            @foreach($result['passengers']['adult'] as $adult)
            <AirTraveler BirthDate="{{$adult['generalInfo']['BirthDate']}}" PassengerTypeCode="ADT" AccompaniedByInfantInd="{{$adult['generalInfo']['AccompaniedByInfantInd']}}" TravelerNationality="IQ" Gender="{{$adult['generalInfo']['Gender']}}">
                <PersonName>
                    <NamePrefix>{{$adult['nameInfo']['NamePrefix']}}</NamePrefix>
                    <GivenName>{{$adult['nameInfo']['GivenName']}}</GivenName>
                    <Surname>{{$adult['nameInfo']['Surname']}}</Surname>
                </PersonName>
                <Document DocID="{{$adult['docsInfo']['DocID']}}" DocType="{{$adult['docsInfo']['DocType']}}" DocIssueCountry="{{$adult['docsInfo']['DocIssueCountry']}}" DocHolderNationality="{{$adult['docsInfo']['DocHolderNationality']}}" EffectiveDate="{{$adult['docsInfo']['EffectiveDate']}}" ExpireDate="{{$adult['docsInfo']['ExpireDate']}}" />
                <TravelerRefNumber RPH="1" />
            </AirTraveler>
            @endforeach

            @if(!empty($result['passengers']['child']))
            @foreach($result['passengers']['child'] as $child)
            <AirTraveler BirthDate="{{$child['generalInfo']['BirthDate']}}" PassengerTypeCode="CHD" AccompaniedByInfantInd="{{$child['generalInfo']['AccompaniedByInfantInd']}}" TravelerNationality="{{$child['generalInfo']['TravelerNationality']}}" Gender="{{$child['generalInfo']['Gender']}}">
                <PersonName>
                    <NamePrefix>{{$child['nameInfo']['NamePrefix']}}</NamePrefix>
                    <GivenName>{{$child['nameInfo']['GivenName']}}</GivenName>
                    <Surname>{{$child['nameInfo']['Surname']}}</Surname>
                </PersonName>
                <TravelerRefNumber RPH="2" />
            </AirTraveler>
            @endforeach
            @endif


            @if(!empty($result['passengers']['infant']))
            @foreach($result['passengers']['infant'] as $infant)
            <AirTraveler BirthDate="{{$infant['generalInfo']['BirthDate']}}" PassengerTypeCode="INF" AccompaniedByInfantInd="{{$infant['generalInfo']['AccompaniedByInfantInd']}}" TravelerNationality="{{$infant['generalInfo']['TravelerNationality']}}" Gender="{{$infant['generalInfo']['Gender']}}">
                <PersonName>
                    <NamePrefix>{{$infant['nameInfo']['NamePrefix']}}</NamePrefix>
                    <GivenName>{{$infant['nameInfo']['GivenName']}}</GivenName>
                    <Surname>{{$infant['nameInfo']['Surname']}}</Surname>
                </PersonName>
                <TravelerRefNumber RPH="3" ParentRPH="1" />
            </AirTraveler>
            @endforeach
            @endif
        </TravelerInfo>

        <ContactPerson>

            <PersonName>
                <GivenName>{{$result['passengers']['adult'][0]['nameInfo']['GivenName']}}</GivenName>
                <Surname>{{$result['passengers']['adult'][0]['nameInfo']['Surname']}}</Surname>
            </PersonName>
            <Telephone PhoneNumber="(44)1233222344" />
            <HomeTelephone PhoneNumber="(44)1233225744" />
            <Email>tba@tba.com</Email>
        </ContactPerson>
        @if(isset($result['ticketsInfo']['ticketLimit']))
        
        <Fulfillment>
            <PaymentDetails />
        </Fulfillment>
        <Ticketing TicketTimeLimit="{{$result['ticketsInfo']['ticketLimit']}}" TicketingStatus="1" />
        <BalanceInfo CurrencyCode="{{$result['pricing']['curency']}}" DecimalPlaces="2" Amount="212.5" />
        <BalanceInfoInAgentCurrency CurrencyCode="{{$result['pricing']['curency']}}" DecimalPlaces="2" Amount="{{$result['pricing']['totalTotalefare']}}" />
        <BookingReferenceID Status="1" Instance="0" ID="{{$result['ticketsInfo']['idTicket']}}" ID_Context="BookingRef" />
        <Offer />
        @else
        <Fulfillment>
            <PaymentDetails>
                <PaymentDetail PaymentType="2">
                    <DirectBill DirectBill_ID="ALRAWDATAINOTA">
                        <CompanyName CompanyShortName="ALRAWDATAIN OTA" Code="ALRAWDATAINOTA" AgentType="TRVL_AGNT" />
                    </DirectBill>
                    <PaymentAmount CurrencyCode="USD" DecimalPlaces="2" Amount="{{$result['pricing']['totalTotalefare']}}" />
                </PaymentDetail>
            </PaymentDetails>
        </Fulfillment>
        @foreach($result['ticketsInfo']['tickets'] as $key=>$ti)
        <Ticketing TravelerRefNumber="{{$key+1}}" TicketDocumentNbr="{{$ti}}" />
        @endforeach
        <BookingReferenceID Status="39" Instance="0" ID="{{$result['ticketsInfo']['idTicket']}}" ID_Context="BookingRef" />
        <Offer />
        @endIf
    </AirReservation>
</OTA_AirBookRS>