<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing"><s:Header><a:Action s:mustUnderstand="1">http://schemas.microsoft.com/net/2005/12/windowscommunicationfoundation/dispatcher/fault</a:Action></s:Header>
    <s:Body>
        <s:Fault>
            <s:Code>
                <s:Value>s:Receiver</s:Value>
                <s:Subcode>
                    <s:Value xmlns:a="http://schemas.microsoft.com/net/2005/12/windowscommunicationfoundation/dispatcher">a:InternalServiceFault</s:Value>
                </s:Subcode>
            </s:Code>
            <s:Reason>
                <s:Text xml:lang="en-GB">Error in deserializing body of request message for operation 'HotelSearch'.</s:Text>
            </s:Reason>
            <s:Detail>
                <ExceptionDetail xmlns="http://schemas.datacontract.org/2004/07/System.ServiceModel" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                    <HelpLink i:nil="true" />
                    <InnerException>
                        <HelpLink i:nil="true" />
                        <InnerException>
                            <HelpLink i:nil="true" />
                            <InnerException i:nil="true" />
                            <Message>The string ' ' is not a valid AllXsd value.</Message>
                            <StackTrace> at System.Xml.Schema.XsdDateTime..ctor(String text, XsdDateTimeFlags kinds)&#xD;
                                at System.Xml.XmlConvert.ToDateTime(String s, XmlDateTimeSerializationMode dateTimeOption)&#xD;
                                at Microsoft.Xml.Serialization.GeneratedAssembly.XmlSerializationReaderIHotelService.Read109_HotelSearchRequest()&#xD;
                                at System.Xml.Serialization.XmlSerializer.Deserialize(XmlReader xmlReader, String encodingStyle, XmlDeserializationEvents events)</StackTrace>
                            <Type>System.FormatException</Type>
                        </InnerException>
                        <Message>There is an error in XML document (11, 13).</Message>
                        <StackTrace> at System.Xml.Serialization.XmlSerializer.Deserialize(XmlReader xmlReader, String encodingStyle, XmlDeserializationEvents events)&#xD;
                            at System.ServiceModel.Dispatcher.XmlSerializerOperationFormatter.DeserializeBody(XmlDictionaryReader reader, MessageVersion version, XmlSerializer serializer, MessagePartDescription returnPart, MessagePartDescriptionCollection bodyParts, Object[] parameters, Boolean isRequest)</StackTrace>
                        <Type>System.InvalidOperationException</Type>
                    </InnerException>
                    <Message>Error in deserializing body of request message for operation 'HotelSearch'.</Message>
                    <StackTrace> at System.ServiceModel.Dispatcher.XmlSerializerOperationFormatter.DeserializeBody(XmlDictionaryReader reader, MessageVersion version, XmlSerializer serializer, MessagePartDescription returnPart, MessagePartDescriptionCollection bodyParts, Object[] parameters, Boolean isRequest)&#xD;
                        at System.ServiceModel.Dispatcher.XmlSerializerOperationFormatter.DeserializeBody(XmlDictionaryReader reader, MessageVersion version, String action, MessageDescription messageDescription, Object[] parameters, Boolean isRequest)&#xD;
                        at System.ServiceModel.Dispatcher.OperationFormatter.DeserializeBodyContents(Message message, Object[] parameters, Boolean isRequest)&#xD;
                        at System.ServiceModel.Dispatcher.OperationFormatter.DeserializeRequest(Message message, Object[] parameters)&#xD;
                        at System.ServiceModel.Dispatcher.DispatchOperationRuntime.DeserializeInputs(MessageRpc&amp; rpc)&#xD;
                        at System.ServiceModel.Dispatcher.DispatchOperationRuntime.InvokeBegin(MessageRpc&amp; rpc)&#xD;
                        at System.ServiceModel.Dispatcher.ImmutableDispatchRuntime.ProcessMessage5(MessageRpc&amp; rpc)&#xD;
                        at System.ServiceModel.Dispatcher.ImmutableDispatchRuntime.ProcessMessage11(MessageRpc&amp; rpc)&#xD;
                        at System.ServiceModel.Dispatcher.MessageRpc.Process(Boolean isOperationContextSet)</StackTrace>
                    <Type>System.ServiceModel.CommunicationException</Type>
                </ExceptionDetail>
            </s:Detail>
        </s:Fault>
    </s:Body>
</s:Envelope>