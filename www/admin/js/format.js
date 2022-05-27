var thousand_sep = ".";
var decimal_point = ",";
var default_precision = 2;

function getFloat(num){
	num = num.split(thousand_sep).join('');
	if(decimal_point==',') num = num.replace(/,/g,'.');
	return parseFloat(num);
}

function formatFloat(aFloat, aPrecision){
	try {
		precision = default_precision;
		if(!isNaN(aPrecision))
			if(Math.abs(aPrecision)<=10)
				precision = aPrecision;
	} catch(e) {
		precision = default_precision;
	}
	
	try {
		number = parseFloat(aFloat+'');
		if(isNaN(number))
			return 0;
	} catch(e) {
		return "NaN";
	}

	number = Math.round(number * Math.pow(10, precision)) / Math.pow(10,precision);
	integerpart = '' + ((number<0) ? Math.ceil(number) :
	Math.floor(number));
	decimalpart = Math.abs(Math.round((number - integerpart)*(Math.pow(10,precision))));
	if(decimalpart<10) decimalpart="0"+decimalpart;
	if(decimalpart==0) decimalpart="00";
	
	var buff = "";
	for(j=-1, i=integerpart.length; i>=0; i--, j++){
		if((j%3) == 0 && j>1) buff = thousand_sep + buff;
		buff = integerpart.charAt(i) + buff;
	}
	
	if(precision>0)
		return buff+decimal_point+decimalpart;
	return buff;
}

function formatInt(aInt){
	return formatFloat(aInt,0);
}
