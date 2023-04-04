<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <soap:Body xmlns:ns1="http://www.opentravel.org/OTA/2003/05">
        <ns1:OTA_AirBookRS Cancel="false" EchoToken="11835513596097885191423" PrimaryLangID="en-us" RetransmissionIndicator="false" SequenceNmbr="1" TransactionIdentifier="TID$16320317938532162.demo2015" Version="2006.01">
            <ns1:AirReservation>
                <ns1:AirItinerary>
                    <ns1:OriginDestinationOptions>
                        @foreach($OriginDestination as $OrDe)
                            <ns1:OriginDestinationOption>
                                <ns1:FlightSegment ArrivalDateTime="{{ $OrDe['ns2__FlightSegment']['@attributes']['ArrivalDateTime'] }}" DepartureDateTime="{{ $OrDe['ns2__FlightSegment']['@attributes']['DepartureDateTime'] }}" FlightNumber="{{ $OrDe['ns2__FlightSegment']['@attributes']['FlightNumber'] }}" RPH="{{ $OrDe['ns2__FlightSegment']['@attributes']['RPH'] }}" ResCabinClass="Y" Status="35" returnFlag="false">
                                    <ns1:DepartureAirport LocationCode="{{ $OrDe['ns2__FlightSegment']['ns2__DepartureAirport']['@attributes']['LocationCode'] }}" Terminal="TerminalX" />
                                    <ns1:ArrivalAirport LocationCode="{{ $OrDe['ns2__FlightSegment']['ns2__ArrivalAirport']['@attributes']['LocationCode'] }}" />
                                    <ns1:Comment></ns1:Comment>
                                </ns1:FlightSegment>
                            </ns1:OriginDestinationOption>
                        @endforeach
                    </ns1:OriginDestinationOptions>
                </ns1:AirItinerary>
                <ns1:PriceInfo RepriceRequired="false">
                    <ns1:ItinTotalFare NegotiatedFare="false">
                        <ns1:BaseFare Amount="{{ $PriceInfo['ns1__ItinTotalFare']['ns1__BaseFare']['@attributes']['Amount'] }}" CurrencyCode="USD" DecimalPlaces="2" />
                            @isset($PriceInfo['ns1__ItinTotalFare']['ns1__Taxes'])
                                <ns1:Taxes>
                                    @foreach ($PriceInfo['ns1__ItinTotalFare']['ns1__Taxes']['ns1__Tax'] as $tax) 
                                        <ns1:Tax Amount="{{ $tax['@attributes']['Amount'] }}" CurrencyCode="{{ $tax['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $tax['@attributes']['DecimalPlaces'] }}" TaxCode="{{ $tax['@attributes']['TaxCode'] }}" />
                                    @endforeach
                                </ns1:Taxes>
                            @endisset
                            @isset($PriceInfo['ns1__ItinTotalFare']['ns1__Taxes'])
                                <ns1:Fees>
                                    @foreach ($PriceInfo['ns1__ItinTotalFare']['ns1__Fees']['ns1__Fee'] as $fee) 
                                        <ns1:Fee Amount="{{ $fee['@attributes']['Amount'] }}" CurrencyCode="{{ $fee['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $fee['@attributes']['DecimalPlaces'] }}" FeeCode="{{ $fee['@attributes']['FeeCode'] }}" />
                                    @endforeach
                                </ns1:Fees>
                            @endisset 
                        <ns1:TotalFare Amount="{{ $PriceInfo['ns1__ItinTotalFare']['ns1__TotalFare']['@attributes']['Amount'] }}" CurrencyCode="USD" DecimalPlaces="2" />
                        <ns1:TotalEquivFare Amount="{{ $PriceInfo['ns1__ItinTotalFare']['ns1__TotalEquivFare']['@attributes']['Amount'] }}" CurrencyCode="USD" DecimalPlaces="2" />
                    </ns1:ItinTotalFare>
                    <ns1:PTC_FareBreakdowns>
                        @isset($PriceInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['@attributes'])
                            <ns1:PTC_FareBreakdown>
                                <ns1:PassengerTypeQuantity Age="0" Code="ADT" Quantity="{{ $PriceInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerTypeQuantity']['@attributes']['Quantity'] }}" />
                                <ns1:FareBasisCodes>
                                    <ns1:FareBasisCode>P</ns1:FareBasisCode>
                                </ns1:FareBasisCodes>
                                <ns1:PassengerFare NegotiatedFare="false">
                                    <ns1:BaseFare Amount="{{ $PriceInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__BaseFare']['@attributes']['Amount'] }}" CurrencyCode="USD" DecimalPlaces="2" />
                                    <ns1:Taxes>
                                        @foreach ($PriceInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__Taxes']['ns1__Tax'] as $tax)
                                            <ns1:Tax Amount="{{ $tax['@attributes']['Amount'] }}" CurrencyCode="{{ $tax['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $tax['@attributes']['DecimalPlaces'] }}" TaxCode="{{ $tax['@attributes']['TaxCode'] }}" TaxName="{{ $tax['@attributes']['TaxName'] }}" />
                                        @endforeach
                                    </ns1:Taxes>
                                    <ns1:Fees>
                                        @foreach ($PriceInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__Fees']['ns1__Fee'] as $fee)
                                            <ns1:Fee Amount="{{ $fee['@attributes']['Amount'] }}" CurrencyCode="{{ $fee['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $fee['@attributes']['DecimalPlaces'] }}" FeeCode="{{ $fee['@attributes']['FeeCode'] }}">YR</ns1:Fee>
                                        @endforeach
                                    </ns1:Fees>
                                    <ns1:TotalFare Amount="{{ $PriceInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__TotalFare']['@attributes']['Amount'] }}" CurrencyCode="{{ $PriceInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__TotalFare']['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $PriceInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__TotalFare']['@attributes']['DecimalPlaces'] }}" />
                                </ns1:PassengerFare>
                                <ns1:TravelerRefNumber RPH="RB|A1$2686008" />
                            </ns1:PTC_FareBreakdown>
                        @endisset
                        @isset($PriceInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown'][0])
                            @foreach ($PriceInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown'] as $down)
                                    <ns1:PTC_FareBreakdown>
                                        <ns1:PassengerTypeQuantity Age="0" Code="{{ $down['ns1__PassengerTypeQuantity']['@attributes']['Code'] }}" Quantity="{{ $down['ns1__PassengerTypeQuantity']['@attributes']['Quantity'] }}" />
                                        <ns1:FareBasisCodes>
                                            <ns1:FareBasisCode>P</ns1:FareBasisCode>
                                        </ns1:FareBasisCodes>
                                        <ns1:PassengerFare NegotiatedFare="false">
                                            <ns1:BaseFare Amount="{{ $down['ns1__PassengerFare']['ns1__BaseFare']['@attributes']['Amount'] }}" CurrencyCode="USD" DecimalPlaces="2" />
                                            <ns1:Taxes>
                                                @foreach ($down['ns1__PassengerFare']['ns1__Taxes']['ns1__Tax'] as $tax)
                                                    <ns1:Tax Amount="{{ $tax['@attributes']['Amount'] }}" CurrencyCode="{{ $tax['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $tax['@attributes']['DecimalPlaces'] }}" TaxCode="{{ $tax['@attributes']['TaxCode'] }}" TaxName="{{ $tax['@attributes']['TaxName'] }}" />
                                                @endforeach
                                            </ns1:Taxes>
                                            <ns1:Fees>
                                                @foreach ($down['ns1__PassengerFare']['ns1__Fees']['ns1__Fee'] as $fee)
                                                    <ns1:Fee Amount="{{ $fee['@attributes']['Amount'] }}" CurrencyCode="{{ $fee['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $fee['@attributes']['DecimalPlaces'] }}" FeeCode="{{ $fee['@attributes']['FeeCode'] }}">YR</ns1:Fee>
                                                @endforeach
                                            </ns1:Fees>
                                            <ns1:TotalFare Amount="{{ $down['ns1__PassengerFare']['ns1__TotalFare']['@attributes']['Amount'] }}" CurrencyCode="{{ $PriceInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__TotalFare']['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $PriceInfo['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__TotalFare']['@attributes']['DecimalPlaces'] }}" />
                                        </ns1:PassengerFare>
                                        <ns1:TravelerRefNumber RPH="RB|A1$2686008" />
                                    </ns1:PTC_FareBreakdown>
                            @endforeach  
                        @endisset
                    </ns1:PTC_FareBreakdowns>
                </ns1:PriceInfo>
                <ns1:TravelerInfo>
                    @if (isset($TravelerInfo['ns2__AirTraveler']['@attributes']))
                        <ns1:AirTraveler AccompaniedByInfant="false" PassengerTypeCode="{{ $TravelerInfo['ns2__AirTraveler']['@attributes']['PassengerTypeCode'] }}">
                            <ns1:PersonName>
                                <ns1:GivenName>{{ $TravelerInfo['ns2__AirTraveler']['ns2__PersonName']['ns2__GivenName'] }}</ns1:GivenName>
                                <ns1:Surname>{{ $TravelerInfo['ns2__AirTraveler']['ns2__PersonName']['ns2__Surname'] }}</ns1:Surname>
                                <ns1:NameTitle>{{ $TravelerInfo['ns2__AirTraveler']['ns2__PersonName']['ns2__NameTitle'] }}</ns1:NameTitle>
                            </ns1:PersonName>
                            <ns1:Telephone DefaultInd="false" FormattedInd="false" PhoneNumber="" />
                            <ns1:Document DocHolderNationality="{{ $TravelerInfo['ns2__AirTraveler']['ns2__Document']['@attributes']['DocHolderNationality'] }}" DocID="{{ $TravelerInfo['ns2__AirTraveler']['ns2__Document']['@attributes']['DocID'] }}" DocIssueCountry="{{ $TravelerInfo['ns2__AirTraveler']['ns2__Document']['@attributes']['DocIssueCountry'] }}" DocType="{{ $TravelerInfo['ns2__AirTraveler']['ns2__Document']['@attributes']['DocType'] }}" ExpireDate="{{ $TravelerInfo['ns2__AirTraveler']['ns2__Document']['@attributes']['ExpireDate'] }}" />
                            <ns1:TravelerRefNumber RPH="RB|A1$2686008" />
                            <ns1:ETicketInfo>
                                <ns1:ETicketInfomation couponNo="1" eTicketNo="{{ $TicketInfo['TicketNo'] }}" flightSegmentCode="{{ $TicketInfo['FlightSegment'] }}" flightSegmentRPH="3259644" status="O" usedStatus="UNUSED" />
                            </ns1:ETicketInfo>
                        </ns1:AirTraveler>        
                    @endif
                    @if(isset($TravelerInfo['ns2__AirTraveler'][0]))
                        @foreach($TravelerInfo['ns2__AirTraveler'] as $key => $traveler)
                            <ns1:AirTraveler AccompaniedByInfant="false" PassengerTypeCode="{{ $traveler['@attributes']['PassengerTypeCode'] }}">
                                <ns1:PersonName>
                                    <ns1:GivenName>{{ $traveler['ns2__PersonName']['ns2__GivenName'] }}</ns1:GivenName>
                                    <ns1:Surname>{{ $traveler['ns2__PersonName']['ns2__Surname'] }}</ns1:Surname>
                                    <ns1:NameTitle>{{ $traveler['ns2__PersonName']['ns2__NameTitle'] }}</ns1:NameTitle>
                                </ns1:PersonName>
                                <ns1:Telephone DefaultInd="false" FormattedInd="false" PhoneNumber="" />
                                <ns1:Document DocHolderNationality="{{ $traveler['ns2__Document']['@attributes']['DocHolderNationality'] }}" DocID="{{ $traveler['ns2__Document']['@attributes']['DocID'] }}" DocIssueCountry="{{ $traveler['ns2__Document']['@attributes']['DocIssueCountry'] }}" DocType="{{ $traveler['ns2__Document']['@attributes']['DocType'] }}" ExpireDate="{{ $traveler['ns2__Document']['@attributes']['ExpireDate'] }}" />
                                <ns1:TravelerRefNumber RPH="RB|A1$2686008" />
                                <ns1:ETicketInfo>
                                    <ns1:ETicketInfomation couponNo="1" eTicketNo="{{ $TicketInfo['TicketNo'][$key]['TicketNo'] }}" flightSegmentCode="{{ $TicketInfo['FlightSegment'] }}" flightSegmentRPH="3259644" status="O" usedStatus="UNUSED" />
                                </ns1:ETicketInfo>
                            </ns1:AirTraveler>
                        @endforeach                        
                    @endif   
                </ns1:TravelerInfo>
                <ns1:Fulfillment>
                    <ns1:PaymentDetails>
                        <ns1:PaymentDetail>
                            <ns1:DirectBill>
                                <ns1:CompanyName Code="GSA11" ContactName="GSA11">GSA11</ns1:CompanyName>
                            </ns1:DirectBill>
                            {{-- <ns1:PaymentAmount Amount="{{ $Fullfillment['ns2__PaymentAmount']['@attributes']['Amount'] }}" CurrencyCode="{{ $Fullfillment['ns2__PaymentAmount']['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $Fullfillment['ns2__PaymentAmount']['@attributes']['DecimalPlaces'] }}" /> --}}
                            {{-- <ns1:PaymentAmountInPayCur Amount="{{ $Fullfillment['@attributes']['Amount'] }}" CurrencyCode="{{ $Fullfillment['@attributes']['CurrencyCode'] }}" DecimalPlaces="{{ $Fullfillment['@attributes']['DecimalPlaces'] }}" /> --}}
                        </ns1:PaymentDetail>
                    </ns1:PaymentDetails>
                </ns1:Fulfillment>
                <ns1:Ticketing ReverseTktgSegmentsInd="false" TicketType="eTicket" TicketingStatus="3">
                    <ns1:TicketAdvisory>Reservation is fully paid and confirmed.</ns1:TicketAdvisory>
                </ns1:Ticketing>
                <ns1:BookingReferenceID ID="{{ $TicketInfo['BookingReferenceID'] }}" Type="14" />
                <ns1:TPA_Extensions>
                    <ns2:AAAirReservationExt xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:ns2="http://www.isaaviation.com/thinair/webservices/OTA/Extensions/2003/05">
                        @isset($ContactInfo['PersonName'])
                            <ns2:ContactInfo>
                                <ns2:PersonName>
                                    <ns2:Title>{{ $ContactInfo['PersonName']['Title'] }}</ns2:Title>
                                    <ns2:FirstName>{{ $ContactInfo['PersonName']['FirstName'] }}</ns2:FirstName>
                                    <ns2:LastName>{{ $ContactInfo['PersonName']['LastName'] }}</ns2:LastName>
                                </ns2:PersonName>
                                <ns2:Mobile>
                                    <ns2:PhoneNumber>{{ substr($ContactInfo['Mobile']['PhoneNumber'], 2) }}</ns2:PhoneNumber>
                                    <ns2:CountryCode>{{ substr($ContactInfo['Mobile']['PhoneNumber'], 0, 2) }}</ns2:CountryCode>
                                    <ns2:AreaCode>73</ns2:AreaCode>
                                </ns2:Mobile>
                                <ns2:Email>{{ $ContactInfo['Email'] }}</ns2:Email>
                                <ns2:Address>
                                    <ns2:CityName>{{ $ContactInfo['Address']['ns1__CityName'] }}</ns2:CityName>
                                    <ns2:CountryName>
                                        <ns2:CountryName>{{ $ContactInfo['Address']['CountryName']['CountryCode'] }}</ns2:CountryName>
                                        <ns2:CountryCode>{{ $ContactInfo['Address']['CountryName']['CountryCode'] }}</ns2:CountryCode>
                                    </ns2:CountryName>
                                </ns2:Address>
                                <ns2:PreferredLanguage>en</ns2:PreferredLanguage>
                            </ns2:ContactInfo>
                        @endisset
                        @isset($ContactInfo[0])
                            @foreach ($ContactInfo[0] as $contact)
                                <ns2:ContactInfo>
                                    <ns2:PersonName>
                                        <ns2:Title>{{ $contact['PersonName']['Title'] }}</ns2:Title>
                                        <ns2:FirstName>{{ $contact['PersonName']['FirstName'] }}</ns2:FirstName>
                                        <ns2:LastName>{{ $contact['PersonName']['LastName'] }}</ns2:LastName>
                                    </ns2:PersonName>
                                    <ns2:Mobile>
                                        <ns2:PhoneNumber>{{ substr($contact['Mobile']['PhoneNumber'], 2) }}</ns2:PhoneNumber>
                                        <ns2:CountryCode>{{ substr($contact['Mobile']['PhoneNumber'], 0, 2) }}</ns2:CountryCode>
                                        <ns2:AreaCode>73</ns2:AreaCode>
                                    </ns2:Mobile>
                                    <ns2:Email>{{ $contact['Email'] }}</ns2:Email>
                                    <ns2:Address>
                                        <ns2:CityName>{{ $contact['Address']['ns1__CityName'] }}</ns2:CityName>
                                        <ns2:CountryName>
                                            <ns2:CountryName>{{ $contact['Address']['CountryName']['CountryCode'] }}</ns2:CountryName>
                                            <ns2:CountryCode>{{ $contact['Address']['CountryName']['CountryCode'] }}</ns2:CountryCode>
                                        </ns2:CountryName>
                                    </ns2:Address>
                                    <ns2:PreferredLanguage>en</ns2:PreferredLanguage>
                                </ns2:ContactInfo>
                            @endforeach
                        @endisset    
                        <ns2:AdminInfo>
                            <ns2:OriginAgentCode>GSA11</ns2:OriginAgentCode>
                        </ns2:AdminInfo>
                        <ns2:ResSummary>
                            <ns2:PTCCounts>
                                <ns2:PTCCount>
                                    <ns2:PassengerTypeCode>ADT</ns2:PassengerTypeCode>
                                    <ns2:PassengerTypeQuantity>{{ $PassCount['Adult'] }}</ns2:PassengerTypeQuantity>
                                </ns2:PTCCount>
                                <ns2:PTCCount>
                                    <ns2:PassengerTypeCode>CHD</ns2:PassengerTypeCode>
                                    <ns2:PassengerTypeQuantity>{{ $PassCount['Child'] }}</ns2:PassengerTypeQuantity>
                                </ns2:PTCCount>
                                <ns2:PTCCount>
                                    <ns2:PassengerTypeCode>INF</ns2:PassengerTypeCode>
                                    <ns2:PassengerTypeQuantity>{{ $PassCount['Infant'] }}</ns2:PassengerTypeQuantity>
                                </ns2:PTCCount>
                            </ns2:PTCCounts>
                        </ns2:ResSummary>
                    </ns2:AAAirReservationExt>
                </ns1:TPA_Extensions>
            </ns1:AirReservation>
            <ns1:Success />
            <ns1:Errors />
        </ns1:OTA_AirBookRS>
    </soap:Body>
</soap:Envelope>



















{{-- <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <soap:Body xmlns:ns1="http://www.opentravel.org/OTA/2003/05">
        <ns1:OTA_AirBookRS Cancel="false" EchoToken="11835513596097885191423" PrimaryLangID="en-us" RetransmissionIndicator="false" SequenceNmbr="1" TransactionIdentifier="TID$1632032339863222-.demo2015" Version="2006.01">
            <ns1:AirReservation>
                <ns1:AirItinerary>
                    <ns1:OriginDestinationOptions>
                        <ns1:OriginDestinationOption>
                            <ns1:FlightSegment ArrivalDateTime="2021-09-30T18:00:00" DepartureDateTime="2021-09-30T14:00:00" FlightNumber="RB555" RPH="RB$DAM/DXB$3259645$20210930140000$20210930180000" ResCabinClass="Y" Status="35" returnFlag="false">
                                <ns1:DepartureAirport LocationCode="DAM" Terminal="TerminalX" />
                                <ns1:ArrivalAirport LocationCode="DXB" />
                                <ns1:Comment>airport_short_names:DAM=Damasc,DXB=null</ns1:Comment>
                            </ns1:FlightSegment>
                        </ns1:OriginDestinationOption>
                    </ns1:OriginDestinationOptions>
                </ns1:AirItinerary>
                <ns1:PriceInfo RepriceRequired="false">
                    <ns1:ItinTotalFare NegotiatedFare="false">
                        <ns1:BaseFare Amount="350.00" CurrencyCode="USD" DecimalPlaces="2" />
                        <ns1:Taxes>
                            <ns1:Tax Amount="6.12" CurrencyCode="USD" DecimalPlaces="2" TaxCode="TOTALTAX" />
                        </ns1:Taxes>
                        <ns1:Fees>
                            <ns1:Fee Amount="80.08" CurrencyCode="USD" DecimalPlaces="2" FeeCode="TOTALFEE" />
                        </ns1:Fees>
                        <ns1:TotalFare Amount="436.20" CurrencyCode="USD" DecimalPlaces="2" />
                        <ns1:TotalEquivFare Amount="436.20" CurrencyCode="USD" DecimalPlaces="2" />
                    </ns1:ItinTotalFare>
                    <ns1:PTC_FareBreakdowns>
                        <ns1:PTC_FareBreakdown>
                            <ns1:PassengerTypeQuantity Age="0" Code="ADT" Quantity="1" />
                            <ns1:FareBasisCodes>
                                <ns1:FareBasisCode>P</ns1:FareBasisCode>
                            </ns1:FareBasisCodes>
                            <ns1:PassengerFare NegotiatedFare="false">
                                <ns1:BaseFare Amount="350.00" CurrencyCode="USD" DecimalPlaces="2" />
                                <ns1:Taxes>
                                    <ns1:Tax Amount="2.73" CurrencyCode="USD" DecimalPlaces="2" TaxCode="TAX" TaxName="INT ADV PAX INF(API)FEE (ZR1)" />
                                    <ns1:Tax Amount="1.00" CurrencyCode="USD" DecimalPlaces="2" TaxCode="TAX" TaxName="Service Security Syria Airport" />
                                    <ns1:Tax Amount="2.39" CurrencyCode="USD" DecimalPlaces="2" TaxCode="TAX" TaxName="AIRPORT TAX &amp; EXIT FEE (FY)" />
                                </ns1:Taxes>
                                <ns1:Fees>
                                    <ns1:Fee Amount="80.00" CurrencyCode="USD" DecimalPlaces="2" FeeCode="SUR">YR</ns1:Fee>
                                    <ns1:Fee Amount="0.08" CurrencyCode="USD" DecimalPlaces="2" FeeCode="SUR">service charge</ns1:Fee>
                                </ns1:Fees>
                                <ns1:TotalFare Amount="436.20" CurrencyCode="USD" DecimalPlaces="2" />
                            </ns1:PassengerFare>
                            <ns1:TravelerRefNumber RPH="RB|A1$2686009" />
                        </ns1:PTC_FareBreakdown>
                    </ns1:PTC_FareBreakdowns>
                </ns1:PriceInfo>
                <ns1:TravelerInfo>
                    <ns1:AirTraveler AccompaniedByInfant="false" PassengerTypeCode="ADT">
                        <ns1:PersonName>
                            <ns1:GivenName>TestA</ns1:GivenName>
                            <ns1:Surname>Petersona</ns1:Surname>
                            <ns1:NameTitle>MR</ns1:NameTitle>
                        </ns1:PersonName>
                        <ns1:Telephone DefaultInd="false" FormattedInd="false" PhoneNumber="" />
                        <ns1:Document DocHolderNationality="CN" DocID="E82037912" DocIssueCountry="CN" DocType="PSPT" ExpireDate="2023-08-13" />
                        <ns1:TravelerRefNumber RPH="RB|A1$2686009" />
                        <ns1:ETicketInfo>
                            <ns1:ETicketInfomation couponNo="1" eTicketNo="0705502663655" flightSegmentCode="DAM/DXB" flightSegmentRPH="3259645" status="O" usedStatus="UNUSED" />
                        </ns1:ETicketInfo>
                    </ns1:AirTraveler>
                </ns1:TravelerInfo>
                <ns1:Fulfillment>
                    <ns1:PaymentDetails>
                        <ns1:PaymentDetail>
                            <ns1:DirectBill>
                                <ns1:CompanyName Code="GSA11" ContactName="GSA11">GSA11</ns1:CompanyName>
                            </ns1:DirectBill>
                            <ns1:PaymentAmount Amount="436.2" CurrencyCode="USD" DecimalPlaces="2" />
                            <ns1:PaymentAmountInPayCur Amount="436.2" CurrencyCode="USD" DecimalPlaces="2" />
                        </ns1:PaymentDetail>
                    </ns1:PaymentDetails>
                </ns1:Fulfillment>
                <ns1:Ticketing ReverseTktgSegmentsInd="false" TicketType="eTicket" TicketingStatus="3">
                    <ns1:TicketAdvisory>Reservation is fully paid and confirmed.</ns1:TicketAdvisory>
                </ns1:Ticketing>
                <ns1:BookingReferenceID ID="11927162" Type="14" />
                <ns1:TPA_Extensions>
                    <ns2:AAAirReservationExt xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:ns2="http://www.isaaviation.com/thinair/webservices/OTA/Extensions/2003/05">
                        <ns2:ContactInfo>
                            <ns2:PersonName>
                                <ns2:Title>MR</ns2:Title>
                                <ns2:FirstName>Test</ns2:FirstName>
                                <ns2:LastName>Testlast</ns2:LastName>
                            </ns2:PersonName>
                            <ns2:Mobile>
                                <ns2:PhoneNumber>73733</ns2:PhoneNumber>
                                <ns2:CountryCode>98</ns2:CountryCode>
                                <ns2:AreaCode>73</ns2:AreaCode>
                            </ns2:Mobile>
                            <ns2:Email>aa@bb.gmail.com</ns2:Email>
                            <ns2:Address>
                                <ns2:CityName>Bristol</ns2:CityName>
                                <ns2:CountryName>
                                    <ns2:CountryName>IN</ns2:CountryName>
                                    <ns2:CountryCode>IN</ns2:CountryCode>
                                </ns2:CountryName>
                            </ns2:Address>
                            <ns2:PreferredLanguage>en</ns2:PreferredLanguage>
                        </ns2:ContactInfo>
                        <ns2:AdminInfo>
                            <ns2:OriginAgentCode>GSA11</ns2:OriginAgentCode>
                        </ns2:AdminInfo>
                        <ns2:ResSummary>
                            <ns2:PTCCounts>
                                <ns2:PTCCount>
                                    <ns2:PassengerTypeCode>ADT</ns2:PassengerTypeCode>
                                    <ns2:PassengerTypeQuantity>1</ns2:PassengerTypeQuantity>
                                </ns2:PTCCount>
                                <ns2:PTCCount>
                                    <ns2:PassengerTypeCode>CHD</ns2:PassengerTypeCode>
                                    <ns2:PassengerTypeQuantity>0</ns2:PassengerTypeQuantity>
                                </ns2:PTCCount>
                                <ns2:PTCCount>
                                    <ns2:PassengerTypeCode>INF</ns2:PassengerTypeCode>
                                    <ns2:PassengerTypeQuantity>0</ns2:PassengerTypeQuantity>
                                </ns2:PTCCount>
                            </ns2:PTCCounts>
                        </ns2:ResSummary>
                    </ns2:AAAirReservationExt>
                </ns1:TPA_Extensions>
            </ns1:AirReservation>
            <ns1:Success />
            <ns1:Errors />
        </ns1:OTA_AirBookRS>
    </soap:Body>
</soap:Envelope> --}}