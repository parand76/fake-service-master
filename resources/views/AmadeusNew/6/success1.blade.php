<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
        <wsa:From>
            <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
        </wsa:From>
        <wsa:Action>http://webservices.amadeus.com/PNRADD_21_1_1A</wsa:Action>
        <wsa:MessageID>urn:uuid:d6edf27e-f377-2684-b58a-f2841debb810</wsa:MessageID>
        <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">
            88cb2c7c-0c8b-4338-b444-a64d6588dd54</wsa:RelatesTo>
        <awsse:Session TransactionStatusCode="InSeries">
            <awsse:SessionId>{{ $SessionId }}</awsse:SessionId>
            <awsse:SequenceNumber>{{ $SessionSequenceNumber }}</awsse:SequenceNumber>
            <awsse:SecurityToken>{{ $SessionToken }}</awsse:SecurityToken>
        </awsse:Session>
    </soap:Header>
    <soap:Body>
        <PNR_Reply xmlns="http://xml.amadeus.com/PNRACC_21_1_1A">
            <pnrHeader>
                <reservationInfo>
                    <reservation>
                        <companyId>1A</companyId>
                    </reservation>
                </reservationInfo>
            </pnrHeader>
            <securityInformation>
                <responsibilityInformation>
                    <typeOfPnrElement>RP</typeOfPnrElement>
                    <officeId>BGWIK3334</officeId>
                </responsibilityInformation>
                <queueingInformation>
                    <queueingOfficeId>BGWIK3334</queueingOfficeId>
                </queueingInformation>
                <cityCode>BGW</cityCode>
            </securityInformation>
            <sbrPOSDetails>
                <sbrUserIdentificationOwn>
                    <originIdentification>
                        <inHouseIdentification1> </inHouseIdentification1>
                    </originIdentification>
                </sbrUserIdentificationOwn>
                <sbrSystemDetails>
                    <deliveringSystem>
                        <companyId> </companyId>
                    </deliveringSystem>
                </sbrSystemDetails>
                <sbrPreferences>
                    <userPreferences>
                        <codedCountry> </codedCountry>
                    </userPreferences>
                </sbrPreferences>
            </sbrPOSDetails>
            <sbrCreationPosDetails>
                <sbrUserIdentificationOwn>
                    <originIdentification>
                        <inHouseIdentification1> </inHouseIdentification1>
                    </originIdentification>
                </sbrUserIdentificationOwn>
                <sbrSystemDetails>
                    <deliveringSystem>
                        <companyId> </companyId>
                    </deliveringSystem>
                </sbrSystemDetails>
                <sbrPreferences>
                    <userPreferences>
                        <codedCountry> </codedCountry>
                    </userPreferences>
                </sbrPreferences>
            </sbrCreationPosDetails>
            <sbrUpdatorPosDetails>
                <sbrUserIdentificationOwn>
                    <originIdentification>
                        <inHouseIdentification1>BGWIK3334</inHouseIdentification1>
                    </originIdentification>
                    <originatorTypeCode>E</originatorTypeCode>
                </sbrUserIdentificationOwn>
                <sbrSystemDetails>
                    <deliveringSystem>
                        <companyId>1A</companyId>
                        <locationId>BGW</locationId>
                    </deliveringSystem>
                </sbrSystemDetails>
                <sbrPreferences>
                    <userPreferences>
                        <codedCountry>IQ</codedCountry>
                    </userPreferences>
                </sbrPreferences>
            </sbrUpdatorPosDetails>
            @if (!is_null($TravellersInfo))
                @if (isset($TravellersInfo[0]) || count($TravellersInfo) > 1)
                    @foreach ($TravellersInfo as $passenger)
                        <travellerInfo>
                            <elementManagementPassenger>
                                <reference>
                                    <qualifier>{{ $passenger['elementManagementPassenger']['reference']['qualifier'] }}
                                    </qualifier>
                                    <number>{{ $passenger['elementManagementPassenger']['reference']['number'] }}
                                    </number>
                                </reference>
                                <segmentName>{{ $passenger['elementManagementPassenger']['segmentName'] }}</segmentName>
                            </elementManagementPassenger>
                            <passengerData>
                                {{-- @if (isset($passenger['passengerData'][0]) || count($passenger['passengerData']) > 1)
                                    @foreach ($passenger['passengerData'] as $passData)
                                        <travellerInformation>
                                            <traveller>
                                                <surname>
                                            {{ $passData['travellerInformation']['traveller']['surname'] }}
                                        </surname>
                                                <quantity>{{ count($passenger['passengerData']) }}</quantity>
                                            </traveller>
                                            <passenger>
                                                <firstName>{{ $passData['travellerInformation']['passenger']['firstName'] }}
                                        </firstName>
                                        <type>{{ $passData['travellerInformation']['passenger']['type'] }}</type>
                                            </passenger>
                                        </travellerInformation>
                                    @endforeach
                                @else
                                    <travellerInformation>
                                        <traveller>
                                            <surname>
                                                {{ $passenger['passengerData']['travellerInformation']['traveller']['surname'] }}
                                            </surname>
                                            <quantity></quantity>
                                        </traveller>
                                        <passenger>
                                            <firstName>
                                                {{ $passenger['passengerData']['travellerInformation']['passenger'] }}
                                            </firstName>
                                            <type>
                                                {{ $passenger['passengerData']['travellerInformation']['passenger']['type'] }}
                                            </type>
                                        </passenger>
                                    </travellerInformation>
                                @endif --}}
                            </passengerData>
                            @if (isset($passenger['passengerData'][0]))
                                @foreach ($passenger['passengerData'] as $num => $passData)
                                    <enhancedPassengerData>
                                        <enhancedTravellerInformation>
                                            <travellerNameInfo>
                                                <quantity>1</quantity>
                                                <type>
                                                    {{ $passData['travellerInformation']['passenger']['type'] }}
                                                </type>
                                            </travellerNameInfo>
                                            <otherPaxNamesDetails>
                                                <nameType>UN</nameType>
                                                <referenceName>Y</referenceName>
                                                <displayedName>Y</displayedName>
                                                <surname>
                                                    {{ $passData['travellerInformation']['traveller']['surname'] }}
                                                </surname>
                                                <givenName>
                                                    {{ $passData['travellerInformation']['passenger']['firstName'] }}
                                                </givenName>
                                            </otherPaxNamesDetails>
                                        </enhancedTravellerInformation>
                                    </enhancedPassengerData>
                                @endforeach
                            @else
                                <enhancedPassengerData>
                                    <enhancedTravellerInformation>
                                        <travellerNameInfo>
                                            <quantity>1</quantity>
                                            {{-- <type>
                                        {{ $passenger['passengerData']['travellerInformation']['passenger']['type'] }}
                                    </type> --}}
                                        </travellerNameInfo>
                                        <otherPaxNamesDetails>
                                            <nameType>UN</nameType>
                                            <referenceName>Y</referenceName>
                                            <displayedName>Y</displayedName>
                                            {{-- <surname>
                                        {{ $passenger['passengerData']['travellerInformation']['traveller']['surname'] }}
                                    </surname>
                                    <givenName>
                                        {{ $passenger['passengerData']['travellerInformation']['passenger']['firstName'] }}
                                    </givenName> --}}
                                        </otherPaxNamesDetails>
                                    </enhancedTravellerInformation>
                                </enhancedPassengerData>
                            @endif
                        </travellerInfo>
                    @endforeach
                @else
                    <travellerInfo>
                        <elementManagementPassenger>
                            <reference>
                                <qualifier>
                                    {{ $TravellersInfo['elementManagementPassenger']['reference']['qualifier'] }}
                                </qualifier>
                                <number>{{ $TravellersInfo['elementManagementPassenger']['reference']['number'] }}
                                </number>
                            </reference>
                            <segmentName>{{ $TravellersInfo['elementManagementPassenger']['segmentName'] }}
                            </segmentName>
                        </elementManagementPassenger>
                        <passengerData>
                            <travellerInformation>
                                <traveller>
                                    <surname>
                                        {{ $TravellersInfo['passengerData']['travellerInformation']['traveller']['surname'] }}
                                    </surname>
                                    <quantity></quantity>
                                </traveller>
                                <passenger>
                                    <firstName>
                                        {{ $TravellersInfo['passengerData']['travellerInformation']['passenger']['firstName'] }}
                                    </firstName>
                                    <type>
                                        {{ $TravellersInfo['passengerData']['travellerInformation']['passenger']['type'] }}
                                    </type>
                                </passenger>
                            </travellerInformation>
                        </passengerData>
                        <enhancedPassengerData>
                            <enhancedTravellerInformation>
                                <travellerNameInfo>
                                    <quantity>
                                        {{ $TravellersInfo['passengerData']['travellerInformation']['traveller']['quantity'] }}
                                    </quantity>
                                    <type>
                                        {{ $TravellersInfo['passengerData']['travellerInformation']['passenger']['type'] }}
                                    </type>
                                </travellerNameInfo>
                                <otherPaxNamesDetails>
                                    <nameType>UN</nameType>
                                    <referenceName>Y</referenceName>
                                    <displayedName>Y</displayedName>
                                    <surname>
                                        {{ $TravellersInfo['passengerData']['travellerInformation']['traveller']['surname'] }}
                                    </surname>
                                    <givenName>
                                        {{ $TravellersInfo['passengerData']['travellerInformation']['passenger']['firstName'] }}
                                    </givenName>
                                </otherPaxNamesDetails>
                            </enhancedTravellerInformation>
                        </enhancedPassengerData>
                    </travellerInfo>
                @endif
            @endif
            <originDestinationDetails>
                <originDestination></originDestination>
                <itineraryInfo>
                    <elementManagementItinerary>
                        <reference>
                            <qualifier>ST</qualifier>
                            <number>1</number>
                        </reference>
                        <segmentName>AIR</segmentName>
                        <lineNumber>2</lineNumber>
                    </elementManagementItinerary>
                    <travelProduct>
                        <product>
                            <depDate>121222</depDate>
                            <depTime>0810</depTime>
                            <arrDate>121222</arrDate>
                            <arrTime>1040</arrTime>
                        </product>
                        <boardpointDetail>
                            <cityCode>ATH</cityCode>
                        </boardpointDetail>
                        <offpointDetail>
                            <cityCode>IST</cityCode>
                        </offpointDetail>
                        <companyDetail>
                            <identification>A3</identification>
                        </companyDetail>
                        <productDetails>
                            <identification>990</identification>
                            <classOfService>T</classOfService>
                        </productDetails>
                        <typeDetail>
                            <detail>ET</detail>
                        </typeDetail>
                    </travelProduct>
                    <itineraryMessageAction>
                        <business>
                            <function>1</function>
                        </business>
                    </itineraryMessageAction>
                    <relatedProduct>
                        <quantity>1</quantity>
                        <status>HK</status>
                    </relatedProduct>
                    <flightDetail>
                        <productDetails>
                            <equipment>320</equipment>
                            <numOfStops>0</numOfStops>
                            <duration>0130</duration>
                            <weekDay>1</weekDay>
                        </productDetails>
                        <mileageTimeDetails>
                            <flightLegMileage>344</flightLegMileage>
                            <unitQualifier>M</unitQualifier>
                        </mileageTimeDetails>
                        <facilities>
                            <entertainement>M</entertainement>
                            <entertainementDescription>S</entertainementDescription>
                        </facilities>
                    </flightDetail>
                    <cabinDetails>
                        <cabinDetails>
                            <classDesignator>M</classDesignator>
                        </cabinDetails>
                    </cabinDetails>
                    <selectionDetails>
                        <selection>
                            <option>P2</option>
                        </selection>
                    </selectionDetails>
                    <carbonDioxydeInfo>
                        <carbonDioxydeAmount>
                            <quantityDetails>
                                <qualifier>COE</qualifier>
                                <value>69.715218</value>
                                <unit>KPP</unit>
                            </quantityDetails>
                        </carbonDioxydeAmount>
                        <carbonDioxydeInfoSource>
                            <freeTextDetails>
                                <textSubjectQualifier>3</textSubjectQualifier>
                                <source>S</source>
                                <encoding>7</encoding>
                            </freeTextDetails>
                            <freeText>SOURCE:ICAO CARBON EMISSIONS CALCULATOR</freeText>
                        </carbonDioxydeInfoSource>
                    </carbonDioxydeInfo>
                    <itineraryfreeFormText>
                        <freeTextQualification>
                            <textSubjectQualifier>3</textSubjectQualifier>
                        </freeTextQualification>
                        <freeText>SEE RTSVC</freeText>
                    </itineraryfreeFormText>
                    <distributionMethod>
                        <distributionMethodDetails>
                            <distriProductCode>E</distriProductCode>
                        </distributionMethodDetails>
                    </distributionMethod>
                    <legInfo>
                        <markerLegInfo></markerLegInfo>
                        <legTravelProduct>
                            <flightDate>
                                <departureDate>121222</departureDate>
                                <departureTime>0810</departureTime>
                                <arrivalDate>121222</arrivalDate>
                                <arrivalTime>1040</arrivalTime>
                            </flightDate>
                            <boardPointDetails>
                                <trueLocationId>ATH</trueLocationId>
                            </boardPointDetails>
                            <offpointDetails>
                                <trueLocationId>IST</trueLocationId>
                            </offpointDetails>
                        </legTravelProduct>
                        <interactiveFreeText>
                            <freeTextQualification>
                                <textSubjectQualifier>ACO</textSubjectQualifier>
                            </freeTextQualification>
                            <freeText>AIRCRAFT OWNER AEGEAN AIRLINES</freeText>
                        </interactiveFreeText>
                    </legInfo>
                    <markerRailTour></markerRailTour>
                </itineraryInfo>
            </originDestinationDetails>
            <dataElementsMaster>
                <marker2></marker2>
                <dataElementsIndiv>
                    <elementManagementData>
                        <segmentName>RF</segmentName>
                    </elementManagementData>
                    <otherDataFreetext>
                        <freetextDetail>
                            <subjectQualifier>3</subjectQualifier>
                            <type>P22</type>
                        </freetextDetail>
                        <longFreetext>AWSUI-ACO-IQ/WS7FAFLY</longFreetext>
                    </otherDataFreetext>
                </dataElementsIndiv>
                <dataElementsIndiv>
                    <elementManagementData>
                        <reference>
                            <qualifier>OT</qualifier>
                            <number>2</number>
                        </reference>
                        <segmentName>AP</segmentName>
                        <lineNumber>3</lineNumber>
                    </elementManagementData>
                    <otherDataFreetext>
                        <freetextDetail>
                            <subjectQualifier>3</subjectQualifier>
                            <type>5</type>
                        </freetextDetail>
                        <longFreetext>BGW 009647733300123 - FLY 4 ALL - A</longFreetext>
                    </otherDataFreetext>
                </dataElementsIndiv>
                <dataElementsIndiv>
                    <elementManagementData>
                        <reference>
                            <qualifier>OT</qualifier>
                            <number>3</number>
                        </reference>
                        <segmentName>TK</segmentName>
                        <lineNumber>4</lineNumber>
                    </elementManagementData>
                    <ticketElement>
                        <ticket>
                            <indicator>OK</indicator>
                            <date>201122</date>
                            <officeId>BGWIK3334</officeId>
                        </ticket>
                    </ticketElement>
                </dataElementsIndiv>
            </dataElementsMaster>
        </PNR_Reply>
    </soap:Body>
</soap:Envelope>
