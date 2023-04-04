<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <soap:Header>
        <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
        <wsa:From>
            <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
        </wsa:From>
        <wsa:Action>http://webservices.amadeus.com/TFOPCQ_19_2_1A</wsa:Action>
        <wsa:MessageID>urn:uuid:2e2c4310-47c7-2c24-5dc9-3cf16b510bab</wsa:MessageID>
        <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">7c220e8a-a4e2-48fa-a69c-6ef79d05e2e9</wsa:RelatesTo>
        <awsse:Session TransactionStatusCode="InSeries">
            <awsse:SessionId>{{ $SessionId }}</awsse:SessionId>
            <awsse:SequenceNumber>3</awsse:SequenceNumber>
            <awsse:SecurityToken>{{ $SessionToken }}</awsse:SecurityToken>
        </awsse:Session>
    </soap:Header>
    <soap:Body>
        <FOP_CreateFormOfPaymentReply xmlns="http://xml.amadeus.com/TFOPCR_19_2_1A">
            <fopDescription>
                <fopReference>
                    <reference>
                        <qualifier>FPT</qualifier>
                        <number>4</number>
                    </reference>
                </fopReference>
                <mopDescription>
                    <fopSequenceNumber>
                        <sequenceDetails>
                            <number>1</number>
                        </sequenceDetails>
                    </fopSequenceNumber>
                    <mopDetails>
                        <fopPNRDetails>
                            <fopDetails>
                                <fopCode>CASH</fopCode>
                                <fopStatus>N</fopStatus>
                                <fopEdiCode>CA</fopEdiCode>
                                <fopReportingCode>CA</fopReportingCode>
                                <fopElecTicketingCode>CA</fopElecTicketingCode>
                            </fopDetails>
                        </fopPNRDetails>
                        <oldFopFreeflow>
                            <freeTextDetails>
                                <textSubjectQualifier>ZZZ</textSubjectQualifier>
                                <source>M</source>
                                <encoding>ZZZ</encoding>
                            </freeTextDetails>
                            <freeText>CASH</freeText>
                        </oldFopFreeflow>
                        <pnrSupplementaryData>
                            <dataAndSwitchMap>
                                <criteriaSetType>S</criteriaSetType>
                                <criteriaDetails>
                                    <attributeType>13</attributeType>
                                    <attributeDescription>1</attributeDescription>
                                </criteriaDetails>
                            </dataAndSwitchMap>
                        </pnrSupplementaryData>
                        <pnrSupplementaryData>
                            <dataAndSwitchMap>
                                <criteriaSetType>D</criteriaSetType>
                                <criteriaDetails>
                                    <attributeType>CUR</attributeType>
                                    <attributeDescription>IQD</attributeDescription>
                                </criteriaDetails>
                                <criteriaDetails>
                                    <attributeType>FOPCODE</attributeType>
                                    <attributeDescription>CASH</attributeDescription>
                                </criteriaDetails>
                            </dataAndSwitchMap>
                        </pnrSupplementaryData>
                    </mopDetails>
                    <paymentModule>
                        <groupUsage>
                            <attributeDetails>
                                <attributeType>FP</attributeType>
                            </attributeDetails>
                        </groupUsage>
                        <dummy></dummy>
                    </paymentModule>
                </mopDescription>
            </fopDescription>
        </FOP_CreateFormOfPaymentReply>
    </soap:Body>
</soap:Envelope>