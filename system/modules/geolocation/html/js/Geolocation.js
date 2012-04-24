/**
 * Geolocation Information Module
 * 
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 */
window.addEvent("domready",function(){if(typeof chosen=="function"){$$(".geochange").chosen()}});var GeoEdit=new Class({Implements:[Options],options:{debug:false,messages:{}},initialize:function(a){this.setOptions(a)},setMessages:function(a){options={};options.messages=a;this.setOptions(options)},changeGeoLocation:function(b,a,e){var d=$(b).getSelected().get("text")[0];var c=$(b).getSelected().get("value")[0];if(typeof(REQUEST_TOKEN)!=="undefined"){data={action:"GeoChangeLocation",location:c,REQUEST_TOKEN:REQUEST_TOKEN}}else{data={action:"GeoChangeLocation",location:c}}if($(a)!=null){$(a).set("html",this.options.messages.changing)}new Request.JSON({method:"post",url:"ajax.php?language="+e,data:data,evalScripts:false,evalResponse:false,onSuccess:function(g,f){if(typeof(REQUEST_TOKEN)!=="undefined"){REQUEST_TOKEN=g.token}if(g.content.success==true){window.location.reload()}else{if($(a)){$(a).set("html",g.content.error)}}}.bind(this),onFailure:function(g,f){if($(a)){$(a).set("html",this.options.messages.noConnection)}}.bind(this)}).send()}});var GeoUpdater=new GeoEdit();