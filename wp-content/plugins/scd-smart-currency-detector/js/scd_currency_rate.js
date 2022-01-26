  var Currency = {
    rates: {"USD":1.0,"EUR":1.18392,"GBP":1.32809,"CAD":0.765539,"ARS":0.0134347,"AUD":0.728254,"BRL":0.188592,"CLP":0.00129616,"CNY":0.146135,"CYP":0.397899,"CZK":0.0447148,"DKK":0.159115,"EEK":0.0706676,"HKD":0.129029,"HUF":0.00328755,"ISK":0.00718798,"INR":0.0136486,"JMD":0.00670881,"JPY":0.00941284,"LVL":1.57329,"LTL":0.320236,"MTL":0.293496,"MXN":0.0463803,"NZD":0.672054,"NOK":0.112181,"PLN":0.265659,"SGD":0.732705,"SKK":21.5517,"SIT":175.439,"ZAR":0.0602587,"KRW":0.000842059,"SEK":0.114407,"CHF":1.09479,"TWD":0.0340801,"UYU":0.0234714,"MYR":0.241098,"BSD":1.0,"CRC":0.0016795,"RON":0.24389,"PHP":0.0205889,"AED":0.272294,"VEB":0.000100125,"IDR":6.77877e-05,"TRY":0.134397,"THB":0.0318488,"TTD":0.147806,"ILS":0.296776,"SYP":0.00195584,"XCD":0.370051,"COP":0.000269169,"RUB":0.0132716,"HRK":0.157001,"KZT":0.00237666,"TZS":0.00043126,"XPT":903.86,"SAR":0.266667,"NIO":0.0287131,"LAK":0.000109839,"OMR":2.60078,"AMD":0.00205302,"CDF":0.000510021,"KPW":0.00111111,"SPL":6.0,"KES":0.00922625,"ZWD":0.00276319,"KHR":0.00024365,"MVR":0.0648588,"GTQ":0.129605,"BZD":0.496796,"BYR":3.78802e-05,"LYD":0.733457,"DZD":0.00779061,"BIF":0.000516791,"GIP":1.32809,"BOB":0.144981,"XOF":0.00180487,"STD":4.81961e-05,"NGN":0.00262522,"PGK":0.289253,"ERN":0.0666667,"MWK":0.00135579,"CUP":0.0377358,"GMD":0.0192927,"CVE":0.0107365,"BTN":0.0136486,"XAF":0.00180487,"UGX":0.000271377,"MAD":0.109001,"MNT":0.000349985,"LSL":0.0602587,"XAG":26.9221,"TOP":0.428771,"SHP":1.32809,"RSD":0.010073,"HTG":0.00892468,"MGA":0.000260232,"MZN":0.0139827,"FKP":1.32809,"BWP":0.0870999,"HNL":0.0406028,"PYG":0.000143356,"JEP":1.32809,"EGP":0.0632538,"LBP":0.00066335,"ANG":0.559329,"WST":0.379128,"TVD":0.728254,"GYD":0.00475608,"GGP":1.32809,"NPR":0.00849057,"KMF":0.00240649,"IRR":2.37934e-05,"XPD":2314.95,"SRD":0.134094,"TMM":5.70031e-05,"SZL":0.0602587,"MOP":0.125271,"BMD":1.0,"XPF":0.00992122,"ETB":0.0275437,"JOD":1.41044,"MDL":0.060555,"MRO":0.00263282,"YER":0.00399429,"BAM":0.605327,"AWG":0.558659,"PEN":0.282208,"VEF":0.100125,"SLL":0.000102179,"KYD":1.2195,"AOA":0.00162658,"TND":0.36535,"TJS":0.0972872,"SCR":0.0556773,"LKR":0.00540384,"DJF":0.00561814,"GNF":0.00010352,"VUV":0.00894552,"SDG":0.0181383,"IMP":1.32809,"GEL":0.324859,"FJD":0.472102,"DOP":0.0171236,"XDR":1.41553,"MUR":0.0251176,"MMK":0.000751932,"LRD":0.00508867,"BBD":0.5,"ZMK":5.09716e-05,"XAU":1933.65,"VND":4.31241e-05,"UAH":0.0359172,"TMT":0.285016,"IQD":0.000841013,"BGN":0.605327,"KGS":0.0127089,"RWF":0.00103773,"BHD":2.65957,"UZS":9.73405e-05,"PKR":0.00602912,"MKD":0.0192018,"AFN":0.0130203,"NAD":0.0602587,"BDT":0.011797,"AZN":0.588286,"SOS":0.00173054,"QAR":0.274725,"PAB":1.0,"CUC":1.0,"SVC":0.114286,"SBD":0.12202,"ALL":0.00953418,"BND":0.732705,"KWD":3.26801,"GHS":0.172706,"ZMW":0.0509716,"XBT":10285.9,"NTD":0.0337206,"BYN":0.378802,"CNH":0.146251,"MRU":0.0263282,"STN":0.0481961,"VES":2.92644e-06,"MXV":0.303536},
    convert: function(amount, from, to) {
      return (amount * this.rates[from]) / this.rates[to];
    }
  };