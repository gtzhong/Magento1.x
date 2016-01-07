var myAjax = new Ajax.Request(
	url,{
		method: "POST",
		parameters : data,
		onComplete: function (xhr) { },
		onSuccess: function(transport) {
			eval("var obj = "+transport.responseText);
		}
	}
);