<ota:OTA_AirLowFareSearchRS xmlns:ota="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" EchoToken="36732" RetransmissionIndicator="false" SequenceNmbr="284" Target="Production" TimeStamp="2021-04-03T07:56:08.128Z" Version="2.001" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 OTA_AirLowFareSearchRS.xsd">
	<ota:Success />
	<ota:PricedItineraries>
		<ota:PricedItinerary SequenceNumber="1">
			<ota:AirItinerary>
				<ota:OriginDestinationOptions>
					@if(!empty($result['searchInformations']['ArrivalDateTimeGO']))
					<ota:OriginDestinationOption>
						<ota:FlightSegment ResBookDesigCode="K" Status="34" FlightNumber="{{$result['searchInformations']['FlightNumberGO']}}" ArrivalDateTime="{{$result['searchInformations']['ArrivalDateTimeGO']}}" DepartureDateTime="{{$result['searchInformations']['DepartureDateTimeGO']}}" StopQuantity="0">
							<ota:DepartureAirport LocationCode="{{$result['searchInformations']['OriginLocationGo']}}" />
							<ota:ArrivalAirport LocationCode="{{$result['searchInformations']['ArivalLocationGo']}}" />
							<ota:OperatingAirline FlightNumber="{{$result['searchInformations']['FlightNumberGO']}}" Code="SX" />
							<ota:MarketingAirline Code="SX" />
							<ota:BookingClassAvails CabinType="Economy">
								<ota:BookingClassAvail ResBookDesigCode="K" ResBookDesigQuantity="12" />
							</ota:BookingClassAvails>
							<ota:TPA_Extensions>
								<Equipment xmlns="" AirEquipType="S20" />
							</ota:TPA_Extensions>
						</ota:FlightSegment>
					</ota:OriginDestinationOption>
					<ota:OriginDestinationOption>
						<ota:FlightSegment ResBookDesigCode="K" Status="34" FlightNumber="{{$result['searchInformations']['FlightNumberBack']}}" ArrivalDateTime="{{$result['searchInformations']['ArrivalDateTimeBack']}}" DepartureDateTime="{{$result['searchInformations']['DepartureDateTimeBack']}}" StopQuantity="0">
							<ota:DepartureAirport LocationCode="{{$result['searchInformations']['OriginLocationBack']}}" />
							<ota:ArrivalAirport LocationCode="{{$result['searchInformations']['ArivalLocationBack']}}" />
							<ota:OperatingAirline FlightNumber="{{$result['searchInformations']['FlightNumberBack']}}" Code="SX" />
							<ota:MarketingAirline Code="SX" />
							<ota:BookingClassAvails CabinType="Economy">
								<ota:BookingClassAvail ResBookDesigCode="K" ResBookDesigQuantity="12" />
							</ota:BookingClassAvails>
							<ota:TPA_Extensions>
								<Equipment xmlns="" AirEquipType="S20" />
							</ota:TPA_Extensions>
						</ota:FlightSegment>
					</ota:OriginDestinationOption>
					@else
					<ota:OriginDestinationOption>
						<ota:FlightSegment ResBookDesigCode="K" Status="34" FlightNumber="{{$result['searchInformations']['FlightNumber']}}" ArrivalDateTime="{{$result['searchInformations']['ArrivalDateTime']}}" DepartureDateTime="{{$result['searchInformations']['DepartureDateTime']}}" StopQuantity="0">
							<ota:DepartureAirport LocationCode="{{$result['searchInformations']['OriginLocation']}}" />
							<ota:ArrivalAirport LocationCode="{{$result['searchInformations']['ArivalLocation']}}" />
							<ota:OperatingAirline FlightNumber="{{$result['searchInformations']['FlightNumber']}}" Code="SX" />
							<ota:MarketingAirline Code="SX" />
							<ota:BookingClassAvails CabinType="Economy">
								<ota:BookingClassAvail ResBookDesigCode="K" ResBookDesigQuantity="12" />
							</ota:BookingClassAvails>
							<ota:TPA_Extensions>
								<Equipment xmlns="" AirEquipType="S20" />
							</ota:TPA_Extensions>
						</ota:FlightSegment>
					</ota:OriginDestinationOption>
					@endif
				</ota:OriginDestinationOptions>
			</ota:AirItinerary>
			<ota:AirItineraryPricingInfo>
				<ota:ItinTotalFare>
					<ota:BaseFare Amount="{{$result['pricing']['totalbasefare']}}" CurrencyCode="EUR" />
					<ota:TotalFare Amount="{{$result['pricing']['totalTotalefare']}}" CurrencyCode="EUR" />
				</ota:ItinTotalFare>
				<ota:FareInfos>
					@if(isset($result['searchInformations']['ArrivalDateTimeGO']))
					<ota:FareInfo>
						<ota:FareReference>KOWEWEB</ota:FareReference>
						<ota:FilingAirline>SX</ota:FilingAirline>
						<ota:DepartureAirport LocationCode="{{$result['searchInformations']['OriginLocationGo']}}" />
						<ota:ArrivalAirport LocationCode="{{$result['searchInformations']['ArivalLocationGo']}}" />
						<ota:TPA_Extensions>
							<PriceGroup xmlns="" Name="Excellence" />
						</ota:TPA_Extensions>
					</ota:FareInfo>
					<ota:FareInfo>
						<ota:FareReference>KOWEWEB</ota:FareReference>
						<ota:FilingAirline>SX</ota:FilingAirline>
						<ota:DepartureAirport LocationCode="{{$result['searchInformations']['OriginLocationBack']}}" />
						<ota:ArrivalAirport LocationCode="{{$result['searchInformations']['ArivalLocationBack']}}" />
						<ota:TPA_Extensions>
							<PriceGroup xmlns="" Name="Excellence" />
						</ota:TPA_Extensions>
					</ota:FareInfo>
					@else
					<ota:FareInfo>
						<ota:FareReference>KOWEWEB</ota:FareReference>
						<ota:FilingAirline>SX</ota:FilingAirline>
						<ota:DepartureAirport LocationCode="{{$result['searchInformations']['OriginLocation']}}" />
						<ota:ArrivalAirport LocationCode="{{$result['searchInformations']['ArivalLocation']}}" />
						<ota:TPA_Extensions>
							<PriceGroup xmlns="" Name="Excellence" />
						</ota:TPA_Extensions>
					</ota:FareInfo>
					@endif
				</ota:FareInfos>
			</ota:AirItineraryPricingInfo>
		</ota:PricedItinerary>
	</ota:PricedItineraries>
</ota:OTA_AirLowFareSearchRS>