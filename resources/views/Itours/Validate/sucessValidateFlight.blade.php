{
    "result": {
        "currency": "{{$currency}}",
        "airTripType": "null",
        "pricedItinerary": {
            "key": "{{$result['key']}}",
            "paymentBeforePNR": true,
            "isDomestic": false,
            "hasExtraBaggage": false,
            "airItinerary": {
                "originDestinationOptions": [
                    @foreach($result['options'] as $item)
                    
                  @if(isset($item['DepartureDateTimeGo']))
                    {
                        "journeyDuration": "{{ $item['FlightDurationGo']}}",
                        "numberOfStop": 1,
                        "flightSegments": [
                            {
                                "flightNumber": "{{$item['FlightNumberGo']}}",
                                "departureDateTime": "{{$item['DepartureDateTimeGo']}}",
                                "arrivalDateTime": "{{$item['ArrivalDateTimeGo']}}",
                                "resBookDesigCode": "T",
                                "arrivalAirport": {
                                    "locationCode": "{{$item['ArivelLocationCodeGo']}}",
                                    "terminalID": null,
                                    "locationName": "{{$item['destinationAirportGo']}}",
                                    "countryName": "{{$item['destinationCountryGo']}}",
                                    "cityName": "{{$item['destinationCityGo']}}"
                                },
                                "departureAirport": {
                                    "locationCode": "{{$item['DeparturLocationCodeGo']}}",
                                    "terminalID": null,
                                    "locationName": "{{$item['originAirportGo']}}",
                                    "countryName": "{{$item['originCountryGo']}}",
                                    "cityName": "{{$item['originCityGo']}}"
                                },
                                "marketingAirline": {
                                    "code": "EW",
                                    "name": "Eurowings GmbH",
                                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Eurowings GmbH.png"
                                },
                                "operatingAirline": {
                                    "code": "EW",
                                    "name": "Eurowings GmbH",
                                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Eurowings GmbH.png"
                                },
                                "airEquipType": null,
                                "statusCode": null,
                                "flightDuration": "01:40:00",
                                "fareBasis": [
                                    {
                                        "fareBasisCode": "TX",
                                        "bookingCode": null,
                                        "passengerType": "ADT"
                                    },
                                    {
                                        "fareBasisCode": "TX",
                                        "bookingCode": null,
                                        "passengerType": "CHD"
                                    }
                                ],
                                "baggageInformation": [
                                    {
                                        "baggageAllowance": 0,
                                        "unitType": "P",
                                        "passengerType": "ADT"
                                    },
                                    {
                                        "baggageAllowance": 0,
                                        "unitType": "P",
                                        "passengerType": "CHD"
                                    }
                                ],
                                "extraBaggageInformation": [],
                                "handBagInformation": null,
                                "cabinClass": {
                                    "name": "Economy",
                                    "code": "Economic"
                                },
                                "isOutbound": true,
                                "stopTime": "01:55:00"
                            }
                           
                        ]
                    },

                    {
                        "journeyDuration": "{{ $item['FlightDurationBack']}}",
                        "numberOfStop": 1,
                        "flightSegments": [
                            {
                                "flightNumber": "{{$item['FlightNumberBack']}}",
                                "departureDateTime": "{{$item['DepartureDateTimeBack']}}",
                                "arrivalDateTime": "{{$item['ArrivalDateTimeBack']}}",
                                "resBookDesigCode": "T",
                                "arrivalAirport": {
                                    "locationCode": "{{$item['ArivelLocationCodeBack']}}",
                                    "terminalID": null,
                                    "locationName": "{{$item['destinationAirportBack']}}",
                                    "countryName": "{{$item['destinationCountryBack']}}",
                                    "cityName": "{{$item['destinationCityBack']}}"
                                },

                                "departureAirport": {
                                    "locationCode": "{{$item['DeparturLocationCodeBack']}}",
                                    "terminalID": null,
                                    "locationName": "{{$item['originAirportBack']}}",
                                    "countryName": "{{$item['originCountryBack']}}",
                                    "cityName": "{{$item['originCityBack']}}"
                                },
                                "marketingAirline": {
                                    "code": "EW",
                                    "name": "Eurowings GmbH",
                                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Eurowings GmbH.png"
                                },
                                "operatingAirline": {
                                    "code": "EW",
                                    "name": "Eurowings GmbH",
                                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Eurowings GmbH.png"
                                },
                                "airEquipType": null,
                                "statusCode": null,
                                "flightDuration": "01:40:00",
                                "fareBasis": [
                                    {
                                        "fareBasisCode": "TX",
                                        "bookingCode": null,
                                        "passengerType": "ADT"
                                    },
                                    {
                                        "fareBasisCode": "TX",
                                        "bookingCode": null,
                                        "passengerType": "CHD"
                                    }
                                ],
                                "baggageInformation": [
                                    {
                                        "baggageAllowance": 0,
                                        "unitType": "P",
                                        "passengerType": "ADT"
                                    },
                                    {
                                        "baggageAllowance": 0,
                                        "unitType": "P",
                                        "passengerType": "CHD"
                                    }
                                ],
                                "extraBaggageInformation": [],
                                "handBagInformation": null,
                                "cabinClass": {
                                    "name": "Economy",
                                    "code": "Economic"
                                },
                                "isOutbound": true,
                                "stopTime": "01:55:00"
                            }
                           
                        ]
                    }

                @else
                    {
                        "journeyDuration": "{{ $item['FlightDuration']}}",
                        "numberOfStop": 1,
                        "flightSegments": [
                            {
                                "flightNumber": "{{$item['FlightNumber']}}",
                                "departureDateTime": "{{$item['DepartureDateTime']}}",
                                "arrivalDateTime": "{{$item['ArrivalDateTime']}}",
                                "resBookDesigCode": "T",
                                "arrivalAirport": {
                                    "locationCode": "{{$item['ArivelLocationCode']}}",
                                    "terminalID": null,
                                    "locationName": "{{$item['destinationAirport']}}",
                                    "countryName": "{{$item['destinationCountry']}}",
                                    "cityName": "{{$item['destinationCity']}}"
                                },
                                "departureAirport": {
                                    "locationCode": "{{$item['DeparturLocationCode']}}",
                                    "terminalID": null,
                                    "locationName": "{{$item['originAirport']}}",
                                    "countryName": "{{$item['originCountry']}}",
                                    "cityName": "{{$item['originCity']}}"
                                },
                                "marketingAirline": {
                                    "code": "EW",
                                    "name": "Eurowings GmbH",
                                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Eurowings GmbH.png"
                                },
                                "operatingAirline": {
                                    "code": "EW",
                                    "name": "Eurowings GmbH",
                                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Eurowings GmbH.png"
                                },
                                "airEquipType": null,
                                "statusCode": null,
                                "flightDuration": "01:40:00",
                                "fareBasis": [
                                    {
                                        "fareBasisCode": "TX",
                                        "bookingCode": null,
                                        "passengerType": "ADT"
                                    },
                                    {
                                        "fareBasisCode": "TX",
                                        "bookingCode": null,
                                        "passengerType": "CHD"
                                    }
                                ],
                                "baggageInformation": [
                                    {
                                        "baggageAllowance": 0,
                                        "unitType": "P",
                                        "passengerType": "ADT"
                                    },
                                    {
                                        "baggageAllowance": 0,
                                        "unitType": "P",
                                        "passengerType": "CHD"
                                    }
                                ],
                                "extraBaggageInformation": [],
                                "handBagInformation": null,
                                "cabinClass": {
                                    "name": "Economy",
                                    "code": "Economic"
                                },
                                "isOutbound": true,
                                "stopTime": "01:55:00"
                            }
                           
                        ]
                    }
                @endif
                    @if (!$loop->last)
            ,
                        @endif
                    @endforeach
                    
                ]
            },
            "airItineraryPricingInfo": {
                "itinTotalFare": {
                    "baseFare": "{{$result['pricing']['totalbasefare']}}",
                    "taxes": [
                        {
                            "amount": "{{$result['pricing']['totalTax']}}",
                            "code": "TotalTax",
                            "name": null
                        }
                    ],
                    "fess": null,
                    "extraBaggage": 0.0,
                    "commission": 0.0,
                    "thirdPartyCommission": 0.0,
                    "totalFare": {{$result['pricing']['totalTotalefare']}},
                    "totalFareWithExtraBaggage": 0.0
                },
                "ptC_FareBreakdown": [
                    {
                        "passengerFare": {
                            "baseFare":  "{{$result['pricing']['adultBaseFare']}}",
                            "taxes": [
                                {
                                    "amount": {{$result['pricing']['taxFare']}},
                                    "code": "TotalTax",
                                    "name": null
                                }
                            ],
                            "fess": null,
                            "extraBaggage": 0.0,
                            "commission": 0.0,
                            "thirdPartyCommission": 0.0,
                            "totalFare": {{$result['pricing']['adultTotaleFare']}},
                            "totalFareWithExtraBaggage": 0.0
                        },
                        "passengerTypeQuantity": {
                            "code": "ADT",
                            "quantity": {{$result['passengers']['adult']}}
                        },
                        "passengerBirthDateRange": {
                            "minBirhDate": "1921-12-15T14:25:00",
                            "maxBirthDate": "2009-12-15T14:25:00"
                        }
                    }
                    @if($result['passengers']['child']!=0)
                    ,
                    @endif
                    @if($result['passengers']['child']!=0)
                    {
                        "passengerFare": {
                            "baseFare": "{{$result['pricing']['childBaseFare']}}",
                            "taxes": [
                                {
                                    "amount": "{{$result['pricing']['taxFare']}}",
                                    "code": "TotalTax",
                                    "name": null
                                }
                            ],
                            "fess": null,
                            "extraBaggage": 0.0,
                            "commission": 0.0,
                            "thirdPartyCommission": 0.0,
                            "totalFare": {{$result['pricing']['childTotaleFare']}},
                            "totalFareWithExtraBaggage": 0.0
                        },
                        "passengerTypeQuantity": {
                            "code": "CHD",
                            "quantity": {{$result['passengers']['child']}}
                        },
                        "passengerBirthDateRange": {
                            "minBirhDate": "2009-12-16T14:25:00",
                            "maxBirthDate": "2019-12-15T14:25:00"
                        }
                    }
                    @endif
                    @if($result['passengers']['infant']!=0)
                    ,
                    @endif
                    @if($result['passengers']['infant']!=0)
                    {
                        "passengerFare": {
                            "baseFare": "{{$result['pricing']['infantBaseFare']}}",
                            "taxes": [
                                {
                                    "amount": "{{$result['pricing']['taxFare']}}",
                                    "code": "TotalTax",
                                    "name": null
                                }
                            ],
                            "fess": null,
                            "extraBaggage": 0.0,
                            "commission": 0.0,
                            "thirdPartyCommission": 0.0,
                            "totalFare": {{$result['pricing']['infantTotalFare']}},
                            "totalFareWithExtraBaggage": 0.0
                        },
                        "passengerTypeQuantity": {
                            "code": "INF",
                            "quantity": {{$result['passengers']['infant']}}
                        },
                        "passengerBirthDateRange": {
                            "minBirhDate": "2020-12-16T14:25:00",
                            "maxBirthDate": "2022-12-15T14:25:00"
                        }
                    }
                    @endif
                ]
            }
        },
        
        
        "provider": "TravelFusion",
        "captchaLink": null
    },
    "targetUrl": null,
    "success": true,
    "error": null,
    "unAuthorizedRequest": false,
    "__abp": true
}