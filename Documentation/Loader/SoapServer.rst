.. index:: ! SoapServer

.. _soap-server:

SoapServer
^^^^^^^^^^

The "SoapServer" Loader scans the Classes/Service/Soap/ folder of the extension wo find SOAP Service classes. If there is a class, the class is the SOAP Server representation. The server is accessable via /?eID=SoapServer&server=SOAPSERVERNAME. If you want to output the wsdl file, just add a &wsdl=1 to the URI.