<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing"><s:Header><a:Action s:mustUnderstand="1">http://TekTravel/HotelBookingApi/IHotelService/AvailabilityAndPricingResponse</a:Action></s:Header><s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><AvailabilityAndPricingResponse xmlns="http://TekTravel/HotelBookingApi"><Status><StatusCode>01</StatusCode><Description>Successful: AvailabilityAndPricing Successful</Description></Status>
            <ResultIndex>311</ResultIndex>
            <AvailableForBook>{{$bookable}}</AvailableForBook>
            <AvailableForConfirmBook>{{$holdable}}</AvailableForConfirmBook>
            <CancellationPoliciesAvailable>true</CancellationPoliciesAvailable>
            <HotelCancellationPolicies>
                <HotelNorms>
                    <string>CheckIn Time-Begin: 2:00 PM </string>
                    <string> CheckIn Time-End: anytime</string>
                    <string>CheckOut Time: 12:00 PM</string>
                    <string>CheckIn Instructions: &amp;lt;ul&amp;gt;  &amp;lt;li&amp;gt;Extra-person charges may apply and vary depending on property policy&amp;lt;/li&amp;gt;&amp;lt;li&amp;gt;Government-issued photo identification and a credit card, debit card, or cash deposit may be required at check-in for incidental charges&amp;lt;/li&amp;gt;&amp;lt;li&amp;gt;Special requests are subject to availability upon check-in and may incur additional charges; special requests cannot be guaranteed&amp;lt;/li&amp;gt;&amp;lt;li&amp;gt;This property accepts credit cards; cash is not accepted&amp;lt;/li&amp;gt;  &amp;lt;/ul&amp;gt; </string>
                    <string> Special Instructions : Front desk staff will greet guests on arrival.</string>
                    <string>Minimum CheckIn Age : 18</string>
                    <string> Optional Fees: &amp;lt;p&amp;gt;The following fees and deposits are charged by the property at time of service, check-in, or check-out. &amp;lt;/p&amp;gt; &amp;lt;ul&amp;gt;      &amp;lt;li&amp;gt;Airport shuttle fee: TRY 4 per vehicle (roundtrip, maximum occupancy 4)&amp;lt;/li&amp;gt; &amp;lt;li&amp;gt;Airport shuttle fee per child: TRY 2 (roundtrip), (from 2 to 4 years old)&amp;lt;/li&amp;gt;                         &amp;lt;/ul&amp;gt; &amp;lt;p&amp;gt;The above list may not be comprehensive. Fees and deposits may not include tax and are subject to change. &amp;lt;/p&amp;gt;</string>
                    <string>Cards Accepted: Visa,Debit cards not accepted,Cash not accepted,Mastercard</string>
                    <string>&amp;lt;ul&amp;gt;  &amp;lt;li&amp;gt;This property offers transfers from the airport (surcharges may apply). Guests must contact the property with arrival details 24 hours prior to arrival, using the contact information on the booking confirmation. &amp;lt;/li&amp;gt; &amp;lt;li&amp;gt;Reservations are required for massage services. Reservations can be made by contacting the hotel prior to arrival, using the contact information on the booking confirmation. &amp;lt;/li&amp;gt;&amp;lt;li&amp;gt;No pets and no service animals are allowed at this property. &amp;lt;/li&amp;gt; &amp;lt;/ul&amp;gt;,Service animals not allowed,Pets not allowed,No rollaway/extra beds available,Essential Workers Only - NO</string>
                    <string>Please refer to the following Terms of Use - http://mytravelagent.online/termsofuse.pdf</string>
                    <string>Please be advised that booking a room does not guarantee bedding type. It might be Double with an extra bed, Double with Sofabed or three separate beds. It may be a double bed only with no extra bed. This is all depending upon the hotel's policies worldwide.</string>
                    <string/>
                </HotelNorms>
                <CancelPolicies PolicyFormat="Nodes">
                    <LastCancellationDeadline>2021-09-06T00:00:00+00:00</LastCancellationDeadline>
                    <CancelPolicy FromDate="2021-09-07" ToDate="2021-09-12" ChargeType="Percentage" CancellationCharge="100" Currency="USD"/>
                    <DefaultPolicy>Early check out will attract full cancellation charge unless otherwise specified.</DefaultPolicy>
                    <AutoCancellationText/>
                </CancelPolicies>
            </HotelCancellationPolicies>
            <PriceVerification Status="Successful" PriceChanged="false" AvailableOnNewPrice="false"/>
            <AccountInfo AgencyBalance="Sufficient" AgencyBlocked="false"/>
            <HotelDetailsVerification Status="Successful" Remarks=""/>
            <HotelDetails HotelName="Askoc Hotel" HotelRating="ThreeStar">
                <Address>Istasyon Arka Sokak Street 15,Istanbul,Istanbul,TR</Address>
                <Map>41.01467|28.977907</Map>
            </HotelDetails>
            <IsFlightDetailsMandatory>false</IsFlightDetailsMandatory>
        </AvailabilityAndPricingResponse>
    </s:Body>
</s:Envelope>