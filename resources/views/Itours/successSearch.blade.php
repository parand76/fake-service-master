{
    "result": {
        "currency": "USD",
        "airTripType": "RoundTrip",
        "pricedItineraries": [
            @foreach($list as $key=>$result)
            {
                "key": "{{$result['key']}}",
                "paymentBeforePNR": {{$result['paymentBeforePNR']}},
                "isDomestic": false,
                "hasExtraBaggage": false,
                "airItinerary": {
                    "originDestinationOptions": [
                        @foreach($result['options'] as $optionKey=>$item)
                        @if(isset($item['DepartureDateTimeGo']))
                        {
                            "journeyDuration": "{{$item['FlightDurationGo']}}",
                            "numberOfStop": 0,
                            "flightSegments": [{
                                "flightNumber": "{{$item['FlightNumberGo']}}",
                                "departureDateTime": "{{$item['DepartureDateTimeGo']}}",
                                "arrivalDateTime": "{{$item['ArrivalDateTimeGo']}}",
                                "resBookDesigCode": "L",
                                "arrivalAirport": {
                                    "locationCode": "{{$item['ArivelLocationCodeGo']}}",
                                    "terminalID": "3",
                                    "locationName": "{{$item['destinationAirportGo']}}",
                                    "countryName": "{{$item['destinationCountryGo']}}",
                                    "cityName": "{{$item['destinationCityGo']}}"
                                },
                                "departureAirport": {
                                    "locationCode": "{{$item['DeparturLocationCodeGo']}}",
                                    "terminalID": "3",
                                    "locationName": "{{$item['originAirportGo']}}",
                                    "countryName": "{{$item['originCountryGo']}}",
                                    "cityName": "{{$item['originCityGo']}}"
                                },
                                "marketingAirline": {
                                    "code": "EK",
                                    "name": "Emirates",
                                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Emirates.png"
                                },
                                "operatingAirline": {
                                    "code": "EK",
                                    "name": "Emirates",
                                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Emirates.png"
                                },
                                "airEquipType": "388",
                                "statusCode": null,
                                "flightDuration": "08:00:00",
                                "fareBasis": [{
                                    "fareBasisCode": "LLXEPAE1",
                                    "bookingCode": "L",
                                    "passengerType": "ADT"
                                }],
                                "baggageInformation": [{
                                    "baggageAllowance": 25,
                                    "unitType": "KG",
                                    "passengerType": "ADT"
                                }],
                                "extraBaggageInformation": [],
                                "handBagInformation": null,
                                "cabinClass": {
                                    "name": "Economy",
                                    "code": "Y"
                                },
                                "isOutbound": true,
                                "stopTime": "00:00:00"
                            }]
                        },
                        {
                            "journeyDuration": "{{$item['FlightDurationBack']}}",
                            "numberOfStop": 0,
                            "flightSegments": [{
                                "flightNumber": "{{$item['FlightNumberBack']}}",
                                "departureDateTime": "{{$item['DepartureDateTimeBack']}}",
                                "arrivalDateTime": "{{$item['ArrivalDateTimeBack']}}",
                                "resBookDesigCode": "L",
                                "arrivalAirport": {
                                    "locationCode": "{{$item['ArivelLocationCodeBack']}}",
                                    "terminalID": "3",
                                    "locationName": "{{$item['destinationAirportBack']}}",
                                    "countryName": "{{$item['destinationCountryBack']}}",
                                    "cityName": "{{$item['destinationCityBack']}}"
                                },
                                "departureAirport": {
                                    "locationCode": "{{$item['DeparturLocationCodeBack']}}",
                                    "terminalID": "3",
                                    "locationName": "{{$item['originAirportBack']}}",
                                    "countryName": "{{$item['originCountryBack']}}",
                                    "cityName": "{{$item['originCityBack']}}"
                                },
                                "marketingAirline": {
                                    "code": "EK",
                                    "name": "Emirates",
                                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Emirates.png"
                                },
                                "operatingAirline": {
                                    "code": "EK",
                                    "name": "Emirates",
                                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Emirates.png"
                                },
                                "airEquipType": "388",
                                "statusCode": null,
                                "flightDuration": "08:00:00",
                                "fareBasis": [{
                                    "fareBasisCode": "LLXEPAE1",
                                    "bookingCode": "L",
                                    "passengerType": "ADT"
                                }],
                                "baggageInformation": [{
                                    "baggageAllowance": 25,
                                    "unitType": "KG",
                                    "passengerType": "ADT"
                                }],
                                "extraBaggageInformation": [],
                                "handBagInformation": null,
                                "cabinClass": {
                                    "name": "Economy",
                                    "code": "Y"
                                },
                                "isOutbound": true,
                                "stopTime": "00:00:00"
                            }]
                        }
                       
                        @else
                        {
                            "journeyDuration": "{{$item['FlightDuration']}}",
                            "numberOfStop": 0,
                            "flightSegments": [{
                                "flightNumber": "{{$item['FlightNumber']}}",
                                "departureDateTime": "{{$item['DepartureDateTime']}}",
                                "arrivalDateTime": "{{$item['ArrivalDateTime']}}",
                                "resBookDesigCode": "L",
                                "arrivalAirport": {
                                    "locationCode": "{{$item['ArivelLocationCode']}}",
                                    "terminalID": "3",
                                    "locationName": "{{$item['destinationAirport']}}",
                                    "countryName": "{{$item['destinationCountry']}}",
                                    "cityName": "{{$item['destinationCity']}}"
                                },
                                "departureAirport": {
                                    "locationCode": "{{$item['DeparturLocationCode']}}",
                                    "terminalID": "3",
                                    "locationName": "{{$item['originAirport']}}",
                                    "countryName": "{{$item['originCountry']}}",
                                    "cityName": "{{$item['originCity']}}"
                                },
                                "marketingAirline": {
                                    "code": "EK",
                                    "name": "Emirates",
                                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Emirates.png"
                                },
                                "operatingAirline": {
                                    "code": "EK",
                                    "name": "Emirates",
                                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Emirates.png"
                                },
                                "airEquipType": "388",
                                "statusCode": null,
                                "flightDuration": "08:00:00",
                                "fareBasis": [{
                                    "fareBasisCode": "LLXEPAE1",
                                    "bookingCode": "L",
                                    "passengerType": "ADT"
                                }],
                                "baggageInformation": [{
                                    "baggageAllowance": 25,
                                    "unitType": "KG",
                                    "passengerType": "ADT"
                                }],
                                "extraBaggageInformation": [],
                                "handBagInformation": null,
                                "cabinClass": {
                                    "name": "Economy",
                                    "code": "Y"
                                },
                                "isOutbound": true,
                                "stopTime": "00:00:00"
                            }]
                        }
                        @endif
                        @if (!$loop->last)
            ,
                        @endif
                        @endforeach
                ]},
                "airItineraryPricingInfo": {
                    "itinTotalFare": {
                        "baseFare": {{$result['pricing']['totalbasefare']}},
                        "taxes": [{
                            "amount": {{$result['pricing']['totalTax']}},
                            "code": "TOTALTAX",
                            "name": null
                        }],
                        "fess": null,
                        "extraBaggage": 0.0,
                        "commission": 0.0,
                        "thirdPartyCommission": 0.0,
                        "totalFare": {{$result['pricing']['totalTotalefare']}},
                        "totalFareWithExtraBaggage": 0.0
                    },
                    "ptC_FareBreakdown": [
                        @if($result['passengers']['adult']!=0)
                        {
                            "passengerFare": {
                                "baseFare": {{$result['pricing']['adultBaseFare']}},
                                "taxes": [
                                    {
                                        "amount": {{$result['pricing']['taxFare']}},
                                        "code": "TotalTax",
                                        "name": null
                                    }
                                ],
                                "fess": null,
                                "extraBaggage": 0,
                                "commission": 0,
                                "thirdPartyCommission": 0,
                                "totalFare": {{$result['pricing']['adultTotaleFare']}},
                                "totalFareWithExtraBaggage": 0
                            },
                            "passengerTypeQuantity": {
                                "code": "ADT",
                                "quantity": {{$result['passengers']['adult']}}
                            },
                            "passengerBirthDateRange": null
                        }

                    @endif
                    @if($result['passengers']['child']!=0)
                    ,
                    @endif
                    @if($result['passengers']['child']!=0)
                    {
                            "passengerFare": {
                                "baseFare": {{$result['pricing']['childBaseFare']}},
                                "taxes": [
                                    {
                                        "amount": {{$result['pricing']['taxFare']}},
                                        "code": "TotalTax",
                                        "name": null
                                    }
                                ],
                                "fess": null,
                                "extraBaggage": 0,
                                "commission": 0,
                                "thirdPartyCommission": 0,
                                "totalFare": {{$result['pricing']['childTotaleFare']}},
                                "totalFareWithExtraBaggage": 0
                            },
                            "passengerTypeQuantity": {
                                "code": "CHD",
                                "quantity": {{$result['passengers']['child']}}
                            },
                            "passengerBirthDateRange": null
                        }
                    @endif
                    @if($result['passengers']['infant']!=0)
                    ,
                    @endif
                    @if($result['passengers']['infant']!=0)
                    {
                            "passengerFare": {
                                "baseFare": {{$result['pricing']['infantBaseFare']}},
                                "taxes": [
                                    {
                                        "amount": {{$result['pricing']['taxFare']}},
                                        "code": "TotalTax",
                                        "name": null
                                    }
                                ],
                                "fess": null,
                                "extraBaggage": 0,
                                "commission": 0,
                                "thirdPartyCommission": 0,
                                "totalFare": {{$result['pricing']['infantTotalFare']}},
                                "totalFareWithExtraBaggage": 0
                            },
                            "passengerTypeQuantity": {
                                "code": "INF",
                                "quantity": {{$result['passengers']['infant']}}
                            },
                            "passengerBirthDateRange": null
                        }

                    @endif
                    ]
                }
            }
            @if (!$loop->last)
            ,
            @endif
            @endforeach
        ]
    },
    "targetUrl": null,
    "success": true,
    "error": null,
    "unAuthorizedRequest": false,
    "__abp": true
}