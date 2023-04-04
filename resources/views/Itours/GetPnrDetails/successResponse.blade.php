{
    "result": {
        "flightSegments": [
            @if(isset($result['options']['ArrivalDateTimeBack']))
            {
                "isPast": false,
                "ticketTimeLimit": null,
                "airlineRefId": "DCLH*OX8RDN",
                "flightNumber": "{{$result['options']['FlightNumberGo']}}",
                "departureDateTime": "{{$result['options']['DepartureDateTimeGo']}}",
                "arrivalDateTime": "{{$result['options']['ArrivalDateTimeGo']}}",
                "resBookDesigCode": "K",
                "arrivalAirport": {
                    "locationCode": "{{$result['options']['ArivelLocationCodeGo']}}",
                    "terminalID": "2",
                    "locationName": "{{$result['options']['destinationAirportGo']}}",
                    "countryName": "{{$result['options']['destinationCountryGo']}}",
                    "cityName": "{{$result['options']['destinationCityGo']}}"
                },
                "departureAirport": {
                    "locationCode": "{{$result['options']['DeparturLocationCodeGo']}}",
                    "terminalID": "3",
                    "locationName": "{{$result['options']['originAirportGo']}}",
                    "countryName": "{{$result['options']['originCountryGo']}}",
                    "cityName": "{{$result['options']['originCityGo']}}"
                },
                "marketingAirline": {
                    "code": "LH",
                    "name": "Deutsche Lufthansa AG",
                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Deutsche Lufthansa AG.png"
                },
                "operatingAirline": {
                    "code": "LH",
                    "name": "Deutsche Lufthansa AG",
                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Deutsche Lufthansa AG.png"
                },
                "airEquipType": "320",
                "statusCode": "HK",
                "flightDuration": "{{$result['options']['FlightDurationGo']}}",
                "fareBasis": [
                    {
                        "fareBasisCode": "K03LGTEA",
                        "bookingCode": null,
                        "passengerType": "ADT"
                    }
                ],
                "baggageInformation": [
                    {
                        "baggageAllowance": 0,
                        "unitType": "N",
                        "passengerType": "ADT"
                    }
                ],
                "extraBaggageInformation": [],
                "handBagInformation": null,
                "cabinClass": {
                    "name": "ECONOMY",
                    "code": "Y"
                },
                "isOutbound": true,
                "stopTime": "00:00:00"
            },
            {
                "isPast": false,
                "ticketTimeLimit": null,
                "airlineRefId": "DCLH*OX8RDN",
                "flightNumber": "{{$result['options']['FlightNumberBack']}}",
                "departureDateTime": "{{$result['options']['DepartureDateTimeBack']}}",
                "arrivalDateTime": "{{$result['options']['ArrivalDateTimeBack']}}",
                "resBookDesigCode": "K",
                "arrivalAirport": {
                    "locationCode": "{{$result['options']['ArivelLocationCodeBack']}}",
                    "terminalID": "2",
                    "locationName": "{{$result['options']['destinationAirportBack']}}",
                    "countryName": "{{$result['options']['destinationCountryBack']}}",
                    "cityName": "{{$result['options']['destinationCityBack']}}"
                },
                "departureAirport": {
                    "locationCode": "{{$result['options']['DeparturLocationCodeBack']}}",
                    "terminalID": "3",
                    "locationName": "{{$result['options']['originAirportBack']}}",
                    "countryName": "{{$result['options']['originCountryBack']}}",
                    "cityName": "{{$result['options']['originCityBack']}}"
                },
                "marketingAirline": {
                    "code": "LH",
                    "name": "Deutsche Lufthansa AG",
                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Deutsche Lufthansa AG.png"
                },
                "operatingAirline": {
                    "code": "LH",
                    "name": "Deutsche Lufthansa AG",
                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Deutsche Lufthansa AG.png"
                },
                "airEquipType": "320",
                "statusCode": "HK",
                "flightDuration": "{{$result['options']['FlightDurationBack']}}",
                "fareBasis": [
                    {
                        "fareBasisCode": "K03LGTEA",
                        "bookingCode": null,
                        "passengerType": "ADT"
                    }
                ],
                "baggageInformation": [
                    {
                        "baggageAllowance": 0,
                        "unitType": "N",
                        "passengerType": "ADT"
                    }
                ],
                "extraBaggageInformation": [],
                "handBagInformation": null,
                "cabinClass": {
                    "name": "ECONOMY",
                    "code": "Y"
                },
                "isOutbound": true,
                "stopTime": "00:00:00"
            }
            @else
            {
                "isPast": false,
                "ticketTimeLimit": null,
                "airlineRefId": "DCLH*OX8RDN",
                "flightNumber": "{{$result['options']['FlightNumber']}}",
                "departureDateTime": "{{$result['options']['DepartureDateTime']}}",
                "arrivalDateTime": "{{$result['options']['ArrivalDateTime']}}",
                "resBookDesigCode": "K",
                "arrivalAirport": {
                    "locationCode": "{{$result['options']['ArivelLocationCode']}}",
                    "terminalID": "2",
                    "locationName": "{{$result['options']['destinationAirport']}}",
                    "countryName": "{{$result['options']['destinationCountry']}}",
                    "cityName": "{{$result['options']['destinationCity']}}"
                },
                "departureAirport": {
                    "locationCode": "{{$result['options']['DeparturLocationCode']}}",
                    "terminalID": "3",
                    "locationName": "{{$result['options']['originAirport']}}",
                    "countryName": "{{$result['options']['originCountry']}}",
                    "cityName": "{{$result['options']['originCity']}}"
                },
                "marketingAirline": {
                    "code": "LH",
                    "name": "Deutsche Lufthansa AG",
                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Deutsche Lufthansa AG.png"
                },
                "operatingAirline": {
                    "code": "LH",
                    "name": "Deutsche Lufthansa AG",
                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/Deutsche Lufthansa AG.png"
                },
                "airEquipType": "320",
                "statusCode": "HK",
                "flightDuration": "{{$result['options']['FlightDuration']}}",
                "fareBasis": [
                    {
                        "fareBasisCode": "K03LGTEA",
                        "bookingCode": null,
                        "passengerType": "ADT"
                    }
                ],
                "baggageInformation": [
                    {
                        "baggageAllowance": 0,
                        "unitType": "N",
                        "passengerType": "ADT"
                    }
                ],
                "extraBaggageInformation": [],
                "handBagInformation": null,
                "cabinClass": {
                    "name": "ECONOMY",
                    "code": "Y"
                },
                "isOutbound": true,
                "stopTime": "00:00:00"
            }
            @endif
        ],
        "passengerFare": [
            
            {
                "fareCalculation": "ROM LH X/MUC LH BER46.18NUC46.18END ROE0.844503",
                "passengerType": "ADT",
                "equivalentFare": 405.0,
                "baseFareCurrencyCode": "NOK",
                "baseFare": {{$result['pricing']['adultBaseFare']}},
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
            }
            
            
        ],
        "itinTotalFare": {
            "equivalentFare": 405.0,
            "baseFareCurrencyCode": "NOK",
            "baseFare": {{$result['pricing']['totalbasefare']}},
            "taxes": [
                {
                    "amount": {{$result['pricing']['totalTax']}},
                    "code": "TotalTax",
                    "name": "TotalTax"
                }
            ],
            "fess": null,
            "extraBaggage": 0.0,
            "commission": 0.0,
            "thirdPartyCommission": 0.0,
            "totalFare": {{$result['pricing']['totalTotalefare']}},
            "totalFareWithExtraBaggage": 0.0
        },
        "passengers": [
            @foreach($result['passengers'] as $passenger)
            {
                "ticketNumber": null,
                "firstName": "{{$passenger['firstName']}}",
                "lastName": "{{$passenger['lastName']}}",
                "code": "{{$passenger['code']}}",
                "gender": {{$passenger['gender']}},
                "email": "{{$result['reserver']['email']}}",
                "birthDate": "{{$passenger['birthDate']}}",
                "passportNumber": null,
                "passportExpireDate": null,
                "nationalId": null,
                "passengerId": null,
                "extraBaggageAmount": 0,
                "isExtraBaggageApplied": false
            }
            @if(!$loop->last)
            ,
            @endif
            @endforeach
        ],
        "pnrStatus": "Made",
        "pnrCode": "{{$result['pnrDetails']['pnr']}}",
        "key": "{{$result['pnrDetails']['key']}}",
        "validatingCarrier": "LH",
        "fareType": "Publish",
        "reserveId": 109342,
        "supplierId": 2,
        "currencyCode": "IRR",
        "isOutSidePNR": false,
        "reserveStatus": "Pending",
        "hasExtraBaggage": false
    },
    "targetUrl": null,
    "success": true,
    "error": null,
    "unAuthorizedRequest": false,
    "__abp": true
}