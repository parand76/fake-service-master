<OTA_AirBookRS xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="50987" TimeStamp="2021-11-29T07:50:47.730Z" Target="Test" Version="2.001" SequenceNmbr="1" PrimaryLangID="En-us">
    <Success/>
    <AirReservation CreatedDateTme="2021-11-29T07:50:47.730Z">
        <AirItinerary DirectionInd="OneWay">
            <OriginDestinationOptions>
                <OriginDestinationOption>
                    <FlightSegment Status="39" FlightNumber="IF701" FareBasisCode="A10OW" ResBookDesigCode="A10" DepartureDateTime="2021-12-01T09:00:00.000+03:00" ArrivalDateTime="2021-12-01T10:00:00.000+03:00" StopQuantity="0" RPH="1556174">
                        <DepartureAirport LocationCode="BGW" LocationName="Baghdad International Airport"/>
                        <ArrivalAirport LocationCode="EBL" LocationName="Erbil International Airport"/>
                        <OperatingAirline Code="IF"/>
                        <Equipment AirEquipType="737-700"/>
                    </FlightSegment>
                </OriginDestinationOption>
            </OriginDestinationOptions>
        </AirItinerary>
        <ArrangerInfo>
            <CompanyInfo CompanyShortName="ALRAWDATAIN OTA" Code="ALRAWDATAINOTA"/>
        </ArrangerInfo>
        <PriceInfo>
            <ItinTotalFare>
                <TotalFare CurrencyCode="USD" DecimalPlaces="2" Amount="69.90"/>
            </ItinTotalFare>
            <PTC_FareBreakdowns>
                <PTC_FareBreakdown>
                    <PassengerTypeQuantity Code="ADT" Quantity="1"/>
                    <FareBasisCodes>
                        <FareBasisCode>A10OW</FareBasisCode>
                    </FareBasisCodes>
                    <PassengerFare>
                        <BaseFare CurrencyCode="USD" DecimalPlaces="2" Amount="48.90"/>
                        <Taxes>
                            <Tax TaxCode="B_S_D" TaxName="BGW Dom Surcharge" CurrencyCode="USD" DecimalPlaces="2" Amount="12.00"/>
                            <Tax TaxCode="BGWD1" TaxName="BGW Dom Departure Tax" CurrencyCode="USD" DecimalPlaces="2" Amount="9.00"/>
                        </Taxes>
                        <TotalFare CurrencyCode="USD" DecimalPlaces="2" Amount="69.90"/>
                    </PassengerFare>
                    <FareInfo>
                        <FareInfo FareBasisCode="A10OW">
                            <Fare BaseAmount="48.90"/>
                        </FareInfo>
                    </FareInfo>
                </PTC_FareBreakdown>
            </PTC_FareBreakdowns>
        </PriceInfo>
        <TravelerInfo>
            <AirTraveler BirthDate="1974-04-16" PassengerTypeCode="ADT" AccompaniedByInfantInd="false" TravelerNationality="IQ" Gender="M">
                <PersonName>
                    <NamePrefix>Mr</NamePrefix>
                    <GivenName>AHMED</GivenName>
                    <Surname>MOHAMMED</Surname>
                </PersonName>
                <Document DocID="E123675422" DocType="2" DocIssueCountry="IQ" DocHolderNationality="IQ" EffectiveDate="2019-10-15" ExpireDate="2029-09-15"/>
                <TravelerRefNumber RPH="1"/>
            </AirTraveler>
        </TravelerInfo>
        <ContactPerson>
            <PersonName>
                <GivenName>AHMED</GivenName>
                <Surname>MOHAMMED</Surname>
            </PersonName>
            <Telephone PhoneNumber="(44)1233222344"/>
            <HomeTelephone PhoneNumber="(44)1233225744"/>
            <Email>tba@tba.com</Email>
        </ContactPerson>
        <Fulfillment>
            <PaymentDetails>
                <PaymentDetail PaymentType="2">
                    <DirectBill DirectBill_ID="ALRAWDATAINOTA">
                        <CompanyName CompanyShortName="ALRAWDATAIN OTA" Code="ALRAWDATAINOTA" AgentType="TRVL_AGNT"/>
                    </DirectBill>
                    <PaymentAmount CurrencyCode="USD" DecimalPlaces="2" Amount="69.90"/>
                </PaymentDetail>
            </PaymentDetails>
        </Fulfillment>
        <Ticketing TravelerRefNumber="1" TicketDocumentNbr="0170110760218"/>
        <BookingReferenceID Status="39" Instance="0" ID="AKR2WI" ID_Context="BookingRef"/>
        <Offer/>
    </AirReservation>
</OTA_AirBookRS>