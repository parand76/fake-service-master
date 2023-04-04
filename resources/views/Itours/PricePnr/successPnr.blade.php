{
    "result": {
        "ticketInformation": [
            "VALIDATING CARRIER - LO",
            "CAT 15 SALES RESTRICTIONS FREE TEXT FOUND - VERIFY RULES",
            "BAG ALLOWANCE     -BERWAW-NIL/LO",
            "1STCHECKED BAG FEE-BERWAW-NOK1143/LO/UP TO 50 POUNDS/23 KILOGRA",
            "MS AND UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
            "2NDCHECKED BAG FEE-BERWAW-NOK1143/LO/UP TO 50 POUNDS/23 KILOGRA",
            "MS AND UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
            "CARRY ON ALLOWANCE",
            "BERWAW-01P/LO",
            "01/UP TO 18 POUNDS/8 KILOGRAMS AND UP TO 46 LINEAR INCHES/118 L",
            "INEAR CENTIMETERS",
            "CARRY ON CHARGES",
            "BERWAW-LO-CARRY ON FEES UNKNOWN-CONTACT CARRIER",
            "ADDITIONAL ALLOWANCES AND/OR DISCOUNTS MAY APPLY DEPENDING ON",
            "FLYER-SPECIFIC FACTORS /E.G. FREQUENT FLYER STATUS/MILITARY/",
            "CREDIT CARD FORM OF PAYMENT/EARLY PURCHASE OVER INTERNET,ETC./",
            "28APR DEPARTURE DATE-----LAST DAY TO PURCHASE 17AUG/1205"
        ],
        "baggagesInformation": "NA",

        @foreach($flightInfo as $flight)
        @if(!empty($flight[0]))
        "flightSegments": [
            @foreach($flight[0]['flightSegments'] as $key=>$item)
            {
                "isPast": false,
                "ticketTimeLimit": null,
                "airlineRefId": "DCLO*PDD57I",
                "flightNumber": "{{$item['flightNumber']}}",
                "departureDateTime": "{{$item['departureDateTime']}}",
                "arrivalDateTime": "{{$item['arrivalDateTime']}}",
                "resBookDesigCode": "O",
                "arrivalAirport": {
                    "locationCode": "{{$item['arrivalAirport']['locationCode']}}",
                    "terminalID": null,
                    "locationName": "{{$item['arrivalAirport']['locationName']}}",
                    "countryName": "{{$item['arrivalAirport']['countryName']}}",
                    "cityName": "{{$item['arrivalAirport']['cityName']}}"
                },
                "departureAirport": {
                    "locationCode": "{{$item['departureAirport']['locationCode']}}",
                    "terminalID": "1",
                    "locationName": "{{$item['departureAirport']['locationName']}}",
                    "countryName": null,
                    "cityName": "{{$item['departureAirport']['cityName']}}"
                },
                "marketingAirline": {
                    "code": "LO",
                    "name": "LOT - Polish Airlines",
                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/LOT - Polish Airlines.png"
                },
                "operatingAirline": {
                    "code": "LO",
                    "name": "LOT - Polish Airlines",
                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/LOT - Polish Airlines.png"
                },
                "airEquipType": "E70",
                "statusCode": "HK",
                "flightDuration": "01:25:00",
                "fareBasis": [
                    {
                        "fareBasisCode": "O1SAV21",
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
            @if(!$loop->last)
            ,
            @endif
            @endforeach
        ],
        @else
        "flightSegments": [
            @foreach($flight['flightSegments'] as $key=>$item)
            {
                "isPast": false,
                "ticketTimeLimit": null,
                "airlineRefId": "DCLO*PDD57I",
                "flightNumber": "{{$item['flightNumber']}}",
                "departureDateTime": "{{$item['departureDateTime']}}",
                "arrivalDateTime": "{{$item['arrivalDateTime']}}",
                "resBookDesigCode": "O",
                "arrivalAirport": {
                    "locationCode": "{{$item['arrivalAirport']['locationCode']}}",
                    "terminalID": null,
                    "locationName": "{{$item['arrivalAirport']['locationName']}}",
                    "countryName": "{{$item['arrivalAirport']['countryName']}}",
                    "cityName": "{{$item['arrivalAirport']['cityName']}}"
                },
                "departureAirport": {
                    "locationCode": "{{$item['departureAirport']['locationCode']}}",
                    "terminalID": "1",
                    "locationName": "{{$item['departureAirport']['locationName']}}",
                    "countryName": null,
                    "cityName": "{{$item['departureAirport']['cityName']}}"
                },
                "marketingAirline": {
                    "code": "LO",
                    "name": "LOT - Polish Airlines",
                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/LOT - Polish Airlines.png"
                },
                "operatingAirline": {
                    "code": "LO",
                    "name": "LOT - Polish Airlines",
                    "photoUrl": "https://cdn3.itours.no/Content/images/airlines/Thumbs/LOT - Polish Airlines.png"
                },
                "airEquipType": "E70",
                "statusCode": "HK",
                "flightDuration": "01:25:00",
                "fareBasis": [
                    {
                        "fareBasisCode": "O1SAV21",
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
            @if(!$loop->last)
            ,
            @endif
            @endforeach
        ],
        @endif
        @endforeach
        
        
    @if($passengersCount['adult']!=0)
        "passengerFare": [
            {
                "fareCalculation": "BER LO WAW53.28NUC53.28END ROE0.844503",
                "passengerType": "ADT",
                "equivalentFare": 468.0,
                "baseFareCurrencyCode": "NOK",
                "baseFare": {{$pricing['adultBaseFare']}},
                "taxes": [
                    {
                        "amount": {{$pricing['taxFare']}},
                        "code": "TotalTax",
                        "name": null
                    }
                ],
                "fess": null,
                "extraBaggage": 0.0,
                "commission": 0.0,
                "thirdPartyCommission": 0.0,
                "totalFare": {{$pricing['adultTotaleFare']}},
                "totalFareWithExtraBaggage": 0.0
            }
        ],
        
    @endif
   
    @if($passengersCount['child']!=0)
        "passengerFare": [
            {
                "fareCalculation": "BER LO WAW53.28NUC53.28END ROE0.844503",
                "passengerType": "CHD",
                "equivalentFare": 468.0,
                "baseFareCurrencyCode": "NOK",
                "baseFare": {{$pricing['childBaseFare']}},
                "taxes": [
                    {
                        "amount": {{$pricing['taxFare']}},
                        "code": "TotalTax",
                        "name": null
                    }
                ],
                "fess": null,
                "extraBaggage": 0.0,
                "commission": 0.0,
                "thirdPartyCommission": 0.0,
                "totalFare": {{$pricing['childTotaleFare']}},
                "totalFareWithExtraBaggage": 0.0
            }
        ],
    @endif
    @if($passengersCount['infant']!=0)
        "passengerFare": [
            {
                "fareCalculation": "BER LO WAW53.28NUC53.28END ROE0.844503",
                "passengerType": "INF",
                "equivalentFare": 468.0,
                "baseFareCurrencyCode": "NOK",
                "baseFare": {{$pricing['infantBaseFare']}},
                "taxes": [
                    {
                        "amount": {{$pricing['taxFare']}},
                        "code": "TotalTax",
                        "name": null
                    }
                ],
                "fess": null,
                "extraBaggage": 0.0,
                "commission": 0.0,
                "thirdPartyCommission": 0.0,
                "totalFare": {{$pricing['infantTotalFare']}},
                "totalFareWithExtraBaggage": 0.0
            }
        ],
    @endif
      
        "itinTotalFare": {
            "equivalentFare": 468.0,
            "baseFareCurrencyCode": "NOK",
            "baseFare": {{$pricing['totalbasefare']}},
            "taxes": [
                {
                    "amount": {{$pricing['totalTax']}},
                    "code": "TotalTax",
                    "name": "TotalTax"
                }
            ],
            "fess": null,
            "extraBaggage": 0.0,
            "commission": 0.0,
            "thirdPartyCommission": 0.0,
            "totalFare": {{$pricing['totalTotalefare']}},
            "totalFareWithExtraBaggage": 0.0
        },
        "passengers": [
            @foreach($passengers as $key=>$passenger)
            {
                "ticketNumber": null,
                "firstName": "{{$passenger['firstName']}}",
                "lastName": "{{$passenger['lastName']}}",
                "code": "{{$passenger['code']}}",
                "gender": {{$passenger['gender']}},
                "email": "{{$reserver['email']}}",
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
        "pnrStatus": "Priced",
        "pnrCode": "{{$resultFlight['pnrCode']}}",
        "key": "{{$resultFlight['flightKey']}}",
        "validatingCarrier": "LO",
        "fareType": "Publish",
        "reserveId": 109375,
        "supplierId": 2,
        "currencyCode": "USD",
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