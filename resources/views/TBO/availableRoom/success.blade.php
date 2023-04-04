<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing"><s:Header><a:Action s:mustUnderstand="1">http://TekTravel/HotelBookingApi/IHotelService/AvailableHotelRoomsResponse</a:Action></s:Header><s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><HotelRoomAvailabilityResponse xmlns="http://TekTravel/HotelBookingApi"><Status><StatusCode>01</StatusCode><Description>Successful: AvailableHotelRoom Successful</Description></Status><ResultIndex>1</ResultIndex><HotelRooms>
                @foreach($roomsInfo as $key=>$room)
                <HotelRoom>
                    <RoomIndex>{{$room['RoomIndex']}}</RoomIndex>
                    <RoomTypeName>{{$room['RoomTypeName']}}</RoomTypeName>
                    <Inclusion>{{$room['Inclusion']}}</Inclusion>
                    <RoomTypeCode>{{$room['RoomTypeCode']}}</RoomTypeCode>
                    <RatePlanCode>{{$room['RatePlanCode']}}</RatePlanCode>
                    <RoomRate IsInstantConfirmed="{{$room['IsInstantConfirmed']}}" B2CRates="{{$room['B2CRates']}}" IsPackageRate="{{$room['IsPackageRate']}}" Currency="{{$room['Currency']}}" TotalFare="{{$room['TotalFare']}}" AgentMarkUp="0.00" RoomFare="{{$room['RoomFare']}}" RoomTax="{{$room['RoomTax']}}">
                        <DayRates>
                            @foreach($dates as $keyDate=>$date)
                            <DayRate Date="{{$date}}" BaseFare="{{12.17016000+$keyDate+20}}"/>
                            @endforeach
                        </DayRates>
                        <ExtraGuestCharges>0</ExtraGuestCharges>
                        <ChildCharges>0</ChildCharges>
                        <Discount>0</Discount>
                        <OtherCharges>0</OtherCharges>
                        <ServiceTax>0</ServiceTax>
                    </RoomRate>
                    <RoomPromtion/>
                    <RoomAdditionalInfo>
                        <Description>&lt;p&gt;&lt;/p&gt;&lt;p&gt;&lt;b&gt;Internet&lt;/b&gt; - Free WiFi &lt;/p&gt;&lt;p&gt;&lt;b&gt;Food and Drink&lt;/b&gt; - Refrigerator&lt;/p&gt;&lt;p&gt;&lt;b&gt;Sleep&lt;/b&gt; - Bed sheets &lt;/p&gt;&lt;p&gt;&lt;b&gt;Bathroom&lt;/b&gt; - Shared bathroom and in-room sink&lt;/p&gt;&lt;p&gt;&lt;b&gt;Comfort&lt;/b&gt; - Climate-controlled air conditioning and heating&lt;/p&gt;&lt;p&gt;&lt;b&gt;Need to Know&lt;/b&gt; - Weekly housekeeping, toothbrush and toothpaste not available&lt;/p&gt;&lt;p&gt;Non-Smoking&lt;/p&gt;&lt;p&gt;Room/bed type depends on availability at check-in &lt;/p&gt;</Description>
                        <ImageURLs>
                            <URL>https://api.tbotechnology.in/imageresource.aspx?img=FbrGPTrju5e5v0qrAGTD8pPBsj8/wYA5lPqBj/Ape0+Hf/or2ZERs9ajKDNCt3kVIo6M/u08yxx1OrzM3H1jyB3g/J9M+Xrre0BRrGCR9g0ws9jl4bcCNA==</URL>
                            <URL>https://api.tbotechnology.in/imageresource.aspx?img=FbrGPTrju5e5v0qrAGTD8pPBsj8/wYA5lPqBj/Ape0+Hf/or2ZERs9ajKDNCt3kVIo6M/u08yxx1OrzM3H1jyE/0KR07tiAzzxNqvTJ0Efj0Gi52HmvgVw==</URL>
                            <URL>https://api.tbotechnology.in/imageresource.aspx?img=FbrGPTrju5e5v0qrAGTD8pPBsj8/wYA5lPqBj/Ape0+Hf/or2ZERs9ajKDNCt3kVIo6M/u08yxx1OrzM3H1jyFRZp8IfF1HnBvatZ34CB3nPVO6+9t0mAg==</URL>
                            <URL>https://api.tbotechnology.in/imageresource.aspx?img=FbrGPTrju5e5v0qrAGTD8pPBsj8/wYA5lPqBj/Ape0+Hf/or2ZERs9ajKDNCt3kVIo6M/u08yxx1OrzM3H1jyCsiNEVeuH2D62LJ9i+HErUSarvxtzZbxw==</URL>
                            <URL>https://api.tbotechnology.in/imageresource.aspx?img=FbrGPTrju5e5v0qrAGTD8pPBsj8/wYA5lPqBj/Ape0+Hf/or2ZERs9ajKDNCt3kVIo6M/u08yxx1OrzM3H1jyLuJbEec4r0sPgO1fAzJIA+C8ILoVhO8zQ==</URL>
                            <URL>https://api.tbotechnology.in/imageresource.aspx?img=FbrGPTrju5e5v0qrAGTD8pPBsj8/wYA5lPqBj/Ape0+Hf/or2ZERs9ajKDNCt3kVIo6M/u08yxx1OrzM3H1jyIeOSuMhe8jkGO/5ZvaJJLz2bqKvhIgXmg==</URL>
                        </ImageURLs>
                    </RoomAdditionalInfo>
                    <CancelPolicies PolicyFormat="Nodes">
                        <LastCancellationDeadline>{{$cnaselingDeadline}}</LastCancellationDeadline>
                        @if($haveCanseling==true)
                            <CancelPolicy RoomTypeName="{{$room['RoomTypeName']}}" FromDate="{{$dates[0]}}" ToDate="{{$cnaselingDeadline}}" ChargeType="Percentage" CancellationCharge="0" Currency="USD"/>
                        @endif
                        <CancelPolicy RoomTypeName="{{$room['RoomTypeName']}}" FromDate="{{$dates[0]}}" ToDate="{{$cnaselingDeadline}}" ChargeType="Percentage" CancellationCharge="100" Currency="USD"/>
                        <DefaultPolicy>Early check out will attract full cancellation charge unless otherwise specified.</DefaultPolicy>
                    </CancelPolicies>
                    <Amenities>Free WiFi|Heating|Non-Smoking|Bedsheets provided|Shower only|Room and bed type depend on availability check-in|Limited housekeeping|In-room climate control (air conditioning)|Shared bathroom with sink in room|Refrigerator|Weekly housekeeping provided</Amenities>
                    <MealType>Room_Only</MealType>
                </HotelRoom>
                 @endforeach

            </HotelRooms>
            <OptionsForBooking>
                <FixedFormat>true</FixedFormat>
              
               @for($a=0;$a < count($indexes);$a+=$countCombination)
               @for($b=0;$b < $countCombination;$b++)
               @if(empty($indexes[$a+$b]))
               @break(2)
               @endif
               @endfor
                <RoomCombination>
                    @for($b=0;$b < $countCombination;$b++)
                    <RoomIndex>{{$indexes[$a+$b]}}</RoomIndex>
                    @endfor
                </RoomCombination>
                @endfor
            </OptionsForBooking>
        </HotelRoomAvailabilityResponse>
    </s:Body>
</s:Envelope>